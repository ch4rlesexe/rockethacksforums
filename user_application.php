<?php
$config = parse_ini_file('myproperties.ini');

if (!isset($config['DBHOST'], $config['DBUSER'], $config['DBPASS'], $config['DBNAME'])) {
    die("Error: Missing database configuration in myproperties.ini");
}

$servername = $config['DBHOST'];
$username = $config['DBUSER'];
$password = $config['DBPASS'];
$dbname = $config['DBNAME'];

session_start();
$logged_in_user_id = $_SESSION['user_id'] ?? null;

if (!$logged_in_user_id) {
    echo "<div class='form-container'><p>Error: User not logged in.</p></div>";
    exit;
}

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("<div class='form-container'><p>Connection failed: " . htmlspecialchars($conn->connect_error) . "</p></div>");
}

$sql = "SELECT id, question1, status FROM applications WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $logged_in_user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Applications</title>
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
            0% { opacity: 0.8; }
            100% { opacity: 1; }
        }

        .form-container {
            background-color: rgba(28, 34, 48, 0.95);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);
            width: 600px;
            position: relative;
            z-index: 1;
            animation: fadeIn 1.5s ease-out;
            text-align: center;
        }

        h2 {
            text-align: center;
            color: #4a90e2;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        th, td {
            padding: 12px;
            border: 1px solid #444;
            text-align: center;
            color: #fff;
        }

        th {
            background-color: #1e2433;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #2c3444;
        }

        a {
            color: #4a90e2;
            text-decoration: none;
            font-weight: bold;
        }

        a:hover {
            text-decoration: underline;
            color: #007bff;
        }

        .btn {
            display: block;
            width: 100%;
            box-sizing: border-box;
            padding: 12px;
            margin-top: 10px;
            background-color: #007bff;
            color: #fff;
            font-size: 1em;
            font-weight: bold;
            text-align: center;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
            text-decoration: none;
        }

        .btn:hover {
            background-color: #0056b3;
            transform: scale(1.05);
        }

        .account-icon {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            font-size: 1.2em;
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

    <div class="form-container">
        <h2>Your Applications</h2>

        <?php if ($result->num_rows > 0): ?>
            <table>
                <tr>
                    <th>Application ID</th>
                    <th>Title (Question 1)</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td><?= htmlspecialchars($row['question1']) ?></td>
                        <td><?= htmlspecialchars($row['status']) ?></td>
                        <td>
                            <a href="edit_application.php?id=<?= $row['id'] ?>">Edit</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>No applications found.</p>
        <?php endif; ?>

        <a href="dashboard.php" class="btn">Back to Dashboard</a>

        <?php
        $stmt->close();
        $conn->close();
        ?>
    </div>

    <button class="account-icon" onclick="window.location.href='account_settings.php'">
        &#128100;
    </button>
</body>
</html>
