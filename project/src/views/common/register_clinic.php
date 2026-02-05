<?php
/**
 * 法人会員登録画面
 */
$pageTitle = '法人会員登録';

Auth::requireGuest();

$errors = [];
$formData = [];
$successMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    CSRF::requireValid();

    require_once __DIR__ . '/../../controllers/AuthController.php';
    $authController = new AuthController();

    $result = $authController->registerClinic($_POST);

    if ($result['success']) {
        $successMessage = $result['message'];
    } else {
        $errors = $result['errors'];
        $formData = $_POST;
    }
}

require_once __DIR__ . '/../layouts/header.php';
?>

<style>
.register-clinic-page {
    padding: var(--space-2xl) 0 var(--space-4xl);
    background: linear-gradient(135deg, var(--color-gray-50) 0%, var(--color-white) 100%);
}

.register-clinic-container {
    max-width: 720px;
    margin: 0 auto;
    padding: 0 var(--space-lg);
}

.register-clinic-header {
    text-align: center;
    margin-bottom: var(--space-2xl);
}

.register-clinic-title {
    font-size: 2rem;
    margin-bottom: var(--space-sm);
}

.register-clinic-subtitle {
    color: var(--color-gray-500);
    font-size: 1rem;
}

.register-clinic-card {
    background: var(--color-white);
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-lg);
    padding: var(--space-2xl);
    border: 1px solid var(--color-gray-100);
}

.form-section {
    margin-bottom: var(--space-xl);
    padding-bottom: var(--space-xl);
    border-bottom: 1px solid var(--color-gray-100);
}

.form-section:last-child {
    margin-bottom: 0;
    padding-bottom: 0;
    border-bottom: none;
}

.form-section-title {
    font-size: 1.125rem;
    margin-bottom: var(--space-lg);
    color: var(--color-primary);
    display: flex;
    align-items: center;
    gap: var(--space-sm);
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

.success-message {
    text-align: center;
    padding: var(--space-2xl);
}

.success-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto var(--space-lg);
    background: var(--color-success-light);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
}
</style>

