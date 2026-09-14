
<?php $__env->startSection('title','Articles'); ?>
<?php $__env->startSection('page-title','Articles'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <div></div>
    <a href="<?php echo e(route('articles.create')); ?>" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Nouvel article
    </a>
</div>

<!-- Filtres -->
<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Nom, référence, code barre..." value="<?php echo e(request('search')); ?>">
            </div>
            <div class="col-md-3">
                <select name="categorie" class="form-select">
                    <option value="">Toutes catégories</option>
                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($cat->id); ?>" <?php echo e(request('categorie') == $cat->id ? 'selected' : ''); ?>><?php echo e($cat->nom); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-2">
                <select name="statut" class="form-select">
                    <option value="">Tous statuts</option>
                    <option value="normal"  <?php echo e(request('statut') == 'normal'  ? 'selected' : ''); ?>>Normal</option>
                    <option value="alerte"  <?php echo e(request('statut') == 'alerte'  ? 'selected' : ''); ?>>En alerte</option>
                    <option value="rupture" <?php echo e(request('statut') == 'rupture' ? 'selected' : ''); ?>>Rupture</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button class="btn btn-primary flex-fill">Filtrer</button>
                <a href="<?php echo e(route('articles.index')); ?>" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Référence</th>
                        <th>Nom</th>
                        <th>Catégorie</th>
                        <th>Prix achat</th>
                        <th>Prix vente</th>
                        <th>Stock</th>
                        <th>Statut</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $articles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $article): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td class="fw-semibold text-primary small"><?php echo e($article->reference); ?></td>
                    <td>
                        <div class="fw-semibold"><?php echo e($article->nom); ?></div>
                        <?php if($article->emplacement): ?><div class="text-muted small"><i class="bi bi-geo-alt"></i> <?php echo e($article->emplacement); ?></div><?php endif; ?>
                    </td>
                    <td><span class="badge bg-light text-dark"><?php echo e($article->categorie->nom ?? '-'); ?></span></td>
                    <td><?php echo e(number_format($article->prix_achat, 0, ',', ' ')); ?> F</td>
                    <td><?php echo e(number_format($article->prix_vente, 0, ',', ' ')); ?> F</td>
                    <td class="fw-bold <?php echo e($article->quantite_stock <= 0 ? 'text-danger' : ($article->quantite_stock <= $article->stock_minimum ? 'text-warning' : 'text-success')); ?>">
                        <?php echo e($article->quantite_stock); ?> <?php echo e($article->unite); ?>

                    </td>
                    <td>
                        <?php if($article->statut_stock === 'rupture'): ?>
                            <span class="badge badge-rupture px-2">Rupture</span>
                        <?php elseif($article->statut_stock === 'alerte'): ?>
                            <span class="badge badge-alerte px-2">Alerte</span>
                        <?php else: ?>
                            <span class="badge badge-normal px-2">Normal</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-end">
                        <div class="btn-group btn-group-sm">
                            <a href="<?php echo e(route('articles.show', $article)); ?>" class="btn btn-outline-info" title="Détails"><i class="bi bi-eye"></i></a>
                            <form action="<?php echo e(route('articles.forceDestroy', $article)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Suppression définitive ?')">
    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
</form>
                            <a href="<?php echo e(route('articles.edit', $article)); ?>" class="btn btn-outline-primary" title="Modifier"><i class="bi bi-pencil"></i></a>
                            <button class="btn btn-outline-warning" title="Ajuster stock" data-bs-toggle="modal" data-bs-target="#ajusterModal<?php echo e($article->id); ?>">
                                <i class="bi bi-sliders"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <!-- Modal ajustement -->
                <div class="modal fade" id="ajusterModal<?php echo e($article->id); ?>" tabindex="-1">
                    <div class="modal-dialog modal-sm">
                        <div class="modal-content">
                            <div class="modal-header"><h6 class="modal-title">Ajuster stock — <?php echo e($article->nom); ?></h6></div>
                            <form action="<?php echo e(route('articles.ajuster', $article)); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label">Quantité (négatif = retrait)</label>
                                        <input type="number" name="quantite" class="form-control" required placeholder="ex: 10 ou -5">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Motif</label>
                                        <input type="text" name="motif" class="form-control" required placeholder="Inventaire, correction...">
                                    </div>
                                    <div class="alert alert-info py-2 small mb-0">
                                        Stock actuel : <strong><?php echo e($article->quantite_stock); ?> <?php echo e($article->unite); ?></strong>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                    <button type="submit" class="btn btn-sm btn-warning">Ajuster</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="8" class="text-center py-5 text-muted">
                    <i class="bi bi-inbox fs-2 d-block mb-2"></i>Aucun article trouvé
                </td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="px-3 py-2"><?php echo e($articles->links()); ?></div>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\PC-CONASUR\Downloads\gest_stocks\resources\views/articles/index.blade.php ENDPATH**/ ?>