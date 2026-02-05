<?php
/**
 * 医師ダッシュボード
 */
$pageTitle = 'マイページ';

require_once __DIR__ . '/../../models/Doctor.php';
require_once __DIR__ . '/../../models/Application.php';
require_once __DIR__ . '/../../models/Favorite.php';
require_once __DIR__ . '/../../models/Job.php';

$doctorModel = new Doctor();
$applicationModel = new Application();
$favoriteModel = new Favorite();
$jobModel = new Job();

$doctor = $doctorModel->findByUserId(Auth::id());
$applicationCount = $applicationModel->countByDoctorId($doctor['id']);
$favoriteCount = $favoriteModel->countByDoctorId($doctor['id']);
$recentApplications = $applicationModel->getByDoctorId($doctor['id'], 1, 5);
$latestJobs = $jobModel->getLatest(4);
$specialties = $doctorModel->getSpecialties($doctor['id']);

require_once __DIR__ . '/../layouts/header.php';
?>

<style>
.dashboard {
    padding: var(--space-8) 0 var(--space-16);
    background: var(--color-gray-50);
    min-height: calc(100vh - 64px);
}

.dashboard-header {
    margin-bottom: var(--space-8);
}

.dashboard-welcome {
    font-size: 1.375rem;
    font-weight: 600;
    margin-bottom: var(--space-1);
}

.dashboard-welcome-name {
    color: var(--color-primary);
}

.dashboard-subtitle {
    color: var(--color-gray-500);
    font-size: 0.9375rem;
}

.dashboard-grid {
    display: grid;
    grid-template-columns: 1fr 300px;
    gap: var(--space-6);
}

.dashboard-main {
    display: flex;
    flex-direction: column;
    gap: var(--space-6);
}

.dashboard-sidebar {
    display: flex;
    flex-direction: column;
    gap: var(--space-6);
}

/* Stats Cards */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: var(--space-4);
}

.stat-card {
    background: var(--color-white);
    border-radius: var(--radius-lg);
    padding: var(--space-5);
    border: 1px solid var(--color-gray-200);
    transition: all var(--transition-base);
}

.stat-card:hover {
    border-color: var(--color-gray-300);
}

.stat-card-icon {
    width: 40px;
    height: 40px;
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.125rem;
    margin-bottom: var(--space-4);
    background: var(--color-gray-100);
}

.stat-card-value {
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--color-gray-900);
    line-height: 1;
    margin-bottom: var(--space-1);
}

.stat-card-label {
    color: var(--color-gray-500);
    font-size: 0.8125rem;
}

/* Section */
.dashboard-section {
    background: var(--color-white);
    border-radius: var(--radius-lg);
    border: 1px solid var(--color-gray-200);
    overflow: hidden;
}

.dashboard-section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: var(--space-5);
    border-bottom: 1px solid var(--color-gray-100);
}

.dashboard-section-title {
    font-size: 0.9375rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: var(--space-2);
}

.dashboard-section-body {
    padding: var(--space-5);
}

/* Application List */
.application-list {
    display: flex;
    flex-direction: column;
}

.application-item {
    display: flex;
    align-items: center;
    gap: var(--space-4);
    padding: var(--space-4);
    border-radius: var(--radius-md);
    transition: background var(--transition-fast);
    text-decoration: none;
    color: inherit;
}

.application-item:hover {
    background: var(--color-gray-50);
}

.application-item + .application-item {
    border-top: 1px solid var(--color-gray-100);
}

.application-logo {
    width: 40px;
    height: 40px;
    background: var(--color-gray-100);
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    font-weight: 600;
    color: var(--color-gray-600);
    flex-shrink: 0;
}

.application-content {
    flex: 1;
    min-width: 0;
}

