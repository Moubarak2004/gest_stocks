<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'Gestion de Stock'); ?> — StockPro</title>
    <!-- Bootstrap 5 -->
    <!-- Bootstrap Icons -->
    <!--<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"> -->
    <!-- Google Fonts -->
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">-->
    <!-- CSS local (remplace Bootstrap, icônes et Google Fonts) -->
    <!--  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">  -->
    <link href="<?php echo e(asset('vendor/bootstrap/css/bootstrap.min.css')); ?>" rel="stylesheet">
<link href="<?php echo e(asset('vendor/bootstrap-icons/bootstrap-icons.css')); ?>" rel="stylesheet">
  <!--  <link href="<?php echo e(asset('css/app.css')); ?>" rel="stylesheet">-->
    <?php echo $__env->yieldPushContent('styles'); ?>
    <style>
        :root {
            --sidebar-width: 260px;
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --sidebar-bg: #1e293b;
            --sidebar-text: #94a3b8;
            --sidebar-active: #2563eb;
        }
        body { font-family: 'Inter', sans-serif; background: #f1f5f9; }

        /* Sidebar */
        #sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            background: var(--sidebar-bg);
            position: fixed;
            top: 0; left: 0;
            z-index: 1000;
            transition: all 0.3s;
            overflow-y: auto;
            padding-bottom: 0;
        }
        #sidebar .sidebar-brand {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }
        #sidebar .sidebar-brand h5 {
            color: #fff;
            font-weight: 700;
            margin: 0;
            font-size: 1.1rem;
        }
        #sidebar .sidebar-brand small { color: var(--sidebar-text); font-size: 0.75rem; }
        #sidebar .nav-section {
            padding: 0.07rem 1rem 0.1rem;
            font-size: 0.6rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #475569;
        }
        #sidebar .nav-link {
            color: var(--sidebar-text);
            padding: 0.55rem 1rem;
            border-radius: 6px;
            margin: 1px 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.6rem;
            font-size: 0.8rem;
            transition: all 0.2s;
        }
        #sidebar .nav-link:hover { background: rgba(255,255,255,0.08); color: #fff; }
        #sidebar .nav-link.active { background: var(--sidebar-active); color: #fff; }
        #sidebar .nav-link i { font-size: 1rem; width: 18px; text-align: center; }
        #sidebar .badge-alerte {
            background: #ef4444;
            color: white;
            border-radius: 20px;
            font-size: 0.7rem;
            padding: 1px 6px;
            margin-left: auto;
        }
        /* Profil utilisateur compact */
        .user-info-compact {
            padding: 0.5rem 0.75rem;
            color: #94a3b8;
            font-size: 0.8rem;
            border-top: 1px solid rgba(255,255,255,0.08);
            margin-top: 0.5rem;
        }
        .user-info-compact a {
            color: #94a3b8;
            text-decoration: none;
        }
        .user-info-compact a:hover {
            color: white;
        }

        /* Main content */
        #main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: all 0.3s;
        }
        .topbar {
            background: white;
            border-bottom: 1px solid #e2e8f0;
            padding: 0.75rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 999;
        }
        .content-area { padding: 1.5rem; }

        /* Cards */
        .card { border: none; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.06); }
        .card-header { border-radius: 12px 12px 0 0 !important; font-weight: 600; }

        /* Stat cards */
        .stat-card { border-radius: 12px; padding: 1.25rem; color: white; }
        .stat-card .stat-value { font-size: 1.75rem; font-weight: 700; }
        .stat-card .stat-label { font-size: 0.8rem; opacity: 0.85; }
        .stat-card i { font-size: 2rem; opacity: 0.3; }

        /* Tables */
        .table th { font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; border-bottom: 2px solid #e2e8f0; }
        .table td { vertical-align: middle; font-size: 0.875rem; }

        /* Badges stock */
        .badge-normal  { background: #dcfce7; color: #166534; }
        .badge-alerte  { background: #fef9c3; color: #854d0e; }
        .badge-rupture { background: #fee2e2; color: #991b1b; }

        /* Alerts */
        .alert { border-radius: 10px; border: none; }

        /* Buttons */
        .btn { border-radius: 8px; font-size: 0.875rem; font-weight: 500; }
        .btn-primary { background: var(--primary); border-color: var(--primary); }
        .btn-primary:hover { background: var(--primary-dark); border-color: var(--primary-dark); }

        /* Forms */
        .form-control, .form-select { border-radius: 8px; border: 1.5px solid #e2e8f0; font-size: 0.875rem; }
        .form-control:focus, .form-select:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(37,99,235,0.1); }
        .form-label { font-weight: 500; font-size: 0.875rem; color: #374151; }

        /* Responsive */
        @media (max-width: 768px) {
            #sidebar { transform: translateX(-100%); }
            #sidebar.show { transform: translateX(0); }
            #main-content { margin-left: 0; }
        }
        .page-title { font-size: 1.3rem; font-weight: 700; color: #1e293b; }

        /* Autocomplete */
        .autocomplete-results {
            position: absolute; z-index: 9999;
            background: white; border: 1px solid #e2e8f0;
            border-radius: 8px; width: 100%;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            max-height: 250px; overflow-y: auto;
        }
        .autocomplete-item {
            padding: 0.5rem 1rem; cursor: pointer; font-size: 0.85rem;
            display: flex; justify-content: space-between;
        }
        .autocomplete-item:hover { background: #f1f5f9; }
    </style>
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body>

<!-- Sidebar -->
<nav id="sidebar">
    <div class="sidebar-brand">
        <h5><i class="bi bi-box-seam me-2"></i>StockPro</h5>
        <small>Gestion de Stocks</small>
    </div>

    <div class="nav-section">Principal</div>
    <a href="<?php echo e(route('dashboard')); ?>" class="nav-link <?php echo e(request()->routeIs('dashboard') ? 'active' : ''); ?>">
        <i class="bi bi-speedometer2"></i> Tableau de bord
    </a>

    <div class="nav-section">Stock</div>
    <a href="<?php echo e(route('articles.index')); ?>" class="nav-link <?php echo e(request()->routeIs('articles.*') ? 'active' : ''); ?>">
        <i class="bi bi-box2"></i> Articles
    </a>
    <?php if(auth()->user()->isGerant() || auth()->user()->isAdmin()): ?>
    <a href="<?php echo e(route('categories.index')); ?>" class="nav-link <?php echo e(request()->routeIs('categories.*') ? 'active' : ''); ?>">
        <i class="bi bi-tags"></i> Catégories
    </a>
    <a href="<?php echo e(route('fournisseurs.index')); ?>" class="nav-link <?php echo e(request()->routeIs('fournisseurs.*') ? 'active' : ''); ?>">
        <i class="bi bi-truck"></i> Fournisseurs
    </a>
    <?php endif; ?>

    <div class="nav-section">Mouvements</div>
    <?php if(auth()->user()->isGerant() || auth()->user()->isAdmin()): ?>
    <a href="<?php echo e(route('entrees.index')); ?>" class="nav-link <?php echo e(request()->routeIs('entrees.*') ? 'active' : ''); ?>">
        <i class="bi bi-arrow-down-circle"></i> Entrées
    </a>
    <?php endif; ?>
    <a href="<?php echo e(route('sorties.index')); ?>" class="nav-link <?php echo e(request()->routeIs('sorties.*') ? 'active' : ''); ?>">
        <i class="bi bi-arrow-up-circle"></i> Sorties / Ventes
    </a>
    <?php if(auth()->user()->isGerant() || auth()->user()->isAdmin()): ?>
    <a href="<?php echo e(route('bons.index')); ?>" class="nav-link <?php echo e(request()->routeIs('bons.*') ? 'active' : ''); ?>">
        <i class="bi bi-file-earmark-text"></i> Bons de commande
    </a>
    <?php endif; ?>

    <div class="nav-section">Suivi</div>
    <?php $alertesCount = \App\Models\Alerte::where('lue', false)->count(); ?>
    <a href="<?php echo e(route('alertes.index')); ?>" class="nav-link <?php echo e(request()->routeIs('alertes.*') ? 'active' : ''); ?>">
        <i class="bi bi-bell"></i> Alertes
        <?php if($alertesCount > 0): ?>
            <span class="badge-alerte"><?php echo e($alertesCount); ?></span>
        <?php endif; ?>
    </a>

    <?php if(auth()->user()->isGerant() || auth()->user()->isAdmin()): ?> 
    <a href="<?php echo e(route('rapports.index')); ?>" class="nav-link <?php echo e(request()->routeIs('rapports.*') ? 'active' : ''); ?>">
        <i class="bi bi-bar-chart"></i> Rapports
    </a>
    <?php endif; ?>

    <?php if(auth()->user()->isAdmin()): ?>
        <div class="nav-section">Administration</div>
        <a href="<?php echo e(route('users.index')); ?>" class="nav-link <?php echo e(request()->routeIs('users.*') ? 'active' : ''); ?>">
            <i class="bi bi-people"></i> Utilisateurs
        </a>
    <?php endif; ?>

    <!-- Prêts d'objets (admin ou gérant) -->
    <?php if(auth()->user()->isGerant() || auth()->user()->isAdmin()): ?>
        <a href="<?php echo e(route('prets.index')); ?>" class="nav-link <?php echo e(request()->routeIs('prets.*') ? 'active' : ''); ?>">
            <i class="bi bi-hand-thumbs-up"></i> Prêts d'objets
        </a>
    <?php endif; ?>

    <!-- ===== DÉCONNEXION ET PROFIL ===== -->
    <div class="nav-section">Compte</div>
    <a href="<?php echo e(route('profile')); ?>" class="nav-link">
        <i class="bi bi-person"></i> Mon profil
    </a>
    <form action="<?php echo e(route('logout')); ?>" method="POST" id="logout-form">
        <?php echo csrf_field(); ?>
        <button class="nav-link border-0 bg-transparent w-100 text-start" style="color: #ef4444;">
            <i class="bi bi-box-arrow-right"></i> Déconnexion
        </button>
    </form>

    <!-- Informations utilisateur compactes avec rôle -->
    <div class="user-info-compact">
        <i class="bi bi-person-circle me-1"></i>
        <?php echo e(auth()->user()->name); ?><br>
        <small class="text-muted"><?php echo e(ucfirst(auth()->user()->role)); ?></small>
    </div>
</nav>

<!-- Main -->
<div id="main-content">
    <div class="topbar">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-sm btn-light d-md-none" id="sidebarToggle">
                <i class="bi bi-list fs-5"></i>
            </button>
            <div>
                <div class="page-title"><?php echo $__env->yieldContent('page-title', 'Dashboard'); ?></div>
                <?php if (! empty(trim($__env->yieldContent('breadcrumb')))): ?>
                <nav aria-label="breadcrumb" style="font-size: 0.8rem;">
                    <ol class="breadcrumb mb-0"><?php echo $__env->yieldContent('breadcrumb'); ?></ol>
                </nav>
                <?php endif; ?>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="<?php echo e(route('alertes.index')); ?>" class="btn btn-sm btn-light position-relative">
                <i class="bi bi-bell"></i>
                <?php if($alertesCount > 0): ?>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:0.6rem">
                    <?php echo e($alertesCount); ?>

                </span>
                <?php endif; ?>
            </a>
            <!-- Menu déroulant du profil -->
            <div class="dropdown">
                <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-person-circle"></i> <?php echo e(auth()->user()->name); ?>

                    <span class="badge bg-secondary"><?php echo e(ucfirst(auth()->user()->role)); ?></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="<?php echo e(route('profile')); ?>"><i class="bi bi-person"></i> Mon profil</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="<?php echo e(route('logout')); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <button class="dropdown-item" type="submit"><i class="bi bi-box-arrow-right"></i> Déconnexion</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="content-area">
        <?php if(session('success')): ?>
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
                <i class="bi bi-check-circle-fill"></i> <?php echo e(session('success')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if(session('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
                <i class="bi bi-exclamation-circle-fill"></i> <?php echo e(session('error')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if($errors->any()): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <strong>Erreurs :</strong>
                <ul class="mb-0 mt-1">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php echo $__env->yieldContent('content'); ?>
    </div>
</div>

<script>
    <!--<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script> -->
    <script src="<?php echo e(asset('vendor/bootstrap/js/bootstrap.bundle.min.js')); ?>"></script>
    // Sidebar toggle mobile
    document.getElementById('sidebarToggle')?.addEventListener('click', () => {
        document.getElementById('sidebar').classList.toggle('show');
    });
</script>
<?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html><?php /**PATH C:\Users\PC-CONASUR\Downloads\gest_stocks\resources\views/layouts/app.blade.php ENDPATH**/ ?>