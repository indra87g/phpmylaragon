<?php
require "../config.php";

if (!empty($_GET['db'])) {
    $DB_NAME = $_GET['db'];

    $conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    if ($conn->query("CREATE DATABASE `$DB_NAME`") === TRUE) {
        echo "success";
    }

    $conn->close();
}