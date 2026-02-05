<?php
/**
 * 管理者: 求人管理
 */
$pageTitle = '求人管理';

require_once __DIR__ . '/../../models/Job.php';

$jobModel = new Job();

$page = max(1, intval($_GET['p'] ?? 1));
$jobs = $jobModel->search([], $page);
$totalCount = $jobModel->searchCount([]);
$totalPages = ceil($totalCount / ITEMS_PER_PAGE);

// ステータス更新
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    CSRF::requireValid();

    $jobId = intval($_POST['job_id']);
    $newStatus = $_POST['new_status'];

    $jobModel->update($jobId, [
        'status' => $newStatus,
        'published_at' => $newStatus === 'published' ? date('Y-m-d H:i:s') : null
    ]);

    header('Location: ' . BASE_PATH . '/?page=admin/jobs');
    exit;
}

require_once __DIR__ . '/../layouts/header.php';
?>

<style>
.admin-page {
    padding: var(--space-2xl) 0 var(--space-4xl);
    background: var(--color-gray-50);
    min-height: calc(100vh - 72px);
}

.job-row {
    display: grid;
    grid-template-columns: 1fr 120px 120px 100px 150px;
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
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--space-xl);">
            <h1 style="font-size: 1.75rem;">求人管理</h1>
            <p class="text-gray">総数: <?php echo $totalCount; ?>件</p>
        </div>

        <?php if (empty($jobs)): ?>
            <div class="card">
                <div class="card-body text-center" style="padding: var(--space-2xl);">
                    <p class="text-gray">求人がありません</p>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($jobs as $job): ?>
                <div class="job-row">
                    <div>
                        <div style="font-weight: 600;"><?php echo e($job['title']); ?></div>
                        <div style="font-size: 0.875rem; color: var(--color-gray-500);"><?php echo e($job['corp_name']); ?></div>
                    </div>
                    <div><?php echo e($job['prefecture']); ?></div>
                    <div>
                        <span class="badge badge-<?php echo $job['status'] === 'published' ? 'success' : 'gray'; ?>">
                            <?php echo e(JOB_STATUS[$job['status']] ?? $job['status']); ?>
                        </span>
                    </div>
                    <div style="font-size: 0.875rem; color: var(--color-gray-500);">
                        <?php echo date('Y/m/d', strtotime($job['created_at'])); ?>
                    </div>
                    <div>
                        <form method="POST" style="display: flex; gap: var(--space-xs);">
                            <?php echo CSRF::tokenField(); ?>
                            <input type="hidden" name="action" value="update_status">
                            <input type="hidden" name="job_id" value="<?php echo $job['id']; ?>">
                            <?php if ($job['status'] !== 'published'): ?>
                                <button type="submit" name="new_status" value="published" class="btn btn-primary btn-sm">公開</button>
                            <?php endif; ?>
                            <?php if ($job['status'] !== 'closed'): ?>
                                <button type="submit" name="new_status" value="closed" class="btn btn-ghost btn-sm">終了</button>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>

            <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="?page=admin/jobs&p=<?php echo $i; ?>"
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
