<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Form</title>
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
            width: 400px;
            position: relative;
            z-index: 1;
            animation: fadeIn 1.5s ease-out;
        }

        h2 {
            text-align: center;
            color: #4a90e2;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin: 10px 0 5px;
            font-weight: bold;
            color: #ddd;
        }

        input[type="text"], input[type="email"], input[type="password"], textarea, input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #444;
            border-radius: 6px;
            box-sizing: border-box;
            background-color: #1e2433;
            color: #fff;
        }

        input::placeholder, textarea::placeholder {
            color: #bbb;
        }

        textarea {
            resize: vertical;
            height: 100px;
        }

        .btn {
            display: block;
            width: 100%;
            box-sizing: border-box;
            padding: 12px;
            margin-bottom: 10px;
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

        .btn.cancel {
            background-color: #dc3545;
        }

        .btn.cancel:hover {
            background-color: #c82333;
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
        <h2>Event Application Form</h2>
        <form action="submit_application.php" method="post" enctype="multipart/form-data">
            <label for="question1">Question 1:</label>
            <input type="text" id="question1" name="question1" placeholder="Enter your response..." required>

            <label for="question2">Question 2:</label>
            <textarea id="question2" name="question2" placeholder="Enter your response..." required></textarea>

            <label for="file-upload">Upload File:</label>
            <input type="file" id="file-upload" name="file-upload">

            <button type="submit" class="btn">Submit Application</button>
        </form>
        <a href="dashboard.php" class="btn cancel">Cancel Application</a>
    </div>
</body>
</html>
