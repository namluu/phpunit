<?php
declare(strict_types=1);
use PHPUnit\Framework\TestCase;
require_once 'src/chapter-04/UserEntity.php';

class UserEntityTest extends TestCase
{
    private $user;

    private $truePassword = 'password123';

    private $wrongPassword = 'wrong-password';

    public function setUp(): void
    {
        $this->user = new UserEntity(
            [
                'firstName' => 'FirstName',
                'lastName' => 'LastName', 
                'email' => 'example@test.com',
                'password' => $this->truePassword
            ]
        );
    }

    public function testValidInput()
    {
        $this->assertTrue($this->user->isInputValid());
    }

    public function testInvalidInput()
    {
        $this->user->email = null;
        $this->assertFalse($this->user->isInputValid());
    }

    public function testCreateHashedPassword()
    {
        $this->user->createPassword();

        $hashedPassword = sha1($this->truePassword . $this->user->salt);
        $this->assertEquals($this->user->password, $hashedPassword);
    }

    public function testCreateWrongPassword()
    {
        $this->user->createPassword();

        $wrongHashed = sha1('12345');
        $this->assertNotEquals($this->user->password, $wrongHashed);
        
        $wrongHashed = sha1($this->wrongPassword . $this->user->salt);
        $this->assertNotEquals($this->user->password, $wrongHashed);
    }

    public function testVerifyPassword()
    {
        $this->user->createPassword();
        $this->assertTrue($this->user->verifyPassword($this->truePassword));
    }

    public function testVerifyWrongPassword()
    {
        $this->user->createPassword();
        $this->assertFalse($this->user->verifyPassword($this->wrongPassword));
    }
}
