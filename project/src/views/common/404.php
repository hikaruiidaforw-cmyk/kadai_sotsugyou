<?php
/**
 * 404エラーページ
 */
$pageTitle = 'ページが見つかりません';

require_once __DIR__ . '/../layouts/header.php';
?>

<style>
.error-page {
    min-height: calc(100vh - 72px);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: var(--space-2xl);
    text-align: center;
}

.error-content {
    max-width: 480px;
}

.error-code {
    font-family: var(--font-display);
    font-size: 8rem;
    font-weight: 600;
    color: var(--color-primary-100);
    line-height: 1;
    margin-bottom: var(--space-md);
}

.error-title {
    font-size: 1.5rem;
    margin-bottom: var(--space-md);
}

.error-description {
    color: var(--color-gray-500);
    margin-bottom: var(--space-xl);
}
</style>

<div class="error-page">
    <div class="error-content">
        <div class="error-code">404</div>
        <h1 class="error-title">ページが見つかりません</h1>
        <p class="error-description">
            お探しのページは存在しないか、移動した可能性があります。<br>
            URLをご確認いただくか、トップページからお探しください。
        </p>
        <a href="<?php echo BASE_PATH; ?>/" class="btn btn-primary btn-lg">トップページへ戻る</a>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
