<?php
// dbconfig.php
$mysqli = new mysqli('localhost', 'root', '', 'dwp');

// Check connection
if ($mysqli->connect_error) {
    die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}
?>
