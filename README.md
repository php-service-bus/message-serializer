[![Build Status](https://travis-ci.org/mmasiukevich/message-serializer.svg?branch=master)](https://travis-ci.org/mmasiukevich/message-serializer)
[![Code Coverage](https://scrutinizer-ci.com/g/mmasiukevich/message-serializer/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/mmasiukevich/message-serializer/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/mmasiukevich/message-serializer/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/mmasiukevich/message-serializer/?branch=master)

## What is it?

A library that provides a basic implementation of message serialization (object -> JSON -> object) for [service-bus](https://github.com/mmasiukevich/service-bus) framework

Currently implemented:
* [Symfony Serialazer](https://github.com/mmasiukevich/message-serializer/blob/master/src/Symfony/SymfonyMessageSerializer.php): Supports normalization/denormalization of public/private properties; DateTime objects; Empty objects with closed constructor, etc. The description of property types is in the PHPDoc.

Usage example ([@see test case](https://github.com/mmasiukevich/message-serializer/blob/master/tests/Symfony/SymfonyMessageSerializerTest.php#L210)):

```php
$serializer = new SymfonyMessageSerializer();

$object = TestMessage::create(
  'message-serializer',
   null,
  'dev-master',
   Author::create('Vasiya', 'Pupkin')
);

echo $serializer->encode($object);
```
Will output:
```json
{
  "message": {
    "componentName": "message-serializer",
    "stableVersion": null,
    "devVersion": "dev-master",
    "author": {
      "firstName": "Vasiya",
      "lastName": "Pupkin"
    }
  },
  "namespace": "Desperado\\ServiceBus\\MessageSerializer\\Tests\\Stubs\\TestMessage"
}
```
