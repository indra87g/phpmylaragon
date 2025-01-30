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
$totalProjects = count($dirList);

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
            <div>
                <span>Apache: <span id="apache-status">🔄 Checking...</span></span>
                <span class="ms-3">MySQL: <span id="mysql-status">🔄 Checking...</span></span>
            </div>
        </div>
    </nav>

    <main class="container mt-5">
        <div class="p-4 bg-body-tertiary rounded">
            <h2>Welcome to phpMyLaragon! 🚀</h2>
            <p class="lead">Laragon web dashboard for web artisans.</p>
            <button class="btn btn-primary rounded-pill px-3">Documentation</button>
            <button class="btn btn-secondary rounded-pill px-3">Laragon Pro</button>
        </div>

        <br />

        <div class="p-4 bg-body-tertiary rounded">
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

        <?php if (!empty($dirList)) : ?>
        <div class="p-4 bg-body-tertiary rounded">
            <h3>Your Projects (
                <?php echo $totalProjects; ?>)
            </h3>
            <input type="text" id="search" class="form-control mb-3" placeholder="Search project...">

            <div class="row" id="project-list">
                <?php foreach ($dirList as $value) : 
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
                <a href="http://localhost/phpmylaragon/?q=info" class="btn btn-secondary rounded-pill"><i
                        class="fas fa-info-circle"></i> PHP Info</a>
                <button class="btn btn-danger rounded-pill"><i class="fas fa-sync"></i> Restart Server</button>
            </div>
        </div>

        <br />

        <?php if (!empty($databases)) : ?>
        <div class="p-4 bg-body-tertiary rounded">
            <h2 class="mt-4">Databases</h2>
            <form class="mt-3" method="POST">
                <input type="text" name="new_db" placeholder="New database name" required class="form-control"/>
                <button type="submit" class="btn btn-primary mt-2">Create Database</button>
            </form><br />
            <ul class="list-group">
            <?php foreach ($databases as $db): ?>
                <li class="list-group-item"><a href="<?php echo $DB_URL.$db; ?>" target="_blank"> <?php echo $db; ?> </a></li>
            <?php endforeach; ?>
            </ul>
        </div>
        <?php else : ?>
        <div class="alert alert-warning">No databases found. Check your mysql server and try again!</div>
        <?php endif; ?>
    </main>

    <script>
        document.getElementById("search").addEventListener("keyup", function () {
            let filter = this.value.toLowerCase();
            let projects = document.getElementById("project-list").getElementsByClassName("col-md-4");
            Array.from(projects).forEach(function (project) {
                let title = project.textContent.toLowerCase();
                prject.style.display = title.includes(filter) ? "block" : "none";
            });
        });
    </script>
    <script>
        async function fetchServerStatus() {
            try {
                const response = await fetch('check_status.php');
                const data = await response.json();

                document.getElementById('apache-status').innerHTML =
                    data.apache ? '<span class="badge bg-success">✅ Running</span>' :
                        '<span class="badge bg-danger">❌ Stopped</span>';

                document.getElementById('mysql-status').innerHTML =
                    data.mysql ? '<span class="badge bg-success">✅ Running</span>' :
                        '<span class="badge bg-danger">❌ Stopped</span>';
            } catch (error) {
                console.error("Error fetching status:", error);
            }
        }

        setInterval(fetchServerStatus, 5000);
        fetchServerStatus();
    </script>


</body>

</html>