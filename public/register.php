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

$errors = [];
$name = $email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';

    // Validation
    if (empty($name)) {
        $errors['name'] = "Name is required.";
    } elseif (strlen($name) > 50) {
        $errors['name'] = "Name must be less than 50 characters.";
    }

    if (empty($email)) {
        $errors['email'] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format.";
    } elseif (strlen($email) > 100) {
        $errors['email'] = "Email must be less than 100 characters.";
    }

    if (empty($password)) {
        $errors['password'] = "Password is required.";
    } elseif (strlen($password) < 8) {
        $errors['password'] = "Password must be at least 8 characters.";
    } elseif (!preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $errors['password'] = "Password must contain at least one uppercase letter and one number.";
    }

    if ($password !== $confirm) {
        $errors['confirm'] = "Passwords do not match.";
    }

    // Only check database if no errors so far
    if (empty($errors)) {
        // Add delay to prevent timing attacks
        usleep(random_int(100000, 300000));
        
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors['email'] = "Email already registered.";
        } else {
            // Insert user with prepared statement
            $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
            $stmt = $pdo->prepare('INSERT INTO users (name, email, password, created_at) VALUES (?, ?, ?, NOW())');
            if ($stmt->execute([$name, $email, $hash])) {
                $_SESSION['registration_success'] = true;
                header('Location: login.php');
                exit;
            } else {
                $errors['general'] = "Registration failed. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Secure App</title>
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
            max-width: 450px;
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
            font-size: 0.85rem;
            margin-top: 0.3rem;
            display: block;
            animation: fadeIn 0.3s ease-out;
        }
        
        .error-message {
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
        
        .password-strength {
            margin-top: 0.5rem;
            height: 5px;
            background: #e9ecef;
            border-radius: 3px;
            overflow: hidden;
        }
        
        .password-strength-bar {
            height: 100%;
            width: 0;
            background: #ddd;
            transition: width 0.3s ease, background 0.3s ease;
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
    </style>
</head>
<body>
    <div class="floating-animation" style="top: 20%; left: 10%; width: 80px; height: 80px;"></div>
    <div class="floating-animation" style="top: 60%; left: 80%; width: 60px; height: 60px; animation-delay: 2s;"></div>
    <div class="floating-animation" style="top: 80%; left: 20%; width: 40px; height: 40px; animation-delay: 4s;"></div>
    
    <div class="container">
        <h2>Create Your Account</h2>
        
        <?php if (!empty($errors['general'])): ?>
            <div class="error-message"><?=htmlspecialchars($errors['general'], ENT_QUOTES, 'UTF-8')?></div>
        <?php endif; ?>
        
        <form method="post" id="registerForm">
            <div class="form-row">
                <label for="name">Full Name</label>
                <input type="text" name="name" id="name" value="<?=htmlspecialchars($name, ENT_QUOTES, 'UTF-8')?>" required>
                <?php if (!empty($errors['name'])): ?>
                    <span class="error"><?=htmlspecialchars($errors['name'], ENT_QUOTES, 'UTF-8')?></span>
                <?php endif; ?>
            </div>
            
            <div class="form-row">
                <label for="email">Email Address</label>
                <input type="email" name="email" id="email" value="<?=htmlspecialchars($email, ENT_QUOTES, 'UTF-8')?>" required>
                <?php if (!empty($errors['email'])): ?>
                    <span class="error"><?=htmlspecialchars($errors['email'], ENT_QUOTES, 'UTF-8')?></span>
                <?php endif; ?>
            </div>
            
            <div class="form-row">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required minlength="8">
                
                <?php if (!empty($errors['password'])): ?>
                    <span class="error"><?=htmlspecialchars($errors['password'], ENT_QUOTES, 'UTF-8')?></span>
                <?php endif; ?>
            </div>
            
            <div class="form-row">
                <label for="confirm">Confirm Password</label>
                <input type="password" name="confirm" id="confirm" required>
                <?php if (!empty($errors['confirm'])): ?>
                    <span class="error"><?=htmlspecialchars($errors['confirm'], ENT_QUOTES, 'UTF-8')?></span>
                <?php endif; ?>
            </div>
            
            <button class="btn" type="submit">Register</button>
            
            <div class="login-link">
                Already have an account? <a href="login.php">Sign in</a>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirm = document.getElementById('confirm').value;
            
            // Client-side password match validation
            if (password !== confirm) {
                e.preventDefault();
                alert('Passwords do not match');
                return false;
            }
            
            // Add loading animation
            const btn = this.querySelector('button[type="submit"]');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner"></span> Creating account...';
            
            return true;
        });
        
        // Password strength indicator
        document.getElementById('password').addEventListener('input', function() {
            const strengthBar = document.getElementById('passwordStrength');
            const password = this.value;
            let strength = 0;
            
            // Length check
            if (password.length >= 8) strength += 20;
            if (password.length >= 12) strength += 20;
            
            // Character variety checks
            if (/[A-Z]/.test(password)) strength += 20;
            if (/[0-9]/.test(password)) strength += 20;
            if (/[^A-Za-z0-9]/.test(password)) strength += 20;
            
            // Update strength bar
            strengthBar.style.width = strength + '%';
            
            // Update color based on strength
            if (strength < 40) {
                strengthBar.style.backgroundColor = '#ff4d4d';
            } else if (strength < 80) {
                strengthBar.style.backgroundColor = '#ffcc00';
            } else {
                strengthBar.style.backgroundColor = '#4ad66d';
            }
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