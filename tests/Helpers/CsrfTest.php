<?php

use PHPUnit\Framework\TestCase;
use capsweb\Helpers\Csrf;

class CsrfTest extends TestCase
{
    protected function setUp(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }
        $_SESSION = [];
    }

    public function testGeneratesToken(): void
    {
        $token = Csrf::generate();
        $this->assertNotEmpty($token);
        $this->assertEquals(64, strlen($token)); // hex de 32 bytes
        $this->assertEquals($token, $_SESSION['csrf_token']);
    }

    public function testValidatesCorrectToken(): void
    {
        $token = Csrf::generate();
        $this->assertTrue(Csrf::validate($token));
    }

    public function testRejectsInvalidToken(): void
    {
        Csrf::generate();
        $this->assertFalse(Csrf::validate('fake-token'));
    }

    public function testRejectsEmptyToken(): void
    {
        $this->assertFalse(Csrf::validate(''));
    }

    public function testFieldContainsInput(): void
    {
        $field = Csrf::field();
        $this->assertStringContainsString('<input type="hidden" name="csrf_token"', $field);
    }
}
