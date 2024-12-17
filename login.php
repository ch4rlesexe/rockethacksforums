<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Roboto', sans-serif;
            background-color: #0b0f19;
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
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
            position: relative;
            background-color: #1c2230;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);
            width: 400px;
            z-index: 10;
            animation: fadeIn 1.5s ease-out;
        }

        h2 {
            text-align: center;
            color: #4a90e2;
            margin-bottom: 20px;
            font-size: 2rem;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #ccc;
        }

        input[type="email"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #444;
            border-radius: 6px;
            box-sizing: border-box;
            background-color: #2b3443;
            color: #fff;
        }

        .btn {
            display: block;
            width: 100%;
            padding: 10px;
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

        .btn:hover {
            background-color: #0056b3;
            transform: scale(1.05);
        }

        .error-message {
            color: #ff4d4f;
            text-align: center;
            margin-bottom: 10px;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="stars"></div>

    <div class="form-container">
        <h2>Login</h2>
        <?php if(isset($_GET['error'])): ?>
            <div class="error-message">Incorrect login details. Please try again.</div>
        <?php endif; ?>
        <form action="login_process.php" method="post">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit" class="btn">Log In</button>
        </form>
    </div>
</body>
</html>
