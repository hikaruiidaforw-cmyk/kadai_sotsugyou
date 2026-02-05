<?php
/**
 * 管理者: 医師会員一覧
 */
$pageTitle = '医師会員管理';

require_once __DIR__ . '/../../models/Doctor.php';
require_once __DIR__ . '/../../models/User.php';

$doctorModel = new Doctor();
$userModel = new User();

$page = max(1, intval($_GET['p'] ?? 1));
$statusFilter = $_GET['status'] ?? '';
$doctors = $doctorModel->getAll($page, ITEMS_PER_PAGE, ['status' => $statusFilter]);
$totalCount = $doctorModel->count(['status' => $statusFilter]);
$totalPages = ceil($totalCount / ITEMS_PER_PAGE);

// ステータス更新処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    CSRF::requireValid();

    $userId = intval($_POST['user_id']);
    $newStatus = $_POST['new_status'];

    if (in_array($newStatus, [STATUS_ACTIVE, STATUS_SUSPENDED, STATUS_PENDING])) {
        $userModel->updateStatus($userId, $newStatus);
        header('Location: ' . BASE_PATH . '/?page=admin/doctors&status=' . $statusFilter);
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

.admin-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: var(--space-xl);
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

.doctor-row {
    display: grid;
    grid-template-columns: 50px 1fr 120px 100px 150px;
    align-items: center;
    gap: var(--space-md);
    padding: var(--space-md) var(--space-lg);
    background: var(--color-white);
    border: 1px solid var(--color-gray-200);
    border-radius: var(--radius-md);
    margin-bottom: var(--space-sm);
}

.doctor-avatar {
    width: 50px;
    height: 50px;
    background: var(--color-primary-100);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--color-primary);
    font-weight: 600;
}

.doctor-info-name {
    font-weight: 600;
}

.doctor-info-email {
    font-size: 0.875rem;
    color: var(--color-gray-500);
}
</style>

<div class="admin-page">
    <div class="container">
        <div class="admin-header">
            <h1 style="font-size: 1.75rem;">医師会員管理</h1>
            <p class="text-gray">総数: <?php echo $totalCount; ?>名</p>
        </div>

        <div class="filter-bar">
            <a href="?page=admin/doctors" class="filter-btn <?php echo !$statusFilter ? 'active' : ''; ?>">すべて</a>
            <a href="?page=admin/doctors&status=pending" class="filter-btn <?php echo $statusFilter === 'pending' ? 'active' : ''; ?>">承認待ち</a>
            <a href="?page=admin/doctors&status=active" class="filter-btn <?php echo $statusFilter === 'active' ? 'active' : ''; ?>">有効</a>
            <a href="?page=admin/doctors&status=suspended" class="filter-btn <?php echo $statusFilter === 'suspended' ? 'active' : ''; ?>">停止中</a>
        </div>

        <?php if (empty($doctors)): ?>
            <div class="card">
                <div class="card-body text-center" style="padding: var(--space-2xl);">
                    <p class="text-gray">該当する医師会員がいません</p>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($doctors as $doctor): ?>
                <div class="doctor-row">
                    <div class="doctor-avatar">
                        <?php echo mb_substr($doctor['last_name'], 0, 1); ?>
                    </div>
                    <div>
                        <div class="doctor-info-name"><?php echo e($doctor['last_name'] . ' ' . $doctor['first_name']); ?> 先生</div>
                        <div class="doctor-info-email"><?php echo e($doctor['email']); ?></div>
                    </div>
                    <div><?php echo e($doctor['prefecture'] ?? '-'); ?></div>
                    <div>
                        <span class="badge badge-<?php echo $doctor['user_status'] === 'active' ? 'success' : ($doctor['user_status'] === 'pending' ? 'warning' : 'error'); ?>">
                            <?php echo $doctor['user_status'] === 'active' ? '有効' : ($doctor['user_status'] === 'pending' ? '承認待ち' : '停止'); ?>
                        </span>
                    </div>
                    <div>
                        <form method="POST" style="display: flex; gap: var(--space-xs);">
                            <?php echo CSRF::tokenField(); ?>
                            <input type="hidden" name="action" value="update_status">
                            <input type="hidden" name="user_id" value="<?php echo $doctor['user_id']; ?>">
                            <?php if ($doctor['user_status'] !== 'active'): ?>
                                <button type="submit" name="new_status" value="active" class="btn btn-primary btn-sm">承認</button>
                            <?php endif; ?>
                            <?php if ($doctor['user_status'] !== 'suspended'): ?>
                                <button type="submit" name="new_status" value="suspended" class="btn btn-ghost btn-sm">停止</button>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>

            <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="?page=admin/doctors&status=<?php echo $statusFilter; ?>&p=<?php echo $i; ?>"
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
