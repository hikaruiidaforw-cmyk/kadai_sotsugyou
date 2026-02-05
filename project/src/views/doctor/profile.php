<?php
/**
 * 医師プロフィール編集画面
 */
$pageTitle = 'プロフィール編集';

require_once __DIR__ . '/../../models/Doctor.php';
require_once __DIR__ . '/../../models/Specialty.php';
require_once __DIR__ . '/../../helpers/file_upload.php';

$doctorModel = new Doctor();
$specialtyModel = new Specialty();

$doctor = $doctorModel->findByUserId(Auth::id());
$allSpecialties = $specialtyModel->getAll();
$doctorSpecialties = $doctorModel->getSpecialties($doctor['id']);
$doctorSpecialtyIds = array_column($doctorSpecialties, 'id');

$message = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    CSRF::requireValid();

    $data = [
        'last_name' => $_POST['last_name'] ?? '',
        'first_name' => $_POST['first_name'] ?? '',
        'last_name_kana' => $_POST['last_name_kana'] ?? '',
        'first_name_kana' => $_POST['first_name_kana'] ?? '',
        'phone' => $_POST['phone'] ?? '',
        'postal_code' => $_POST['postal_code'] ?? '',
        'prefecture' => $_POST['prefecture'] ?? '',
        'address' => $_POST['address'] ?? '',
        'self_introduction' => $_POST['self_introduction'] ?? '',
        'desired_salary_min' => $_POST['desired_salary_min'] ?? null,
        'desired_salary_max' => $_POST['desired_salary_max'] ?? null,
        'desired_opening_year' => $_POST['desired_opening_year'] ?? null,
    ];

    // プロフィール写真アップロード
    if (!empty($_FILES['profile_photo']['name'])) {
        $uploader = new FileUpload();
        $photoPath = $uploader->uploadImage($_FILES['profile_photo'], 'profiles');
        if ($photoPath) {
            $data['profile_photo'] = $photoPath;
        } else {
            $errors['profile_photo'] = $uploader->getErrors();
        }
    }

    if (empty($errors)) {
        $doctorModel->update($doctor['id'], $data);

        // 専門科目更新
        $specialtyIds = $_POST['specialties'] ?? [];
        $mainSpecialtyId = $_POST['main_specialty'] ?? null;
        $doctorModel->setSpecialties($doctor['id'], $specialtyIds, $mainSpecialtyId);

        $message = 'プロフィールを更新しました';
        $doctor = $doctorModel->findByUserId(Auth::id());
        $doctorSpecialties = $doctorModel->getSpecialties($doctor['id']);
        $doctorSpecialtyIds = array_column($doctorSpecialties, 'id');
    }
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

.profile-header {
    margin-bottom: var(--space-xl);
}

.profile-title {
    font-size: 1.75rem;
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

.photo-upload {
    display: flex;
    align-items: center;
    gap: var(--space-lg);
    margin-bottom: var(--space-xl);
}

.photo-preview {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: var(--color-gray-100);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3rem;
    color: var(--color-primary);
    overflow: hidden;
}

.photo-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
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

.specialty-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: var(--space-sm);
}

