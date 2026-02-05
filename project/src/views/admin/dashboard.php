<?php
/**
 * 管理者ダッシュボード
 */
$pageTitle = '管理ダッシュボード';

require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../models/Doctor.php';
require_once __DIR__ . '/../../models/Clinic.php';
require_once __DIR__ . '/../../models/Job.php';
require_once __DIR__ . '/../../models/Application.php';

$userModel = new User();
$doctorModel = new Doctor();
$clinicModel = new Clinic();
$jobModel = new Job();
$applicationModel = new Application();

$doctorCount = $userModel->count(ROLE_DOCTOR);
$clinicCount = $userModel->count(ROLE_CLINIC);
$pendingDoctors = $userModel->count(ROLE_DOCTOR, STATUS_PENDING);
$pendingClinics = $userModel->count(ROLE_CLINIC, STATUS_PENDING);
$jobCount = $jobModel->searchCount();

require_once __DIR__ . '/../layouts/header.php';
?>

<style>
.admin-dashboard {
    padding: var(--space-2xl) 0 var(--space-4xl);
    background: var(--color-gray-50);
    min-height: calc(100vh - 72px);
}

.admin-header {
    margin-bottom: var(--space-2xl);
}

.admin-title {
    font-size: 1.75rem;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: var(--space-md);
    margin-bottom: var(--space-2xl);
}

.stat-card {
    background: var(--color-white);
    border-radius: var(--radius-lg);
    padding: var(--space-lg);
    border: 1px solid var(--color-gray-200);
    text-align: center;
}

.stat-card-value {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--color-primary);
    line-height: 1;
    margin-bottom: var(--space-xs);
}

.stat-card-label {
    color: var(--color-gray-500);
    font-size: 0.875rem;
}

.admin-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: var(--space-xl);
}

.admin-section {
    background: var(--color-white);
    border-radius: var(--radius-lg);
    border: 1px solid var(--color-gray-200);
}

.admin-section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: var(--space-lg);
    border-bottom: 1px solid var(--color-gray-100);
}

.admin-section-title {
    font-size: 1.125rem;
}

.admin-section-body {
    padding: var(--space-lg);
}

.quick-action {
    display: block;
    padding: var(--space-md);
    background: var(--color-gray-50);
    border-radius: var(--radius-md);
    margin-bottom: var(--space-sm);
    transition: all var(--transition-fast);
    text-decoration: none;
    color: inherit;
}

.quick-action:hover {
    background: var(--color-primary-100);
}

@media (max-width: 1024px) {
    .stats-grid {
        grid-template-columns: repeat(3, 1fr);
    }

    .admin-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="admin-dashboard">
    <div class="container">
        <div class="admin-header">
            <h1 class="admin-title">管理ダッシュボード</h1>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-card-value"><?php echo $doctorCount; ?></div>
                <div class="stat-card-label">医師会員数</div>
            </div>
            <div class="stat-card">
                <div class="stat-card-value"><?php echo $clinicCount; ?></div>
                <div class="stat-card-label">法人会員数</div>
            </div>
            <div class="stat-card">
                <div class="stat-card-value"><?php echo $jobCount; ?></div>
                <div class="stat-card-label">公開中求人</div>
            </div>
            <div class="stat-card">
                <div class="stat-card-value" style="color: var(--color-warning);"><?php echo $pendingDoctors; ?></div>
                <div class="stat-card-label">承認待ち医師</div>
            </div>
            <div class="stat-card">
                <div class="stat-card-value" style="color: var(--color-warning);"><?php echo $pendingClinics; ?></div>
                <div class="stat-card-label">承認待ち法人</div>
            </div>
        </div>

        <div class="admin-grid">
            <div class="admin-section">
                <div class="admin-section-header">
                    <h2 class="admin-section-title">クイックアクション</h2>
                </div>
                <div class="admin-section-body">
                    <a href="<?php echo BASE_PATH; ?>/?page=admin/doctors" class="quick-action">
                        👨‍⚕️ 医師会員を管理
                    </a>
                    <a href="<?php echo BASE_PATH; ?>/?page=admin/clinics" class="quick-action">
                        🏥 法人会員を管理
                    </a>
                    <a href="<?php echo BASE_PATH; ?>/?page=admin/jobs" class="quick-action">
                        📋 求人を管理
                    </a>
                    <a href="<?php echo BASE_PATH; ?>/?page=admin/applications" class="quick-action">
                        📝 マッチング状況を確認
                    </a>
                </div>
            </div>

            <div class="admin-section">
                <div class="admin-section-header">
                    <h2 class="admin-section-title">システム情報</h2>
                </div>
                <div class="admin-section-body">
                    <p><strong>サービス名:</strong> MedCareer Bridge</p>
                    <p><strong>バージョン:</strong> 1.0</p>
                    <p><strong>PHP:</strong> <?php echo phpversion(); ?></p>
                    <p><strong>現在時刻:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
