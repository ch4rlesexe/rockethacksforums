<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Application Portal</title>
    <link rel="stylesheet" href="styles.css">
    <style>

        body {
            margin: 0;
            padding: 0;
            font-family: 'Roboto', sans-serif;
            color: #ffffff;
            background-color: #0b0f19;
            overflow: hidden;
        }

        h1, p {
            text-align: center;
            margin: 0;
        }

        .landing-page {
            position: relative;
            z-index: 10;
            text-align: center;
            padding: 100px 20px;
        }

        .landing-page h1 {
            font-size: 3rem;
            margin-bottom: 20px;
            animation: fadeIn 2s ease-out;
        }

        .landing-page p {
            font-size: 1.2rem;
            margin-bottom: 30px;
            animation: fadeIn 2s ease-out 0.5s;
        }

        .btn {
            display: inline-block;
            margin: 10px;
            padding: 10px 20px;
            font-size: 1rem;
            font-weight: bold;
            text-decoration: none;
            color: #fff;
            background-color: #007bff;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
            animation: fadeIn 2s ease-out 1s;
        }

        .btn:hover {
            background-color: #0056b3;
            transform: scale(1.05);
        }

        .rocket-container {
            position: absolute;
            bottom: -150px;
            left: 50%;
            transform: translateX(-50%);
            animation: rocketTakeoff 5s ease-out forwards, rocketGlow 2s infinite alternate;
            z-index: 5;
        }

        .rocket {
            width: 100px;
            height: auto;
            display: block;
        }

.flames {
    position: absolute;
    bottom: -40px; 
    left: 50%;
    transform: translateX(-50%);
    width: 20px;
    height: 70px;
    opacity: 0.9;
    filter: blur(5px);
}

        .flame-layer {
            position: absolute;
            bottom: 0;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            animation: flameFlicker 0.1s infinite alternate;
        }

        .flame-layer:nth-child(1) {
            background: linear-gradient(to bottom, orange, transparent);
            width: 120%;
            height: 100%;
            left: -10%;
            opacity: 0.7;
        }

        .flame-layer:nth-child(2) {
            background: linear-gradient(to bottom, yellow, transparent);
            width: 100%;
            height: 90%;
            opacity: 0.9;
        }

        .flame-layer:nth-child(3) {
            background: linear-gradient(to bottom, red, transparent);
            width: 80%;
            height: 80%;
            opacity: 0.6;
            left: 10%;
        }

        @keyframes flameFlicker {
            0% {
                transform: scaleY(1);
                opacity: 0.7;
            }
            100% {
                transform: scaleY(1.2);
                opacity: 1;
            }
        }

        @keyframes rocketTakeoff {
            0% {
                bottom: -150px;
                opacity: 0;
            }
            50% {
                opacity: 1;
            }
            100% {
                bottom: 120%;
                opacity: 0.2;
            }
        }

        @keyframes rocketGlow {
            0% {
                filter: drop-shadow(0 0 10px rgba(255, 255, 255, 0.5));
            }
            100% {
                filter: drop-shadow(0 0 20px rgba(255, 255, 255, 1));
            }
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

        @keyframes fadeIn {
            0% {
                opacity: 0;
                transform: translateY(20px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div class="stars"></div>

    <div class="landing-page">
        <h1>Welcome to the RocketHacks Application Portal</h1>
        <p>Join us for this exclusive event. Create an account to start your application.</p>
        <button class="btn" onclick="window.location.href='signup.php'">Sign Up</button>
        <button class="btn" onclick="window.location.href='login.php'">Log In</button>
    </div>

    <div class="rocket-container">
        <img src="utrocket.png" alt="Rocket" class="rocket">
        <div class="flames">
            <div class="flame-layer"></div>
            <div class="flame-layer"></div>
            <div class="flame-layer"></div>
        </div>
    </div>
</body>
</html>
