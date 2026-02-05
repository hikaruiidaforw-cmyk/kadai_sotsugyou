<?php
/**
 * 法人向け応募詳細画面
 */

require_once __DIR__ . '/../../models/Clinic.php';
require_once __DIR__ . '/../../models/Application.php';
require_once __DIR__ . '/../../models/Message.php';
require_once __DIR__ . '/../../models/Doctor.php';

$clinicModel = new Clinic();
$applicationModel = new Application();
$messageModel = new Message();
$doctorModel = new Doctor();

$applicationId = intval($_GET['id'] ?? 0);
$application = $applicationModel->findById($applicationId);
$clinic = $clinicModel->findByUserId(Auth::id());

if (!$application || $application['clinic_id'] != $clinic['id']) {
    header('Location: ' . BASE_PATH . '/?page=clinic/applications');
    exit;
}

$pageTitle = $application['last_name'] . ' ' . $application['first_name'] . ' 先生';
$doctor = $doctorModel->findById($application['doctor_id']);
$doctorSpecialties = $doctorModel->getSpecialties($application['doctor_id']);

// ステータス更新処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    CSRF::requireValid();

    if ($_POST['action'] === 'update_status' && isset($_POST['status'])) {
        $applicationModel->updateStatus($applicationId, $_POST['status']);
        $application = $applicationModel->findById($applicationId);
    } elseif ($_POST['action'] === 'send_message' && isset($_POST['message'])) {
        $body = trim($_POST['message']);
        if (!empty($body)) {
            $messageModel->create([
                'application_id' => $applicationId,
                'sender_user_id' => Auth::id(),
                'body' => $body
            ]);
        }
        header('Location: ' . BASE_PATH . '/?page=clinic/application&id=' . $applicationId);
        exit;
    }
}

$messageModel->markAsRead($applicationId, Auth::id());
$messages = $messageModel->getByApplicationId($applicationId);

require_once __DIR__ . '/../layouts/header.php';
?>

<style>
.application-page {
    padding: var(--space-2xl) 0 var(--space-4xl);
    background: var(--color-gray-50);
}

.application-grid {
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
}

.doctor-card {
    background: var(--color-white);
    border-radius: var(--radius-lg);
    padding: var(--space-xl);
    border: 1px solid var(--color-gray-200);
    margin-bottom: var(--space-xl);
}

.doctor-header {
    display: flex;
    gap: var(--space-lg);
    margin-bottom: var(--space-xl);
}

.doctor-avatar {
    width: 100px;
    height: 100px;
    background: var(--color-primary-100);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    color: var(--color-primary);
    flex-shrink: 0;
    overflow: hidden;
}

.doctor-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.doctor-name {
    font-size: 1.5rem;
    margin-bottom: var(--space-xs);
}

.doctor-specialties {
    display: flex;
    gap: var(--space-xs);
    flex-wrap: wrap;
    margin-bottom: var(--space-sm);
}

.doctor-info-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: var(--space-md);
}

.doctor-info-item {
    padding: var(--space-md);
    background: var(--color-gray-50);
    border-radius: var(--radius-md);
}

.doctor-info-label {
    font-size: 0.75rem;
    color: var(--color-gray-500);
    margin-bottom: var(--space-xs);
}

.doctor-info-value {
    font-weight: 500;
}

.messages-section {
    background: var(--color-white);
    border-radius: var(--radius-lg);
    border: 1px solid var(--color-gray-200);
    display: flex;
    flex-direction: column;
    height: 500px;
}

.messages-header {
    padding: var(--space-lg);
    border-bottom: 1px solid var(--color-gray-100);
}

.messages-list {
    flex: 1;
    overflow-y: auto;
    padding: var(--space-lg);
}

.message-item {
    display: flex;
    gap: var(--space-md);
    margin-bottom: var(--space-md);
    max-width: 80%;
}

.message-item.sent {
    margin-left: auto;
    flex-direction: row-reverse;
}

.message-avatar {
    width: 36px;
    height: 36px;
    background: var(--color-gray-100);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.875rem;
    flex-shrink: 0;
}

.message-body {
    padding: var(--space-md);
    background: var(--color-gray-50);
    border-radius: var(--radius-md);
}

.message-item.sent .message-body {
    background: var(--color-primary-100);
}

.messages-form {
    padding: var(--space-lg);
    border-top: 1px solid var(--color-gray-100);
}

.sidebar-card {
    background: var(--color-white);
    border-radius: var(--radius-lg);
    padding: var(--space-xl);
    border: 1px solid var(--color-gray-200);
    margin-bottom: var(--space-xl);
}

.sidebar-card-title {
    font-size: 1rem;
    margin-bottom: var(--space-lg);
    padding-bottom: var(--space-md);
    border-bottom: 1px solid var(--color-gray-100);
}

.status-select {
    display: flex;
    flex-direction: column;
    gap: var(--space-sm);
}

