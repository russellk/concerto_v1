<?php

include_once("database.class.php");

class Screen {
    private $id;
    private $name;
    private $group_id;
    private $location;
    private $mac_address;
    private $width;
    private $height;
    private $template_id;
    private $last_updated;
    private $last_ip;
    private $controls_display;
    private $time_on;
    private $time_off;
    
    static function macToID($mac) {
        $id = Database::queryFetchValue("SELECT id FROM screen WHERE mac_address = " . Database::quote(hexdec($mac)) . " LIMIT 1;");
        return($id);
    }
    
    function __construct($id, $user) {
        
    }
}
?>

