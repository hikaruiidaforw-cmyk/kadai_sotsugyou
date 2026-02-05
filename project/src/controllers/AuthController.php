<?php
/**
 * 認証コントローラー
 */

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Doctor.php';
require_once __DIR__ . '/../models/Clinic.php';

class AuthController {
    private User $userModel;
    private Doctor $doctorModel;
    private Clinic $clinicModel;

    public function __construct() {
        $this->userModel = new User();
        $this->doctorModel = new Doctor();
        $this->clinicModel = new Clinic();
    }

    /**
     * ログイン処理
     */
    public function login(array $data): array {
        $validator = new Validator($data);
        $validator
            ->required('email', 'メールアドレス')
            ->email('email', 'メールアドレス')
            ->required('password', 'パスワード');

        if ($validator->fails()) {
            return ['success' => false, 'errors' => $validator->getErrors()];
        }

        $user = $this->userModel->findByEmail($data['email']);

        if (!$user || !$this->userModel->verifyPassword($data['password'], $user['password'])) {
            return ['success' => false, 'errors' => ['email' => ['メールアドレスまたはパスワードが正しくありません']]];
        }

        if ($user['status'] === STATUS_PENDING) {
            return ['success' => false, 'errors' => ['email' => ['アカウントは承認待ちです']]];
        }

        if ($user['status'] === STATUS_SUSPENDED) {
            return ['success' => false, 'errors' => ['email' => ['このアカウントは停止されています']]];
        }

        // ログイン名取得
        $name = '';
        if ($user['role'] === ROLE_DOCTOR) {
            $doctor = $this->doctorModel->findByUserId($user['id']);
            $name = $doctor ? $doctor['last_name'] . ' ' . $doctor['first_name'] : '';
        } elseif ($user['role'] === ROLE_CLINIC) {
            $clinic = $this->clinicModel->findByUserId($user['id']);
            $name = $clinic ? $clinic['corp_name'] : '';
        } else {
            $name = '管理者';
        }

        Auth::login([
            'id' => $user['id'],
            'email' => $user['email'],
            'role' => $user['role'],
            'name' => $name
        ]);

        $redirect = match($user['role']) {
            ROLE_DOCTOR => '?page=doctor/dashboard',
            ROLE_CLINIC => '?page=clinic/dashboard',
            ROLE_ADMIN => '?page=admin/dashboard',
            default => '?page=home'
        };

        return ['success' => true, 'redirect' => $redirect];
    }

    /**
     * 医師登録処理
     */
    public function registerDoctor(array $data): array {
        $validator = new Validator($data);
        $validator
            ->required('email', 'メールアドレス')
            ->email('email', 'メールアドレス')
            ->required('password', 'パスワード')
            ->password('password', 'パスワード')
            ->passwordConfirm('password', 'password_confirm', 'パスワード確認')
            ->required('last_name', '姓')
            ->required('first_name', '名')
            ->required('last_name_kana', '姓（カナ）')
            ->required('first_name_kana', '名（カナ）')
            ->required('birth_date', '生年月日')
            ->date('birth_date', '生年月日')
            ->required('gender', '性別')
            ->required('phone', '電話番号')
            ->phone('phone', '電話番号')
            ->required('license_number', '医師免許番号')
            ->required('license_date', '医師免許取得日')
            ->date('license_date', '医師免許取得日');

        if ($validator->fails()) {
            return ['success' => false, 'errors' => $validator->getErrors()];
        }

        if ($this->userModel->emailExists($data['email'])) {
            return ['success' => false, 'errors' => ['email' => ['このメールアドレスは既に登録されています']]];
        }

        try {
            $pdo = getDBConnection();
            $pdo->beginTransaction();

            // ユーザー作成
            $userId = $this->userModel->create([
                'email' => $data['email'],
                'password' => $data['password'],
                'role' => ROLE_DOCTOR,
                'status' => STATUS_PENDING
            ]);

            // 医師情報作成
            $this->doctorModel->create([
                'user_id' => $userId,
                'last_name' => $data['last_name'],
                'first_name' => $data['first_name'],
                'last_name_kana' => $data['last_name_kana'],
                'first_name_kana' => $data['first_name_kana'],
                'birth_date' => $data['birth_date'],
                'gender' => $data['gender'],
                'phone' => $data['phone'],
                'postal_code' => $data['postal_code'] ?? null,
                'prefecture' => $data['prefecture'] ?? null,
                'address' => $data['address'] ?? null,
                'license_number' => $data['license_number'],
                'license_date' => $data['license_date']
            ]);

            $pdo->commit();

            return ['success' => true, 'message' => '登録が完了しました。管理者の承認後、ログインが可能になります。'];

        } catch (Exception $e) {
            $pdo->rollBack();
            error_log('Doctor registration error: ' . $e->getMessage());
            return ['success' => false, 'errors' => ['general' => ['登録処理中にエラーが発生しました']]];
        }
    }

    /**
     * 法人登録処理
     */
    public function registerClinic(array $data): array {
        $validator = new Validator($data);
        $validator
            ->required('email', 'メールアドレス')
            ->email('email', 'メールアドレス')
            ->required('password', 'パスワード')
            ->password('password', 'パスワード')
            ->passwordConfirm('password', 'password_confirm', 'パスワード確認')
            ->required('corp_name', '法人名')
            ->required('corp_number', '法人番号')
            ->required('representative_name', '代表者名')
            ->required('postal_code', '郵便番号')
            ->postalCode('postal_code', '郵便番号')
            ->required('prefecture', '都道府県')
            ->required('address', '住所')
            ->required('phone', '電話番号')
            ->phone('phone', '電話番号')
            ->required('contact_person_name', '担当者名')
            ->required('contact_person_email', '担当者メールアドレス')
            ->email('contact_person_email', '担当者メールアドレス');

        if ($validator->fails()) {
            return ['success' => false, 'errors' => $validator->getErrors()];
        }

        if ($this->userModel->emailExists($data['email'])) {
            return ['success' => false, 'errors' => ['email' => ['このメールアドレスは既に登録されています']]];
        }

        try {
            $pdo = getDBConnection();
            $pdo->beginTransaction();

            // ユーザー作成
            $userId = $this->userModel->create([
                'email' => $data['email'],
                'password' => $data['password'],
                'role' => ROLE_CLINIC,
                'status' => STATUS_PENDING
            ]);

            // 法人情報作成
            $this->clinicModel->create([
                'user_id' => $userId,
                'corp_name' => $data['corp_name'],
                'corp_number' => $data['corp_number'],
                'representative_name' => $data['representative_name'],
                'established_date' => $data['established_date'] ?? null,
                'postal_code' => $data['postal_code'],
                'prefecture' => $data['prefecture'],
                'address' => $data['address'],
                'phone' => $data['phone'],
                'website_url' => $data['website_url'] ?? null,
                'business_description' => $data['business_description'] ?? null,
                'facility_count' => $data['facility_count'] ?? 1,
                'contact_person_name' => $data['contact_person_name'],
                'contact_person_position' => $data['contact_person_position'] ?? null,
                'contact_person_email' => $data['contact_person_email'],
                'contact_person_phone' => $data['contact_person_phone'] ?? null
            ]);

            $pdo->commit();

            return ['success' => true, 'message' => '登録が完了しました。管理者の承認後、ログインが可能になります。'];

        } catch (Exception $e) {
            $pdo->rollBack();
            error_log('Clinic registration error: ' . $e->getMessage());
            return ['success' => false, 'errors' => ['general' => ['登録処理中にエラーが発生しました']]];
        }
    }
}
