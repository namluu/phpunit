<?php
declare(strict_types=1);
use PHPUnit\Framework\TestCase;
require_once 'src/chapter-04/UserManager.php';
require_once 'src/chapter-04/UserEntity.php';
require_once 'utils/Mail.php';

class UserManagerTest extends TestCase
{
    protected $user;

    protected $email;

    protected $db;

    protected $config;

    protected $statement;

    protected $sql;

    protected $userManager;

    protected function setUp(): void
    {
        $this->user = new UserEntity(array('firstName' => 'FirtsName','lastName' => 'LastName', 'email' => 'user@example.com','password' => 'password123'));
        $this->email = $this->createMock('Mail');
        $this->db = $this->createMock('PDO');
        $this->config = new \stdClass();
        $this->config->email = 'test@example.com';
        $this->config->site_url = 'http://example.com';

        $this->statement = $this->getMockBuilder(\stdClass::class)
            ->disableOriginalConstructor()
            ->setMethods(['bindParam', 'execute', 'errorInfo'])
            ->getMock();

        $this->sql = "INSERT INTO users(firstname, lastname, email, password, salt) VALUES (:firstname, :lastname, :email, :password, :salt)";

        $this->db->expects($this->any())->method('prepare')//once
            ->with($this->sql)
            ->willReturn($this->statement);

        $this->userManager = new UserManager($this->email, $this->db, $this->config);
    }

    public function testInvalidUser()
    {
        $this->user->email = null;
        
        $this->expectException(\Exception::class);
        $this->assertTrue($this->userManager->createUser($this->user));
    }

    public function testCreateUser()
    {        
        $this->statement->expects($this->once())->method('execute')->willReturn(true);
        $this->db->expects($this->once())->method('lastInsertId')->willReturn(1);
    
        $this->assertTrue($this->userManager->createUser($this->user));
        $this->assertEquals(sha1('password123'.$this->user->salt),$this->user->password);
        $this->assertTrue($this->user->userId > 0);
    }

    public function testCannotCreateUser()
    {
        $this->statement->expects($this->once())->method('execute')->willReturn(false);
        $this->statement->expects($this->once())->method('errorInfo')->willReturn([]);

        $this->expectException(\Exception::class);
        $this->assertTrue($this->userManager->createUser($this->user));
    }
}
