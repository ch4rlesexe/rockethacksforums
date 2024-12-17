<?php
session_start();

$ini_array = parse_ini_file("myproperties.ini", true);
$servername = $ini_array['DB']['DBHOST'];
$username = $ini_array['DB']['DBUSER'];
$password = $ini_array['DB']['DBPASS'];
$dbname = $ini_array['DB']['DBNAME'];

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . htmlspecialchars($conn->connect_error));
}

$submission_deadline = "2024-12-31 23:59:59";
$userName = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Guest';
$user_id = $_SESSION['user_id'] ?? null;
$role = $_SESSION['role'] ?? 'user';
$isAdmin = ($role === 'admin');

$application_status = 'Not Started';
$hasApplication = false;

if ($user_id) {
    $status_query = "SELECT status FROM applications WHERE user_id = ? ORDER BY submitted_at DESC LIMIT 1";
    $stmt = $conn->prepare($status_query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $application_status = $row['status'];
        $hasApplication = true;
    }
    $stmt->close();
}
$conn->close();

if (new DateTime() > new DateTime($submission_deadline)) {
    echo "<div class='dashboard'><p>Submissions are now closed as of $submission_deadline.</p></div>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
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
            position: relative;
            overflow: hidden;
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
            0% {
                opacity: 0.8;
            }
            100% {
                opacity: 1;
            }
        }

        .dashboard-container {
            background-color: rgba(28, 34, 48, 0.95);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);
            text-align: center;
            width: 400px;
            z-index: 10;
            position: relative;
            animation: fadeIn 1.5s ease-out;
        }

        h2 {
            font-size: 2rem;
            color: #4a90e2;
            margin-bottom: 20px;
        }

        p {
            font-size: 1.2rem;
            margin-bottom: 20px;
            color: #ddd;
        }

        .btn {
            display: block;
            width: 100%;
            padding: 12px;
            margin-top: 10px;
            font-size: 1rem;
            font-weight: bold;
            color: #fff;
            background-color: #007bff;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .btn:hover:not(.disabled) {
            background-color: #0056b3;
            transform: scale(1.05);
        }

        .btn.disabled {
            background-color: #6c757d;
            cursor: not-allowed;
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

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div class="stars"></div>

    <button class="account-icon" onclick="window.location.href='account_settings.php'">
        &#128100;
    </button>

    <div class="dashboard-container">
        <h2>Welcome, <?= htmlspecialchars($userName) ?></h2>
        <p>Status: <?= htmlspecialchars($application_status) ?></p>
        
        <?php if ($hasApplication && in_array($application_status, ['Pending', 'Accepted', 'Denied'])): ?>
            <button class="btn disabled">You already have a pending or processed application</button>
        <?php else: ?>
            <button class="btn" onclick="window.location.href='application_form.php'">Start/Continue Application</button>
        <?php endif; ?>
        
        <button class="btn" onclick="window.location.href='user_application.php'">View/Edit Application</button>
        
        <?php if ($isAdmin): ?>
            <button class="btn" onclick="window.location.href='admin_dashboard.php'">View Admin Dashboard</button>
        <?php endif; ?>
    </div>
</body>
</html>
