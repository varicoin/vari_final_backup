<?php
session_start();
unset($_SESSION['sessionid']);
session_destroy();
header('location:index.php');