.application-title {
    font-size: 0.875rem;
    font-weight: 500;
    margin-bottom: var(--space-1);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.application-meta {
    font-size: 0.8125rem;
    color: var(--color-gray-500);
}

.application-status {
    flex-shrink: 0;
}

/* Profile Card */
.profile-card-photo {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    background: var(--color-gray-100);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    font-weight: 600;
    color: var(--color-gray-600);
    margin: 0 auto var(--space-4);
    overflow: hidden;
}

.profile-card-photo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.profile-card-name {
    text-align: center;
    font-size: 1rem;
    font-weight: 600;
    margin-bottom: var(--space-1);
}

.profile-card-specialty {
    text-align: center;
    color: var(--color-gray-500);
    font-size: 0.8125rem;
    margin-bottom: var(--space-5);
}

.profile-card-stats {
    display: flex;
    justify-content: center;
    gap: var(--space-8);
    padding: var(--space-4) 0;
    border-top: 1px solid var(--color-gray-100);
    border-bottom: 1px solid var(--color-gray-100);
    margin-bottom: var(--space-5);
}

.profile-stat {
    text-align: center;
}

.profile-stat-value {
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--color-gray-900);
}

.profile-stat-label {
    font-size: 0.6875rem;
    color: var(--color-gray-500);
    text-transform: uppercase;
    letter-spacing: 0.03em;
}

/* Job Card Mini */
.job-list-mini {
    display: flex;
    flex-direction: column;
    gap: var(--space-3);
}

.job-item-mini {
    display: block;
    padding: var(--space-4);
    background: var(--color-gray-50);
    border-radius: var(--radius-md);
    transition: all var(--transition-fast);
    text-decoration: none;
    color: inherit;
}

.job-item-mini:hover {
    background: var(--color-gray-100);
}

