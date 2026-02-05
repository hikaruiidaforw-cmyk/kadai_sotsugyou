<?php
/**
 * Clinicモデル（医療法人）
 */

require_once __DIR__ . '/../config/database.php';

class Clinic {
    private PDO $db;

    public function __construct() {
        $this->db = getDBConnection();
    }

    /**
     * 法人情報作成
     */
    public function create(array $data): int {
        $sql = "INSERT INTO clinics (
            user_id, corp_name, corp_number, representative_name, established_date,
            postal_code, prefecture, address, phone, website_url,
            business_description, facility_count, employee_count, annual_revenue,
            introduction, contact_person_name, contact_person_position,
            contact_person_email, contact_person_phone, created_at
        ) VALUES (
            :user_id, :corp_name, :corp_number, :representative_name, :established_date,
            :postal_code, :prefecture, :address, :phone, :website_url,
            :business_description, :facility_count, :employee_count, :annual_revenue,
            :introduction, :contact_person_name, :contact_person_position,
            :contact_person_email, :contact_person_phone, NOW()
        )";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'user_id' => $data['user_id'],
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
            'employee_count' => $data['employee_count'] ?? null,
            'annual_revenue' => $data['annual_revenue'] ?? null,
            'introduction' => $data['introduction'] ?? null,
            'contact_person_name' => $data['contact_person_name'],
            'contact_person_position' => $data['contact_person_position'] ?? null,
            'contact_person_email' => $data['contact_person_email'],
            'contact_person_phone' => $data['contact_person_phone'] ?? null
        ]);

        return (int)$this->db->lastInsertId();
    }

    /**
     * IDで法人情報取得
     */
    public function findById(int $id): ?array {
        $sql = "SELECT c.*, u.email, u.status as user_status
                FROM clinics c
                JOIN users u ON c.user_id = u.id
                WHERE c.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * ユーザーIDで法人情報取得
     */
    public function findByUserId(int $userId): ?array {
        $sql = "SELECT c.*, u.email, u.status as user_status
                FROM clinics c
                JOIN users u ON c.user_id = u.id
                WHERE c.user_id = :user_id";
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
            'corp_name', 'corp_number', 'representative_name', 'established_date',
            'postal_code', 'prefecture', 'address', 'phone', 'website_url',
            'business_description', 'facility_count', 'employee_count', 'annual_revenue',
            'introduction', 'logo_image', 'contact_person_name', 'contact_person_position',
            'contact_person_email', 'contact_person_phone'
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

        $sql = "UPDATE clinics SET " . implode(', ', $fields) . ", updated_at = NOW() WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * 法人一覧取得（管理者用）
     */
    public function getAll(int $page = 1, int $perPage = ITEMS_PER_PAGE, array $filters = []): array {
        $offset = ($page - 1) * $perPage;
        $params = [];

        $sql = "SELECT c.*, u.email, u.status as user_status
                FROM clinics c
                JOIN users u ON c.user_id = u.id
                WHERE 1=1";

        if (!empty($filters['status'])) {
            $sql .= " AND u.status = :status";
            $params['status'] = $filters['status'];
        }
        if (!empty($filters['prefecture'])) {
            $sql .= " AND c.prefecture = :prefecture";
            $params['prefecture'] = $filters['prefecture'];
        }

        $sql .= " ORDER BY c.created_at DESC LIMIT :limit OFFSET :offset";

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
     * 法人総数取得
     */
    public function count(array $filters = []): int {
        $sql = "SELECT COUNT(*) FROM clinics c JOIN users u ON c.user_id = u.id WHERE 1=1";
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
