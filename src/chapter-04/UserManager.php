<?php
class UserManager
{
    private $db;
    private $email;
    private $config;

    public function __construct($email, $db, $config)
    {
        $this->email = $email;
        $this->db = $db;
        $this->config = $config;
    }

    /**
     * sends activation email
     */
    private function sendActivationEmail($user)
    {
        $this->email->setEmailFrom($this->config->email);
        $this->email->setEmailTo($user->email);
        $this->email->setTitle('Your account has been activated');
        $this->email->setBody("Dear {$user->firstName}\n
                       Your account has been activated\n
                       Please visit {$this->config->site_url}\n
                       Thank you");
        $this->email->send();
    }

    /**
     * stores user to the database
     * @return bool
     */
    public function createUser($user)
    {
        if (!$user->isInputValid()) {
            throw new \InvalidArgumentException('Invalid user data');
        }
        $user->createPassword();
        $sql = "INSERT INTO users(firstname, lastname, email, password, salt) VALUES (:firstname, :lastname, :email, :password, :salt)";
        $statement = $this->db->prepare($sql);
        $statement->bindParam(':firstname', $user->firstName);
        $statement->bindParam(':lastname', $user->lastName);
        $statement->bindParam(':email', $user->email);
        $statement->bindParam(':password', $user->password);
        $statement->bindParam(':salt', $user->salt);
        if ($statement->execute()) {
            $user->userId = $this->db->lastInsertId();
            $this->sendActivationEmail($user);
            return true;
        } else {
            throw new \Exception('User wasnt saved:'.implode(':',$statement->errorInfo()));
        } 
    }
}