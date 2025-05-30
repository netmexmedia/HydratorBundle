<?php

namespace Netmex\HydratorBundle\Tests\Exception;

use Netmex\HydratorBundle\Exception\MappingValidationException;
use PHPUnit\Framework\TestCase;

class MappingValidationExceptionTest extends TestCase
{
    public function testViolationsAreStoredAndReturned()
    {
        $violations = [
            ['context' => 'user', 'property' => 'email', 'message' => 'This value is not valid.']
        ];

        $exception = new MappingValidationException($violations);

        $this->assertEquals($violations, $exception->getViolations());
    }

    public function testFormattedMessageIncludesContextAndMessage()
    {
        $violations = [
            ['context' => 'user', 'property' => 'email', 'message' => 'Invalid email address.'],
            ['context' => 'user', 'property' => 'password', 'message' => ['Too short.', 'Must contain a number.']],
            ['context' => 'order', 'property' => 'amount', 'message' => 'Must be positive.'],
        ];

        $exception = new MappingValidationException($violations, 'Validation Failed: ');

        $message = $exception->getMessage();

        $this->assertStringContainsString('USER errors:', $message);
        $this->assertStringContainsString('- email:', $message);
        $this->assertStringContainsString('• Invalid email address.', $message);
        $this->assertStringContainsString('- password:', $message);
        $this->assertStringContainsString('• Too short.', $message);
        $this->assertStringContainsString('• Must contain a number.', $message);
        $this->assertStringContainsString('ORDER errors:', $message);
        $this->assertStringContainsString('• Must be positive.', $message);
    }
}
