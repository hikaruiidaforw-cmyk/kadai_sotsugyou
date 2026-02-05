<?php
/**
 * 法人求人一覧画面
 */
$pageTitle = '求人管理';

require_once __DIR__ . '/../../models/Clinic.php';
require_once __DIR__ . '/../../models/Job.php';

$clinicModel = new Clinic();
$jobModel = new Job();

$clinic = $clinicModel->findByUserId(Auth::id());
$page = max(1, intval($_GET['p'] ?? 1));
$jobs = $jobModel->getByClinicId($clinic['id'], $page);

require_once __DIR__ . '/../layouts/header.php';
?>

<style>
.jobs-page {
    padding: var(--space-2xl) 0 var(--space-4xl);
    background: var(--color-gray-50);
    min-height: calc(100vh - 72px);
}

.jobs-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: var(--space-xl);
}

.jobs-title {
    font-size: 1.75rem;
}

.jobs-grid {
    display: grid;
    gap: var(--space-md);
}

.job-row {
    display: grid;
    grid-template-columns: 1fr 150px 120px 100px 120px;
    align-items: center;
    gap: var(--space-md);
    padding: var(--space-lg);
    background: var(--color-white);
    border: 1px solid var(--color-gray-200);
    border-radius: var(--radius-lg);
}

.job-row-title {
    font-weight: 600;
}

.job-row-meta {
    color: var(--color-gray-500);
    font-size: 0.875rem;
}
</style>

<div class="jobs-page">
    <div class="container">
        <div class="jobs-header">
            <h1 class="jobs-title">求人管理</h1>
            <a href="<?php echo BASE_PATH; ?>/?page=clinic/job/create" class="btn btn-primary">
                ＋ 新規求人を作成
            </a>
        </div>

        <?php if (empty($jobs)): ?>
            <div class="card">
                <div class="card-body text-center" style="padding: var(--space-3xl);">
                    <p class="text-gray mb-2">まだ求人がありません</p>
                    <a href="<?php echo BASE_PATH; ?>/?page=clinic/job/create" class="btn btn-primary">最初の求人を作成</a>
                </div>
            </div>
        <?php else: ?>
            <div class="jobs-grid">
                <?php foreach ($jobs as $job): ?>
                    <div class="job-row">
                        <div>
                            <div class="job-row-title"><?php echo e($job['title']); ?></div>
                            <div class="job-row-meta"><?php echo e($job['facility_name']); ?></div>
                        </div>
                        <div class="text-center">
                            <div class="job-row-title"><?php echo $job['application_count']; ?>名</div>
                            <div class="job-row-meta">応募者数</div>
                        </div>
                        <div class="text-center">
                            <span class="badge badge-<?php echo $job['status'] === 'published' ? 'success' : 'gray'; ?>">
                                <?php echo e(JOB_STATUS[$job['status']] ?? $job['status']); ?>
                            </span>
                        </div>
                        <div class="text-center">
                            <div class="job-row-meta"><?php echo date('Y/m/d', strtotime($job['created_at'])); ?></div>
                        </div>
                        <div class="text-center">
                            <a href="<?php echo BASE_PATH; ?>/?page=clinic/job/edit&id=<?php echo $job['id']; ?>" class="btn btn-secondary btn-sm">編集</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
