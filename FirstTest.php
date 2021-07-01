<?php
declare(strict_types=1);
use PHPUnit\Framework\TestCase;

class FirstTest extends TestCase
{
    public function testAddition()
    {
        $this->assertEquals(2, 1 + 1);
    }

    public function testSubtraction()
    {
        //$this->assertTrue(0.17 == (1 - 0.83));
        $this->assertEquals(0.17, 1 - 0.83);
    }

    public function testMultiplication()
    {
        $this->assertEquals(10, 2 * 5);
    }

    public function testDivision()
    {
        $this->assertTrue(2 == (10 / 5));
    }
}
