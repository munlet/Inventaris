<?php session_start(); ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Stok Inventaris SMKN 2 Bandung</title>

  
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #4A90E2 0%, #2E5BBA 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        /* Dotted pattern background */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: radial-gradient(circle, rgba(255,255,255,0.3) 2px, transparent 2px);
            background-size: 20px 20px;
            z-index: 1;
        }

        .container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            position: relative;
            z-index: 2;
            border: 3px solid #2E5BBA;
            transition: transform 0.3s ease;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
        }

        .title {
            font-size: 2.2em;
            font-weight: bold;
            color: #87CEEB;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
            margin-bottom: 10px;
            letter-spacing: 2px;
        }

        .subtitle {
            font-size: 2.5em;
            font-weight: bold;
            color: #87CEEB;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
            letter-spacing: 3px;
        }

        .login-form {
            display: flex;
            flex-direction: column;
            gap: 25px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .form-label {
            font-size: 1.3em;
            font-weight: bold;
            color: #4A90E2;
            text-align: center;
        }

        .form-input {
            background: #2E5BBA;
            border: none;
            border-radius: 10px;
            padding: 15px 20px;
            font-size: 1.1em;
            font-weight: bold;
            color: white;
            text-align: center;
            outline: none;
            transition: all 0.3s ease;
        }

        .form-input::placeholder {
            color: rgba(255, 255, 255, 0.8);
        }

        .form-input:focus {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(46, 91, 186, 0.4);
        }

        .login-button {
            background: #2E5BBA;
            border: none;
            border-radius: 10px;
            padding: 15px 20px;
            font-size: 1.3em;
            font-weight: bold;
            color: white;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        .login-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(46, 91, 186, 0.4);
            background: #4A90E2;
        }

        .divider {
            height: 2px;
            background: #4A90E2;
            margin: 20px 0;
            border-radius: 1px;
        }

        .signup-text {
            text-align: center;
            font-size: 1.4em;
            font-weight: bold;
            color: #4A90E2;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .signup-text:hover {
            transform: scale(1.05);
            color: #2E5BBA;
        }

        .success-message {
            position: fixed;
            top: 20px;
            right: 20px;
            background: rgba(135, 206, 235, 0.95);
            color: #2E5BBA;
            padding: 15px 25px;
            border-radius: 10px;
            font-weight: bold;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            transform: translateX(400px);
            transition: transform 0.5s ease;
            z-index: 3;
            border: 2px solid #4A90E2;
        }

        .success-message.show {
            transform: translateX(0);
        }

        .error-message {
            background: rgba(220, 53, 69, 0.95);
            color: white;
            padding: 15px 25px;
            border-radius: 10px;
            font-weight: bold;
            margin-top: 20px;
            text-align: center;
            border: 2px solid #dc3545;
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        .hamburger {
            position: fixed;
            top: 30px;
            right: 30px;
            width: 40px;
            height: 30px;
            cursor: pointer;
            z-index: 4;
        }

        .hamburger span {
            display: block;
            width: 100%;
            height: 4px;
            background: white;
            margin: 6px 0;
            border-radius: 2px;
            transition: 0.3s;
        }

        @media (max-width: 500px) {
            .container {
                margin: 20px;
                padding: 30px 25px;
            }
            
            .title {
                font-size: 1.8em;
            }
            
            .subtitle {
                font-size: 2em;
            }
        }
    </style>
</head>
<body>
    <div class="hamburger">
        <span></span>
        <span></span>
        <span></span>
    </div>

    <div class="container">
        <div class="header">
            <div class="title">STOK INVENTARIS</div>
            <div class="subtitle">SMKN 2 BANDUNG</div>
        </div>

        <form class="login-form" action="../auth.php" method="POST">
            <div class="form-group">
                <label class="form-label">LOGIN</label>
                <input type="text" class="form-input" placeholder="Username" name="username" required>
            </div>

            <div class="form-group">
                <label class="form-label">PASSWORD</label>
                <input type="password" class="form-input" placeholder="password" name="password" required>
            </div>

            <button type="submit" class="login-button">LOGIN</button>

            <div class="divider"></div>
        </form>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="error-message">
                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Success message for login -->
    <?php if (isset($_SESSION['login_success'])): ?>
        <div class="success-message show" id="successMessage">
            <?= $_SESSION['login_success']; unset($_SESSION['login_success']); ?>
        </div>
        <script>
            setTimeout(() => {
                document.getElementById('successMessage').classList.remove('show');
            }, 3000);
        </script>
    <?php endif; ?>

    <script>
        // Add floating animation to container
        const container = document.querySelector('.container');
        let floating = true;
        
        function floatAnimation() {
            if (floating) {
                container.style.transform = 'translateY(-10px)';
            } else {
                container.style.transform = 'translateY(0px)';
            }
            floating = !floating;
        }
        
        setInterval(floatAnimation, 3000);
    </script>
</body>
</html>