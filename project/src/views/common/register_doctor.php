<?php
/**
 * 医師会員登録画面
 */
$pageTitle = '医師会員登録';

Auth::requireGuest();

$errors = [];
$formData = [];
$successMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    CSRF::requireValid();

    require_once __DIR__ . '/../../controllers/AuthController.php';
    $authController = new AuthController();

    $result = $authController->registerDoctor($_POST);

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
.register-doctor-page {
    padding: var(--space-2xl) 0 var(--space-4xl);
    background: linear-gradient(135deg, var(--color-gray-50) 0%, var(--color-white) 100%);
}

.register-doctor-container {
    max-width: 720px;
    margin: 0 auto;
    padding: 0 var(--space-lg);
}

.register-doctor-header {
    text-align: center;
    margin-bottom: var(--space-2xl);
}

.register-doctor-title {
    font-size: 2rem;
    margin-bottom: var(--space-sm);
}

.register-doctor-subtitle {
    color: var(--color-gray-500);
    font-size: 1rem;
}

.register-doctor-card {
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

<div class="register-doctor-page">
    <div class="register-doctor-container">
        <div class="register-doctor-header">
            <h1 class="register-doctor-title">医師会員登録</h1>
            <p class="register-doctor-subtitle">必要事項をご入力ください</p>
        </div>

        <div class="register-doctor-card">
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
                                   placeholder="example@email.com" required>
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

                    <!-- 基本情報 -->
                    <div class="form-section">
                        <h3 class="form-section-title">👤 基本情報</h3>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label required" for="last_name">姓</label>
                                <input type="text" id="last_name" name="last_name" class="form-input"
                                       value="<?php echo e($formData['last_name'] ?? ''); ?>"
                                       placeholder="山田" required>
                                <?php if (!empty($errors['last_name'])): ?>
                                    <p class="form-error"><?php echo e($errors['last_name'][0]); ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="form-group">
                                <label class="form-label required" for="first_name">名</label>
                                <input type="text" id="first_name" name="first_name" class="form-input"
                                       value="<?php echo e($formData['first_name'] ?? ''); ?>"
                                       placeholder="太郎" required>
                                <?php if (!empty($errors['first_name'])): ?>
                                    <p class="form-error"><?php echo e($errors['first_name'][0]); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label required" for="last_name_kana">姓（カナ）</label>
                                <input type="text" id="last_name_kana" name="last_name_kana" class="form-input"
                                       value="<?php echo e($formData['last_name_kana'] ?? ''); ?>"
                                       placeholder="ヤマダ" required>
                                <?php if (!empty($errors['last_name_kana'])): ?>
                                    <p class="form-error"><?php echo e($errors['last_name_kana'][0]); ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="form-group">
                                <label class="form-label required" for="first_name_kana">名（カナ）</label>
                                <input type="text" id="first_name_kana" name="first_name_kana" class="form-input"
                                       value="<?php echo e($formData['first_name_kana'] ?? ''); ?>"
                                       placeholder="タロウ" required>
                                <?php if (!empty($errors['first_name_kana'])): ?>
                                    <p class="form-error"><?php echo e($errors['first_name_kana'][0]); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label required" for="birth_date">生年月日</label>
                                <input type="date" id="birth_date" name="birth_date" class="form-input"
                                       value="<?php echo e($formData['birth_date'] ?? ''); ?>" required>
                                <?php if (!empty($errors['birth_date'])): ?>
                                    <p class="form-error"><?php echo e($errors['birth_date'][0]); ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="form-group">
                                <label class="form-label required" for="gender">性別</label>
                                <select id="gender" name="gender" class="form-select" required>
                                    <option value="">選択してください</option>
                                    <option value="male" <?php echo ($formData['gender'] ?? '') === 'male' ? 'selected' : ''; ?>>男性</option>
                                    <option value="female" <?php echo ($formData['gender'] ?? '') === 'female' ? 'selected' : ''; ?>>女性</option>
                                    <option value="other" <?php echo ($formData['gender'] ?? '') === 'other' ? 'selected' : ''; ?>>その他</option>
                                </select>
                                <?php if (!empty($errors['gender'])): ?>
                                    <p class="form-error"><?php echo e($errors['gender'][0]); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label required" for="phone">電話番号</label>
                            <input type="tel" id="phone" name="phone" class="form-input"
                                   value="<?php echo e($formData['phone'] ?? ''); ?>"
                                   placeholder="090-1234-5678" required>
                            <?php if (!empty($errors['phone'])): ?>
                                <p class="form-error"><?php echo e($errors['phone'][0]); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- 医師免許情報 -->
                    <div class="form-section">
                        <h3 class="form-section-title">📋 医師免許情報</h3>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label required" for="license_number">医師免許番号</label>
                                <input type="text" id="license_number" name="license_number" class="form-input"
                                       value="<?php echo e($formData['license_number'] ?? ''); ?>"
                                       placeholder="123456" required>
                                <?php if (!empty($errors['license_number'])): ?>
                                    <p class="form-error"><?php echo e($errors['license_number'][0]); ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="form-group">
                                <label class="form-label required" for="license_date">取得年月日</label>
                                <input type="date" id="license_date" name="license_date" class="form-input"
                                       value="<?php echo e($formData['license_date'] ?? ''); ?>" required>
                                <?php if (!empty($errors['license_date'])): ?>
                                    <p class="form-error"><?php echo e($errors['license_date'][0]); ?></p>
                                <?php endif; ?>
                            </div>
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
