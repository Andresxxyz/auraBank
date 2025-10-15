<?php

session_start();
session_unset();
session_destroy();
header("Location: /aurabank/login.php");
exit();
?>