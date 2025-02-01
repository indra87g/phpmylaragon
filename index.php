<?php
require 'config.php';

if (!empty($_GET['q'])) {
    switch ($_GET['q']) {
        case 'info':
            phpinfo();
            exit();
        case 'hello':
            echo "Hello World!";
            exit();
    }
}

$dirList = glob($_SERVER['DOCUMENT_ROOT'] . '/*', GLOB_ONLYDIR);

$ignoreList = [];
$ignoreFile = './.projectignore';

if (file_exists($ignoreFile)) {
    $ignoreList = file($ignoreFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
}
$filteredProjects = array_filter($dirList, function ($project) use ($ignoreList) {
    return !in_array(basename($project), $ignoreList);
});
$totalProjects = count($filteredProjects);

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS);
$databases = [];
if (!$conn->connect_error) {
    $result = $conn->query("SHOW DATABASES");
    while ($row = $result->fetch_assoc()) {
        $databases[] = $row['Database'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['new_db'])) {
    $newDb = $_POST['new_db'];
    if ($conn->query("CREATE DATABASE `$newDb`")) {
        header("Location: index.php?db_created=1");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <?php require_once 'components/Header.php'; ?>
</head>
<body>
    <nav class="navbar navbar-dark bg-success shadow fixed-top">
        <div class="container">
            <h1 class="navbar-brand"><i class="fas fa-server"></i> phpMyLaragon</h1>
        </div>
    </nav>

    <br />

    <main class="container mt-5">
        <div class="p-4 bg-body-tertiary rounded">
            <h2>Welcome to phpMyLaragon! 🚀</h2>
            <p class="lead">Laragon web dashboard for web artisans.</p>
            <a class="btn btn-primary rounded-pill px-3" href="https://phpmylaragon.vercel.app">Documentation</a>
            <button class="btn btn-primary rounded-pill px-3" onclick="notImplementedAlert()" >What's New?</button>
        </div>

        <br />

        <div class="p-4 bg-body-tertiary rounded">
            <h3>Server Status</h3>
            <ul>
                <li><strong>Apache:</strong>
                    <span id="apache-status">🔄 Checking...</span>
                </li>
                <li><strong>MySQL:</strong>
                    <span id="mysql-status">🔄 Checking...</span>
                </li>
                <li><strong>Redis:</strong>
                    <span id="redis-status">🔄 Checking...</span>
                </li>
            </ul>
            <h3>Server Information</h3>
            <ul>
                <li><strong>Webserver:</strong>
                    <?php echo $_SERVER['SERVER_SOFTWARE']; ?>
                </li>
                <li><strong>PHP version:</strong>
                    <?php echo phpversion(); ?>
                </li>
                <li><strong>Document Root:</strong>
                    <?php echo $_SERVER['DOCUMENT_ROOT']; ?>
                </li>
            </ul>
        </div>

        <br />

        <?php if (!empty($filteredProjects)) : ?>
        <div class="p-4 bg-body-tertiary rounded">
            <h3>Your Projects (
                <?php echo $totalProjects; ?>)
            </h3>
            <input type="text" id="search" class="form-control mb-3" placeholder="Search project...">

            <div class="row" id="project-list">
                <?php foreach ($filteredProjects as $value) : 
                    $projectname = basename($value);
                    $link = 'http://localhost/' . $projectname;
                    $icon = '<i class="fas fa-folder"></i>';
                ?>
                <div class="col-md-4">
                    <div class="card project-card p-3 mb-3">
                        <h5>
                            <?php echo $icon . ' ' . $projectname; ?>
                        </h5>
                        <a href="<?php echo $link; ?>" target="_blank" class="btn btn-sm btn-success"><i
                                class="fas fa-globe"></i> Open</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php else : ?>
        <div class="alert alert-warning">No projects found in the www directory.</div>
        <?php endif; ?>

        <br />

        <div class="p-4 bg-body-tertiary rounded">
            <h3>Toolbox</h3>
            <div class="d-flex gap-2">
                <a href="http://localhost/phpmyadmin" class="btn btn-primary rounded-pill"><i
                        class="fas fa-database"></i> phpMyAdmin</a>
                <a href="http://localhost/phpmylaragon/?q=info" class="btn btn-primary rounded-pill"><i
                        class="fas fa-info-circle"></i> PHP Info</a>
                <a href="http://localhost/laragon" class="btn btn-primary rounded-pill"><i
                        class="fas fa-file"></i> File Uploader</a>
            </div>
        </div>

        <br />

        <?php if (!empty($databases)) : ?>
        <div class="p-4 bg-body-tertiary rounded">
            <h2 class="mt-4">Databases</h2>
            <form class="mt-3" method="POST">
                <input type="text" name="new_db" placeholder="New database name" required class="form-control"/>
                <button type="submit" class="btn btn-primary mt-2" onclick="actionSuccessAlert()">Create Database</button>
            </form><br />
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th scope="col">Name</th>
                        <th>Action</th>
                    </tr>
                </thead>
            <?php foreach ($databases as $db): ?>
                <tbody>
                    <tr>
                        <td><?php echo $db; ?></td>
                        <td>
                            <div class="btn-group" role="group">
                                <a class="btn btn-primary btn-sm" href="<?php echo $DB_URL.$db; ?>" target="_blank">Open</a>
                            </div>
                            <div class="btn-group" role="group">
                                <button class="btn btn-danger btn-sm" onclick="notImplementedAlert()">Delete</button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            <?php endforeach; ?>
            </table>
        </div>
        <?php else : ?>
        <div class="alert alert-warning">No databases found. Check your mysql server and try again!</div>
        <?php endif; ?>
    </main>
</body>
</html>