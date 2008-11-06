<?php

include_once("accessor.interface.php");

define('USER_USERNAME_REGEX', '/^[a-z\d_]{4,24}$/i');
define('USER_EMAIL_REGEX', '/^[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$/');
define('USER_SALT_LENGTH', 4);

class User implements Accessor {
    static function compare($user1, $user2) {
        if(!$user1 || !$user2)
            return false;
        return $user1->getId() == $user2->getId();
    }
    
    static function create($username, $password, $name, $email, $accessor = NULL) {
        self::checkUsername($username);
        self::checkEmail($email);

        //create a random salt and concat with the password to make the SHA1 hash
        $salt = self::generateSalt();
        $hash = sha1($salt . $password);
        
        //commit the user to the database
        $sql = "INSERT INTO `user` (username, password_hash, salt, name, email)
                VALUES (?, ?, ?, ?, ?);";
        $result = Database::prepare($sql)->execute(array($username, $hash, $salt, $name, $email));
        
        //return a user object
        return new User($username, $accessor);
    }

    private $accessor;

    private $id;
    private $username;
    private $name;
    private $email;
    private $allow_email;
    private $groups = NULL;
    
    function __construct($username, $accessor = NULL) {
        $this->username = strtolower($username);
        $this->accessor = $accessor;
        $username = Database::quote($username);
        $sql = "SELECT id, name, email, allow_email FROM `user` WHERE username = $username LIMIT 1;";
        $result = Database::queryFetchRowAssoc($sql);
        if(!$result) {
            throw(new UserException(UserException::NOT_FOUND, $this));
        }
        $this->id = $result["id"];
        $this->name = $result["name"];
        $this->email = $result["email"];
        $this->allow_email = $result["allow_email"];
    }
    
    public function authenticate($password) {
        $sql = "SELECT salt FROM `user` WHERE username = '$this->username' LIMIT 1;";
        $salt = Database::queryFetchValue($sql);
        $hash = Database::quote(sha1($salt . $password));
        echo $hash;
        $sql = "SELECT COUNT(id) FROM `user` WHERE username = '$this->username' AND password_hash = $hash LIMIT 1;";
        $result = Database::queryFetchValue($sql);
        return $result;
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function getUsername() {
        return $this->username;
    }
    
    public function getId() {
        return $this->id;
    }
    
    public function getEmail() {
        if($this->allow_email || self::compare($this, $this->accessor))
            return $this->email;
        return NULL;
    }
    
    public function getAllowEmail() {
        return $this->allow_email;
    }

    public function checkGroup($group) {
        $this->fetchGroups();
        return isset($this->groups[$group->getId()]);
    }
    
    public function modify($password = NULL, $name = NULL, $email = NULL, $allow_email = NULL) {
        $commit = array();
        if($password) {
            $salt = self::generateSalt();
            $commit[] = "salt = " . Database::quote($salt);
            $hash = sha1($salt . $password);
            $commit[] = "password_hash = " . Database::quote($hash);
        }
        if($name) {
            $commit[] = "name = " . Database::quote($name);
        }
        if($email && $this->email != $email) {
            self::checkEmail($email);
            $commit[] = "email = " . Database::quote($email);
        }
        if($allow_email) {
        
        }
        if(count($commit)) {
            $sql = "UPDATE `user` SET " . join($commit, ', ') . " WHERE id = $this->id LIMIT 1;";
            echo $sql;
            Database::query($sql);
        }
    }
    
    public function isAdministrator() {
        $adminGroup = new Group(ADMINISTRATOR_GROUP_ID);
        return $this->checkGroup($adminGroup);
    }

    private function fetchGroups() {
        if(!$this->groups) {
            $sql = "SELECT group_id FROM `user_group` WHERE user_id = $this->id;";
            $result = Database::queryFetchAllAssoc($sql);
            foreach($result as $row) {
                $group = new Group($row["group_id"]);
                $this->groups[$group->getId()] = $group;
            }
            unset($result);
        }
    }
    
    static private function checkUsername($username) {
        //checks if the username is in spec
        $username = strtolower($username);
        if(!preg_match(USER_USERNAME_REGEX, $username)) {
            throw(new UserException(UserException::INVALID_USERNAME));
        }
        //or in the database already
        $sql = "SELECT COUNT(username) FROM `user` WHERE username = '$username';";
        if(Database::queryfetchValue($sql)) {
            throw(new UserException(UserException::USERNAME_ALREADY_EXISTS));
        }
    }

    static private function checkEmail($email) {
        //phoney emails will die!
        if(!preg_match(USER_EMAIL_REGEX, $email)) {
            throw(new UserException(UserException::INVALID_EMAIL));
        }
        //or people that try to register twice
        $sql = "SELECT COUNT(email) FROM `user` WHERE email = '$email';";
        if(Database::queryfetchValue($sql)) {
            throw(new UserException(UserException::EMAIL_ALREADY_EXISTS));
        }    
    }
    
    static private function generateSalt() {
        return substr(sha1(uniqid(rand(), true)), 0, USER_SALT_LENGTH);
    }
}

class UserException extends Exception {
    const CUSTOM = 0;
    const NOT_FOUND = 1;
    const PERMISSION_DENIED = 2;
    const USERNAME_ALREADY_EXISTS = 3;
    const EMAIL_ALREADY_EXISTS = 4;
    const INVALID_USERNAME = 5;    
    const INVALID_EMAIL = 6;    

    public function __construct($e, $user = NULL, $message = NULL) {
        switch ($e) {
            case self::CUSTOM:
                parent::__construct($message, $e);
            case self::NOT_FOUND:
                parent::__construct("User " . $user->getUsername() . " was not found in the database.", $e);
                break;
            case self::PERMISSION_DENIED:
                parent::__construct("User " . $user->getUsername() . " does not have permission.", $e);
                break;
            case self::USERNAME_ALREADY_EXISTS:
                parent::__construct("Username already exists in the system.", $e);
                break;
            case self::EMAIL_ALREADY_EXISTS:
                parent::__construct("Email Address already exists in the system.", $e);
                break;
            case self::INVALID_USERNAME:
                parent::__construct("Username is invalid.  Please keep the username alphanumeric.", $e);
                break;
            case self::INVALID_EMAIL:
                parent::__construct("Email address is not a valid email address.", $e);
                break;
            default: 
                parent::__construct("An Unknown User Error $e has occured", $e);
                break;
        }
    }
}
?>
