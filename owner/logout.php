<?php
session_start();
unset($_SESSION['owner_id']);
unset($_SESSION['owner_username']);
unset($_SESSION['owner_nama']);
session_destroy();
header("location: ../index.php");
exit;
