<?php

class UserManager
{
    /**
     * sends activation email
     */
    private function sendActivationEmail()
    {
        global $config;
        $email = new \Util\Mail($config);
        $email->setEmailFrom($config->email);
        $email->setEmailTo($this->email);
        $email->setTitle('Your account has been activated');
        $email->setBody("Dear {$this->firstName}\n
                       Your account has been activated\n
                       Please visit {$config->site_url}\n
                       Thank you");
        $email->send();
    }

    /**
     * stores user to the database
     * @return bool
     */
    public function createUser()
    {
        global $config;
        if (!$this->isInputValid()) {
            return false;
        }
        $this->createPassword();
        $db = $config->db;
        /* @var $db \PDO */
        $sql = "INSERT INTO users(firstname, lastname, email,
             password, salt) VALUES (:firstname, :lastname, :email,
             :password, :salt)";
        $statement = $db->prepare($sql);
        $statement->bindParam(':firstname', $this->firstName);
        $statement->bindParam(':lastname', $this->lastName);
        $statement->bindParam(':email', $this->email);
        $statement->bindParam(':password', $this->password);
        $statement->bindParam(':salt', $this->salt);
        if ($statement->execute()) {
            $this->userId = $db->lastInsertId();
            $this->sendActivationEmail();
            return true;
        } else {
            throw new \Exception('User wasnt saved:'.implode(':',$statement->errorInfo()));
        } 
    }

}