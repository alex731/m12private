<?php
include_once("common.php");

$c->Execute("update clients set last_login='".$_SESSION["time_login"]."' where is_admin='1'");
session_destroy();

header("Location: index.html");

?>