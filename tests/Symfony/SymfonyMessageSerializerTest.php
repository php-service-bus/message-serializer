<?php

/**
 * Messages serializer implementation.
 *
 * @author  Maksim Masiukevich <contacts@desperado.dev>
 * @license MIT
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types = 1);

namespace ServiceBus\MessageSerializer\Tests\Symfony;

use PHPUnit\Framework\TestCase;
use ServiceBus\MessageSerializer\Exceptions\DenormalizeFailed;
use ServiceBus\MessageSerializer\Exceptions\EncodeMessageFailed;
use ServiceBus\MessageSerializer\Symfony\SymfonySerializer;
use ServiceBus\MessageSerializer\Tests\Stubs\Author;
use ServiceBus\MessageSerializer\Tests\Stubs\AuthorCollection;
use ServiceBus\MessageSerializer\Tests\Stubs\ClassWithPrivateConstructor;
use ServiceBus\MessageSerializer\Tests\Stubs\EmptyClassWithPrivateConstructor;
use ServiceBus\MessageSerializer\Tests\Stubs\MixedWithLegacy;
use ServiceBus\MessageSerializer\Tests\Stubs\TestMessage;
use ServiceBus\MessageSerializer\Tests\Stubs\WithDateTimeField;
use ServiceBus\MessageSerializer\Tests\Stubs\WithNullableObjectArgument;
use ServiceBus\MessageSerializer\Tests\Stubs\WithPrivateProperties;
use function ServiceBus\Common\now;
use function ServiceBus\Common\readReflectionPropertyValue;

/**
 *
 */
final class SymfonyMessageSerializerTest extends TestCase
{
    /**
     * @test
     *
     * @throws \Throwable
     */
    public function emptyClassWithClosedConstructor(): void
    {
        $serializer = new SymfonySerializer();
        $object     = EmptyClassWithPrivateConstructor::create();

        self::assertEquals(
            $object,
            $serializer->decode($serializer->encode($object), EmptyClassWithPrivateConstructor::class)
        );
    }

    /**
     * @test
     *
     * @throws \Throwable
     */
    public static function classWithClosedConstructor(): void
    {
        $serializer = new SymfonySerializer();
        $object     = ClassWithPrivateConstructor::create(__METHOD__);

        self::assertSame(
            \get_object_vars($object),
            \get_object_vars(
                $serializer->decode(
                    $serializer->encode($object),
                    ClassWithPrivateConstructor::class
                )
            )
        );
    }

    /**
     * @test
     *
     * @throws \Throwable
     */
    public function withDateTime(): void
    {
        $serializer = new SymfonySerializer();
        $object     = new WithDateTimeField(new \DateTimeImmutable('NOW'));

        /** @var WithDateTimeField $result */
        $result = $serializer->decode($serializer->encode($object), WithDateTimeField::class);

        self::assertSame(
            $object->dateTimeValue->format('Y-m-d H:i:s'),
            $result->dateTimeValue->format('Y-m-d H:i:s')
        );
    }

    /**
     * @test
     *
     * @throws \Throwable
     */
    public function wthNullableObjectArgument(): void
    {
        $serializer = new SymfonySerializer();

        $object = WithNullableObjectArgument::withObject('qwerty', ClassWithPrivateConstructor::create('qqq'));

        self::assertSame(
            \get_object_vars($object),
            \get_object_vars(
                $serializer->decode(
                    $serializer->encode($object),
                    WithNullableObjectArgument::class
                )
            )
        );

        $object = WithNullableObjectArgument::withoutObject('qwerty');

        self::assertSame(
            \get_object_vars($object),
            \get_object_vars(
                $serializer->decode(
                    $serializer->encode($object),
                    WithNullableObjectArgument::class
                )
            )
        );
    }

    /**
     * @test
     *
     * @throws \Throwable
     */
    public function denormalizeToUnknownClass(): void
    {
        $this->expectException(DenormalizeFailed::class);
        $this->expectExceptionMessage('Class `Qwerty` not exists');

        /** @noinspection PhpUndefinedClassInspection */
        (new SymfonySerializer())->denormalize([], \Qwerty::class);
    }

    /**
     * @test
     *
     * @throws \Throwable
     */
    public function withWrongCharset(): void
    {
        $this->expectException(EncodeMessageFailed::class);
        $this->expectExceptionMessage('Message serialization failed: Malformed UTF-8 characters, possibly incorrectly encoded');

        (new SymfonySerializer())->encode(
            ClassWithPrivateConstructor::create(
                \iconv('utf-8', 'windows-1251', 'тест')
            )
        );
    }

    /**
     * @test
     *
     * @throws \Throwable
     */
    public function successFlow(): void
    {
        $serializer = new SymfonySerializer();

        $object = TestMessage::create(
            'message-serializer',
            null,
            'dev-master',
            Author::create('Vasiya', 'Pupkin')
        );

        self::assertSame(
            \get_object_vars($object),
            \get_object_vars(
                $serializer->decode(
                    $serializer->encode($object),
                    TestMessage::class
                )
            )
        );
    }

    /**
     * @test
     *
     * @throws \Throwable
     */
    public function successCollection(): void
    {
        $serializer = new SymfonySerializer();

        $object = new AuthorCollection();

        $object->collection[] = Author::create('qwerty', 'root');
        $object->collection[] = Author::create('root', 'qwerty');

        $unserialized = $serializer->decode($serializer->encode($object), AuthorCollection::class);

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
     *
     * @requires PHP >= 7.4
     *
     * @throws \Throwable
     */
    public function legacyPropertiesSupport(): void
    {
        $serializer = new SymfonySerializer();

        $object = new MixedWithLegacy(
            'qwerty',
            new \DateTimeImmutable('2019-01-01', new \DateTimeZone('UTC')),
            100500
        );

        $unserialized = $serializer->decode($serializer->encode($object), MixedWithLegacy::class);

        self::assertSame($object->string, $unserialized->string);
        self::assertSame($object->dateTime->getTimestamp(), $unserialized->dateTime->getTimestamp());
        self::assertSame($object->long, $unserialized->long);
    }

    /**
     * @test
     *
     * @requires PHP >= 7.4
     *
     * @throws \Throwable
     */
    public function privateMixedPropertiesSupport(): void
    {
        $serializer = new SymfonySerializer();

        $object = new WithPrivateProperties(
            'Some string',
            100500,
            now()
        );

        $unserialized = $serializer->decode($serializer->encode($object), WithPrivateProperties::class);

        self::assertSame('Some string', readReflectionPropertyValue($unserialized, 'qwerty'));
        self::assertSame(100500, readReflectionPropertyValue($unserialized, 'root'));
    }
}
