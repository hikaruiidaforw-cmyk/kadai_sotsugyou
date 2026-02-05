<?php
/**
 * Doctorモデル
 */

require_once __DIR__ . '/../config/database.php';

class Doctor {
    private PDO $db;

    public function __construct() {
        $this->db = getDBConnection();
    }

    /**
     * 医師情報作成
     */
    public function create(array $data): int {
        $sql = "INSERT INTO doctors (
            user_id, last_name, first_name, last_name_kana, first_name_kana,
            birth_date, gender, phone, postal_code, prefecture, address,
            license_number, license_date, created_at
        ) VALUES (
            :user_id, :last_name, :first_name, :last_name_kana, :first_name_kana,
            :birth_date, :gender, :phone, :postal_code, :prefecture, :address,
            :license_number, :license_date, NOW()
        )";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'user_id' => $data['user_id'],
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

        return (int)$this->db->lastInsertId();
    }

    /**
     * IDで医師情報取得
     */
    public function findById(int $id): ?array {
        $sql = "SELECT d.*, u.email, u.status as user_status
                FROM doctors d
                JOIN users u ON d.user_id = u.id
                WHERE d.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * ユーザーIDで医師情報取得
     */
    public function findByUserId(int $userId): ?array {
        $sql = "SELECT d.*, u.email, u.status as user_status
                FROM doctors d
                JOIN users u ON d.user_id = u.id
                WHERE d.user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * プロフィール更新
     */
    public function update(int $id, array $data): bool {
        $fields = [];
        $params = ['id' => $id];

        $allowedFields = [
            'last_name', 'first_name', 'last_name_kana', 'first_name_kana',
            'birth_date', 'gender', 'phone', 'postal_code', 'prefecture', 'address',
            'profile_photo', 'resume_file', 'self_introduction',
            'desired_regions', 'desired_salary_min', 'desired_salary_max', 'desired_opening_year'
        ];

        foreach ($allowedFields as $field) {
            if (array_key_exists($field, $data)) {
                $fields[] = "{$field} = :{$field}";
                $params[$field] = $data[$field];
            }
        }

        if (empty($fields)) {
            return false;
        }

        $sql = "UPDATE doctors SET " . implode(', ', $fields) . ", updated_at = NOW() WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * 専門科目を設定
     */
    public function setSpecialties(int $doctorId, array $specialtyIds, int $mainSpecialtyId = null): bool {
        // 既存の専門科目を削除
        $sql = "DELETE FROM doctor_specialties WHERE doctor_id = :doctor_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['doctor_id' => $doctorId]);

        // 新しい専門科目を追加
        $sql = "INSERT INTO doctor_specialties (doctor_id, specialty_id, is_main, created_at)
                VALUES (:doctor_id, :specialty_id, :is_main, NOW())";
        $stmt = $this->db->prepare($sql);

        foreach ($specialtyIds as $specialtyId) {
            $stmt->execute([
                'doctor_id' => $doctorId,
                'specialty_id' => $specialtyId,
                'is_main' => ($specialtyId == $mainSpecialtyId) ? 1 : 0
            ]);
        }

        return true;
    }

    /**
     * 専門科目取得
     */
    public function getSpecialties(int $doctorId): array {
        $sql = "SELECT s.*, ds.is_main
                FROM doctor_specialties ds
                JOIN specialties s ON ds.specialty_id = s.id
                WHERE ds.doctor_id = :doctor_id
                ORDER BY ds.is_main DESC, s.sort_order";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['doctor_id' => $doctorId]);
        return $stmt->fetchAll();
    }

    /**
     * 医師一覧取得（管理者用）
     */
    public function getAll(int $page = 1, int $perPage = ITEMS_PER_PAGE, array $filters = []): array {
        $offset = ($page - 1) * $perPage;
        $params = [];

        $sql = "SELECT d.*, u.email, u.status as user_status
                FROM doctors d
                JOIN users u ON d.user_id = u.id
                WHERE 1=1";

        if (!empty($filters['status'])) {
            $sql .= " AND u.status = :status";
            $params['status'] = $filters['status'];
        }
        if (!empty($filters['prefecture'])) {
            $sql .= " AND d.prefecture = :prefecture";
            $params['prefecture'] = $filters['prefecture'];
        }

        $sql .= " ORDER BY d.created_at DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue('limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue('offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * 医師総数取得
     */
    public function count(array $filters = []): int {
        $sql = "SELECT COUNT(*) FROM doctors d JOIN users u ON d.user_id = u.id WHERE 1=1";
        $params = [];

        if (!empty($filters['status'])) {
            $sql .= " AND u.status = :status";
            $params['status'] = $filters['status'];
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }
}
