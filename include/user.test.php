<?php
include("config.inc.php");
include("user.class.php");

try {
    $auth = new User("test");
} catch(Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
}

echo $auth->authenticate("balls") . "<br/>\n";

$auth->modify("balls", "Testing", "test@example.com", NULL);

//echo $auth->isAdministrator();

//$new_user = User::create("test", "balls", "Test Case", "test@example.com");
//echo $new_user->getId();

//$group = new Group(1);
//echo $user->checkGroup($group);

//foreach($user->getGroups() as $group)
//    echo $group->getName() . "<br />\n";
?>
