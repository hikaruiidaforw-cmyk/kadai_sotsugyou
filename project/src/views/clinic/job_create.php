<?php
/**
 * 求人作成画面
 */
$pageTitle = '求人作成';

require_once __DIR__ . '/../../models/Clinic.php';
require_once __DIR__ . '/../../models/Job.php';
require_once __DIR__ . '/../../models/Specialty.php';

$clinicModel = new Clinic();
$jobModel = new Job();
$specialtyModel = new Specialty();

$clinic = $clinicModel->findByUserId(Auth::id());
$specialties = $specialtyModel->getAll();

$errors = [];
$formData = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    CSRF::requireValid();

    $formData = $_POST;

    // バリデーション
    $validator = new Validator($_POST);
    $validator
        ->required('title', '募集タイトル')
        ->required('facility_name', '施設名')
        ->required('postal_code', '郵便番号')
        ->required('prefecture', '都道府県')
        ->required('address', '住所')
        ->required('description', '業務内容')
        ->required('work_hours', '勤務時間')
        ->required('transfer_min_tenure_months', '最低勤務期間')
        ->required('transfer_price_type', '譲渡価格タイプ')
        ->required('transfer_scope', '譲渡対象範囲');

    if ($validator->fails()) {
        $errors = $validator->getErrors();
    } else {
        $jobId = $jobModel->create([
            'clinic_id' => $clinic['id'],
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
            'status' => $_POST['save_draft'] ? 'draft' : 'pending'
        ]);

        // 診療科目設定
        if (!empty($_POST['specialties'])) {
            $jobModel->setSpecialties($jobId, $_POST['specialties']);
        }

        header('Location: ' . BASE_PATH . '/?page=clinic/jobs');
        exit;
    }
}

require_once __DIR__ . '/../layouts/header.php';
?>

<style>
.job-create-page {
    padding: var(--space-2xl) 0 var(--space-4xl);
    background: var(--color-gray-50);
}

.job-create-container {
    max-width: 900px;
    margin: 0 auto;
}

.job-create-card {
    background: var(--color-white);
    border-radius: var(--radius-lg);
    padding: var(--space-xl);
    border: 1px solid var(--color-gray-200);
    margin-bottom: var(--space-xl);
}

.job-create-card-title {
    font-size: 1.125rem;
    margin-bottom: var(--space-lg);
    padding-bottom: var(--space-md);
    border-bottom: 2px solid var(--color-primary);
    display: flex;
    align-items: center;
    gap: var(--space-sm);
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

.form-actions {
    display: flex;
    gap: var(--space-md);
    justify-content: flex-end;
}

@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
    }

    .specialty-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .form-actions {
        flex-direction: column;
    }
}
</style>

