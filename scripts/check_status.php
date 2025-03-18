<?php
header('Content-Type: application/json');

function isServiceRunning($host, $port)
{
    $connection = @fsockopen($host, $port);
    if (is_resource($connection)) {
        fclose($connection);
        return true;
    }
    return false;
}

$response = [
    'apache' => isServiceRunning('localhost', 80),
    'nginx' => isServiceRunning('localhost', 8080),
    'mysql' => isServiceRunning('localhost', 3306),
    'redis' => isServiceRunning('localhost', 6379),
    'memcached' => isServiceRunning('localhost', 11211),
];

echo json_encode($response);
?>