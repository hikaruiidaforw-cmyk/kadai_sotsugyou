<?php
/**
 * ログイン画面
 */
$pageTitle = 'ログイン';

Auth::requireGuest();

$errors = [];
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    CSRF::requireValid();

    require_once __DIR__ . '/../../controllers/AuthController.php';
    $authController = new AuthController();

    $result = $authController->login($_POST);

    if ($result['success']) {
        header('Location: ' . $result['redirect']);
        exit;
    } else {
        $errors = $result['errors'];
        $email = $_POST['email'] ?? '';
    }
}

require_once __DIR__ . '/../layouts/header.php';
?>

<style>
.auth-page {
    min-height: calc(100vh - 64px);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: var(--space-8) var(--space-6);
    background: var(--color-gray-50);
}

.auth-container {
    width: 100%;
    max-width: 400px;
}

.auth-card {
    background: var(--color-white);
    border-radius: var(--radius-xl);
    padding: var(--space-10);
    border: 1px solid var(--color-gray-200);
}

.auth-header {
    text-align: center;
    margin-bottom: var(--space-8);
}

.auth-logo {
    width: 48px;
    height: 48px;
    background: var(--color-primary);
    border-radius: var(--radius-lg);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.25rem;
    font-weight: 600;
    margin: 0 auto var(--space-5);
}

.auth-title {
    font-size: 1.375rem;
    font-weight: 600;
    margin-bottom: var(--space-2);
}

.auth-subtitle {
    color: var(--color-gray-500);
    font-size: 0.875rem;
}

.auth-footer {
    text-align: center;
    margin-top: var(--space-6);
    padding-top: var(--space-6);
    border-top: 1px solid var(--color-gray-100);
}

.auth-footer-text {
    color: var(--color-gray-600);
    font-size: 0.875rem;
}
</style>

<div class="auth-page">
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <div class="auth-logo">M</div>
                <h1 class="auth-title">ログイン</h1>
                <p class="auth-subtitle">アカウントにログインしてください</p>
            </div>

            <?php if (!empty($errors['email'])): ?>
                <div class="alert alert-error">
                    <?php echo e($errors['email'][0]); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <?php echo CSRF::tokenField(); ?>

                <div class="form-group">
                    <label class="form-label required" for="email">メールアドレス</label>
                    <input type="email" id="email" name="email" class="form-input"
                           value="<?php echo e($email); ?>"
                           placeholder="example@email.com" required>
                </div>

                <div class="form-group">
                    <label class="form-label required" for="password">パスワード</label>
                    <input type="password" id="password" name="password" class="form-input"
                           placeholder="8文字以上" required>
                </div>

                <div class="form-group" style="display: flex; justify-content: space-between; align-items: center;">
                    <label class="form-checkbox">
                        <input type="checkbox" name="remember">
                        <span>ログイン状態を保持</span>
                    </label>
                    <a href="<?php echo BASE_PATH; ?>/?page=password/reset" class="text-sm">パスワードをお忘れですか？</a>
                </div>

                <button type="submit" class="btn btn-primary btn-lg" style="width: 100%;">
                    ログイン
                </button>
            </form>

            <div class="auth-footer">
                <p class="auth-footer-text">
                    アカウントをお持ちでない方は
                    <a href="<?php echo BASE_PATH; ?>/?page=register">新規会員登録</a>
                </p>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
