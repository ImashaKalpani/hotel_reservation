<?php
require 'config.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = "Both fields are required.";
    } else {
        // Add delay to prevent timing attacks
        usleep(random_int(100000, 300000));
        
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user) {
            if (password_verify($password, $user['password'])) {
                // Regenerate session ID to prevent session fixation
                session_regenerate_id(true);
                
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['last_login'] = time();
                
                header('Location: index.php');
                exit;
            }
        }
        
        // Generic error message to prevent user enumeration
        $error = "Invalid email or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Secure App</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --accent-color: #4cc9f0;
            --error-color: #f72585;
            --success-color: #4ad66d;
            --text-color: #2b2d42;
            --light-color: #f8f9fa;
            --dark-color: #212529;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: var(--text-color);
        }
        
        .container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            padding: 2.5rem;
            position: relative;
            overflow: hidden;
            animation: fadeIn 0.6s ease-out;
        }
        
        .container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, var(--primary-color), var(--accent-color));
            animation: rainbow 8s linear infinite;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes rainbow {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        h2 {
            text-align: center;
            margin-bottom: 1.5rem;
            color: var(--primary-color);
            font-weight: 600;
        }
        
        .form-row {
            margin-bottom: 1.5rem;
            position: relative;
        }
        
        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--dark-color);
        }
        
        input {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        input:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
        }
        
        .btn {
            width: 100%;
            padding: 0.8rem;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .btn:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3);
        }
        
        .btn:active {
            transform: translateY(0);
        }
        
        .error {
            color: var(--error-color);
            background: rgba(247, 37, 133, 0.1);
            padding: 0.8rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            text-align: center;
            animation: shake 0.5s ease-in-out;
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-5px); }
            40%, 80% { transform: translateX(5px); }
        }
        
        .forgot-password {
            text-align: center;
            margin-top: 1rem;
        }
        
        .forgot-password a {
            color: var(--primary-color);
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s ease;
        }
        
        .forgot-password a:hover {
            color: var(--secondary-color);
            text-decoration: underline;
        }
        
        .floating-animation {
            position: absolute;
            width: 50px;
            height: 50px;
            background: rgba(67, 97, 238, 0.1);
            border-radius: 50%;
            animation: float 15s infinite linear;
            z-index: -1;
        }
        
        .floating-animation:nth-child(1) {
            top: 20%;
            left: 10%;
            width: 80px;
            height: 80px;
            animation-delay: 0s;
        }
        
        .floating-animation:nth-child(2) {
            top: 60%;
            left: 80%;
            width: 60px;
            height: 60px;
            animation-delay: 2s;
        }
        
        .floating-animation:nth-child(3) {
            top: 80%;
            left: 20%;
            width: 40px;
            height: 40px;
            animation-delay: 4s;
        }
        
        @keyframes float {
            0% { transform: translateY(0) rotate(0deg); opacity: 1; }
            50% { transform: translateY(-50px) rotate(180deg); opacity: 0.7; }
            100% { transform: translateY(0) rotate(360deg); opacity: 1; }
        }
        
        @media (max-width: 480px) {
            .container {
                padding: 1.5rem;
                margin: 0 1rem;
            }
        }

         .login-link {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.9rem;
        }
        
        .login-link a {
            color: var(--primary-color);
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .login-link a:hover {
            color: var(--secondary-color);
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="floating-animation"></div>
    <div class="floating-animation"></div>
    <div class="floating-animation"></div>
    
    <div class="container">
        <h2>Welcome Back</h2>
        
        <?php if (!empty($error)): ?>
            <p class="error"><?=htmlspecialchars($error, ENT_QUOTES, 'UTF-8')?></p>
        <?php endif; ?>
        
        <form method="post" id="loginForm">
            <div class="form-row">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" value="<?=htmlspecialchars($email ?? '', ENT_QUOTES, 'UTF-8')?>" required autocomplete="email">
            </div>
            
            <div class="form-row">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required autocomplete="current-password">
            </div>
            
            <button class="btn" type="submit">Login</button>
            
            
            <div class="forgot-password">
                <a href="">Forgot your password?</a>
            </div>
        
             <div class="login-link">
                Don't have an account? <a href="register.php">Sign up</a>
            </div>

        </form>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            
            if (!email || !password) {
                e.preventDefault();
                alert('Please fill in all fields');
                return false;
            }
            
            // Add loading animation
            const btn = this.querySelector('button[type="submit"]');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner"></span> Logging in...';
            
            return true;
        });
        
        // Add floating label effect
        const inputs = document.querySelectorAll('input');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.querySelector('label').style.color = '#4361ee';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.querySelector('label').style.color = '';
            });
        });
    </script>
</body>
</html>