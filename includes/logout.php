<?php
// Include auth functions
require_once 'auth.php';

// Logout user
logoutUser();

// Redirect to home page
header('Location: /AgriCool_Link/index.php');
exit;
?>