<div class="job-create-page">
    <div class="container job-create-container">
        <h1 style="font-size: 1.75rem; margin-bottom: var(--space-xl);">新規求人作成</h1>

        <form method="POST" action="">
            <?php echo CSRF::tokenField(); ?>

            <!-- 基本情報 -->
            <div class="job-create-card">
                <h2 class="job-create-card-title">📋 基本情報</h2>

                <div class="form-group">
                    <label class="form-label required">募集タイトル</label>
                    <input type="text" name="title" class="form-input"
                           value="<?php echo e($formData['title'] ?? ''); ?>"
                           placeholder="例: 【内科】○○クリニック院長候補募集" required>
                    <?php if (!empty($errors['title'])): ?>
                        <p class="form-error"><?php echo e($errors['title'][0]); ?></p>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label class="form-label required">施設名</label>
                    <input type="text" name="facility_name" class="form-input"
                           value="<?php echo e($formData['facility_name'] ?? ''); ?>"
                           placeholder="例: 健康会 渋谷クリニック" required>
                </div>

                <div class="form-group">
                    <label class="form-label">診療科目</label>
                    <div class="specialty-grid">
                        <?php foreach ($specialties as $spec): ?>
                            <label class="form-checkbox">
                                <input type="checkbox" name="specialties[]" value="<?php echo $spec['id']; ?>"
                                    <?php echo in_array($spec['id'], $formData['specialties'] ?? []) ? 'checked' : ''; ?>>
                                <span><?php echo e($spec['name']); ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- 勤務地 -->
            <div class="job-create-card">
                <h2 class="job-create-card-title">📍 勤務地</h2>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label required">郵便番号</label>
                        <input type="text" name="postal_code" class="form-input"
                               value="<?php echo e($formData['postal_code'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label required">都道府県</label>
                        <select name="prefecture" class="form-select" required>
                            <option value="">選択してください</option>
                            <?php foreach (PREFECTURES as $pref): ?>
                                <option value="<?php echo e($pref); ?>" <?php echo ($formData['prefecture'] ?? '') === $pref ? 'selected' : ''; ?>>
                                    <?php echo e($pref); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label required">住所</label>
                    <input type="text" name="address" class="form-input"
                           value="<?php echo e($formData['address'] ?? ''); ?>" required>
                </div>
            </div>

            <!-- 業務・勤務条件 -->
            <div class="job-create-card">
                <h2 class="job-create-card-title">💼 業務・勤務条件</h2>

                <div class="form-group">
                    <label class="form-label required">業務内容</label>
                    <textarea name="description" class="form-textarea" rows="6" required><?php echo e($formData['description'] ?? ''); ?></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label required">勤務時間</label>
                    <textarea name="work_hours" class="form-textarea" rows="4" required><?php echo e($formData['work_hours'] ?? ''); ?></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">年収下限（万円）</label>
                        <input type="number" name="salary_min" class="form-input"
                               value="<?php echo e($formData['salary_min'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">年収上限（万円）</label>
                        <input type="number" name="salary_max" class="form-input"
                               value="<?php echo e($formData['salary_max'] ?? ''); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">給与詳細</label>
                    <textarea name="salary_description" class="form-textarea" rows="3"><?php echo e($formData['salary_description'] ?? ''); ?></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">福利厚生</label>
                    <textarea name="benefits" class="form-textarea" rows="3"><?php echo e($formData['benefits'] ?? ''); ?></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">応募条件</label>
                    <textarea name="requirements" class="form-textarea" rows="3"><?php echo e($formData['requirements'] ?? ''); ?></textarea>
                </div>
            </div>

            <!-- 譲渡特約条件 -->
            <div class="job-create-card" style="background: linear-gradient(135deg, var(--color-accent-light) 0%, rgba(201, 162, 39, 0.1) 100%); border-color: var(--color-accent);">
                <h2 class="job-create-card-title">🔑 譲渡特約条件</h2>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label required">最低勤務期間（月）</label>
                        <select name="transfer_min_tenure_months" class="form-select" required>
                            <option value="">選択してください</option>
                            <?php foreach ([12, 24, 36, 48, 60] as $months): ?>
                                <option value="<?php echo $months; ?>" <?php echo ($formData['transfer_min_tenure_months'] ?? '') == $months ? 'selected' : ''; ?>>
                                    <?php echo $months; ?>ヶ月（<?php echo $months / 12; ?>年）
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label required">譲渡価格タイプ</label>
                        <select name="transfer_price_type" class="form-select" required id="price-type">
                            <option value="">選択してください</option>
                            <option value="fixed" <?php echo ($formData['transfer_price_type'] ?? '') === 'fixed' ? 'selected' : ''; ?>>固定価格</option>
                            <option value="formula" <?php echo ($formData['transfer_price_type'] ?? '') === 'formula' ? 'selected' : ''; ?>>算定方式</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group" id="fixed-price-group">
                        <label class="form-label">固定価格（万円）</label>
                        <input type="number" name="transfer_price_fixed" class="form-input"
                               value="<?php echo e($formData['transfer_price_fixed'] ?? ''); ?>">
                    </div>
                    <div class="form-group" id="formula-group">
                        <label class="form-label">算定方式</label>
                        <input type="text" name="transfer_price_formula" class="form-input"
                               value="<?php echo e($formData['transfer_price_formula'] ?? ''); ?>"
                               placeholder="例: 年間売上の1.5倍">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">業績目標</label>
                    <input type="text" name="transfer_performance_target" class="form-input"
                           value="<?php echo e($formData['transfer_performance_target'] ?? ''); ?>"
                           placeholder="例: 年間売上1億円以上の維持">
                </div>

                <div class="form-group">
                    <label class="form-label required">譲渡対象範囲</label>
                    <textarea name="transfer_scope" class="form-textarea" rows="4" required><?php echo e($formData['transfer_scope'] ?? ''); ?></textarea>
                    <p class="form-hint">施設、設備、患者基盤、スタッフ等の譲渡対象を記載してください</p>
                </div>

                <div class="form-group">
                    <label class="form-label">その他条件</label>
                    <textarea name="transfer_other_conditions" class="form-textarea" rows="3"><?php echo e($formData['transfer_other_conditions'] ?? ''); ?></textarea>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" name="save_draft" value="1" class="btn btn-secondary btn-lg">
                    下書き保存
                </button>
                <button type="submit" class="btn btn-primary btn-lg">
                    承認申請する
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
