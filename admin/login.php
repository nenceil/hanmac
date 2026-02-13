<?php
session_start();
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Hanmac Lighting</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
    <style>
        .login-page {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f8f9fa;
            padding: 20px;
            box-sizing: border-box;
        }
        .login-container {
            background-color: #fff;
            padding: 2.5rem;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            width: 100%;
            max-width: 400px;
            box-sizing: border-box;
        }
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .login-logo {
            max-width: 180px;
            height: auto;
            margin-bottom: 0.5rem;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-label {
            display: block;
            margin-bottom: 0.6rem;
            font-weight: 500;
            color: #444;
            font-size: 0.9rem;
        }
        .form-input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            font-family: inherit;
            font-size: 1rem;
            box-sizing: border-box;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .form-input:focus {
            outline: none;
            border-color: #000;
            box-shadow: 0 0 0 3px rgba(0,0,0,0.05);
        }
        .login-btn {
            width: 100%;
            padding: 14px;
            background-color: #000;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: opacity 0.3s;
            margin-top: 0.5rem;
        }
        .login-btn:hover {
            opacity: 0.8;
        }
        .error-msg {
            background-color: #fff5f5;
            color: #c53030;
            padding: 10px;
            border-radius: 6px;
            font-size: 0.85rem;
            margin-bottom: 1.5rem;
            text-align: center;
            border: 1px solid #fed7d7;
        }
        
        /* Mobile Specific */
        @media (max-width: 480px) {
            .login-container {
                padding: 1.75rem;
                box-shadow: none;
                border: 1px solid #eee;
            }
        }
    </style>
</head>
<body>

    <div class="login-page">
        <div class="login-container">
            <div class="login-header">
                <img src="../assets/images/logo_hanmac.png" alt="Hanmac Logo" class="login-logo">
            </div>
            
            <?php if (isset($_GET['error'])): ?>
                <div class="error-msg">Incorrect username or password.</div>
            <?php endif; ?>

            <form action="login_process.php" method="POST">
                <div class="form-group">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" id="username" name="username" class="form-input" required placeholder="Enter username">
                </div>
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password" class="form-input" required placeholder="••••••••">
                </div>
                <button type="submit" class="login-btn">Sign In</button>
            </form>
            <div style="text-align: center; margin-top: 1.5rem;">
                <a href="../index.php" style="text-decoration: none; color: #888; font-size: 0.85rem;">&larr; Back to Website</a>
            </div>
        </div>
    </div>

</body>
</html>
