<?php
/**
 * エントリーポイント
 * MedCareer Bridge - クリニック院長就職支援サービス
 */

// エラー表示設定（開発環境用）
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 設定ファイル読み込み
require_once __DIR__ . '/../src/config/constants.php';
require_once __DIR__ . '/../src/config/database.php';
require_once __DIR__ . '/../src/helpers/validation.php';
require_once __DIR__ . '/../src/middleware/auth.php';
require_once __DIR__ . '/../src/middleware/csrf.php';

// セッション開始
Auth::startSession();

// ルーティング
$page = $_GET['page'] ?? 'home';
$action = $_GET['action'] ?? 'index';

// ページマッピング
$routes = [
    // 共通ページ
    'home' => 'common/home.php',
    'login' => 'common/login.php',
    'register' => 'common/register.php',
    'register/doctor' => 'common/register_doctor.php',
    'register/clinic' => 'common/register_clinic.php',
    'logout' => 'common/logout.php',
    'terms' => 'common/terms.php',
    'privacy' => 'common/privacy.php',

    // 医師向けページ
    'doctor/dashboard' => 'doctor/dashboard.php',
    'doctor/profile' => 'doctor/profile.php',
    'doctor/jobs' => 'doctor/jobs.php',
    'doctor/job' => 'doctor/job_detail.php',
    'doctor/applications' => 'doctor/applications.php',
    'doctor/application' => 'doctor/application_detail.php',
    'doctor/favorites' => 'doctor/favorites.php',

    // 法人向けページ
    'clinic/dashboard' => 'clinic/dashboard.php',
    'clinic/profile' => 'clinic/profile.php',
    'clinic/jobs' => 'clinic/jobs.php',
    'clinic/job/create' => 'clinic/job_create.php',
    'clinic/job/edit' => 'clinic/job_edit.php',
    'clinic/applications' => 'clinic/applications.php',
    'clinic/application' => 'clinic/application_detail.php',

    // 管理者向けページ
    'admin/dashboard' => 'admin/dashboard.php',
    'admin/doctors' => 'admin/doctors.php',
    'admin/doctor' => 'admin/doctor_detail.php',
    'admin/clinics' => 'admin/clinics.php',
    'admin/clinic' => 'admin/clinic_detail.php',
    'admin/jobs' => 'admin/jobs.php',
    'admin/job' => 'admin/job_detail.php',
    'admin/applications' => 'admin/applications.php',
];

// ビューファイルのパス取得
$viewPath = $routes[$page] ?? null;

if ($viewPath === null) {
    http_response_code(404);
    $viewPath = 'common/404.php';
}

// アクセス制御
$protectedRoutes = [
    'doctor/' => ROLE_DOCTOR,
    'clinic/' => ROLE_CLINIC,
    'admin/' => ROLE_ADMIN
];

foreach ($protectedRoutes as $prefix => $requiredRole) {
    if (strpos($page, $prefix) === 0) {
        Auth::requireRole($requiredRole);
        break;
    }
}

// ビュー読み込み
$fullPath = __DIR__ . '/../src/views/' . $viewPath;
if (file_exists($fullPath)) {
    require_once $fullPath;
} else {
    http_response_code(500);
    echo 'ページが見つかりません';
}
