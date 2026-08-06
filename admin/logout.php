<?php
require_once '../includes/auth.php';

session_start();
session_destroy();

header("Location: /admin/login.php");
exit();
?>
