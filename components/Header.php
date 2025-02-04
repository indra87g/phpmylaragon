<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link rel="stylesheet" href="styles/main.css" />
<link rel="stylesheet" href="styles/bootstrap.min.css" />
<link rel="stylesheet" href="styles/sweetalert2.min.css">
<link rel="stylesheet" href="styles/fontawesome/all.min.css" />

<style>
    .project-card {
        transition: 0.3s;
        border-radius: 12px;
    }
    .project-card:hover {
        transform: translateY(-5px);
        box-shadow: 0px 4px 10px rgba(255, 255, 255, 0.1);
    }
    .server-status {
        font-size: 14px;
        padding: 4px 10px;
        border-radius: 12px;
    }
</style>

<script src="scripts/bootstrap.bundle.min.js"></script>
<script src="scripts/sweetalert2.all.min.js"></script>
<script src="scripts/color-modes.js"></script>
<script>
    document.getElementById("search").addEventListener("keyup", function () {
        let filter = this.value.toLowerCase();
        let projects = document
            .getElementById("project-list")
            .getElementsByClassName("col-md-4");
        Array.from(projects).forEach(function (project) {
            let title = project.textContent.toLowerCase();
            prject.style.display = title.includes(filter) ? "block" : "none";
        });
    });
</script>
<script>
    async function fetchServerStatus() {
        try {
            const response = await fetch("scripts/check_status.php");
            const data = await response.json();

            document.getElementById("apache-status").innerHTML = data.apache
                ? '<span class="badge bg-success">✅ Running</span>'
                : '<span class="badge bg-danger">❌ Stopped</span>';

            document.getElementById("mysql-status").innerHTML = data.mysql
                ? '<span class="badge bg-success">✅ Running</span>'
                : '<span class="badge bg-danger">❌ Stopped</span>';

            document.getElementById("redis-status").innerHTML = data.redis
                ? '<span class="badge bg-success">✅ Running</span>'
                : '<span class="badge bg-danger">❌ Stopped</span>';
        } catch (error) {
            console.error("Error fetching status:", error);
        }
    }

    setInterval(fetchServerStatus, 5000);
    fetchServerStatus();
</script>
<script>
    function notImplementedAlert() {
        Swal.fire({
            icon: "error",
            title: "Not Implemented Yet...",
            text: "Check back later!",
            showConfirmButton: true,
        });
    }

    function actionSuccessAlert(title, text, timer) {
        Swal.fire({
            icon: "success",
            title: title,
            text: text,
            showConfirmButton: false,
            timer: timer,
        });  
    }

    function actionFailedAlert(title, text, timer) {
        Swal.fire({
            icon: "alert",
            title: title,
            text: text,
            showConfirmButton: false,
            timer: timer,
        });
    }

    function actionAskAlert(title, text, confirm_button, cancel_button, deleteDb, Db) {
        if (deleteDb === true) {
            Swal.fire({
                icon: "question",
                title: title,
                text: text,
                showCancelButton: true,
                confirmButtonText: confirm_button,
                cancelButtonText: cancel_button,
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `scripts/delete_database.php?db=${Db}` ;
                }
            });   
        } else {
            Swal.fire({
                icon: "question",
                title: title,
                text: text,
                showCancelButton: true,
                confirmButtonText: confirm_button,
                cancelButtonText: cancel_button,
            });
        }
    }

    function deleteDbAlert(db_name) {
        actionAskAlert(
            `Are you sure to delete database '${db_name}'?`,
            "Your data will lost forever!",
            "Yes",
            "No",
            true,
            db_name
        )
    }
</script>

<?php require_once 'colorMode.php' ?>
