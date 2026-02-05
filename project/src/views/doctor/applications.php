<?php
/**
 * 応募一覧画面
 */
$pageTitle = '応募管理';

require_once __DIR__ . '/../../models/Doctor.php';
require_once __DIR__ . '/../../models/Application.php';

$doctorModel = new Doctor();
$applicationModel = new Application();

$doctor = $doctorModel->findByUserId(Auth::id());
$page = max(1, intval($_GET['p'] ?? 1));
$applications = $applicationModel->getByDoctorId($doctor['id'], $page);
$totalCount = $applicationModel->countByDoctorId($doctor['id']);
$totalPages = ceil($totalCount / ITEMS_PER_PAGE);

require_once __DIR__ . '/../layouts/header.php';
?>

<style>
.applications-page {
    padding: var(--space-2xl) 0 var(--space-4xl);
    background: var(--color-gray-50);
    min-height: calc(100vh - 72px);
}

.applications-header {
    margin-bottom: var(--space-xl);
}

.applications-title {
    font-size: 1.75rem;
    margin-bottom: var(--space-xs);
}

.applications-list {
    display: flex;
    flex-direction: column;
    gap: var(--space-md);
}

.application-card {
    display: grid;
    grid-template-columns: 64px 1fr auto;
    gap: var(--space-lg);
    padding: var(--space-lg);
    background: var(--color-white);
    border: 1px solid var(--color-gray-200);
    border-radius: var(--radius-lg);
    transition: all var(--transition-base);
    text-decoration: none;
    color: inherit;
}

.application-card:hover {
    border-color: var(--color-primary-300);
    box-shadow: var(--shadow-md);
}

.application-logo {
    width: 64px;
    height: 64px;
    background: var(--color-gray-100);
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: var(--color-primary);
}

.application-content {
    min-width: 0;
}

.application-title {
    font-size: 1rem;
    font-weight: 600;
    margin-bottom: var(--space-xs);
}

.application-company {
    color: var(--color-gray-500);
    font-size: 0.875rem;
    margin-bottom: var(--space-sm);
}

.application-meta {
    display: flex;
    gap: var(--space-md);
    font-size: 0.875rem;
    color: var(--color-gray-500);
}

.application-actions {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: var(--space-sm);
}

.application-date {
    font-size: 0.75rem;
    color: var(--color-gray-400);
}

.unread-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 20px;
    height: 20px;
    background: var(--color-error);
    color: white;
    border-radius: 50%;
    font-size: 0.625rem;
    font-weight: 600;
}

@media (max-width: 768px) {
    .application-card {
        grid-template-columns: 1fr;
    }

    .application-logo {
        display: none;
    }
}
</style>

<div class="applications-page">
    <div class="container">
        <div class="applications-header">
            <h1 class="applications-title">応募管理</h1>
            <p class="text-gray">応募した求人の状況を確認できます</p>
        </div>

        <?php if (empty($applications)): ?>
            <div class="card">
                <div class="card-body text-center" style="padding: var(--space-3xl);">
                    <p class="text-gray mb-2">まだ応募がありません</p>
                    <a href="<?php echo BASE_PATH; ?>/?page=doctor/jobs" class="btn btn-primary">求人を探す</a>
                </div>
            </div>
        <?php else: ?>
            <div class="applications-list">
                <?php foreach ($applications as $app): ?>
                    <a href="<?php echo BASE_PATH; ?>/?page=doctor/application&id=<?php echo $app['id']; ?>" class="application-card">
                        <div class="application-logo">
                            <?php echo mb_substr($app['facility_name'], 0, 1); ?>
                        </div>
                        <div class="application-content">
                            <h3 class="application-title"><?php echo e($app['job_title']); ?></h3>
                            <p class="application-company"><?php echo e($app['corp_name']); ?></p>
                            <div class="application-meta">
                                <span>📍 <?php echo e($app['job_prefecture']); ?></span>
                                <span>💰 <?php echo number_format($app['salary_min']); ?>〜<?php echo number_format($app['salary_max']); ?>万円</span>
                            </div>
                        </div>
                        <div class="application-actions">
                            <span class="badge badge-<?php echo $app['status'] === 'offered' || $app['status'] === 'accepted' ? 'success' : ($app['status'] === 'rejected' || $app['status'] === 'declined' ? 'error' : 'primary'); ?>">
                                <?php echo e(APPLICATION_STATUS[$app['status']] ?? $app['status']); ?>
                            </span>
                            <span class="application-date">
                                応募日: <?php echo date('Y/m/d', strtotime($app['applied_at'])); ?>
                            </span>
                            <?php if ($app['unread_count'] > 0): ?>
                                <span class="unread-badge"><?php echo $app['unread_count']; ?></span>
                            <?php endif; ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>

            <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="?page=doctor/applications&p=<?php echo $i; ?>"
                           class="pagination-item <?php echo $i === $page ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
