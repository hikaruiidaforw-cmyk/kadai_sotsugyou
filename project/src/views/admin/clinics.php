<?php
/**
 * 管理者: 法人会員一覧
 */
$pageTitle = '法人会員管理';

require_once __DIR__ . '/../../models/Clinic.php';
require_once __DIR__ . '/../../models/User.php';

$clinicModel = new Clinic();
$userModel = new User();

$page = max(1, intval($_GET['p'] ?? 1));
$statusFilter = $_GET['status'] ?? '';
$clinics = $clinicModel->getAll($page, ITEMS_PER_PAGE, ['status' => $statusFilter]);
$totalCount = $clinicModel->count(['status' => $statusFilter]);
$totalPages = ceil($totalCount / ITEMS_PER_PAGE);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    CSRF::requireValid();

    $userId = intval($_POST['user_id']);
    $newStatus = $_POST['new_status'];

    if (in_array($newStatus, [STATUS_ACTIVE, STATUS_SUSPENDED, STATUS_PENDING])) {
        $userModel->updateStatus($userId, $newStatus);
        header('Location: ' . BASE_PATH . '/?page=admin/clinics&status=' . $statusFilter);
        exit;
    }
}

require_once __DIR__ . '/../layouts/header.php';
?>

<style>
.admin-page {
    padding: var(--space-2xl) 0 var(--space-4xl);
    background: var(--color-gray-50);
    min-height: calc(100vh - 72px);
}

.filter-bar {
    display: flex;
    gap: var(--space-md);
    margin-bottom: var(--space-lg);
}

.filter-btn {
    padding: var(--space-sm) var(--space-md);
    background: var(--color-white);
    border: 1px solid var(--color-gray-200);
    border-radius: var(--radius-md);
    font-size: 0.875rem;
    cursor: pointer;
    transition: all var(--transition-fast);
    text-decoration: none;
    color: inherit;
}

.filter-btn:hover,
.filter-btn.active {
    background: var(--color-primary);
    color: white;
    border-color: var(--color-primary);
}

.clinic-row {
    display: grid;
    grid-template-columns: 50px 1fr 100px 100px 150px;
    align-items: center;
    gap: var(--space-md);
    padding: var(--space-md) var(--space-lg);
    background: var(--color-white);
    border: 1px solid var(--color-gray-200);
    border-radius: var(--radius-md);
    margin-bottom: var(--space-sm);
}

.clinic-logo {
    width: 50px;
    height: 50px;
    background: var(--color-gray-100);
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--color-primary);
    font-weight: 600;
}
</style>

<div class="admin-page">
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--space-xl);">
            <h1 style="font-size: 1.75rem;">法人会員管理</h1>
            <p class="text-gray">総数: <?php echo $totalCount; ?>社</p>
        </div>

        <div class="filter-bar">
            <a href="?page=admin/clinics" class="filter-btn <?php echo !$statusFilter ? 'active' : ''; ?>">すべて</a>
            <a href="?page=admin/clinics&status=pending" class="filter-btn <?php echo $statusFilter === 'pending' ? 'active' : ''; ?>">承認待ち</a>
            <a href="?page=admin/clinics&status=active" class="filter-btn <?php echo $statusFilter === 'active' ? 'active' : ''; ?>">有効</a>
            <a href="?page=admin/clinics&status=suspended" class="filter-btn <?php echo $statusFilter === 'suspended' ? 'active' : ''; ?>">停止中</a>
        </div>

        <?php if (empty($clinics)): ?>
            <div class="card">
                <div class="card-body text-center" style="padding: var(--space-2xl);">
                    <p class="text-gray">該当する法人会員がいません</p>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($clinics as $clinic): ?>
                <div class="clinic-row">
                    <div class="clinic-logo">
                        <?php echo mb_substr($clinic['corp_name'], 0, 1); ?>
                    </div>
                    <div>
                        <div style="font-weight: 600;"><?php echo e($clinic['corp_name']); ?></div>
                        <div style="font-size: 0.875rem; color: var(--color-gray-500);"><?php echo e($clinic['email']); ?></div>
                    </div>
                    <div><?php echo e($clinic['prefecture']); ?></div>
                    <div>
                        <span class="badge badge-<?php echo $clinic['user_status'] === 'active' ? 'success' : ($clinic['user_status'] === 'pending' ? 'warning' : 'error'); ?>">
                            <?php echo $clinic['user_status'] === 'active' ? '有効' : ($clinic['user_status'] === 'pending' ? '承認待ち' : '停止'); ?>
                        </span>
                    </div>
                    <div>
                        <form method="POST" style="display: flex; gap: var(--space-xs);">
                            <?php echo CSRF::tokenField(); ?>
                            <input type="hidden" name="action" value="update_status">
                            <input type="hidden" name="user_id" value="<?php echo $clinic['user_id']; ?>">
                            <?php if ($clinic['user_status'] !== 'active'): ?>
                                <button type="submit" name="new_status" value="active" class="btn btn-primary btn-sm">承認</button>
                            <?php endif; ?>
                            <?php if ($clinic['user_status'] !== 'suspended'): ?>
                                <button type="submit" name="new_status" value="suspended" class="btn btn-ghost btn-sm">停止</button>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>

            <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="?page=admin/clinics&status=<?php echo $statusFilter; ?>&p=<?php echo $i; ?>"
                           class="pagination-item <?php echo $i === $page ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
