<?php
header('Content-Type: application/json');

function isServiceRunning($host, $port) {
    $connection = @fsockopen($host, $port);
    if (is_resource($connection)) {
        fclose($connection);
        return true;
    }
    return false;
}

$response = [
    'apache' => isServiceRunning('localhost', 80),
    'mysql' => isServiceRunning('localhost', 3306),
];

echo json_encode($response);
?>