<div class="register-clinic-page">
    <div class="register-clinic-container">
        <div class="register-clinic-header">
            <h1 class="register-clinic-title">法人会員登録</h1>
            <p class="register-clinic-subtitle">必要事項をご入力ください</p>
        </div>

        <div class="register-clinic-card">
            <?php if ($successMessage): ?>
                <div class="success-message">
                    <div class="success-icon">✓</div>
                    <h2 class="mb-2">登録申請が完了しました</h2>
                    <p class="text-gray mb-3"><?php echo e($successMessage); ?></p>
                    <a href="<?php echo BASE_PATH; ?>/?page=login" class="btn btn-primary">ログインページへ</a>
                </div>
            <?php else: ?>
                <?php if (!empty($errors['general'])): ?>
                    <div class="alert alert-error">
                        <?php echo e($errors['general'][0]); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <?php echo CSRF::tokenField(); ?>

                    <!-- アカウント情報 -->
                    <div class="form-section">
                        <h3 class="form-section-title">📧 アカウント情報</h3>

                        <div class="form-group">
                            <label class="form-label required" for="email">メールアドレス</label>
                            <input type="email" id="email" name="email" class="form-input"
                                   value="<?php echo e($formData['email'] ?? ''); ?>"
                                   placeholder="example@company.com" required>
                            <?php if (!empty($errors['email'])): ?>
                                <p class="form-error"><?php echo e($errors['email'][0]); ?></p>
                            <?php endif; ?>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label required" for="password">パスワード</label>
                                <input type="password" id="password" name="password" class="form-input"
                                       placeholder="8文字以上、英数字混合" required>
                                <?php if (!empty($errors['password'])): ?>
                                    <p class="form-error"><?php echo e($errors['password'][0]); ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="form-group">
                                <label class="form-label required" for="password_confirm">パスワード確認</label>
                                <input type="password" id="password_confirm" name="password_confirm" class="form-input"
                                       placeholder="パスワードを再入力" required>
                                <?php if (!empty($errors['password_confirm'])): ?>
                                    <p class="form-error"><?php echo e($errors['password_confirm'][0]); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- 法人情報 -->
                    <div class="form-section">
                        <h3 class="form-section-title">🏥 法人情報</h3>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label required" for="corp_name">法人名</label>
                                <input type="text" id="corp_name" name="corp_name" class="form-input"
                                       value="<?php echo e($formData['corp_name'] ?? ''); ?>"
                                       placeholder="医療法人社団 〇〇会" required>
                                <?php if (!empty($errors['corp_name'])): ?>
                                    <p class="form-error"><?php echo e($errors['corp_name'][0]); ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="form-group">
                                <label class="form-label required" for="corp_number">法人番号</label>
                                <input type="text" id="corp_number" name="corp_number" class="form-input"
                                       value="<?php echo e($formData['corp_number'] ?? ''); ?>"
                                       placeholder="13桁の法人番号" required>
                                <?php if (!empty($errors['corp_number'])): ?>
                                    <p class="form-error"><?php echo e($errors['corp_number'][0]); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label required" for="representative_name">代表者名</label>
                            <input type="text" id="representative_name" name="representative_name" class="form-input"
                                   value="<?php echo e($formData['representative_name'] ?? ''); ?>"
                                   placeholder="代表者のお名前" required>
                            <?php if (!empty($errors['representative_name'])): ?>
                                <p class="form-error"><?php echo e($errors['representative_name'][0]); ?></p>
                            <?php endif; ?>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label required" for="postal_code">郵便番号</label>
                                <input type="text" id="postal_code" name="postal_code" class="form-input"
                                       value="<?php echo e($formData['postal_code'] ?? ''); ?>"
                                       placeholder="123-4567" required>
                                <?php if (!empty($errors['postal_code'])): ?>
                                    <p class="form-error"><?php echo e($errors['postal_code'][0]); ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="form-group">
                                <label class="form-label required" for="prefecture">都道府県</label>
                                <select id="prefecture" name="prefecture" class="form-select" required>
                                    <option value="">選択してください</option>
                                    <?php foreach (PREFECTURES as $pref): ?>
                                        <option value="<?php echo e($pref); ?>" <?php echo ($formData['prefecture'] ?? '') === $pref ? 'selected' : ''; ?>>
                                            <?php echo e($pref); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (!empty($errors['prefecture'])): ?>
                                    <p class="form-error"><?php echo e($errors['prefecture'][0]); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label required" for="address">住所</label>
                            <input type="text" id="address" name="address" class="form-input"
                                   value="<?php echo e($formData['address'] ?? ''); ?>"
                                   placeholder="市区町村以降の住所" required>
                            <?php if (!empty($errors['address'])): ?>
                                <p class="form-error"><?php echo e($errors['address'][0]); ?></p>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label class="form-label required" for="phone">電話番号</label>
                            <input type="tel" id="phone" name="phone" class="form-input"
                                   value="<?php echo e($formData['phone'] ?? ''); ?>"
                                   placeholder="03-1234-5678" required>
                            <?php if (!empty($errors['phone'])): ?>
                                <p class="form-error"><?php echo e($errors['phone'][0]); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- 担当者情報 -->
                    <div class="form-section">
                        <h3 class="form-section-title">👤 担当者情報</h3>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label required" for="contact_person_name">担当者名</label>
                                <input type="text" id="contact_person_name" name="contact_person_name" class="form-input"
                                       value="<?php echo e($formData['contact_person_name'] ?? ''); ?>"
                                       placeholder="担当者のお名前" required>
                                <?php if (!empty($errors['contact_person_name'])): ?>
                                    <p class="form-error"><?php echo e($errors['contact_person_name'][0]); ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="contact_person_position">役職</label>
                                <input type="text" id="contact_person_position" name="contact_person_position" class="form-input"
                                       value="<?php echo e($formData['contact_person_position'] ?? ''); ?>"
                                       placeholder="人事部長など">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label required" for="contact_person_email">担当者メールアドレス</label>
                            <input type="email" id="contact_person_email" name="contact_person_email" class="form-input"
                                   value="<?php echo e($formData['contact_person_email'] ?? ''); ?>"
                                   placeholder="担当者のメールアドレス" required>
                            <?php if (!empty($errors['contact_person_email'])): ?>
                                <p class="form-error"><?php echo e($errors['contact_person_email'][0]); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- 利用規約 -->
                    <div class="form-group">
                        <label class="form-checkbox">
                            <input type="checkbox" name="agree_terms" required>
                            <span>
                                <a href="<?php echo BASE_PATH; ?>/?page=terms" target="_blank">利用規約</a>および
                                <a href="<?php echo BASE_PATH; ?>/?page=privacy" target="_blank">プライバシーポリシー</a>に同意します
                            </span>
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg" style="width: 100%;">
                        登録申請する
                    </button>
                </form>

                <div class="text-center mt-3">
                    <p class="text-gray text-sm">
                        すでにアカウントをお持ちの方は
                        <a href="<?php echo BASE_PATH; ?>/?page=login">ログイン</a>
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
