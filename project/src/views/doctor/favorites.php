<?php
/**
 * お気に入り一覧画面
 */
$pageTitle = 'お気に入り';

require_once __DIR__ . '/../../models/Doctor.php';
require_once __DIR__ . '/../../models/Favorite.php';

$doctorModel = new Doctor();
$favoriteModel = new Favorite();

$doctor = $doctorModel->findByUserId(Auth::id());
$page = max(1, intval($_GET['p'] ?? 1));
$favorites = $favoriteModel->getByDoctorId($doctor['id'], $page);
$totalCount = $favoriteModel->countByDoctorId($doctor['id']);
$totalPages = ceil($totalCount / ITEMS_PER_PAGE);

require_once __DIR__ . '/../layouts/header.php';
?>

<style>
.favorites-page {
    padding: var(--space-2xl) 0 var(--space-4xl);
    background: var(--color-gray-50);
    min-height: calc(100vh - 72px);
}

.favorites-header {
    margin-bottom: var(--space-xl);
}

.favorites-title {
    font-size: 1.75rem;
    margin-bottom: var(--space-xs);
}
</style>

<div class="favorites-page">
    <div class="container">
        <div class="favorites-header">
            <h1 class="favorites-title">お気に入り</h1>
            <p class="text-gray">お気に入りに登録した求人（<?php echo $totalCount; ?>件）</p>
        </div>

        <?php if (empty($favorites)): ?>
            <div class="card">
                <div class="card-body text-center" style="padding: var(--space-3xl);">
                    <p class="text-gray mb-2">お気に入りはまだありません</p>
                    <a href="<?php echo BASE_PATH; ?>/?page=doctor/jobs" class="btn btn-primary">求人を探す</a>
                </div>
            </div>
        <?php else: ?>
            <div class="jobs-list" style="display: flex; flex-direction: column; gap: var(--space-md);">
                <?php foreach ($favorites as $job): ?>
                    <div class="job-card-full" style="display: grid; grid-template-columns: 80px 1fr auto; gap: var(--space-lg); padding: var(--space-lg); background: var(--color-white); border: 1px solid var(--color-gray-200); border-radius: var(--radius-lg);">
                        <div style="width: 80px; height: 80px; background: var(--color-gray-100); border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center; font-size: 2rem; color: var(--color-primary);">
                            <?php echo mb_substr($job['facility_name'], 0, 1); ?>
                        </div>
                        <div>
                            <h3 style="font-size: 1.125rem; font-weight: 600; margin-bottom: var(--space-sm);">
                                <a href="<?php echo BASE_PATH; ?>/?page=doctor/job&id=<?php echo $job['job_id']; ?>" style="color: inherit;">
                                    <?php echo e($job['title']); ?>
                                </a>
                            </h3>
                            <p style="color: var(--color-gray-500); font-size: 0.9375rem; margin-bottom: var(--space-sm);">
                                <?php echo e($job['corp_name']); ?>
                            </p>
                            <div style="display: flex; gap: var(--space-md); font-size: 0.875rem; color: var(--color-gray-600);">
                                <span>📍 <?php echo e($job['prefecture']); ?></span>
                                <span>💰 年収 <?php echo number_format($job['salary_min']); ?>〜<?php echo number_format($job['salary_max']); ?>万円</span>
                            </div>
                        </div>
                        <div style="display: flex; flex-direction: column; gap: var(--space-sm); justify-content: center;">
                            <a href="<?php echo BASE_PATH; ?>/?page=doctor/job&id=<?php echo $job['job_id']; ?>" class="btn btn-primary">
                                詳細を見る
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="?page=doctor/favorites&p=<?php echo $i; ?>"
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
