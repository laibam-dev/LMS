<?php
session_start();
session_unset();     // session variables clear
session_destroy();  // session destroy

header("Location: login.php");
exit();
