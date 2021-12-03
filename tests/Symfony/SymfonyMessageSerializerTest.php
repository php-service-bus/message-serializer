<?php

/** @noinspection PhpUnhandledExceptionInspection */

/**
 * Messages serializer implementation.
 *
 * @author  Maksim Masiukevich <contacts@desperado.dev>
 * @license MIT
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace ServiceBus\MessageSerializer\Tests\Symfony;

use PHPUnit\Framework\TestCase;
use ServiceBus\MessageSerializer\Exceptions\DenormalizeFailed;
use ServiceBus\MessageSerializer\Exceptions\EncodeObjectFailed;
use ServiceBus\MessageSerializer\Symfony\SymfonyJsonObjectSerializer;
use ServiceBus\MessageSerializer\Symfony\SymfonyObjectDenormalizer;
use ServiceBus\MessageSerializer\Symfony\SymfonyObjectNormalizer;
use ServiceBus\MessageSerializer\Tests\Stubs\Author;
use ServiceBus\MessageSerializer\Tests\Stubs\AuthorCollection;
use ServiceBus\MessageSerializer\Tests\Stubs\ClassWithPrivateConstructor;
use ServiceBus\MessageSerializer\Tests\Stubs\EmptyClassWithPrivateConstructor;
use ServiceBus\MessageSerializer\Tests\Stubs\MixedWithLegacy;
use ServiceBus\MessageSerializer\Tests\Stubs\TestMessage;
use ServiceBus\MessageSerializer\Tests\Stubs\WithDateTimeField;
use ServiceBus\MessageSerializer\Tests\Stubs\WithNullableObjectArgument;
use ServiceBus\MessageSerializer\Tests\Stubs\WithPrivateProperties;
use function ServiceBus\Common\jsonDecode;
use function ServiceBus\Common\now;
use function ServiceBus\Common\readReflectionPropertyValue;

/**
 *
 */
final class SymfonyMessageSerializerTest extends TestCase
{
    /**
     * @var SymfonyJsonObjectSerializer
     */
    private $serializer;

    /**
     * @var SymfonyObjectNormalizer
     */
    private $normalizer;

    /**
     * @var SymfonyObjectDenormalizer
     */
    private $denormalizer;

    protected function setUp(): void
    {
        $this->normalizer   = new SymfonyObjectNormalizer();
        $this->denormalizer = new SymfonyObjectDenormalizer();

        $this->serializer = new SymfonyJsonObjectSerializer(
            normalizer: $this->normalizer,
            denormalizer: $this->denormalizer
        );
    }

    /**
     * @test
     */
    public function emptyClassWithClosedConstructor(): void
    {
        $object = EmptyClassWithPrivateConstructor::create();

        self::assertEquals(
            $object,
            $this->denormalizer->handle(
                jsonDecode($this->serializer->encode($object)),
                EmptyClassWithPrivateConstructor::class
            )
        );
    }

    /**
     * @test
     */
    public function classWithClosedConstructor(): void
    {
        $object = ClassWithPrivateConstructor::create(__METHOD__);

        self::assertSame(
            \get_object_vars($object),
            \get_object_vars(
                $this->serializer->decode(
                    $this->serializer->encode($object),
                    ClassWithPrivateConstructor::class
                )
            )
        );
    }

    /**
     * @test
     */
    public function withDateTime(): void
    {
        $object = new WithDateTimeField(new \DateTimeImmutable('NOW'));

        /** @var WithDateTimeField $result */
        $result = $this->serializer->decode($this->serializer->encode($object), WithDateTimeField::class);

        self::assertSame(
            $object->dateTimeValue->format('Y-m-d H:i:s'),
            $result->dateTimeValue->format('Y-m-d H:i:s')
        );
    }

    /**
     * @test
     */
    public function wthNullableObjectArgument(): void
    {
        $object = WithNullableObjectArgument::withObject('qwerty', ClassWithPrivateConstructor::create('qqq'));

        self::assertSame(
            \get_object_vars($object),
            \get_object_vars(
                $this->serializer->decode(
                    $this->serializer->encode($object),
                    WithNullableObjectArgument::class
                )
            )
        );

        $object = WithNullableObjectArgument::withoutObject('qwerty');

        self::assertSame(
            \get_object_vars($object),
            \get_object_vars(
                $this->serializer->decode(
                    $this->serializer->encode($object),
                    WithNullableObjectArgument::class
                )
            )
        );
    }

    /**
     * @test
     */
    public function denormalizeToUnknownClass(): void
    {
        $this->expectException(DenormalizeFailed::class);
        $this->expectExceptionMessage('Class `Qwerty` not exists');

        /** @noinspection PhpUndefinedClassInspection */
        $this->denormalizer->handle([], \Qwerty::class);
    }

    /**
     * @test
     */
    public function withWrongCharset(): void
    {
        $this->expectException(EncodeObjectFailed::class);
        $this->expectExceptionMessage('Malformed UTF-8 characters, possibly incorrectly encoded');

        $this->serializer->encode(
            ClassWithPrivateConstructor::create(
                \iconv('utf-8', 'windows-1251', 'тест')
            )
        );
    }

    /**
     * @test
     */
    public function successFlow(): void
    {
        $object = TestMessage::create(
            'message-serializer',
            null,
            'dev-master',
            Author::create('Vasiya', 'Pupkin')
        );

        self::assertSame(
            \get_object_vars($object),
            \get_object_vars(
                $this->serializer->decode(
                    $this->serializer->encode($object),
                    TestMessage::class
                )
            )
        );
    }

    /**
     * @test
     */
    public function successCollection(): void
    {
        $object = new AuthorCollection();

        $object->collection[] = Author::create('qwerty', 'root');
        $object->collection[] = Author::create('root', 'qwerty');

        $unserialized = $this->serializer->decode($this->serializer->encode($object), AuthorCollection::class);

        self::assertSame(
            \array_map(
                static function (Author $author): string
                {
                    return $author->firstName;
                },
                $object->collection
            ),
            \array_map(
                static function (Author $author): string
                {
                    return $author->firstName;
                },
                $unserialized->collection
            )
        );
    }

    /**
     * @test
     */
    public function legacyPropertiesSupport(): void
    {
        $object = new MixedWithLegacy(
            'qwerty',
            new \DateTimeImmutable('2019-01-01', new \DateTimeZone('UTC')),
            100500
        );

        $unserialized = $this->serializer->decode($this->serializer->encode($object), MixedWithLegacy::class);

        self::assertSame($object->string, $unserialized->string);
        self::assertSame($object->dateTime->getTimestamp(), $unserialized->dateTime->getTimestamp());
        self::assertSame($object->long, $unserialized->long);
    }

    /**
     * @test
     */
    public function privateMixedPropertiesSupport(): void
    {
        $object = new WithPrivateProperties(
            'Some string',
            100500,
            now()
        );

        $unserialized = $this->serializer->decode($this->serializer->encode($object), WithPrivateProperties::class);

        self::assertSame('Some string', readReflectionPropertyValue($unserialized, 'qwerty'));
        self::assertSame(100500, readReflectionPropertyValue($unserialized, 'root'));
    }
}
