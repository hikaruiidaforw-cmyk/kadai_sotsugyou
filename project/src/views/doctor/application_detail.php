<?php
/**
 * 応募詳細・メッセージ画面
 */

require_once __DIR__ . '/../../models/Doctor.php';
require_once __DIR__ . '/../../models/Application.php';
require_once __DIR__ . '/../../models/Message.php';

$doctorModel = new Doctor();
$applicationModel = new Application();
$messageModel = new Message();

$applicationId = intval($_GET['id'] ?? 0);
$application = $applicationModel->findById($applicationId);
$doctor = $doctorModel->findByUserId(Auth::id());

if (!$application || $application['doctor_id'] != $doctor['id']) {
    header('Location: ' . BASE_PATH . '/?page=doctor/applications');
    exit;
}

$pageTitle = $application['job_title'];

// メッセージ送信処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    CSRF::requireValid();

    $body = trim($_POST['message']);
    if (!empty($body)) {
        $messageModel->create([
            'application_id' => $applicationId,
            'sender_user_id' => Auth::id(),
            'body' => $body
        ]);
    }

    header('Location: ' . BASE_PATH . '/?page=doctor/application&id=' . $applicationId);
    exit;
}

// 既読処理
$messageModel->markAsRead($applicationId, Auth::id());
$messages = $messageModel->getByApplicationId($applicationId);

require_once __DIR__ . '/../layouts/header.php';
?>

<style>
.application-detail-page {
    padding: var(--space-2xl) 0 var(--space-4xl);
    background: var(--color-gray-50);
    min-height: calc(100vh - 72px);
}

.application-detail-grid {
    display: grid;
    grid-template-columns: 1fr 360px;
    gap: var(--space-xl);
}

.back-link {
    display: inline-flex;
    align-items: center;
    gap: var(--space-sm);
    color: var(--color-gray-500);
    margin-bottom: var(--space-lg);
    font-size: 0.875rem;
}

/* Status Card */
.status-card {
    background: var(--color-white);
    border-radius: var(--radius-lg);
    padding: var(--space-xl);
    border: 1px solid var(--color-gray-200);
    margin-bottom: var(--space-xl);
}

.status-card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: var(--space-lg);
}

.status-card-title {
    font-size: 1.25rem;
}

.status-card-company {
    color: var(--color-gray-500);
    margin-top: var(--space-xs);
}

.status-timeline {
    display: flex;
    justify-content: space-between;
    position: relative;
    margin-top: var(--space-xl);
}

.status-timeline::before {
    content: '';
    position: absolute;
    top: 16px;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--color-gray-200);
    border-radius: 2px;
}

.status-step {
    position: relative;
    z-index: 1;
    text-align: center;
    flex: 1;
}

.status-step-dot {
    width: 32px;
    height: 32px;
    margin: 0 auto var(--space-sm);
    background: var(--color-gray-200);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    color: var(--color-gray-400);
}

.status-step.active .status-step-dot {
    background: var(--color-primary);
    color: white;
}

.status-step.completed .status-step-dot {
    background: var(--color-success);
    color: white;
}

.status-step-label {
    font-size: 0.75rem;
    color: var(--color-gray-500);
}

.status-step.active .status-step-label {
    color: var(--color-primary);
    font-weight: 600;
}

/* Messages */
.messages-section {
    background: var(--color-white);
    border-radius: var(--radius-lg);
    border: 1px solid var(--color-gray-200);
    display: flex;
    flex-direction: column;
    height: 600px;
}

.messages-header {
    padding: var(--space-lg);
    border-bottom: 1px solid var(--color-gray-100);
}

.messages-header-title {
    font-size: 1.125rem;
}

.messages-list {
    flex: 1;
    overflow-y: auto;
    padding: var(--space-lg);
    display: flex;
    flex-direction: column;
    gap: var(--space-md);
}

.message-item {
    display: flex;
    gap: var(--space-md);
    max-width: 80%;
}

.message-item.sent {
    margin-left: auto;
    flex-direction: row-reverse;
}

.message-avatar {
    width: 40px;
    height: 40px;
    background: var(--color-gray-100);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    flex-shrink: 0;
}

.message-item.sent .message-avatar {
    background: var(--color-primary-100);
    color: var(--color-primary);
}

.message-content {
    flex: 1;
}

.message-sender {
    font-size: 0.75rem;
    color: var(--color-gray-500);
    margin-bottom: var(--space-xs);
}

.message-body {
    padding: var(--space-md);
    background: var(--color-gray-50);
    border-radius: var(--radius-md);
    line-height: 1.6;
}

.message-item.sent .message-body {
    background: var(--color-primary-100);
}

.message-time {
    font-size: 0.625rem;
    color: var(--color-gray-400);
    margin-top: var(--space-xs);
    text-align: right;
}

