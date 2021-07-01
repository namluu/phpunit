<?php
declare(strict_types=1);
use PHPUnit\Framework\TestCase;
use User\UserEntity;
#require_once 'UserEntity.php';

class UserEntityTest extends TestCase
{
    private $user;

    public function setUp(): void
    {
        $this->user = new UserEntity(
            [
                'firstName' => 'FirstName',
                'lastName' => 'LastName', 
                'email' => 'example@test.com',
                'password' => 'password123'
            ]
        );
    }

    public function testValidInput()
    {
        $this->assertTrue($this->user->isInputValid());
        $this->user->email = null;
        $this->assertFalse($this->user->isInputValid());
    }
}
