<?php
/**
 * 求人詳細画面
 */

require_once __DIR__ . '/../../models/Job.php';
require_once __DIR__ . '/../../models/Doctor.php';
require_once __DIR__ . '/../../models/Application.php';
require_once __DIR__ . '/../../models/Favorite.php';

$jobModel = new Job();
$doctorModel = new Doctor();
$applicationModel = new Application();
$favoriteModel = new Favorite();

$jobId = intval($_GET['id'] ?? 0);
$job = $jobModel->findById($jobId);

if (!$job || $job['status'] !== 'published') {
    header('Location: ' . BASE_PATH . '/?page=doctor/jobs');
    exit;
}

$pageTitle = $job['title'];
$doctor = $doctorModel->findByUserId(Auth::id());
$specialties = $jobModel->getSpecialties($jobId);
$hasApplied = $applicationModel->hasApplied($jobId, $doctor['id']);
$isFavorite = $favoriteModel->isFavorite($doctor['id'], $jobId);

// 応募処理
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    CSRF::requireValid();

    if ($_POST['action'] === 'apply' && !$hasApplied) {
        $applicationModel->create([
            'job_id' => $jobId,
            'doctor_id' => $doctor['id'],
            'cover_letter' => $_POST['cover_letter'] ?? ''
        ]);
        $hasApplied = true;
        $message = '応募が完了しました。法人からの連絡をお待ちください。';
    } elseif ($_POST['action'] === 'favorite') {
        if ($isFavorite) {
            $favoriteModel->remove($doctor['id'], $jobId);
            $isFavorite = false;
        } else {
            $favoriteModel->add($doctor['id'], $jobId);
            $isFavorite = true;
        }
    }
}

require_once __DIR__ . '/../layouts/header.php';
?>

<style>
.job-detail-page {
    padding: var(--space-2xl) 0 var(--space-4xl);
    background: var(--color-gray-50);
}

.job-detail-grid {
    display: grid;
    grid-template-columns: 1fr 360px;
    gap: var(--space-xl);
}

.job-detail-main {
    display: flex;
    flex-direction: column;
    gap: var(--space-xl);
}

.job-detail-sidebar {
    display: flex;
    flex-direction: column;
    gap: var(--space-xl);
}

/* Header Card */
.job-header-card {
    background: var(--color-white);
    border-radius: var(--radius-xl);
    padding: var(--space-xl);
    border: 1px solid var(--color-gray-200);
}

.job-header-top {
    display: flex;
    gap: var(--space-lg);
    margin-bottom: var(--space-lg);
}

.job-header-logo {
    width: 80px;
    height: 80px;
    background: var(--color-primary-100);
    border-radius: var(--radius-lg);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: var(--color-primary);
    flex-shrink: 0;
}

.job-header-content {
    flex: 1;
}

.job-header-title {
    font-size: 1.5rem;
    margin-bottom: var(--space-sm);
}

.job-header-company {
    color: var(--color-gray-600);
    margin-bottom: var(--space-sm);
}

.job-header-tags {
    display: flex;
    gap: var(--space-xs);
    flex-wrap: wrap;
}

.job-header-meta {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: var(--space-md);
    padding-top: var(--space-lg);
    border-top: 1px solid var(--color-gray-100);
}

.job-meta-item {
    text-align: center;
    padding: var(--space-md);
    background: var(--color-gray-50);
    border-radius: var(--radius-md);
}

.job-meta-label {
    font-size: 0.75rem;
    color: var(--color-gray-500);
    margin-bottom: var(--space-xs);
}

.job-meta-value {
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--color-gray-900);
}

/* Section */
.job-section {
    background: var(--color-white);
    border-radius: var(--radius-lg);
    padding: var(--space-xl);
    border: 1px solid var(--color-gray-200);
}

.job-section-title {
    font-size: 1.125rem;
    margin-bottom: var(--space-lg);
    padding-bottom: var(--space-md);
    border-bottom: 2px solid var(--color-primary);
    display: flex;
    align-items: center;
    gap: var(--space-sm);
}

.job-section-content {
    white-space: pre-line;
    line-height: 1.8;
}

