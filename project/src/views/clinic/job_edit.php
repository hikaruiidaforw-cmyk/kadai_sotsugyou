<?php
/**
 * 求人編集画面
 */

require_once __DIR__ . '/../../models/Clinic.php';
require_once __DIR__ . '/../../models/Job.php';
require_once __DIR__ . '/../../models/Specialty.php';

$clinicModel = new Clinic();
$jobModel = new Job();
$specialtyModel = new Specialty();

$clinic = $clinicModel->findByUserId(Auth::id());
$jobId = intval($_GET['id'] ?? 0);
$job = $jobModel->findById($jobId);

if (!$job || $job['clinic_id'] != $clinic['id']) {
    header('Location: ' . BASE_PATH . '/?page=clinic/jobs');
    exit;
}

$pageTitle = '求人編集';
$specialties = $specialtyModel->getAll();
$jobSpecialties = $jobModel->getSpecialties($jobId);
$jobSpecialtyIds = array_column($jobSpecialties, 'id');

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    CSRF::requireValid();

    $jobModel->update($jobId, [
        'title' => $_POST['title'],
        'facility_name' => $_POST['facility_name'],
        'postal_code' => $_POST['postal_code'],
        'prefecture' => $_POST['prefecture'],
        'address' => $_POST['address'],
        'description' => $_POST['description'],
        'work_hours' => $_POST['work_hours'],
        'salary_min' => $_POST['salary_min'] ?: null,
        'salary_max' => $_POST['salary_max'] ?: null,
        'salary_description' => $_POST['salary_description'],
        'benefits' => $_POST['benefits'],
        'requirements' => $_POST['requirements'],
        'transfer_min_tenure_months' => $_POST['transfer_min_tenure_months'],
        'transfer_performance_target' => $_POST['transfer_performance_target'],
        'transfer_price_type' => $_POST['transfer_price_type'],
        'transfer_price_fixed' => $_POST['transfer_price_fixed'] ?: null,
        'transfer_price_formula' => $_POST['transfer_price_formula'],
        'transfer_scope' => $_POST['transfer_scope'],
        'transfer_other_conditions' => $_POST['transfer_other_conditions'],
        'status' => $_POST['status']
    ]);

    if (!empty($_POST['specialties'])) {
        $jobModel->setSpecialties($jobId, $_POST['specialties']);
    }

    $message = '求人を更新しました';
    $job = $jobModel->findById($jobId);
    $jobSpecialties = $jobModel->getSpecialties($jobId);
    $jobSpecialtyIds = array_column($jobSpecialties, 'id');
}

require_once __DIR__ . '/../layouts/header.php';
?>

<style>
.job-edit-page {
    padding: var(--space-2xl) 0 var(--space-4xl);
    background: var(--color-gray-50);
}

.job-edit-container {
    max-width: 900px;
    margin: 0 auto;
}

.job-edit-card {
    background: var(--color-white);
    border-radius: var(--radius-lg);
    padding: var(--space-xl);
    border: 1px solid var(--color-gray-200);
    margin-bottom: var(--space-xl);
}

.job-edit-card-title {
    font-size: 1.125rem;
    margin-bottom: var(--space-lg);
    padding-bottom: var(--space-md);
    border-bottom: 2px solid var(--color-primary);
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: var(--space-md);
}

.specialty-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: var(--space-sm);
}

