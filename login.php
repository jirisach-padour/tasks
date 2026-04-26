<?php
$secrets = '/etc/tasks/secrets.php';
if (file_exists($secrets)) require_once $secrets;

function e(mixed $v): string {
    return htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.gc_maxlifetime', 2592000); // 30 dní
session_set_cookie_params(['lifetime' => 2592000, 'path' => '/', 'secure' => true, 'httponly' => true, 'samesite' => 'Strict']);
session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = trim($_POST['username'] ?? '');
    $pass = $_POST['password'] ?? '';
    if ($user === (defined('APP_USER') ? APP_USER : '')
        && defined('APP_PASS_HASH')
        && password_verify($pass, APP_PASS_HASH)) {
        $_SESSION['authenticated'] = true;
        $_SESSION['user'] = $user;
        header('Location: index.php');
        exit;
    }
    $error = 'Nesprávné přihlašovací údaje.';
    sleep(1); // throttle brute-force
}

// Pokud už přihlášen, redirect
if (!empty($_SESSION['authenticated'])) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="cs">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Tasks — Přihlášení</title>
<style>
:root{--red:#E05C4E;--red-hover:#C94F42;--navy:#1B3468;--grey-bg:#F4F5F7;--grey-border:#DDE1E7;--grey-text:#5E6778;--white:#FFFFFF;--radius:8px;--font:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:var(--font);font-size:14px;background:var(--navy);min-height:100vh;display:flex;align-items:center;justify-content:center}
.login-wrap{width:100%;max-width:380px;padding:0 20px}
.login-logo{text-align:center;margin-bottom:32px}
.login-logo h1{color:#fff;font-size:28px;font-weight:700;letter-spacing:-.3px}
.login-logo p{color:rgba(255,255,255,.55);font-size:13px;margin-top:6px}
.login-card{background:var(--white);border-radius:12px;padding:32px;box-shadow:0 8px 40px rgba(0,0,0,.3)}
.field{margin-bottom:18px}
.field label{display:block;font-size:12px;font-weight:600;color:var(--grey-text);margin-bottom:6px;text-transform:uppercase;letter-spacing:.4px}
.field input{width:100%;height:42px;padding:0 12px;border:1px solid var(--grey-border);border-radius:var(--radius);font-size:14px;font-family:var(--font);outline:none;transition:border-color .15s;color:var(--navy)}
.field input:focus{border-color:var(--navy)}
.btn-login{width:100%;height:44px;background:var(--red);color:#fff;border:none;border-radius:var(--radius);font-size:14px;font-weight:700;font-family:var(--font);cursor:pointer;transition:background .15s;margin-top:4px}
.btn-login:hover{background:var(--red-hover)}
.error{background:#FEE8E7;color:#E63327;font-size:13px;padding:10px 14px;border-radius:var(--radius);margin-bottom:18px;border:1px solid #fcc}
</style>
</head>
<body>
<div class="login-wrap">
  <div class="login-logo">
    <h1>Tasks</h1>
    <p>Osobní prioritizace · Jiří Šach</p>
  </div>
  <div class="login-card">
    <?php if ($error): ?>
    <div class="error"><?= e($error) ?></div>
    <?php endif; ?>
    <form method="POST" autocomplete="on">
      <div class="field">
        <label for="username">Uživatelské jméno</label>
        <input type="text" id="username" name="username" value="<?= e($_POST['username'] ?? '') ?>" autofocus autocomplete="username">
      </div>
      <div class="field">
        <label for="password">Heslo</label>
        <div style="position:relative">
          <input type="password" id="password" name="password" autocomplete="current-password" style="width:100%;padding-right:40px">
          <button type="button" id="togglePass" onclick="var i=document.getElementById('password');var b=document.getElementById('togglePass');i.type=i.type==='password'?'text':'password';b.textContent=i.type==='password'?'👁':'🙈';" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;font-size:16px;color:var(--grey-text);line-height:1;padding:0">👁</button>
        </div>
      </div>
      <button type="submit" class="btn-login">Přihlásit se</button>
    </form>
  </div>
</div>
</body>
</html>
