<?php
/**
 * 管理者: マッチング一覧
 */
$pageTitle = 'マッチング管理';

require_once __DIR__ . '/../../models/Application.php';

$applicationModel = new Application();

$page = max(1, intval($_GET['p'] ?? 1));
$applications = $applicationModel->getAll($page);

require_once __DIR__ . '/../layouts/header.php';
?>

<style>
.admin-page {
    padding: var(--space-2xl) 0 var(--space-4xl);
    background: var(--color-gray-50);
    min-height: calc(100vh - 72px);
}

.application-row {
    display: grid;
    grid-template-columns: 1fr 1fr 120px 100px;
    align-items: center;
    gap: var(--space-md);
    padding: var(--space-md) var(--space-lg);
    background: var(--color-white);
    border: 1px solid var(--color-gray-200);
    border-radius: var(--radius-md);
    margin-bottom: var(--space-sm);
}
</style>

<div class="admin-page">
    <div class="container">
        <h1 style="font-size: 1.75rem; margin-bottom: var(--space-xl);">マッチング管理</h1>

        <?php if (empty($applications)): ?>
            <div class="card">
                <div class="card-body text-center" style="padding: var(--space-2xl);">
                    <p class="text-gray">応募がありません</p>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($applications as $app): ?>
                <div class="application-row">
                    <div>
                        <div style="font-weight: 600;"><?php echo e($app['last_name'] . ' ' . $app['first_name']); ?> 先生</div>
                        <div style="font-size: 0.875rem; color: var(--color-gray-500);">医師</div>
                    </div>
                    <div>
                        <div style="font-weight: 600;"><?php echo e($app['job_title']); ?></div>
                        <div style="font-size: 0.875rem; color: var(--color-gray-500);"><?php echo e($app['corp_name']); ?></div>
                    </div>
                    <div>
                        <span class="badge badge-<?php echo $app['status'] === 'accepted' ? 'success' : ($app['status'] === 'rejected' ? 'error' : 'primary'); ?>">
                            <?php echo e(APPLICATION_STATUS[$app['status']] ?? $app['status']); ?>
                        </span>
                    </div>
                    <div style="font-size: 0.875rem; color: var(--color-gray-500);">
                        <?php echo date('Y/m/d', strtotime($app['applied_at'])); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
