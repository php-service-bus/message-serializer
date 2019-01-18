<?php

/**
 * PHP Service Bus (publish-subscribe pattern implementation) serializer component
 *
 * @author  Maksim Masiukevich <dev@async-php.com>
 * @license MIT
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types = 1);

namespace ServiceBus\MessageSerializer\Tests\Symfony;

use ServiceBus\MessageSerializer\Symfony\SymfonyMessageSerializer;
use ServiceBus\MessageSerializer\Tests\Stubs\Author;
use ServiceBus\MessageSerializer\Tests\Stubs\ClassWithPrivateConstructor;
use ServiceBus\MessageSerializer\Tests\Stubs\EmptyClassWithPrivateConstructor;
use ServiceBus\MessageSerializer\Tests\Stubs\TestMessage;
use ServiceBus\MessageSerializer\Tests\Stubs\WithDateTimeField;
use ServiceBus\MessageSerializer\Tests\Stubs\WithNullableObjectArgument;
use PHPUnit\Framework\TestCase;

/**
 *
 */
final class SymfonyMessageSerializerTest extends TestCase
{
    /**
     * @test
     *
     * @return void
     *
     * @throws \Throwable
     */
    public function emptyClassWithClosedConstructor(): void
    {
        $serializer = new SymfonyMessageSerializer();
        $object     = EmptyClassWithPrivateConstructor::create();

        $encoded = $serializer->encode($object);

        $data = \json_decode($encoded, true);

        static::assertArrayHasKey('message', $data);
        static::assertArrayHasKey('namespace', $data);

        static::assertEmpty($data['message']);
        static::assertSame(EmptyClassWithPrivateConstructor::class, $data['namespace']);

        static::assertEquals($object, $serializer->decode($encoded));
    }

    /**
     * @test
     *
     * @return void
     *
     * @throws \Throwable
     */
    public static function classWithClosedConstructor(): void
    {
        $serializer = new SymfonyMessageSerializer();
        $object     = ClassWithPrivateConstructor::create(__METHOD__);

        static::assertEquals(
            $object,
            $serializer->decode($serializer->encode($object))
        );
    }

    /**
     * @test
     * @expectedException \ServiceBus\MessageSerializer\Exceptions\DecodeMessageFailed
     * @expectedExceptionMessage Class "SomeClass" not found
     *
     * @return void
     */
    public function classNotFound(): void
    {
        (new SymfonyMessageSerializer())->decode(\json_encode(['message' => 'someValue', 'namespace' => \SomeClass::class]));
    }

    /**
     * @test
     * @expectedException \ServiceBus\MessageSerializer\Exceptions\DecodeMessageFailed
     * @expectedExceptionMessage The serialized data must contains a "namespace" field (indicates the message class)
     *                           and "message" (indicates the message parameters)
     *
     * @return void
     */
    public function withoutNamespace(): void
    {
        (new SymfonyMessageSerializer())->decode(\json_encode(['message' => 'someValue']));
    }

    /**
     * @test
     * @expectedException \ServiceBus\MessageSerializer\Exceptions\DecodeMessageFailed
     * @expectedExceptionMessage The serialized data must contains a "namespace" field (indicates the message class)
     *                           and "message" (indicates the message parameters)
     *
     * @return void
     */
    public function withoutPayload(): void
    {
        (new SymfonyMessageSerializer())->decode(\json_encode(['namespace' => __CLASS__]));
    }

    /**
     * @test
     *
     * @return void
     *
     * @throws \Throwable
     */
    public function withDateTime(): void
    {
        $serializer = new SymfonyMessageSerializer();
        $object     = new WithDateTimeField(new \DateTimeImmutable('NOW'));

        /** @var WithDateTimeField $result */
        $result = $serializer->decode($serializer->encode($object));

        static::assertEquals(
            $object->dateTimeValue->format('Y-m-d H:i:s'),
            $result->dateTimeValue->format('Y-m-d H:i:s')
        );
    }

    /**
     * @test
     *
     * @return void
     *
     * @throws \Throwable
     */
    public function wthNullableObjectArgument(): void
    {
        $serializer = new SymfonyMessageSerializer();

        $object = WithNullableObjectArgument::withObject('qwerty', ClassWithPrivateConstructor::create('qqq'));

        static::assertEquals($object, $serializer->decode($serializer->encode($object)));

        $object = WithNullableObjectArgument::withoutObject('qwerty');

        static::assertEquals($object, $serializer->decode($serializer->encode($object)));
    }

    /**
     * @test
     * @expectedException \ServiceBus\MessageSerializer\Exceptions\DenormalizeFailed
     * @expectedExceptionMessage Class Qwerty does not exist
     *
     * @return void
     *
     * @throws \Throwable
     */
    public function denormalizeToUnknownClass(): void
    {
        (new SymfonyMessageSerializer())->denormalize([], \Qwerty::class);
    }

    /**
     * @test
     * @expectedException \ServiceBus\MessageSerializer\Exceptions\EncodeMessageFailed
     * @expectedExceptionMessage JSON serialize failed: Malformed UTF-8 characters, possibly incorrectly encoded
     *
     * @return void
     *
     * @throws \Throwable
     */
    public function withWrongCharset(): void
    {
        (new SymfonyMessageSerializer())->encode(
            ClassWithPrivateConstructor::create(
                \iconv('utf-8', 'windows-1251', 'тест')
            )
        );
    }

    /**
     * @test
     * @expectedException \ServiceBus\MessageSerializer\Exceptions\DecodeMessageFailed
     * @expectedExceptionMessage The type of the "value" attribute for class
     *                           "ServiceBus\MessageSerializer\Tests\Stubs\ClassWithPrivateConstructor"
     *                           must be one of "string" ("integer" given)
     *
     * @return void
     *
     * @throws \Throwable
     */
    public function withIncorrectType(): void
    {
        $serializer = new SymfonyMessageSerializer();

        $data = $serializer->encode(ClassWithPrivateConstructor::create(100));

        $serializer->decode($data);
    }

    /**
     * @test
     *
     * @return void
     *
     * @throws \Throwable
     */
    public function successFlow(): void
    {
        $serializer = new SymfonyMessageSerializer();

        $object = TestMessage::create(
            'message-serializer',
            null,
            'dev-master',
            Author::create('Vasiya', 'Pupkin')
        );

        static::assertEquals(
            $object,
            $serializer->decode($serializer->encode($object))
        );
    }
}
