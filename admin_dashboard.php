<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$config = parse_ini_file('myproperties.ini');

if (!isset($config['DBHOST'], $config['DBUSER'], $config['DBPASS'], $config['DBNAME'])) {
    die("Error: Missing database configuration in myproperties.ini");
}

$servername = $config['DBHOST'];
$username = $config['DBUSER'];
$password = $config['DBPASS'];
$dbname = $config['DBNAME'];

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . htmlspecialchars($conn->connect_error));
}

$logged_in_user_id = $_SESSION['user_id'];
$admin_check_query = "SELECT role FROM users WHERE id = ?";
$admin_stmt = $conn->prepare($admin_check_query);
$admin_stmt->bind_param("i", $logged_in_user_id);
$admin_stmt->execute();
$admin_result = $admin_stmt->get_result();
$user_role = $admin_result->fetch_assoc()['role'] ?? '';

if ($user_role !== 'admin') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'], $_POST['application_id'])) {
    $status = $_POST['status'];
    $application_id = $_POST['application_id'];
    $update_query = "UPDATE applications SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("si", $status, $application_id);
    $stmt->execute();
    $stmt->close();
}

$sql = "SELECT id, user_id, question1, question2, file_path, submitted_at, status FROM applications";
$result = $conn->query($sql);
$applications = $result->fetch_all(MYSQLI_ASSOC);
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Applications</title>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #0b0f19;
            color: #ffffff;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            overflow: hidden;
            position: relative;
        }

        .stars {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('https://www.transparenttextures.com/patterns/stardust.png');
            animation: twinkle 3s infinite alternate;
            z-index: 0;
        }

        @keyframes twinkle {
            0% { opacity: 0.8; }
            100% { opacity: 1; }
        }

        .form-container {
            background-color: rgba(28, 34, 48, 0.95);
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);
            width: 90%;
            max-width: 700px;
            height: 80vh;
            overflow-y: auto;
            position: relative;
            z-index: 1;
        }

        .application-summary {
            background-color: #1e2433;
            margin-bottom: 10px;
            padding: 12px;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .application-summary:hover {
            background-color: #4a90e2;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .modal-content {
            background-color: #2b3345;
            padding: 20px;
            border-radius: 12px;
            width: 90%;
            max-width: 600px;
            color: #fff;
            text-align: left;
            position: relative;
            overflow-y: auto;
            max-height: 90vh;
        }

        .close-btn {
            position: absolute;
            top: 10px;
            right: 15px;
            color: #aaa;
            font-size: 1.5em;
            cursor: pointer;
        }

        .btn {
            background-color: #007bff;
            color: #ffffff;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            margin: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .btn.accept { background-color: #28a745; }
        .btn.deny { background-color: #dc3545; }

        .btn:hover {
            background-color: #0056b3;
        }

        .btn.accept:hover { background-color: #218838; }
        .btn.deny:hover { background-color: #c82333; }

        a {
            color: #4da6ff;
            text-decoration: underline;
        }
        .account-icon {
            position: absolute;
            top: 20px;
            right: 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 1.5rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
            z-index: 10;
        }

        .account-icon:hover {
            background-color: #0056b3;
        }

    </style>
</head>
<body>
    <div class="stars"></div>

    <div class="form-container">
        <h2>All Applications</h2>

        <?php foreach ($applications as $app): ?>
            <div class="application-summary" onclick="openModal(<?= htmlspecialchars(json_encode($app)) ?>)">
                <p><strong>ID:</strong> <?= htmlspecialchars($app['id']) ?></p>
                <p><strong>Title:</strong> <?= htmlspecialchars($app['question1']) ?></p>
                <p><strong>Status:</strong> <?= htmlspecialchars($app['status']) ?></p>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="modal" id="applicationModal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal()">&times;</span>
            <h3>Application Details</h3>
            <p><strong>ID:</strong> <span id="app-id"></span></p>
            <p><strong>Question 1:</strong> <span id="app-q1"></span></p>
            <p><strong>Question 2:</strong> <span id="app-q2"></span></p>
            <p><strong>Submitted At:</strong> <span id="app-date"></span></p>
            <p><strong>Uploaded File:</strong> <a href="#" id="app-file" target="_blank">View File</a></p>
            <p><strong>Status:</strong> <span id="app-status"></span></p>

            <form method="POST">
                <input type="hidden" name="application_id" id="application_id">
                <button type="submit" name="status" value="Accepted" class="btn accept">Accept</button>
                <button type="submit" name="status" value="Denied" class="btn deny">Deny</button>
            </form>
        </div>
    </div>

    <script>
        const modal = document.getElementById('applicationModal');

        function openModal(appData) {
            document.getElementById('app-id').textContent = appData.id;
            document.getElementById('app-q1').textContent = appData.question1;
            document.getElementById('app-q2').textContent = appData.question2;
            document.getElementById('app-date').textContent = appData.submitted_at;
            document.getElementById('app-status').textContent = appData.status;
            document.getElementById('application_id').value = appData.id;

            const fileLink = document.getElementById('app-file');
            if (appData.file_path) {
                fileLink.href = appData.file_path;
                fileLink.textContent = "View File";
            } else {
                fileLink.textContent = "No file uploaded";
                fileLink.href = "#";
            }

            modal.style.display = "flex";
        }

        function closeModal() {
            modal.style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target === modal) {
                closeModal();
            }
        }
    </script>
        <button class="account-icon" onclick="window.location.href='account_settings.php'">
        &#128100;
    </button>
</body>
</html>