.job-item-mini-title {
    font-size: 0.8125rem;
    font-weight: 500;
    margin-bottom: var(--space-1);
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.job-item-mini-meta {
    font-size: 0.6875rem;
    color: var(--color-gray-500);
}

.empty-state {
    text-align: center;
    padding: var(--space-8);
    color: var(--color-gray-500);
    font-size: 0.875rem;
}

/* Quick Links */
.quick-link {
    display: flex;
    align-items: center;
    gap: var(--space-3);
    padding: var(--space-3);
    border-radius: var(--radius-md);
    font-size: 0.875rem;
    color: var(--color-gray-600);
    transition: all var(--transition-fast);
    text-decoration: none;
}

.quick-link:hover {
    background: var(--color-gray-50);
    color: var(--color-gray-900);
}

@media (max-width: 1024px) {
    .dashboard-grid {
        grid-template-columns: 1fr;
    }

    .stats-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="dashboard">
    <div class="container">
        <div class="dashboard-header">
            <h1 class="dashboard-welcome">
                こんにちは、<span class="dashboard-welcome-name"><?php echo e($doctor['last_name'] . ' ' . $doctor['first_name']); ?></span>先生
            </h1>
            <p class="dashboard-subtitle">本日もお疲れ様です。最新の求人情報をチェックしましょう。</p>
        </div>

        <div class="stats-grid mb-4">
            <div class="stat-card">
                <div class="stat-card-icon">1</div>
                <div class="stat-card-value"><?php echo $applicationCount; ?></div>
                <div class="stat-card-label">応募中の求人</div>
            </div>
            <div class="stat-card">
                <div class="stat-card-icon">2</div>
                <div class="stat-card-value"><?php echo $jobModel->searchCount(); ?></div>
                <div class="stat-card-label">公開中の求人</div>
            </div>
            <div class="stat-card">
                <div class="stat-card-icon">3</div>
                <div class="stat-card-value"><?php echo $favoriteCount; ?></div>
                <div class="stat-card-label">お気に入り</div>
            </div>
        </div>

        <div class="dashboard-grid">
            <div class="dashboard-main">
                <!-- 応募状況 -->
                <div class="dashboard-section">
                    <div class="dashboard-section-header">
                        <h2 class="dashboard-section-title">応募状況</h2>
                        <a href="<?php echo BASE_PATH; ?>/?page=doctor/applications" class="btn btn-ghost btn-sm">すべて見る</a>
                    </div>
                    <div class="dashboard-section-body">
                        <?php if (empty($recentApplications)): ?>
                            <div class="empty-state">
                                <p>まだ応募がありません</p>
                                <a href="<?php echo BASE_PATH; ?>/?page=doctor/jobs" class="btn btn-primary mt-2">求人を探す</a>
                            </div>
                        <?php else: ?>
                            <div class="application-list">
                                <?php foreach ($recentApplications as $app): ?>
                                    <a href="<?php echo BASE_PATH; ?>/?page=doctor/application&id=<?php echo $app['id']; ?>" class="application-item">
                                        <div class="application-logo">
                                            <?php echo mb_substr($app['facility_name'], 0, 1); ?>
                                        </div>
                                        <div class="application-content">
                                            <div class="application-title"><?php echo e($app['job_title']); ?></div>
                                            <div class="application-meta"><?php echo e($app['corp_name']); ?></div>
                                        </div>
                                        <div class="application-status">
                                            <span class="badge badge-<?php echo $app['status'] === 'offered' ? 'success' : ($app['status'] === 'rejected' ? 'error' : 'primary'); ?>">
                                                <?php echo e(APPLICATION_STATUS[$app['status']] ?? $app['status']); ?>
                                            </span>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- 新着求人 -->
                <div class="dashboard-section">
                    <div class="dashboard-section-header">
                        <h2 class="dashboard-section-title">新着求人</h2>
                        <a href="<?php echo BASE_PATH; ?>/?page=doctor/jobs" class="btn btn-ghost btn-sm">すべて見る</a>
                    </div>
                    <div class="dashboard-section-body">
                        <?php if (empty($latestJobs)): ?>
                            <div class="empty-state">
                                <p>現在、求人情報はありません</p>
                            </div>
                        <?php else: ?>
                            <div class="job-list-mini">
                                <?php foreach ($latestJobs as $job): ?>
                                    <a href="<?php echo BASE_PATH; ?>/?page=doctor/job&id=<?php echo $job['id']; ?>" class="job-item-mini">
                                        <div class="job-item-mini-title"><?php echo e($job['title']); ?></div>
                                        <div class="job-item-mini-meta">
                                            <?php echo e($job['prefecture']); ?> | 年収 <?php echo number_format($job['salary_min']); ?>〜<?php echo number_format($job['salary_max']); ?>万円
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="dashboard-sidebar">
                <!-- プロフィール -->
                <div class="dashboard-section">
                    <div class="dashboard-section-body">
                        <div class="profile-card-photo">
                            <?php if ($doctor['profile_photo']): ?>
                                <img src="<?php echo BASE_PATH; ?>/uploads/<?php echo e($doctor['profile_photo']); ?>" alt="">
                            <?php else: ?>
                                <?php echo mb_substr($doctor['last_name'], 0, 1); ?>
                            <?php endif; ?>
                        </div>
                        <div class="profile-card-name">
                            <?php echo e($doctor['last_name'] . ' ' . $doctor['first_name']); ?> 先生
                        </div>
                        <div class="profile-card-specialty">
                            <?php if (!empty($specialties)): ?>
                                <?php foreach ($specialties as $i => $spec): ?>
                                    <?php echo $i > 0 ? ' / ' : ''; ?><?php echo e($spec['name']); ?>
                                <?php endforeach; ?>
                            <?php else: ?>
                                専門科目未設定
                            <?php endif; ?>
                        </div>
                        <div class="profile-card-stats">
                            <div class="profile-stat">
                                <div class="profile-stat-value"><?php echo $applicationCount; ?></div>
                                <div class="profile-stat-label">応募</div>
                            </div>
                            <div class="profile-stat">
                                <div class="profile-stat-value"><?php echo $favoriteCount; ?></div>
                                <div class="profile-stat-label">お気に入り</div>
                            </div>
                        </div>
                        <a href="<?php echo BASE_PATH; ?>/?page=doctor/profile" class="btn btn-secondary" style="width: 100%;">
                            プロフィール編集
                        </a>
                    </div>
                </div>

                <!-- クイックリンク -->
                <div class="dashboard-section">
                    <div class="dashboard-section-header">
                        <h3 class="dashboard-section-title">クイックリンク</h3>
                    </div>
                    <div class="dashboard-section-body">
                        <div class="flex flex-col gap-1">
                            <a href="<?php echo BASE_PATH; ?>/?page=doctor/jobs" class="quick-link">
                                求人を検索する
                            </a>
                            <a href="<?php echo BASE_PATH; ?>/?page=doctor/favorites" class="quick-link">
                                お気に入り一覧
                            </a>
                            <a href="<?php echo BASE_PATH; ?>/?page=doctor/applications" class="quick-link">
                                応募履歴を見る
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