.messages-form {
    padding: var(--space-lg);
    border-top: 1px solid var(--color-gray-100);
}

.messages-form-inner {
    display: flex;
    gap: var(--space-md);
}

.messages-form textarea {
    flex: 1;
    resize: none;
}

/* Sidebar */
.job-info-card {
    background: var(--color-white);
    border-radius: var(--radius-lg);
    padding: var(--space-xl);
    border: 1px solid var(--color-gray-200);
}

.job-info-title {
    font-size: 1rem;
    margin-bottom: var(--space-lg);
    padding-bottom: var(--space-md);
    border-bottom: 1px solid var(--color-gray-100);
}

.job-info-item {
    display: flex;
    justify-content: space-between;
    padding: var(--space-sm) 0;
    font-size: 0.875rem;
}

.job-info-label {
    color: var(--color-gray-500);
}

@media (max-width: 1024px) {
    .application-detail-grid {
        grid-template-columns: 1fr;
    }

    .status-timeline {
        flex-wrap: wrap;
        gap: var(--space-md);
    }
}
</style>

<div class="application-detail-page">
    <div class="container">
        <a href="<?php echo BASE_PATH; ?>/?page=doctor/applications" class="back-link">
            ← 応募一覧に戻る
        </a>

        <div class="application-detail-grid">
            <div>
                <!-- ステータス -->
                <div class="status-card">
                    <div class="status-card-header">
                        <div>
                            <h1 class="status-card-title"><?php echo e($application['job_title']); ?></h1>
                            <p class="status-card-company"><?php echo e($application['corp_name']); ?></p>
                        </div>
                        <span class="badge badge-<?php echo $application['status'] === 'offered' || $application['status'] === 'accepted' ? 'success' : ($application['status'] === 'rejected' || $application['status'] === 'declined' ? 'error' : 'primary'); ?>">
                            <?php echo e(APPLICATION_STATUS[$application['status']] ?? $application['status']); ?>
                        </span>
                    </div>

                    <?php
                    $statusOrder = ['applied', 'document_screening', 'interview_scheduling', 'interview_completed', 'offered', 'accepted'];
                    $currentIndex = array_search($application['status'], $statusOrder);
                    ?>
                    <div class="status-timeline">
                        <?php foreach (['応募', '書類選考', '面接調整', '面接完了', '内定', '承諾'] as $i => $label): ?>
                            <div class="status-step <?php echo $i < $currentIndex ? 'completed' : ($i === $currentIndex ? 'active' : ''); ?>">
                                <div class="status-step-dot">
                                    <?php echo $i < $currentIndex ? '✓' : ($i + 1); ?>
                                </div>
                                <div class="status-step-label"><?php echo $label; ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- メッセージ -->
                <div class="messages-section">
                    <div class="messages-header">
                        <h2 class="messages-header-title">メッセージ</h2>
                    </div>
                    <div class="messages-list">
                        <?php if (empty($messages)): ?>
                            <div class="text-center text-gray" style="padding: var(--space-xl);">
                                メッセージはまだありません
                            </div>
                        <?php else: ?>
                            <?php foreach ($messages as $msg): ?>
                                <div class="message-item <?php echo $msg['sender_user_id'] == Auth::id() ? 'sent' : ''; ?>">
                                    <div class="message-avatar">
                                        <?php echo mb_substr($msg['sender_name'], 0, 1); ?>
                                    </div>
                                    <div class="message-content">
                                        <div class="message-sender"><?php echo e($msg['sender_name']); ?></div>
                                        <div class="message-body"><?php echo nl2br(e($msg['body'])); ?></div>
                                        <div class="message-time"><?php echo date('Y/m/d H:i', strtotime($msg['created_at'])); ?></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <form class="messages-form" method="POST">
                        <?php echo CSRF::tokenField(); ?>
                        <div class="messages-form-inner">
                            <textarea name="message" class="form-textarea" rows="2" placeholder="メッセージを入力..." required></textarea>
                            <button type="submit" class="btn btn-primary">送信</button>
                        </div>
                    </form>
                </div>
            </div>

            <div>
                <div class="job-info-card">
                    <h3 class="job-info-title">求人情報</h3>
                    <div class="job-info-item">
                        <span class="job-info-label">施設名</span>
                        <span><?php echo e($application['facility_name']); ?></span>
                    </div>
                    <div class="job-info-item">
                        <span class="job-info-label">応募日</span>
                        <span><?php echo date('Y年m月d日', strtotime($application['applied_at'])); ?></span>
                    </div>
                    <a href="<?php echo BASE_PATH; ?>/?page=doctor/job&id=<?php echo $application['job_id']; ?>" class="btn btn-secondary mt-3" style="width: 100%;">
                        求人詳細を見る
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