/* Transfer Info */
.transfer-section {
    background: linear-gradient(135deg, var(--color-accent-light) 0%, rgba(201, 162, 39, 0.1) 100%);
    border: 2px solid var(--color-accent);
}

.transfer-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: var(--space-md);
}

.transfer-item {
    padding: var(--space-md);
    background: var(--color-white);
    border-radius: var(--radius-md);
}

.transfer-item-label {
    font-size: 0.875rem;
    color: var(--color-gray-500);
    margin-bottom: var(--space-xs);
}

.transfer-item-value {
    font-weight: 600;
}

/* Sidebar */
.apply-card {
    background: var(--color-white);
    border-radius: var(--radius-lg);
    padding: var(--space-xl);
    border: 1px solid var(--color-gray-200);
    position: sticky;
    top: 90px;
}

.apply-card-title {
    font-size: 1.25rem;
    margin-bottom: var(--space-lg);
    text-align: center;
}

.apply-card-actions {
    display: flex;
    flex-direction: column;
    gap: var(--space-md);
}

.company-card {
    background: var(--color-white);
    border-radius: var(--radius-lg);
    padding: var(--space-xl);
    border: 1px solid var(--color-gray-200);
}

.company-card-header {
    display: flex;
    align-items: center;
    gap: var(--space-md);
    margin-bottom: var(--space-lg);
}

.company-card-logo {
    width: 56px;
    height: 56px;
    background: var(--color-gray-100);
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: var(--color-primary);
}

.company-card-name {
    font-weight: 600;
}

.company-card-info {
    font-size: 0.875rem;
    color: var(--color-gray-500);
    line-height: 1.6;
}