@media (max-width: 1024px) {
    .application-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="application-page">
    <div class="container">
        <a href="<?php echo BASE_PATH; ?>/?page=clinic/applications" class="back-link">← 応募者一覧に戻る</a>

        <div class="application-grid">
            <div>
                <!-- 医師情報 -->
                <div class="doctor-card">
                    <div class="doctor-header">
                        <div class="doctor-avatar">
                            <?php if ($doctor['profile_photo']): ?>
                                <img src="<?php echo BASE_PATH; ?>/uploads/<?php echo e($doctor['profile_photo']); ?>" alt="">
                            <?php else: ?>
                                <?php echo mb_substr($doctor['last_name'], 0, 1); ?>
                            <?php endif; ?>
                        </div>
                        <div>
                            <h1 class="doctor-name"><?php echo e($doctor['last_name'] . ' ' . $doctor['first_name']); ?> 先生</h1>
                            <div class="doctor-specialties">
                                <?php foreach ($doctorSpecialties as $spec): ?>
                                    <span class="badge badge-primary"><?php echo e($spec['name']); ?></span>
                                <?php endforeach; ?>
                            </div>
                            <p class="text-gray text-sm">医師免許番号: <?php echo e($doctor['license_number']); ?></p>
                        </div>
                    </div>

                    <div class="doctor-info-grid">
                        <div class="doctor-info-item">
                            <div class="doctor-info-label">生年月日</div>
                            <div class="doctor-info-value"><?php echo date('Y年m月d日', strtotime($doctor['birth_date'])); ?></div>
                        </div>
                        <div class="doctor-info-item">
                            <div class="doctor-info-label">電話番号</div>
                            <div class="doctor-info-value"><?php echo e($doctor['phone']); ?></div>
                        </div>
                        <div class="doctor-info-item">
                            <div class="doctor-info-label">居住地</div>
                            <div class="doctor-info-value"><?php echo e($doctor['prefecture']); ?></div>
                        </div>
                        <div class="doctor-info-item">
                            <div class="doctor-info-label">免許取得</div>
                            <div class="doctor-info-value"><?php echo date('Y年', strtotime($doctor['license_date'])); ?></div>
                        </div>
                    </div>

                    <?php if ($doctor['self_introduction']): ?>
                        <div class="mt-3">
                            <h3 class="text-sm font-semibold mb-2">自己PR</h3>
                            <p style="line-height: 1.8;"><?php echo nl2br(e($doctor['self_introduction'])); ?></p>
                        </div>
                    <?php endif; ?>

                    <?php if ($application['cover_letter']): ?>
                        <div class="mt-3">
                            <h3 class="text-sm font-semibold mb-2">志望動機</h3>
                            <p style="line-height: 1.8;"><?php echo nl2br(e($application['cover_letter'])); ?></p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- メッセージ -->
                <div class="messages-section">
                    <div class="messages-header">
                        <h2 style="font-size: 1.125rem;">メッセージ</h2>
                    </div>
                    <div class="messages-list">
                        <?php foreach ($messages as $msg): ?>
                            <div class="message-item <?php echo $msg['sender_user_id'] == Auth::id() ? 'sent' : ''; ?>">
                                <div class="message-avatar"><?php echo mb_substr($msg['sender_name'], 0, 1); ?></div>
                                <div>
                                    <div class="message-body"><?php echo nl2br(e($msg['body'])); ?></div>
                                    <div class="text-xs text-gray mt-1"><?php echo date('Y/m/d H:i', strtotime($msg['created_at'])); ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <form class="messages-form" method="POST">
                        <?php echo CSRF::tokenField(); ?>
                        <input type="hidden" name="action" value="send_message">
                        <div style="display: flex; gap: var(--space-md);">
                            <textarea name="message" class="form-textarea" rows="2" placeholder="メッセージを入力..." required style="flex: 1;"></textarea>
                            <button type="submit" class="btn btn-primary">送信</button>
                        </div>
                    </form>
                </div>
            </div>

            <div>
                <!-- ステータス変更 -->
                <div class="sidebar-card">
                    <h3 class="sidebar-card-title">選考ステータス</h3>
                    <p class="mb-3">
                        現在のステータス:
                        <span class="badge badge-primary"><?php echo e(APPLICATION_STATUS[$application['status']] ?? $application['status']); ?></span>
                    </p>
                    <form method="POST">
                        <?php echo CSRF::tokenField(); ?>
                        <input type="hidden" name="action" value="update_status">
                        <div class="status-select">
                            <?php foreach (APPLICATION_STATUS as $key => $label): ?>
                                <label class="form-radio">
                                    <input type="radio" name="status" value="<?php echo $key; ?>"
                                        <?php echo $application['status'] === $key ? 'checked' : ''; ?>>
                                    <span><?php echo e($label); ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                        <button type="submit" class="btn btn-primary mt-3" style="width: 100%;">ステータスを更新</button>
                    </form>
                </div>

                <!-- 求人情報 -->
                <div class="sidebar-card">
                    <h3 class="sidebar-card-title">応募求人</h3>
                    <p class="font-medium"><?php echo e($application['job_title']); ?></p>
                    <p class="text-sm text-gray"><?php echo e($application['facility_name']); ?></p>
                    <p class="text-sm text-gray mt-2">応募日: <?php echo date('Y年m月d日', strtotime($application['applied_at'])); ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
