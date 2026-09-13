<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — StockPro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #1e293b 0%, #2563eb 100%);
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
        }
        .login-card {
            background: white; border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            width: 100%; max-width: 420px; padding: 2.5rem;
        }
        .login-logo { text-align: center; margin-bottom: 2rem; }
        .login-logo .icon {
            width: 72px; height: 72px; background: #2563eb;
            border-radius: 20px; display: inline-flex;
            align-items: center; justify-content: center;
            margin-bottom: 1rem;
        }
        .login-logo h4 { font-weight: 700; color: #1e293b; margin: 0; }
        .login-logo p { color: #64748b; font-size: 0.875rem; }
        .form-control { padding: 0.75rem 1rem; border-radius: 10px; border: 1.5px solid #e2e8f0; }
        .form-control:focus { border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37,99,235,0.1); }
        .btn-login { background: #2563eb; border: none; padding: 0.75rem; border-radius: 10px; font-weight: 600; width: 100%; font-size: 1rem; }
        .btn-login:hover { background: #1d4ed8; }
        .input-group-text { border-radius: 10px 0 0 10px; background: #f8fafc; border: 1.5px solid #e2e8f0; }
    </style>
</head>
<body>
<div class="login-card">
    <div class="login-logo">
        <div class="icon">
            <i class="bi bi-box-seam text-white" style="font-size:2rem"></i>
        </div>
        <h4>StockPro</h4>
        <p>Système de gestion de stocks</p>
    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-success py-2 mb-3"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if($errors->any()): ?>
        <div class="alert alert-danger py-2 mb-3">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <div><?php echo e($e); ?></div> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>

    <form action="<?php echo e(route('login')); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <div class="mb-3">
            <label class="form-label fw-semibold">Adresse email</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-envelope text-muted"></i></span>
                <input type="email" name="email" class="form-control"
                       placeholder="votre@email.com" value="<?php echo e(old('email')); ?>" required autofocus>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">Mot de passe</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock text-muted"></i></span>
                <input type="password" name="password" class="form-control"
                       id="passwordInput" placeholder="••••••••" required>
                <button class="btn btn-outline-secondary" type="button"
                        onclick="togglePwd()" style="border-radius:0 10px 10px 0">
                    <i class="bi bi-eye" id="eyeIcon"></i>
                </button>
            </div>
        </div>
        <div class="mb-4 d-flex align-items-center justify-content-between">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                <label class="form-check-label text-muted small" for="remember">Se souvenir de moi</label>
            </div>
        </div>
        <button type="submit" class="btn btn-primary btn-login">
            <i class="bi bi-box-arrow-in-right me-2"></i>Se connecter
        </button>
    </form>
</div>
<script>
function togglePwd() {
    const input = document.getElementById('passwordInput');
    const icon  = document.getElementById('eyeIcon');
    if (input.type === 'password') {
        input.type = 'text'; icon.className = 'bi bi-eye-slash';
    } else {
        input.type = 'password'; icon.className = 'bi bi-eye';
    }
}
</script>
</body>
</html>
<?php /**PATH C:\Users\PC-CONASUR\Downloads\gest_stocks\resources\views/auth/login.blade.php ENDPATH**/ ?>