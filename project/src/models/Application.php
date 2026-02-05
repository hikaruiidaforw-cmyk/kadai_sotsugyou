<?php
/**
 * Applicationモデル（応募情報）
 */

require_once __DIR__ . '/../config/database.php';

class Application {
    private PDO $db;

    public function __construct() {
        $this->db = getDBConnection();
    }

    /**
     * 応募作成
     */
    public function create(array $data): int {
        $sql = "INSERT INTO applications (job_id, doctor_id, status, cover_letter, applied_at, created_at)
                VALUES (:job_id, :doctor_id, 'applied', :cover_letter, NOW(), NOW())";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'job_id' => $data['job_id'],
            'doctor_id' => $data['doctor_id'],
            'cover_letter' => $data['cover_letter'] ?? null
        ]);

        return (int)$this->db->lastInsertId();
    }

    /**
     * IDで応募取得
     */
    public function findById(int $id): ?array {
        $sql = "SELECT a.*,
                j.title as job_title, j.facility_name, j.prefecture as job_prefecture,
                d.last_name, d.first_name, d.profile_photo,
                c.corp_name, c.id as clinic_id
                FROM applications a
                JOIN jobs j ON a.job_id = j.id
                JOIN doctors d ON a.doctor_id = d.id
                JOIN clinics c ON j.clinic_id = c.id
                WHERE a.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * 応募済みチェック
     */
    public function hasApplied(int $jobId, int $doctorId): bool {
        $sql = "SELECT COUNT(*) FROM applications WHERE job_id = :job_id AND doctor_id = :doctor_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['job_id' => $jobId, 'doctor_id' => $doctorId]);
        return (int)$stmt->fetchColumn() > 0;
    }

    /**
     * ステータス更新
     */
    public function updateStatus(int $id, string $status, string $memo = null): bool {
        $sql = "UPDATE applications SET status = :status, status_changed_at = NOW(), updated_at = NOW()";
        $params = ['id' => $id, 'status' => $status];

        if ($memo !== null) {
            $sql .= ", clinic_memo = :memo";
            $params['memo'] = $memo;
        }

        $sql .= " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * 医師の応募一覧取得
     */
    public function getByDoctorId(int $doctorId, int $page = 1, int $perPage = ITEMS_PER_PAGE): array {
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT a.*,
                j.title as job_title, j.facility_name, j.prefecture as job_prefecture,
                j.salary_min, j.salary_max,
                c.corp_name, c.logo_image as clinic_logo,
                (SELECT COUNT(*) FROM messages m WHERE m.application_id = a.id AND m.is_read = 0 AND m.sender_user_id != (SELECT user_id FROM doctors WHERE id = :doctor_id2)) as unread_count
                FROM applications a
                JOIN jobs j ON a.job_id = j.id
                JOIN clinics c ON j.clinic_id = c.id
                WHERE a.doctor_id = :doctor_id
                ORDER BY a.applied_at DESC
                LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue('doctor_id', $doctorId, PDO::PARAM_INT);
        $stmt->bindValue('doctor_id2', $doctorId, PDO::PARAM_INT);
        $stmt->bindValue('limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue('offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * 法人の応募者一覧取得
     */
    public function getByClinicId(int $clinicId, int $page = 1, int $perPage = ITEMS_PER_PAGE, array $filters = []): array {
        $offset = ($page - 1) * $perPage;
        $params = [];

        $sql = "SELECT a.*,
                j.title as job_title, j.facility_name,
                d.last_name, d.first_name, d.profile_photo, d.id as doctor_id,
                (SELECT user_id FROM clinics WHERE id = :clinic_id2) as clinic_user_id,
                (SELECT COUNT(*) FROM messages m WHERE m.application_id = a.id AND m.is_read = 0 AND m.sender_user_id = (SELECT user_id FROM doctors WHERE id = d.id)) as unread_count
                FROM applications a
                JOIN jobs j ON a.job_id = j.id
                JOIN doctors d ON a.doctor_id = d.id
                WHERE j.clinic_id = :clinic_id";

        if (!empty($filters['status'])) {
            $sql .= " AND a.status = :status";
            $params['status'] = $filters['status'];
        }
        if (!empty($filters['job_id'])) {
            $sql .= " AND a.job_id = :job_id";
            $params['job_id'] = $filters['job_id'];
        }

        $sql .= " ORDER BY a.applied_at DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue('clinic_id', $clinicId, PDO::PARAM_INT);
        $stmt->bindValue('clinic_id2', $clinicId, PDO::PARAM_INT);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue('limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue('offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * 応募総数取得（医師）
     */
    public function countByDoctorId(int $doctorId): int {
        $sql = "SELECT COUNT(*) FROM applications WHERE doctor_id = :doctor_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['doctor_id' => $doctorId]);
        return (int)$stmt->fetchColumn();
    }

    /**
     * 応募総数取得（法人）
     */
    public function countByClinicId(int $clinicId, array $filters = []): int {
        $sql = "SELECT COUNT(*) FROM applications a
                JOIN jobs j ON a.job_id = j.id
                WHERE j.clinic_id = :clinic_id";
        $params = ['clinic_id' => $clinicId];

        if (!empty($filters['status'])) {
            $sql .= " AND a.status = :status";
            $params['status'] = $filters['status'];
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    /**
     * 全応募一覧取得（管理者用）
     */
    public function getAll(int $page = 1, int $perPage = ITEMS_PER_PAGE, array $filters = []): array {
        $offset = ($page - 1) * $perPage;
        $params = [];

        $sql = "SELECT a.*,
                j.title as job_title, j.facility_name,
                d.last_name, d.first_name,
                c.corp_name
                FROM applications a
                JOIN jobs j ON a.job_id = j.id
                JOIN doctors d ON a.doctor_id = d.id
                JOIN clinics c ON j.clinic_id = c.id
                WHERE 1=1";

        if (!empty($filters['status'])) {
            $sql .= " AND a.status = :status";
            $params['status'] = $filters['status'];
        }

        $sql .= " ORDER BY a.applied_at DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue('limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue('offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
