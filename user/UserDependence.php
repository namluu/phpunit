<?php

class UserDependency
{
    public $userId;
    public $firstName;
    public $lastName;
    public $email;
    public $password;
    public $salt;

    /**
     * @param array $options
     */
    public function __construct(array $options)
    {
        foreach ($options as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        } 
    }

    /**
     * validates properties
     * @return bool
     */
    public function isInputValid()
    {
        if (
            empty($this->firstName) || 
            empty($this->lastName) ||
            empty($this->email) || 
            empty($this->password) ||
            !filter_var($this->email, FILTER_VALIDATE_EMAIL)
        ) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * creates password hash
     */
    public function createPassword()
    {
        $this->salt = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 15);
        $this->password = sha1($this->password . $this->salt);
    }

    /**
     * verifies password
     * @param  string $password
     * @return bool
     */
    public function verifyPassword($password)
    {
        return ($this->password === sha1($password . $this->salt));
    }

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