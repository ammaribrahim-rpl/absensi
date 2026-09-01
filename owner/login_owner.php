<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Owner — Absensi</title>
  <link rel="icon" href="../img/Fevicon.png" type="image/png">
  <link href="../vendors/fontawesome/css/all.min.css" rel="stylesheet" media="all">
  <link href="../vendor/bootstrap-4.1/bootstrap.min.css" rel="stylesheet" media="all">
  <link href="../css/modern-custom.css" rel="stylesheet" media="all">
  <style>
    .input-icon-wrap { position: relative; }
    .input-icon-wrap .input-icon {
      position: absolute;
      left: 13px;
      top: 50%;
      transform: translateY(-50%);
      color: var(--color-subtle);
      font-size: 0.85rem;
      pointer-events: none;
    }
    .input-icon-wrap .form-control {
      padding-left: 38px !important;
    }
    .brand-icon {
      width: 64px;
      height: 64px;
      border-radius: 18px;
      background: #f3e8ff;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 16px;
    }
    .brand-icon i {
      font-size: 1.8rem !important;
      color: #7e22ce !important;
      line-height: 1 !important;
    }
    .btn-login-owner {
      background-color: #7e22ce !important;
      border-color: #7e22ce !important;
      color: #fff !important;
      font-weight: 700 !important;
      padding: 11px 20px !important;
      font-size: 0.9rem !important;
    }
    .btn-login-owner:hover {
      background-color: #6b21a8 !important;
      box-shadow: 0 4px 14px rgba(126, 34, 206, 0.35) !important;
    }
    .login-divider {
      display: flex;
      align-items: center;
      gap: 10px;
      color: var(--color-subtle);
      font-size: 0.75rem;
      margin: 18px 0;
    }
    .login-divider::before,
    .login-divider::after {
      content: '';
      flex: 1;
      height: 1px;
      background: var(--color-border);
    }
  </style>
</head>
<body>
<div class="login-wrapper">
  <div class="login-card">

    <!-- Brand -->
    <div class="brand">
      <div class="brand-icon">
        <i class="fas fa-crown"></i>
      </div>
      <h1>ABSENSI</h1>
      <p>Portal Login Owner Executive</p>
    </div>

    <?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger py-2 small mb-3" role="alert">
      <i class="fas fa-exclamation-circle mr-1"></i>
      <?php echo htmlspecialchars($_GET['error']); ?>
    </div>
    <?php endif; ?>

    <form action="proses_login_owner.php" method="POST">
      <div class="form-group mb-3">
        <label for="username"><i class="fas fa-user-plus mr-1"></i> Username Owner</label>
        <div class="input-icon-wrap">
          <i class="input-icon fas fa-user"></i>
        <input id="username" type="text" class="form-control" name="username"
                 placeholder="Masukkan username" required autofocus autocomplete="username">
        </div>
      </div>
      <div class="form-group mb-4">
        <label for="password"><i class="fas fa-lock mr-1"></i> Password</label>
        <div class="input-icon-wrap">
          <i class="input-icon fas fa-lock"></i>
          <input id="password" type="password" class="form-control" name="password"
                 placeholder="Masukkan password" required autocomplete="current-password">
        </div>
      </div>
      <button type="submit" class="btn btn-login-owner btn-block">
        <i class="fas fa-sign-in-alt mr-2"></i> Masuk ke Executive Dashboard
      </button>
    </form>

    <div class="login-divider">atau</div>

    <div class="text-center">
      <a href="../index.php" style="font-size:0.82rem;color:var(--color-muted);">
        <i class="fas fa-arrow-left mr-1"></i> Kembali ke Beranda
      </a>
    </div>
  </div>
</div>

<script src="../vendor/jquery-3.2.1.min.js"></script>
<script src="../vendor/bootstrap-4.1/bootstrap.min.js"></script>
</body>
</html>