@media (max-width: 1024px) {
    .job-detail-grid {
        grid-template-columns: 1fr;
    }

    .apply-card {
        position: static;
    }

    .job-header-meta {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="job-detail-page">
    <div class="container">
        <?php if ($message): ?>
            <div class="alert alert-success mb-3"><?php echo e($message); ?></div>
        <?php endif; ?>

        <div class="job-detail-grid">
            <div class="job-detail-main">
                <!-- ヘッダー -->
                <div class="job-header-card">
                    <div class="job-header-top">
                        <div class="job-header-logo">
                            <?php echo mb_substr($job['facility_name'], 0, 1); ?>
                        </div>
                        <div class="job-header-content">
                            <h1 class="job-header-title"><?php echo e($job['title']); ?></h1>
                            <p class="job-header-company"><?php echo e($job['corp_name']); ?></p>
                            <div class="job-header-tags">
                                <?php foreach ($specialties as $spec): ?>
                                    <span class="badge badge-primary"><?php echo e($spec['name']); ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <div class="job-header-meta">
                        <div class="job-meta-item">
                            <div class="job-meta-label">勤務地</div>
                            <div class="job-meta-value"><?php echo e($job['prefecture']); ?></div>
                        </div>
                        <div class="job-meta-item">
                            <div class="job-meta-label">年収</div>
                            <div class="job-meta-value"><?php echo number_format($job['salary_min']); ?>〜<?php echo number_format($job['salary_max']); ?>万円</div>
                        </div>
                        <div class="job-meta-item">
                            <div class="job-meta-label">譲渡価格</div>
                            <div class="job-meta-value">
                                <?php echo $job['transfer_price_type'] === 'fixed' ? number_format($job['transfer_price_fixed']) . '万円' : '算定方式'; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 譲渡特約条件 -->
                <div class="job-section transfer-section">
                    <h2 class="job-section-title">🔑 譲渡特約条件</h2>
                    <div class="transfer-grid">
                        <div class="transfer-item">
                            <div class="transfer-item-label">最低勤務期間</div>
                            <div class="transfer-item-value"><?php echo floor($job['transfer_min_tenure_months'] / 12); ?>年（<?php echo $job['transfer_min_tenure_months']; ?>ヶ月）</div>
                        </div>
                        <div class="transfer-item">
                            <div class="transfer-item-label">譲渡価格</div>
                            <div class="transfer-item-value">
                                <?php if ($job['transfer_price_type'] === 'fixed'): ?>
                                    <?php echo number_format($job['transfer_price_fixed']); ?>万円
                                <?php else: ?>
                                    算定方式<br><small class="text-gray"><?php echo e($job['transfer_price_formula']); ?></small>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="transfer-item">
                            <div class="transfer-item-label">譲渡対象</div>
                            <div class="transfer-item-value"><?php echo nl2br(e($job['transfer_scope'])); ?></div>
                        </div>
                        <?php if ($job['transfer_performance_target']): ?>
                        <div class="transfer-item">
                            <div class="transfer-item-label">業績目標</div>
                            <div class="transfer-item-value"><?php echo e($job['transfer_performance_target']); ?></div>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php if ($job['transfer_other_conditions']): ?>
                        <div class="mt-3">
                            <div class="transfer-item-label">その他条件</div>
                            <p class="job-section-content"><?php echo e($job['transfer_other_conditions']); ?></p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- 業務内容 -->
                <div class="job-section">
                    <h2 class="job-section-title">📋 業務内容</h2>
                    <div class="job-section-content"><?php echo e($job['description']); ?></div>
                </div>

                <!-- 勤務条件 -->
                <div class="job-section">
                    <h2 class="job-section-title">🕒 勤務条件</h2>
                    <div class="job-section-content"><?php echo e($job['work_hours']); ?></div>
                </div>

                <!-- 給与・待遇 -->
                <div class="job-section">
                    <h2 class="job-section-title">💰 給与・待遇</h2>
                    <div class="job-section-content"><?php echo e($job['salary_description']); ?></div>
                    <?php if ($job['benefits']): ?>
                        <div class="mt-3">
                            <strong>福利厚生</strong>
                            <div class="job-section-content"><?php echo e($job['benefits']); ?></div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- 応募条件 -->
                <?php if ($job['requirements']): ?>
                <div class="job-section">
                    <h2 class="job-section-title">✅ 応募条件</h2>
                    <div class="job-section-content"><?php echo e($job['requirements']); ?></div>
                </div>
                <?php endif; ?>

                <!-- 勤務地 -->
                <div class="job-section">
                    <h2 class="job-section-title">📍 勤務地</h2>
                    <p><strong><?php echo e($job['facility_name']); ?></strong></p>
                    <p>〒<?php echo e($job['postal_code']); ?> <?php echo e($job['prefecture']); ?><?php echo e($job['address']); ?></p>
                </div>
            </div>

            <div class="job-detail-sidebar">
                <!-- 応募カード -->
                <div class="apply-card">
                    <h3 class="apply-card-title">この求人に応募する</h3>
                    <div class="apply-card-actions">
                        <?php if ($hasApplied): ?>
                            <div class="alert alert-info mb-0">
                                この求人に応募済みです
                            </div>
                            <a href="<?php echo BASE_PATH; ?>/?page=doctor/applications" class="btn btn-secondary" style="width: 100%;">
                                応募状況を確認
                            </a>
                        <?php else: ?>
                            <form method="POST" action="">
                                <?php echo CSRF::tokenField(); ?>
                                <input type="hidden" name="action" value="apply">
                                <div class="form-group">
                                    <label class="form-label">志望動機（任意）</label>
                                    <textarea name="cover_letter" class="form-textarea" rows="4"
                                              placeholder="この求人に興味を持った理由など"></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary btn-lg" style="width: 100%;">
                                    応募する
                                </button>
                            </form>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <?php echo CSRF::tokenField(); ?>
                            <input type="hidden" name="action" value="favorite">
                            <button type="submit" class="btn <?php echo $isFavorite ? 'btn-secondary' : 'btn-ghost'; ?>" style="width: 100%;">
                                <?php echo $isFavorite ? '❤️ お気に入り済み' : '♡ お気に入りに追加'; ?>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- 法人情報 -->
                <div class="company-card">
                    <div class="company-card-header">
                        <div class="company-card-logo">
                            <?php echo mb_substr($job['corp_name'], 0, 1); ?>
                        </div>
                        <div>
                            <div class="company-card-name"><?php echo e($job['corp_name']); ?></div>
                        </div>
                    </div>
                    <p class="company-card-info">
                        求人の詳細は応募後にご確認いただけます。
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
