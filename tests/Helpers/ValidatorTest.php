<?php

use PHPUnit\Framework\TestCase;
use capsweb\Helpers\Validator;

class ValidatorTest extends TestCase
{
    public function testRequired(): void
    {
        $this->assertTrue(Validator::required('hello'));
        $this->assertFalse(Validator::required(''));
        $this->assertFalse(Validator::required('  '));
    }

    public function testMaxLength(): void
    {
        $this->assertTrue(Validator::maxLength('hello', 10));
        $this->assertFalse(Validator::maxLength('hello world', 5));
    }

    public function testNumeric(): void
    {
        $this->assertTrue(Validator::numeric(10));
        $this->assertTrue(Validator::numeric('10.5'));
        $this->assertFalse(Validator::numeric(-1));
        $this->assertFalse(Validator::numeric('abc'));
    }

    public function testEmail(): void
    {
        $this->assertTrue(Validator::email('test@example.com'));
        $this->assertFalse(Validator::email('not-an-email'));
    }

    public function testSanitizeString(): void
    {
        $this->assertEquals('hello', Validator::sanitizeString(' hello '));
        $this->assertEquals('&lt;script&gt;', Validator::sanitizeString('<script>'));
    }

    public function testInt(): void
    {
        $this->assertEquals(10, Validator::int('10'));
        $this->assertEquals(0, Validator::int('abc'));
    }

    public function testFloat(): void
    {
        $this->assertEquals(10.5, Validator::float('10.5'));
        $this->assertEquals(0.0, Validator::float('abc'));
    }
}