@media (max-width: 768px) {
    .form-row, .specialty-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="job-edit-page">
    <div class="container job-edit-container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--space-xl);">
            <h1 style="font-size: 1.75rem;">求人編集</h1>
            <span class="badge badge-<?php echo $job['status'] === 'published' ? 'success' : 'gray'; ?>">
                <?php echo e(JOB_STATUS[$job['status']] ?? $job['status']); ?>
            </span>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo e($message); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <?php echo CSRF::tokenField(); ?>

            <div class="job-edit-card">
                <h2 class="job-edit-card-title">基本情報</h2>

                <div class="form-group">
                    <label class="form-label">ステータス</label>
                    <select name="status" class="form-select">
                        <?php foreach (JOB_STATUS as $key => $label): ?>
                            <option value="<?php echo $key; ?>" <?php echo $job['status'] === $key ? 'selected' : ''; ?>>
                                <?php echo e($label); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label required">募集タイトル</label>
                    <input type="text" name="title" class="form-input" value="<?php echo e($job['title']); ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label required">施設名</label>
                    <input type="text" name="facility_name" class="form-input" value="<?php echo e($job['facility_name']); ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">診療科目</label>
                    <div class="specialty-grid">
                        <?php foreach ($specialties as $spec): ?>
                            <label class="form-checkbox">
                                <input type="checkbox" name="specialties[]" value="<?php echo $spec['id']; ?>"
                                    <?php echo in_array($spec['id'], $jobSpecialtyIds) ? 'checked' : ''; ?>>
                                <span><?php echo e($spec['name']); ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="job-edit-card">
                <h2 class="job-edit-card-title">勤務地</h2>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label required">郵便番号</label>
                        <input type="text" name="postal_code" class="form-input" value="<?php echo e($job['postal_code']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label required">都道府県</label>
                        <select name="prefecture" class="form-select" required>
                            <?php foreach (PREFECTURES as $pref): ?>
                                <option value="<?php echo e($pref); ?>" <?php echo $job['prefecture'] === $pref ? 'selected' : ''; ?>>
                                    <?php echo e($pref); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label required">住所</label>
                    <input type="text" name="address" class="form-input" value="<?php echo e($job['address']); ?>" required>
                </div>
            </div>

            <div class="job-edit-card">
                <h2 class="job-edit-card-title">業務・勤務条件</h2>

                <div class="form-group">
                    <label class="form-label required">業務内容</label>
                    <textarea name="description" class="form-textarea" rows="6" required><?php echo e($job['description']); ?></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label required">勤務時間</label>
                    <textarea name="work_hours" class="form-textarea" rows="4" required><?php echo e($job['work_hours']); ?></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">年収下限（万円）</label>
                        <input type="number" name="salary_min" class="form-input" value="<?php echo e($job['salary_min']); ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">年収上限（万円）</label>
                        <input type="number" name="salary_max" class="form-input" value="<?php echo e($job['salary_max']); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">給与詳細</label>
                    <textarea name="salary_description" class="form-textarea" rows="3"><?php echo e($job['salary_description']); ?></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">福利厚生</label>
                    <textarea name="benefits" class="form-textarea" rows="3"><?php echo e($job['benefits']); ?></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">応募条件</label>
                    <textarea name="requirements" class="form-textarea" rows="3"><?php echo e($job['requirements']); ?></textarea>
                </div>
            </div>

            <div class="job-edit-card" style="background: linear-gradient(135deg, var(--color-accent-light) 0%, rgba(201, 162, 39, 0.1) 100%); border-color: var(--color-accent);">
                <h2 class="job-edit-card-title">譲渡特約条件</h2>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label required">最低勤務期間（月）</label>
                        <select name="transfer_min_tenure_months" class="form-select" required>
                            <?php foreach ([12, 24, 36, 48, 60] as $months): ?>
                                <option value="<?php echo $months; ?>" <?php echo $job['transfer_min_tenure_months'] == $months ? 'selected' : ''; ?>>
                                    <?php echo $months; ?>ヶ月
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label required">譲渡価格タイプ</label>
                        <select name="transfer_price_type" class="form-select" required>
                            <option value="fixed" <?php echo $job['transfer_price_type'] === 'fixed' ? 'selected' : ''; ?>>固定価格</option>
                            <option value="formula" <?php echo $job['transfer_price_type'] === 'formula' ? 'selected' : ''; ?>>算定方式</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">固定価格（万円）</label>
                        <input type="number" name="transfer_price_fixed" class="form-input" value="<?php echo e($job['transfer_price_fixed']); ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">算定方式</label>
                        <input type="text" name="transfer_price_formula" class="form-input" value="<?php echo e($job['transfer_price_formula']); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">業績目標</label>
                    <input type="text" name="transfer_performance_target" class="form-input" value="<?php echo e($job['transfer_performance_target']); ?>">
                </div>

                <div class="form-group">
                    <label class="form-label required">譲渡対象範囲</label>
                    <textarea name="transfer_scope" class="form-textarea" rows="4" required><?php echo e($job['transfer_scope']); ?></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">その他条件</label>
                    <textarea name="transfer_other_conditions" class="form-textarea" rows="3"><?php echo e($job['transfer_other_conditions']); ?></textarea>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-lg" style="width: 100%;">更新する</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
