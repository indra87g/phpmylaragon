<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link rel="stylesheet" href="styles/main.css" />
<link rel="stylesheet" href="styles/bootstrap.min.css" />
<link rel="stylesheet" href="styles/sweetalert2.min.css">
<link rel="stylesheet" href="styles/fontawesome/all.min.css" />
<link rel="stylesheet" href="styles/simple-datatables.css">

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
<script src="scripts/simple-datatables.js"></script>
<script src="scripts/color-modes.js"></script>
<script src="scripts/datatables-simple-demo.js"></script>
<script>
    async function fetchDeleteDb(db_name) {
        try {
            const response = await fetch(`scripts/delete_database.php?db=${db_name}`);
            const data = await response.text();

            if (data === "success") {
                console.log(`Database ${db_name} deleted successfully`);
                actionSuccessToast("Success!", `Database ${db_name} deleted successfully`, 3000);
            }
        } catch (error) {
            console.error("Error fetching data:", error);
            actionFailedToast("Failed!", "Error fetching data. see console for more info.", 3000)
        }
    }

    async function fetchCreateDb(db_name) {
        try {
            const response = await fetch(`scripts/create_database.php?db=${db_name}`);
            const data = await response.text();

            if (data === "success") {
                console.log(`Database ${db_name} created successfully`);
                actionSuccessToast("Success!", `Database ${db_name} created successfully`, 3000);
            }
        } catch (error) {
            console.error("Error fetching data:", error);
            actionFailedToast("Failed!", "Error fetching data. see console for more info.", 3000)
        }
    }

    async function fetchDatetime() {
        try {
            const response = await fetch("scripts/datetime.php");
            const data = await response.text();

            document.getElementById("datetime").innerHTML = `<strong>Time:</strong> ${data}`
        } catch (error) {
            console.log("Error fetching data:", error)
        }
    }

    async function fetchServerStatus() {
        try {
            const response = await fetch("scripts/check_status.php");
            const data = await response.json();

            document.getElementById("apache-status").innerHTML = data.apache
                ? '<span class="badge bg-success">✅ Running</span>'
                : '<span class="badge bg-danger">❌ Stopped</span>';

            document.getElementById("nginx-status").innerHTML = data.nginx
                ? '<span class="badge bg-success">✅ Running</span>'
                : '<span class="badge bg-danger">❌ Stopped</span>';

            document.getElementById("mysql-status").innerHTML = data.mysql
                ? '<span class="badge bg-success">✅ Running</span>'
                : '<span class="badge bg-danger">❌ Stopped</span>';

            document.getElementById("redis-status").innerHTML = data.redis
                ? '<span class="badge bg-success">✅ Running</span>'
                : '<span class="badge bg-danger">❌ Stopped</span>';

            document.getElementById("apache-status").innerHTML = data.apache
                ? '<span class="badge bg-success">✅ Running</span>'
                : '<span class="badge bg-danger">❌ Stopped</span>';

            document.getElementById("memcached-status").innerHTML = data.memcached
                ? '<span class="badge bg-success">✅ Running</span>'
                : '<span class="badge bg-danger">❌ Stopped</span>';

        } catch (error) {
            console.error("Error fetching status:", error);
        }
    }

    setInterval(fetchDatetime, 1000);
    setInterval(fetchServerStatus, 5000);
    window.onload = fetchDatetime();
    fetchServerStatus();
</script>
<script>
    function notImplementedToast() {
        Swal.fire({
            toast: true,
            position: "top-start",
            background: "#212529",
            color: '#DEE2E6',
            icon: "question",
            title: "Not Implemented Yet...",
            text: "Check back later!",
            showConfirmButton: false,
            timer: 3000
        });
    }

    function actionSuccessToast(title, text, timer) {
        Swal.fire({
            toast: true,
            position: "top-start",
            background: "#212529",
            color: '#DEE2E6',
            icon: "success",
            title: title,
            text: text,
            showConfirmButton: false,
            timer: timer,
        }).then(() => {
            window.location.href = 'http://localhost/phpmylaragon';
        })
    }

    function actionFailedToast(title, text, timer) {
        Swal.fire({
            toast: true,
            position: "top-start",
            background: "#212529",
            color: '#DEE2E6',
            icon: "alert",
            title: title,
            text: text,
            showConfirmButton: false,
            timer: timer,
        });
    }

    function actionAskAlert(title, text, confirm_button, cancel_button, deleteDb, createDb, Db) {
        if (deleteDb === true) {
            Swal.fire({
                background: "#212529",
                color: '#DEE2E6',
                icon: "question",
                title: title,
                text: text,
                showCancelButton: true,
                confirmButtonText: confirm_button,
                cancelButtonText: cancel_button,
            }).then((result) => {
                if (result.isConfirmed) {
                    fetchDeleteDb(Db);
                }
            });
        } else if (createDb === true) {
            Swal.fire({
                background: "#212529",
                color: '#DEE2E6',
                icon: "question",
                title: "Enter database name",
                input: "text",
                inputPlaceholder: "Enter database name here",
                showCancelButton: true,
            }).then((result) => {
                if (result.value) {
                    fetchCreateDb(result.value);
                }
            })
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
            false,
            db_name
        )
    }

    function createDbAlert() {
        actionAskAlert(
            "",
            "",
            "",
            "",
            false,
            true,
            ""
        )
    }
</script>

<?php require_once 'colorMode.php' ?>