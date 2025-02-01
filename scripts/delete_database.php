<?php
require '../config.php';

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS);
if ($conn->query("DROP DATABASE")) {
    header("Location: index.php?db_deleted=1");
    exit();
}
?>