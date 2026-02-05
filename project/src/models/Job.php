<?php
/**
 * Jobモデル（求人情報）
 */

require_once __DIR__ . '/../config/database.php';

class Job {
    private PDO $db;

    public function __construct() {
        $this->db = getDBConnection();
    }

    /**
     * 求人作成
     */
    public function create(array $data): int {
        $sql = "INSERT INTO jobs (
            clinic_id, title, facility_name, postal_code, prefecture, address,
            description, work_hours, salary_min, salary_max, salary_description,
            benefits, requirements, recruitment_count, application_deadline,
            transfer_min_tenure_months, transfer_performance_target, transfer_price_type,
            transfer_price_fixed, transfer_price_formula, transfer_scope,
            transfer_option_deadline, transfer_other_conditions, status, created_at
        ) VALUES (
            :clinic_id, :title, :facility_name, :postal_code, :prefecture, :address,
            :description, :work_hours, :salary_min, :salary_max, :salary_description,
            :benefits, :requirements, :recruitment_count, :application_deadline,
            :transfer_min_tenure_months, :transfer_performance_target, :transfer_price_type,
            :transfer_price_fixed, :transfer_price_formula, :transfer_scope,
            :transfer_option_deadline, :transfer_other_conditions, :status, NOW()
        )";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'clinic_id' => $data['clinic_id'],
            'title' => $data['title'],
            'facility_name' => $data['facility_name'],
            'postal_code' => $data['postal_code'],
            'prefecture' => $data['prefecture'],
            'address' => $data['address'],
            'description' => $data['description'],
            'work_hours' => $data['work_hours'],
            'salary_min' => $data['salary_min'] ?? null,
            'salary_max' => $data['salary_max'] ?? null,
            'salary_description' => $data['salary_description'] ?? null,
            'benefits' => $data['benefits'] ?? null,
            'requirements' => $data['requirements'] ?? null,
            'recruitment_count' => $data['recruitment_count'] ?? 1,
            'application_deadline' => $data['application_deadline'] ?? null,
            'transfer_min_tenure_months' => $data['transfer_min_tenure_months'],
            'transfer_performance_target' => $data['transfer_performance_target'] ?? null,
            'transfer_price_type' => $data['transfer_price_type'],
            'transfer_price_fixed' => $data['transfer_price_fixed'] ?? null,
            'transfer_price_formula' => $data['transfer_price_formula'] ?? null,
            'transfer_scope' => $data['transfer_scope'],
            'transfer_option_deadline' => $data['transfer_option_deadline'] ?? null,
            'transfer_other_conditions' => $data['transfer_other_conditions'] ?? null,
            'status' => $data['status'] ?? 'draft'
        ]);

        return (int)$this->db->lastInsertId();
    }

    /**
     * IDで求人取得
     */
    public function findById(int $id): ?array {
        $sql = "SELECT j.*, c.corp_name, c.logo_image as clinic_logo
                FROM jobs j
                JOIN clinics c ON j.clinic_id = c.id
                WHERE j.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * 求人更新
     */
    public function update(int $id, array $data): bool {
        $fields = [];
        $params = ['id' => $id];

        $allowedFields = [
            'title', 'facility_name', 'postal_code', 'prefecture', 'address',
            'description', 'work_hours', 'salary_min', 'salary_max', 'salary_description',
            'benefits', 'requirements', 'recruitment_count', 'application_deadline',
            'transfer_min_tenure_months', 'transfer_performance_target', 'transfer_price_type',
            'transfer_price_fixed', 'transfer_price_formula', 'transfer_scope',
            'transfer_option_deadline', 'transfer_other_conditions', 'status', 'published_at'
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

        $sql = "UPDATE jobs SET " . implode(', ', $fields) . ", updated_at = NOW() WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * 診療科目を設定
     */
    public function setSpecialties(int $jobId, array $specialtyIds): bool {
        $sql = "DELETE FROM job_specialties WHERE job_id = :job_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['job_id' => $jobId]);

        $sql = "INSERT INTO job_specialties (job_id, specialty_id, created_at) VALUES (:job_id, :specialty_id, NOW())";
        $stmt = $this->db->prepare($sql);

        foreach ($specialtyIds as $specialtyId) {
            $stmt->execute([
                'job_id' => $jobId,
                'specialty_id' => $specialtyId
            ]);
        }

        return true;
    }

    /**
     * 診療科目取得
     */
    public function getSpecialties(int $jobId): array {
        $sql = "SELECT s.* FROM job_specialties js
                JOIN specialties s ON js.specialty_id = s.id
                WHERE js.job_id = :job_id
                ORDER BY s.sort_order";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['job_id' => $jobId]);
        return $stmt->fetchAll();
    }

    /**
     * 求人検索
     */
    public function search(array $filters = [], int $page = 1, int $perPage = ITEMS_PER_PAGE): array {
        $offset = ($page - 1) * $perPage;
        $params = [];

        $sql = "SELECT DISTINCT j.*, c.corp_name, c.logo_image as clinic_logo,
                GROUP_CONCAT(DISTINCT s.name SEPARATOR ', ') as specialty_names
                FROM jobs j
                JOIN clinics c ON j.clinic_id = c.id
                LEFT JOIN job_specialties js ON j.id = js.job_id
                LEFT JOIN specialties s ON js.specialty_id = s.id
                WHERE j.status = 'published'";

        if (!empty($filters['prefecture'])) {
            $sql .= " AND j.prefecture = :prefecture";
            $params['prefecture'] = $filters['prefecture'];
        }
        if (!empty($filters['specialty_id'])) {
            $sql .= " AND js.specialty_id = :specialty_id";
            $params['specialty_id'] = $filters['specialty_id'];
        }
        if (!empty($filters['salary_min'])) {
            $sql .= " AND (j.salary_max >= :salary_min OR j.salary_max IS NULL)";
            $params['salary_min'] = $filters['salary_min'];
        }
        if (!empty($filters['salary_max'])) {
            $sql .= " AND (j.salary_min <= :salary_max OR j.salary_min IS NULL)";
            $params['salary_max'] = $filters['salary_max'];
        }
        if (!empty($filters['transfer_price_min'])) {
            $sql .= " AND (j.transfer_price_fixed >= :transfer_price_min OR j.transfer_price_fixed IS NULL)";
            $params['transfer_price_min'] = $filters['transfer_price_min'];
        }
        if (!empty($filters['transfer_price_max'])) {
            $sql .= " AND (j.transfer_price_fixed <= :transfer_price_max OR j.transfer_price_fixed IS NULL)";
            $params['transfer_price_max'] = $filters['transfer_price_max'];
        }
        if (!empty($filters['keyword'])) {
            $sql .= " AND (j.title LIKE :keyword OR j.description LIKE :keyword OR j.facility_name LIKE :keyword)";
            $params['keyword'] = '%' . $filters['keyword'] . '%';
        }

        $sql .= " GROUP BY j.id ORDER BY j.published_at DESC LIMIT :limit OFFSET :offset";

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
     * 検索結果総数取得
     */
    public function searchCount(array $filters = []): int {
        $params = [];

        $sql = "SELECT COUNT(DISTINCT j.id) FROM jobs j
                LEFT JOIN job_specialties js ON j.id = js.job_id
                WHERE j.status = 'published'";

        if (!empty($filters['prefecture'])) {
            $sql .= " AND j.prefecture = :prefecture";
            $params['prefecture'] = $filters['prefecture'];
        }
        if (!empty($filters['specialty_id'])) {
            $sql .= " AND js.specialty_id = :specialty_id";
            $params['specialty_id'] = $filters['specialty_id'];
        }
        if (!empty($filters['keyword'])) {
            $sql .= " AND (j.title LIKE :keyword OR j.description LIKE :keyword)";
            $params['keyword'] = '%' . $filters['keyword'] . '%';
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    /**
     * 法人の求人一覧取得
     */
    public function getByClinicId(int $clinicId, int $page = 1, int $perPage = ITEMS_PER_PAGE): array {
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT j.*,
                (SELECT COUNT(*) FROM applications a WHERE a.job_id = j.id) as application_count
                FROM jobs j
                WHERE j.clinic_id = :clinic_id
                ORDER BY j.created_at DESC
                LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue('clinic_id', $clinicId, PDO::PARAM_INT);
        $stmt->bindValue('limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue('offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * 新着求人取得
     */
    public function getLatest(int $limit = 10): array {
        $sql = "SELECT j.*, c.corp_name, c.logo_image as clinic_logo,
                GROUP_CONCAT(DISTINCT s.name SEPARATOR ', ') as specialty_names
                FROM jobs j
                JOIN clinics c ON j.clinic_id = c.id
                LEFT JOIN job_specialties js ON j.id = js.job_id
                LEFT JOIN specialties s ON js.specialty_id = s.id
                WHERE j.status = 'published'
                GROUP BY j.id
                ORDER BY j.published_at DESC
                LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * 削除
     */
    public function delete(int $id): bool {
        $sql = "DELETE FROM jobs WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
}
