<?php

$db_name = "workers";
$host = "localhost";
$username = "root";
$password = "";

$connection = new mysqli($host, $username, $password, $db_name);

if ($connection->connect_error) die();
