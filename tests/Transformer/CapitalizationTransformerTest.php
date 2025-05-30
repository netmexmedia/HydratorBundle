<?php

use Netmex\HydratorBundle\Transformer\CapitalizationTransformer;
use PHPUnit\Framework\TestCase;

class CapitalizationTransformerTest extends TestCase
{
    private CapitalizationTransformer $transformer;

    protected function setUp(): void
    {
        $this->transformer = new CapitalizationTransformer();
    }

    public function testTransformConvertsToUppercase()
    {
        $this->assertSame('HELLO', $this->transformer->transform('hello'));
        $this->assertSame('HELLO WORLD!', $this->transformer->transform('Hello World!'));
        $this->assertSame('', $this->transformer->transform(''));
        $this->assertSame('123', $this->transformer->transform('123'));
    }

    public function testReverseTransformConvertsToLowercase()
    {
        $this->assertSame('hello', $this->transformer->reverseTransform('HELLO'));
        $this->assertSame('hello world!', $this->transformer->reverseTransform('Hello World!'));
        $this->assertSame('', $this->transformer->reverseTransform(''));
        $this->assertSame('123', $this->transformer->reverseTransform('123'));
    }
}
