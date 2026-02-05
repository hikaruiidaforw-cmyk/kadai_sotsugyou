<?php
/**
 * 法人応募者一覧画面
 */
$pageTitle = '応募者管理';

require_once __DIR__ . '/../../models/Clinic.php';
require_once __DIR__ . '/../../models/Application.php';

$clinicModel = new Clinic();
$applicationModel = new Application();

$clinic = $clinicModel->findByUserId(Auth::id());
$page = max(1, intval($_GET['p'] ?? 1));
$applications = $applicationModel->getByClinicId($clinic['id'], $page);
$totalCount = $applicationModel->countByClinicId($clinic['id']);
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

.applicant-card {
    display: grid;
    grid-template-columns: 60px 1fr auto auto;
    gap: var(--space-lg);
    padding: var(--space-lg);
    background: var(--color-white);
    border: 1px solid var(--color-gray-200);
    border-radius: var(--radius-lg);
    margin-bottom: var(--space-md);
    transition: all var(--transition-base);
}

.applicant-card:hover {
    box-shadow: var(--shadow-md);
}

.applicant-avatar {
    width: 60px;
    height: 60px;
    background: var(--color-primary-100);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: var(--color-primary);
}

.applicant-info {
    min-width: 0;
}

.applicant-name {
    font-size: 1.125rem;
    font-weight: 600;
    margin-bottom: var(--space-xs);
}

.applicant-job {
    color: var(--color-gray-500);
    font-size: 0.875rem;
}

.applicant-date {
    color: var(--color-gray-400);
    font-size: 0.75rem;
    margin-top: var(--space-xs);
}

.applicant-actions {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: var(--space-sm);
}
</style>

<div class="applications-page">
    <div class="container">
        <div class="applications-header">
            <h1 class="applications-title">応募者管理</h1>
            <p class="text-gray">応募者数: <?php echo $totalCount; ?>名</p>
        </div>

        <?php if (empty($applications)): ?>
            <div class="card">
                <div class="card-body text-center" style="padding: var(--space-3xl);">
                    <p class="text-gray">まだ応募がありません</p>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($applications as $app): ?>
                <div class="applicant-card">
                    <div class="applicant-avatar">
                        <?php echo mb_substr($app['last_name'], 0, 1); ?>
                    </div>
                    <div class="applicant-info">
                        <div class="applicant-name"><?php echo e($app['last_name'] . ' ' . $app['first_name']); ?> 先生</div>
                        <div class="applicant-job"><?php echo e($app['job_title']); ?></div>
                        <div class="applicant-date">応募日: <?php echo date('Y/m/d H:i', strtotime($app['applied_at'])); ?></div>
                    </div>
                    <div>
                        <span class="badge badge-<?php echo $app['status'] === 'offered' || $app['status'] === 'accepted' ? 'success' : ($app['status'] === 'rejected' ? 'error' : 'primary'); ?>">
                            <?php echo e(APPLICATION_STATUS[$app['status']] ?? $app['status']); ?>
                        </span>
                    </div>
                    <div class="applicant-actions">
                        <a href="<?php echo BASE_PATH; ?>/?page=clinic/application&id=<?php echo $app['id']; ?>" class="btn btn-primary btn-sm">詳細を見る</a>
                    </div>
                </div>
            <?php endforeach; ?>

            <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="?page=clinic/applications&p=<?php echo $i; ?>"
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
