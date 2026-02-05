<?php
/**
 * 会員登録選択画面
 */
$pageTitle = '会員登録';

Auth::requireGuest();

require_once __DIR__ . '/../layouts/header.php';
?>

<style>
.register-page {
    min-height: calc(100vh - 72px);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: var(--space-3xl) var(--space-lg);
    background: linear-gradient(135deg, var(--color-gray-50) 0%, var(--color-white) 100%);
}

.register-container {
    width: 100%;
    max-width: 900px;
    text-align: center;
}

.register-header {
    margin-bottom: var(--space-2xl);
}

.register-title {
    font-size: 2rem;
    margin-bottom: var(--space-md);
}

.register-subtitle {
    color: var(--color-gray-500);
    font-size: 1.125rem;
}

.register-options {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: var(--space-xl);
}

.register-option {
    background: var(--color-white);
    border: 2px solid var(--color-gray-200);
    border-radius: var(--radius-xl);
    padding: var(--space-2xl);
    text-align: center;
    transition: all var(--transition-base);
    text-decoration: none;
    color: inherit;
}

.register-option:hover {
    border-color: var(--color-primary);
    box-shadow: var(--shadow-lg);
    transform: translateY(-4px);
}

.register-option-icon {
    width: 100px;
    height: 100px;
    margin: 0 auto var(--space-lg);
    background: linear-gradient(135deg, var(--color-primary-100) 0%, var(--color-primary-200) 100%);
    border-radius: var(--radius-xl);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3rem;
}

.register-option-title {
    font-size: 1.5rem;
    margin-bottom: var(--space-sm);
    color: var(--color-gray-900);
}

.register-option-description {
    color: var(--color-gray-500);
    font-size: 0.9375rem;
    line-height: 1.7;
    margin-bottom: var(--space-lg);
}

.register-option-features {
    text-align: left;
    padding: var(--space-lg);
    background: var(--color-gray-50);
    border-radius: var(--radius-md);
    margin-bottom: var(--space-lg);
}

.register-option-feature {
    display: flex;
    align-items: center;
    gap: var(--space-sm);
    margin-bottom: var(--space-sm);
    font-size: 0.875rem;
    color: var(--color-gray-600);
}

.register-option-feature:last-child {
    margin-bottom: 0;
}

.register-option-feature::before {
    content: '✓';
    color: var(--color-success);
    font-weight: bold;
}

.register-footer {
    margin-top: var(--space-2xl);
    color: var(--color-gray-500);
    font-size: 0.9375rem;
}

@media (max-width: 768px) {
    .register-options {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="register-page">
    <div class="register-container">
        <div class="register-header">
            <h1 class="register-title">会員登録</h1>
            <p class="register-subtitle">あなたに合った登録タイプをお選びください</p>
        </div>

        <div class="register-options">
            <a href="<?php echo BASE_PATH; ?>/?page=register/doctor" class="register-option">
                <div class="register-option-icon">👨‍⚕️</div>
                <h2 class="register-option-title">医師の方</h2>
                <p class="register-option-description">
                    将来の独立開業を目指す医師の方<br>
                    院長候補として経験を積みたい方
                </p>
                <div class="register-option-features">
                    <div class="register-option-feature">譲渡特約付き求人へ応募可能</div>
                    <div class="register-option-feature">経営ノウハウを実践で習得</div>
                    <div class="register-option-feature">専門スタッフによるサポート</div>
                    <div class="register-option-feature">完全無料でご利用可能</div>
                </div>
                <span class="btn btn-primary btn-lg">医師として登録する</span>
            </a>

            <a href="<?php echo BASE_PATH; ?>/?page=register/clinic" class="register-option">
                <div class="register-option-icon">🏥</div>
                <h2 class="register-option-title">医療法人の方</h2>
                <p class="register-option-description">
                    管理医師不足でお困りの法人の方<br>
                    事業承継をお考えの開業医の方
                </p>
                <div class="register-option-features">
                    <div class="register-option-feature">意欲ある院長候補を採用</div>
                    <div class="register-option-feature">将来の事業承継を計画的に</div>
                    <div class="register-option-feature">マッチング支援サービス</div>
                    <div class="register-option-feature">成功報酬型の料金体系</div>
                </div>
                <span class="btn btn-primary btn-lg">法人として登録する</span>
            </a>
        </div>

        <div class="register-footer">
            <p>
                すでにアカウントをお持ちの方は
                <a href="<?php echo BASE_PATH; ?>/?page=login">ログイン</a>
            </p>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
