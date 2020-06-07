<?php
require('function.php');
session_destroy();
header("Location:login.php");
debug('ログアウトしました');
?>