<?php
require_once __DIR__ . '/helpers/session.php';
require_once __DIR__ . '/helpers/utils.php';

startSession();

if (isLoggedIn()) {
    header("Location: /Personal-budget-dashboard/modules/dashboard.php");
    exit();
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/controllers/authController.php';

    $auth = new AuthController();
    $result = $auth->login($_POST['email'] ?? '', $_POST['password'] ?? '');

    if ($result['success']) {
        header("Location: /Personal-budget-dashboard/modules/dashboard.php?msg=" . $result['message']);
        exit();
    } else {
        $errors = $result['errors'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - Budget Dashboard</title>

<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}
body {
    height: 100vh;
    overflow: hidden;
    background: url('/Personal-budget-dashboard/assets/images/login.jpg') no-repeat center center/cover;
    display: flex;
    justify-content: center;
    align-items: center;
    font-family: "Poppins", sans-serif;
}

/* Compact Glass Card */
.login-card {
    width: 100%;
    max-width: 400px;
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border-radius: 16px;
    padding: 20px;
    box-shadow: 0 0 20px rgba(102, 126, 234, 0.35);
    animation: fadeInUp 0.8s ease;
    color: #fff;
}

@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.login-header {
    text-align: center;
    margin-bottom: 20px;
}

.login-header i {
    font-size: 45px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.login-header h2 {
    margin: 8px 0 4px;
    font-weight: 700;
    font-size: 20px;
}

.login-header p {
    font-size: 12px;
    opacity: 0.85;
}

/* Labels */
.form-label {
    font-weight: 600;
    color: #eee;
    font-size: 13px;
}

/* Inputs */
.form-control {
    padding: 10px;
    border-radius: 10px;
    border: none;
    background: rgba(255, 255, 255, 0.85);
    font-size: 14px;
    transition: 0.3s ease;
}

.form-control:focus {
    box-shadow: 0 0 10px rgba(118, 75, 162, 0.7);
    transform: scale(1.01);
}

/* Button */
.btn-login {
    width: 100%;
    margin-top: 12px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    padding: 10px;
    border-radius: 10px;
    border: none;
    font-weight: 600;
    font-size: 14px;
    transition: 0.3s ease;
}

.btn-login:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(102, 126, 234, 0.35);
}

/* Register Link */
.register-link {
    text-align: center;
    margin-top: 15px;
    font-size: 13px;
}

.register-link a {
    color: white;
    font-weight: 600;
    text-decoration: none;
}

.register-link a:hover {
    text-decoration: underline;
}

/* Error Box */
.alert {
    border-radius: 8px;
    background: rgba(255, 0, 0, 0.25);
    color: white;
    border: none;
    font-size: 13px;
}
</style>
</head>

<body>

<div class="login-card">

    <div class="login-header">
        <i class="fas fa-wallet"></i>
        <h2>Welcome Back</h2>
        <p>Login to your Budget Dashboard</p>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert p-2 mb-3">
            <ul class="mb-0 ps-3">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST">

        <div class="mb-2">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required
                value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        </div>

        <div class="mb-2">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <button type="submit" class="btn-login">
            <i class="fas fa-sign-in-alt"></i> Login
        </button>

        <div class="register-link">
            Don't have an account? <a href="/Personal-budget-dashboard/register.php">Register</a>
        </div>

    </form>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

</body>
</html>
