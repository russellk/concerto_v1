<?php

class Group {
    static function compare($group1, $group2) {
        if(!$group1 || !$group2)
            return false;
        return $group1->getId() == $group2->getId();
    }

    private $id;
    private $name;
    
    function __construct($id) {
        $this->id = $id;
        $id = Database::quote($id);
        $sql = "SELECT name FROM `group` WHERE id = $id LIMIT 1;";
        $result = Database::queryFetchRowAssoc($sql);
        if(!$result) {
            throw(new GroupException(GroupException::GROUP_NOT_FOUND, $this));
        }
        $this->name = $result["name"];
        unset($result);
    }
    
    public function getId() {
        return $this->id;
    }
    
    public function getName() {
        return $this->name;
    }
}

class GroupException extends Exception {
    const GROUP_CUSTOM = 0;
    const GROUP_NOT_FOUND = 1;

    public function __construct($e, $group, $message = NULL) {
        switch ($e) {
            case self::USER_CUSTOM:
                parent::__construct($message, $e);
            case self::USER_NOT_FOUND:
                parent::__construct("Group " . $group->getName() . " was not found in the database", $e);
                break;
            default: 
                parent::__construct("An Unknown User Error $e has occured", $e);
                break;
        }
    }
}
?>
