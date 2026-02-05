<?php
/**
 * 法人ダッシュボード
 */
$pageTitle = 'マイページ';

require_once __DIR__ . '/../../models/Clinic.php';
require_once __DIR__ . '/../../models/Job.php';
require_once __DIR__ . '/../../models/Application.php';

$clinicModel = new Clinic();
$jobModel = new Job();
$applicationModel = new Application();

$clinic = $clinicModel->findByUserId(Auth::id());
$jobs = $jobModel->getByClinicId($clinic['id'], 1, 5);
$applications = $applicationModel->getByClinicId($clinic['id'], 1, 5);
$applicationCount = $applicationModel->countByClinicId($clinic['id']);
$newApplicationCount = $applicationModel->countByClinicId($clinic['id'], ['status' => 'applied']);

require_once __DIR__ . '/../layouts/header.php';
?>

<style>
.dashboard {
    padding: var(--space-2xl) 0 var(--space-4xl);
    background: var(--color-gray-50);
    min-height: calc(100vh - 72px);
}

.dashboard-header {
    margin-bottom: var(--space-2xl);
}

.dashboard-welcome {
    font-size: 1.5rem;
    margin-bottom: var(--space-xs);
}

.dashboard-welcome-name {
    color: var(--color-primary);
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: var(--space-md);
    margin-bottom: var(--space-2xl);
}

.stat-card {
    background: var(--color-white);
    border-radius: var(--radius-lg);
    padding: var(--space-lg);
    border: 1px solid var(--color-gray-200);
}

.stat-card-icon {
    width: 48px;
    height: 48px;
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    margin-bottom: var(--space-md);
}

.stat-card-value {
    font-size: 2rem;
    font-weight: 700;
    line-height: 1;
    margin-bottom: var(--space-xs);
}

.stat-card-label {
    color: var(--color-gray-500);
    font-size: 0.875rem;
}

.dashboard-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: var(--space-xl);
}

.dashboard-section {
    background: var(--color-white);
    border-radius: var(--radius-lg);
    border: 1px solid var(--color-gray-200);
}

.dashboard-section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: var(--space-lg);
    border-bottom: 1px solid var(--color-gray-100);
}

.dashboard-section-title {
    font-size: 1.125rem;
}

.dashboard-section-body {
    padding: var(--space-lg);
}

.list-item {
    display: flex;
    align-items: center;
    gap: var(--space-md);
    padding: var(--space-md);
    border-radius: var(--radius-md);
    transition: background var(--transition-fast);
}

.list-item:hover {
    background: var(--color-gray-50);
}

.list-item + .list-item {
    border-top: 1px solid var(--color-gray-100);
}

.list-item-icon {
    width: 40px;
    height: 40px;
    background: var(--color-gray-100);
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
}

.list-item-content {
    flex: 1;
    min-width: 0;
}

.list-item-title {
    font-weight: 500;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.list-item-meta {
    font-size: 0.875rem;
    color: var(--color-gray-500);
}

@media (max-width: 1024px) {
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .dashboard-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="dashboard">
    <div class="container">
        <div class="dashboard-header">
            <h1 class="dashboard-welcome">
                <span class="dashboard-welcome-name"><?php echo e($clinic['corp_name']); ?></span> 様
            </h1>
            <p class="text-gray">法人管理ダッシュボード</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-card-icon" style="background: var(--color-primary-100);">📋</div>
                <div class="stat-card-value"><?php echo count($jobs); ?></div>
                <div class="stat-card-label">掲載中の求人</div>
            </div>
            <div class="stat-card">
                <div class="stat-card-icon" style="background: var(--color-success-light);">👥</div>
                <div class="stat-card-value"><?php echo $applicationCount; ?></div>
                <div class="stat-card-label">応募者数</div>
            </div>
            <div class="stat-card">
                <div class="stat-card-icon" style="background: var(--color-warning-light);">🆕</div>
                <div class="stat-card-value"><?php echo $newApplicationCount; ?></div>
                <div class="stat-card-label">新規応募</div>
            </div>
            <div class="stat-card">
                <div class="stat-card-icon" style="background: var(--color-info-light);">🏥</div>
                <div class="stat-card-value"><?php echo $clinic['facility_count']; ?></div>
                <div class="stat-card-label">運営施設数</div>
            </div>
        </div>

        <div class="dashboard-grid">
            <!-- 求人一覧 -->
            <div class="dashboard-section">
                <div class="dashboard-section-header">
                    <h2 class="dashboard-section-title">📋 求人一覧</h2>
                    <a href="<?php echo BASE_PATH; ?>/?page=clinic/jobs" class="btn btn-ghost btn-sm">すべて見る</a>
                </div>
                <div class="dashboard-section-body">
                    <?php if (empty($jobs)): ?>
                        <div class="text-center text-gray" style="padding: var(--space-xl);">
                            <p class="mb-2">まだ求人がありません</p>
                            <a href="<?php echo BASE_PATH; ?>/?page=clinic/job/create" class="btn btn-primary">求人を作成</a>
                        </div>
                    <?php else: ?>
                        <?php foreach ($jobs as $job): ?>
                            <a href="<?php echo BASE_PATH; ?>/?page=clinic/job/edit&id=<?php echo $job['id']; ?>" class="list-item">
                                <div class="list-item-icon">📄</div>
                                <div class="list-item-content">
                                    <div class="list-item-title"><?php echo e($job['title']); ?></div>
                                    <div class="list-item-meta">応募者: <?php echo $job['application_count']; ?>名</div>
                                </div>
                                <span class="badge badge-<?php echo $job['status'] === 'published' ? 'success' : 'gray'; ?>">
                                    <?php echo e(JOB_STATUS[$job['status']] ?? $job['status']); ?>
                                </span>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- 最新の応募 -->
            <div class="dashboard-section">
                <div class="dashboard-section-header">
                    <h2 class="dashboard-section-title">👥 最新の応募</h2>
                    <a href="<?php echo BASE_PATH; ?>/?page=clinic/applications" class="btn btn-ghost btn-sm">すべて見る</a>
                </div>
                <div class="dashboard-section-body">
                    <?php if (empty($applications)): ?>
                        <div class="text-center text-gray" style="padding: var(--space-xl);">
                            まだ応募がありません
                        </div>
                    <?php else: ?>
                        <?php foreach ($applications as $app): ?>
                            <a href="<?php echo BASE_PATH; ?>/?page=clinic/application&id=<?php echo $app['id']; ?>" class="list-item">
                                <div class="list-item-icon">👤</div>
                                <div class="list-item-content">
                                    <div class="list-item-title"><?php echo e($app['last_name'] . ' ' . $app['first_name']); ?> 先生</div>
                                    <div class="list-item-meta"><?php echo e($app['job_title']); ?></div>
                                </div>
                                <span class="badge badge-primary">
                                    <?php echo e(APPLICATION_STATUS[$app['status']] ?? $app['status']); ?>
                                </span>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <a href="<?php echo BASE_PATH; ?>/?page=clinic/job/create" class="btn btn-primary btn-lg">
                ＋ 新規求人を作成
            </a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
