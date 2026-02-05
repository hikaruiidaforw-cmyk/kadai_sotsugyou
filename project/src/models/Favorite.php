<?php
/**
 * Favoriteモデル（お気に入り）
 */

require_once __DIR__ . '/../config/database.php';

class Favorite {
    private PDO $db;

    public function __construct() {
        $this->db = getDBConnection();
    }

    /**
     * お気に入り追加
     */
    public function add(int $doctorId, int $jobId): bool {
        $sql = "INSERT IGNORE INTO favorites (doctor_id, job_id, created_at)
                VALUES (:doctor_id, :job_id, NOW())";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'doctor_id' => $doctorId,
            'job_id' => $jobId
        ]);
    }

    /**
     * お気に入り削除
     */
    public function remove(int $doctorId, int $jobId): bool {
        $sql = "DELETE FROM favorites WHERE doctor_id = :doctor_id AND job_id = :job_id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'doctor_id' => $doctorId,
            'job_id' => $jobId
        ]);
    }

    /**
     * お気に入りチェック
     */
    public function isFavorite(int $doctorId, int $jobId): bool {
        $sql = "SELECT COUNT(*) FROM favorites WHERE doctor_id = :doctor_id AND job_id = :job_id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'doctor_id' => $doctorId,
            'job_id' => $jobId
        ]);
        return (int)$stmt->fetchColumn() > 0;
    }

    /**
     * 医師のお気に入り一覧取得
     */
    public function getByDoctorId(int $doctorId, int $page = 1, int $perPage = ITEMS_PER_PAGE): array {
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT f.*, j.*, c.corp_name, c.logo_image as clinic_logo,
                GROUP_CONCAT(DISTINCT s.name SEPARATOR ', ') as specialty_names
                FROM favorites f
                JOIN jobs j ON f.job_id = j.id
                JOIN clinics c ON j.clinic_id = c.id
                LEFT JOIN job_specialties js ON j.id = js.job_id
                LEFT JOIN specialties s ON js.specialty_id = s.id
                WHERE f.doctor_id = :doctor_id AND j.status = 'published'
                GROUP BY f.id
                ORDER BY f.created_at DESC
                LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue('doctor_id', $doctorId, PDO::PARAM_INT);
        $stmt->bindValue('limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue('offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * お気に入り総数取得
     */
    public function countByDoctorId(int $doctorId): int {
        $sql = "SELECT COUNT(*) FROM favorites f
                JOIN jobs j ON f.job_id = j.id
                WHERE f.doctor_id = :doctor_id AND j.status = 'published'";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['doctor_id' => $doctorId]);
        return (int)$stmt->fetchColumn();
    }
}
