<?php
require('function.php');

$p_id = (!empty($_GET['p_id'])) ? $_GET['p_id'] : '';
debug($p_id);
debug('GETパラメータ:'.print_r($_GET,true));
?>