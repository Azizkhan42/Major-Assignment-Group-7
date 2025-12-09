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
    $result = $auth->register(
        $_POST['name'] ?? '',
        $_POST['email'] ?? '',
        $_POST['password'] ?? '',
        $_POST['confirm_password'] ?? ''
    );

    if ($result['success']) {
        header("Location: /Personal-budget-dashboard/index.php?msg=" . $result['message']);
        exit();
    } else {
        $errors = $result['errors'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Register - Personal Budget Dashboard</title>

<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<style>
  /* --- Reset & base --- */
  * { box-sizing: border-box; margin: 0; padding: 0; }
  html, body { height: 100%; }
  body {
    height: 100vh;
    overflow: hidden; /* keep everything inside the viewport */
    font-family: "Inter", system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    /* animated gradient background */
    background: linear-gradient(120deg, #0f172a 0%, #1e293b 40%, #3b185f 100%);
  }

  /* subtle moving gradient layer */
  .bg-anim {
    position: fixed;
    inset: 0;
    z-index: -2;
    background: linear-gradient(60deg, rgba(106,90,224,0.18), rgba(142,68,173,0.14), rgba(16,185,129,0.08));
    background-size: 400% 400%;
    animation: gradientShift 12s ease infinite;
    pointer-events: none;
    filter: blur(30px) saturate(120%);
    opacity: 0.95;
  }

  @keyframes gradientShift {
    0%{ background-position: 0% 50%; }
    50%{ background-position: 100% 50%; }
    100%{ background-position: 0% 50%; }
  }

  /* optional soft noise overlay to add depth */
  .bg-noise {
    position: fixed;
    inset: 0;
    z-index: -1;
    background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="1600" height="900"><filter id="n"><feTurbulence baseFrequency="0.9" numOctaves="2" seed="2" stitchTiles="stitch"/></filter><rect width="100%" height="100%" filter="url(%23n)" opacity="0.02"/></svg>');
    opacity: 1;
    pointer-events: none;
  }

  /* --- Card --- */
  .register-container {
    width: 100%;
    max-width: 640px;           /* slightly wider for nicer layout */
    border-radius: 16px;
    padding: 28px;
    position: relative;
    overflow: hidden;
    /* glass card */
    background: linear-gradient(180deg, rgba(255,255,255,0.06), rgba(255,255,255,0.03));
    border: 1px solid rgba(255,255,255,0.06);
    box-shadow: 0 12px 40px rgba(2,6,23,0.6);
    backdrop-filter: blur(8px) saturate(120%);
    -webkit-backdrop-filter: blur(8px) saturate(120%);
  }

  /* header */
  .register-top {
    display:flex;
    gap:14px;
    align-items:center;
    margin-bottom: 18px;
  }
  .logo-round {
    width:56px;
    height:56px;
    border-radius:12px;
    display:flex;
    align-items:center;
    justify-content:center;
    background: linear-gradient(135deg,#6a5ae0,#8e44ad);
    box-shadow: 0 6px 18px rgba(106,90,224,0.25);
    font-size:20px;
  }
  .register-title h1 {
    font-size:20px;
    margin-bottom:4px;
    letter-spacing:0.2px;
  }
  .register-title p {
    margin:0;
    color: rgba(255,255,255,0.82);
    font-size:13px;
  }

  /* errors */
  .alert {
    border-radius: 10px;
    border: none;
    padding: 10px 12px;
    font-size: 14px;
  }

  /* form grid: two columns on desktop, single column on small screens */
  .form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
  }

  /* make inputs full width and visually consistent */
  label { display:block; margin-bottom:6px; font-weight:600; color:#eef2ff; font-size:13px; }
  .form-control {
    width:100%;
    padding: 11px 12px;
    border-radius: 10px;
    border: 1px solid rgba(255,255,255,0.07);
    background: linear-gradient(180deg, rgba(255,255,255,0.03), rgba(255,255,255,0.01));
    color: #fff;
    font-size:14px;
    outline: none;
    transition: box-shadow .18s ease, transform .12s ease, border-color .12s ease;
  }

  .form-control::placeholder { color: rgba(255,255,255,0.5); }

  .form-control:focus {
    border-color: rgba(106,90,224,0.9);
    box-shadow: 0 6px 18px rgba(106,90,224,0.12);
    transform: translateY(-1px);
  }

  /* single-row helper */
  .col-span-2 { grid-column: 1 / -1; } /* element spans both columns */

  /* submit */
  .actions {
    margin-top: 12px;
  }
  .btn-register {
    display:inline-flex;
    align-items:center;
    gap:10px;
    justify-content:center;
    width:100%;
    padding: 12px 14px;
    border-radius: 12px;
    border: none;
    background: linear-gradient(90deg,#6a5ae0,#8e44ad);
    color: #fff;
    font-weight: 700;
    font-size: 15px;
    box-shadow: 0 10px 28px rgba(102,86,224,0.18);
    transition: transform .14s ease, box-shadow .14s ease;
  }
  .btn-register:hover { transform: translateY(-3px); box-shadow: 0 16px 40px rgba(102,86,224,0.22); }

  .login-link { text-align:center; margin-top:12px; font-weight:600; color: rgba(255,255,255,0.9); }
  .login-link a { color: #fff; text-decoration: underline; }

  /* small screens: stack fields, reduce padding */
  @media (max-width: 640px) {
    body { padding: 14px; }
    .register-container { padding: 20px; max-width: 520px; }
    .form-grid { grid-template-columns: 1fr; }
    .logo-round { width:48px; height:48px; font-size:18px; border-radius:10px; }
  }

  /* very small screens: ensure fits in 100vh */
  @media (max-height: 720px) {
    .register-container { max-height: 92vh; overflow-y: auto; padding: 18px; }
  }
</style>
</head>
<body>

  <!-- background layers -->
  <div class="bg-anim" aria-hidden="true"></div>
  <div class="bg-noise" aria-hidden="true"></div>

  <div class="register-container" role="main" aria-label="Register form">
    <div class="register-top">
      <div class="logo-round" aria-hidden="true"><i class="fas fa-coins" style="font-size:18px;"></i></div>
      <div class="register-title">
        <h1>Create Account</h1>
        <p>Set up your Personal Budget Dashboard — secure & fast.</p>
      </div>
    </div>

    <?php if (!empty($errors)): ?>
      <div class="alert alert-danger" role="alert">
        <strong>Registration Error</strong>
        <ul style="margin:6px 0 0 16px;">
          <?php foreach ($errors as $error): ?>
            <li><?php echo htmlspecialchars($error); ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form method="POST" novalidate>

      <div class="form-grid">
        <!-- Full Name -->
        <div>
          <label for="name">Full Name</label>
          <input id="name" class="form-control" name="name" type="text" placeholder="Jane Doe"
                 value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" required />
        </div>

        <!-- Email -->
        <div>
          <label for="email">Email</label>
          <input id="email" class="form-control" name="email" type="email" placeholder="you@company.com"
                 value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required />
        </div>

        <!-- Password -->
        <div>
          <label for="password">Password</label>
          <input id="password" class="form-control" name="password" type="password" placeholder="Min 6 characters" required />
        </div>

        <!-- Confirm -->
        <div>
          <label for="confirm_password">Confirm Password</label>
          <input id="confirm_password" class="form-control" name="confirm_password" type="password" placeholder="Repeat password" required />
        </div>

        <!-- Any extra full-width element (optional) example: terms or note -->
        <div class="col-span-2" style="margin-top: 6px;">
          <small style="color: rgba(255,255,255,0.78);">By creating an account you agree to our <a href="#" style="color:#cfc0ff;text-decoration:underline;">Terms</a>.</small>
        </div>
      </div>

      <div class="actions">
        <button type="submit" class="btn-register">
          <i class="fas fa-user-check"></i> Create Account
        </button>
      </div>

      <div class="login-link">Already have an account? <a href="/Personal-budget-dashboard/index.php">Login here</a></div>
    </form>
  </div>

</body>
</html>
