<?php
/**
 * 法人プロフィール編集画面
 */
$pageTitle = 'プロフィール編集';

require_once __DIR__ . '/../../models/Clinic.php';

$clinicModel = new Clinic();
$clinic = $clinicModel->findByUserId(Auth::id());

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    CSRF::requireValid();

    $data = $_POST;
    unset($data['csrf_token']);

    $clinicModel->update($clinic['id'], $data);
    $message = 'プロフィールを更新しました';
    $clinic = $clinicModel->findByUserId(Auth::id());
}

require_once __DIR__ . '/../layouts/header.php';
?>

<style>
.profile-page {
    padding: var(--space-2xl) 0 var(--space-4xl);
    background: var(--color-gray-50);
}

.profile-container {
    max-width: 800px;
    margin: 0 auto;
}

.profile-card {
    background: var(--color-white);
    border-radius: var(--radius-lg);
    padding: var(--space-xl);
    border: 1px solid var(--color-gray-200);
    margin-bottom: var(--space-xl);
}

.profile-card-title {
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

@media (max-width: 640px) {
    .form-row {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="profile-page">
    <div class="container profile-container">
        <h1 style="font-size: 1.75rem; margin-bottom: var(--space-xl);">プロフィール編集</h1>

        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo e($message); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <?php echo CSRF::tokenField(); ?>

            <div class="profile-card">
                <h2 class="profile-card-title">法人情報</h2>

                <div class="form-group">
                    <label class="form-label required">法人名</label>
                    <input type="text" name="corp_name" class="form-input" value="<?php echo e($clinic['corp_name']); ?>" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">法人番号</label>
                        <input type="text" name="corp_number" class="form-input" value="<?php echo e($clinic['corp_number']); ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">代表者名</label>
                        <input type="text" name="representative_name" class="form-input" value="<?php echo e($clinic['representative_name']); ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">郵便番号</label>
                        <input type="text" name="postal_code" class="form-input" value="<?php echo e($clinic['postal_code']); ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">都道府県</label>
                        <select name="prefecture" class="form-select">
                            <?php foreach (PREFECTURES as $pref): ?>
                                <option value="<?php echo e($pref); ?>" <?php echo $clinic['prefecture'] === $pref ? 'selected' : ''; ?>>
                                    <?php echo e($pref); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">住所</label>
                    <input type="text" name="address" class="form-input" value="<?php echo e($clinic['address']); ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">電話番号</label>
                    <input type="tel" name="phone" class="form-input" value="<?php echo e($clinic['phone']); ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">Webサイト</label>
                    <input type="url" name="website_url" class="form-input" value="<?php echo e($clinic['website_url']); ?>">
                </div>
            </div>

            <div class="profile-card">
                <h2 class="profile-card-title">事業情報</h2>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">運営施設数</label>
                        <input type="number" name="facility_count" class="form-input" value="<?php echo e($clinic['facility_count']); ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">従業員数</label>
                        <input type="number" name="employee_count" class="form-input" value="<?php echo e($clinic['employee_count']); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">事業内容</label>
                    <textarea name="business_description" class="form-textarea" rows="4"><?php echo e($clinic['business_description']); ?></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">法人紹介</label>
                    <textarea name="introduction" class="form-textarea" rows="6"><?php echo e($clinic['introduction']); ?></textarea>
                </div>
            </div>

            <div class="profile-card">
                <h2 class="profile-card-title">担当者情報</h2>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">担当者名</label>
                        <input type="text" name="contact_person_name" class="form-input" value="<?php echo e($clinic['contact_person_name']); ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">役職</label>
                        <input type="text" name="contact_person_position" class="form-input" value="<?php echo e($clinic['contact_person_position']); ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">メールアドレス</label>
                        <input type="email" name="contact_person_email" class="form-input" value="<?php echo e($clinic['contact_person_email']); ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">電話番号</label>
                        <input type="tel" name="contact_person_phone" class="form-input" value="<?php echo e($clinic['contact_person_phone']); ?>">
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-lg" style="width: 100%;">更新する</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