@media (max-width: 640px) {
    .specialty-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
</style>

<div class="profile-page">
    <div class="container profile-container">
        <div class="profile-header">
            <h1 class="profile-title">プロフィール編集</h1>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo e($message); ?></div>
        <?php endif; ?>

        <form method="POST" action="" enctype="multipart/form-data">
            <?php echo CSRF::tokenField(); ?>

            <!-- 基本情報 -->
            <div class="profile-card">
                <h2 class="profile-card-title">基本情報</h2>

                <div class="photo-upload">
                    <div class="photo-preview">
                        <?php if ($doctor['profile_photo']): ?>
                            <img src="<?php echo BASE_PATH; ?>/uploads/<?php echo e($doctor['profile_photo']); ?>" alt="">
                        <?php else: ?>
                            <?php echo mb_substr($doctor['last_name'], 0, 1); ?>
                        <?php endif; ?>
                    </div>
                    <div>
                        <input type="file" name="profile_photo" id="profile_photo" accept="image/*" style="display: none;">
                        <label for="profile_photo" class="btn btn-secondary">写真を変更</label>
                        <p class="text-sm text-gray mt-1">JPG/PNG、最大2MB</p>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label required">姓</label>
                        <input type="text" name="last_name" class="form-input" value="<?php echo e($doctor['last_name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label required">名</label>
                        <input type="text" name="first_name" class="form-input" value="<?php echo e($doctor['first_name']); ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label required">姓（カナ）</label>
                        <input type="text" name="last_name_kana" class="form-input" value="<?php echo e($doctor['last_name_kana']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label required">名（カナ）</label>
                        <input type="text" name="first_name_kana" class="form-input" value="<?php echo e($doctor['first_name_kana']); ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label required">電話番号</label>
                    <input type="tel" name="phone" class="form-input" value="<?php echo e($doctor['phone']); ?>" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">郵便番号</label>
                        <input type="text" name="postal_code" class="form-input" value="<?php echo e($doctor['postal_code']); ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">都道府県</label>
                        <select name="prefecture" class="form-select">
                            <option value="">選択してください</option>
                            <?php foreach (PREFECTURES as $pref): ?>
                                <option value="<?php echo e($pref); ?>" <?php echo $doctor['prefecture'] === $pref ? 'selected' : ''; ?>>
                                    <?php echo e($pref); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">住所</label>
                    <input type="text" name="address" class="form-input" value="<?php echo e($doctor['address']); ?>">
                </div>
            </div>

            <!-- 専門科目 -->
            <div class="profile-card">
                <h2 class="profile-card-title">専門科目</h2>

                <div class="specialty-grid">
                    <?php foreach ($allSpecialties as $spec): ?>
                        <label class="form-checkbox">
                            <input type="checkbox" name="specialties[]" value="<?php echo $spec['id']; ?>"
                                <?php echo in_array($spec['id'], $doctorSpecialtyIds) ? 'checked' : ''; ?>>
                            <span><?php echo e($spec['name']); ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- 希望条件 -->
            <div class="profile-card">
                <h2 class="profile-card-title">希望条件</h2>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">希望年収（下限）</label>
                        <select name="desired_salary_min" class="form-select">
                            <option value="">指定なし</option>
                            <?php foreach ([1000, 1200, 1500, 1800, 2000, 2500, 3000] as $salary): ?>
                                <option value="<?php echo $salary; ?>" <?php echo $doctor['desired_salary_min'] == $salary ? 'selected' : ''; ?>>
                                    <?php echo number_format($salary); ?>万円
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">希望年収（上限）</label>
                        <select name="desired_salary_max" class="form-select">
                            <option value="">指定なし</option>
                            <?php foreach ([1500, 2000, 2500, 3000, 4000, 5000] as $salary): ?>
                                <option value="<?php echo $salary; ?>" <?php echo $doctor['desired_salary_max'] == $salary ? 'selected' : ''; ?>>
                                    <?php echo number_format($salary); ?>万円
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">希望開業年</label>
                    <select name="desired_opening_year" class="form-select">
                        <option value="">指定なし</option>
                        <?php for ($y = date('Y'); $y <= date('Y') + 10; $y++): ?>
                            <option value="<?php echo $y; ?>" <?php echo $doctor['desired_opening_year'] == $y ? 'selected' : ''; ?>>
                                <?php echo $y; ?>年
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>

            <!-- 自己PR -->
            <div class="profile-card">
                <h2 class="profile-card-title">自己PR</h2>

                <div class="form-group mb-0">
                    <textarea name="self_introduction" class="form-textarea" rows="6" placeholder="経験、スキル、将来のビジョンなどをご記入ください"><?php echo e($doctor['self_introduction']); ?></textarea>
                    <p class="form-hint">最大2000文字</p>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-lg" style="width: 100%;">
                プロフィールを更新
            </button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
