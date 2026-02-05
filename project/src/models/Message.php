<?php
/**
 * Messageモデル
 */

require_once __DIR__ . '/../config/database.php';

class Message {
    private PDO $db;

    public function __construct() {
        $this->db = getDBConnection();
    }

    /**
     * メッセージ送信
     */
    public function create(array $data): int {
        $sql = "INSERT INTO messages (application_id, sender_user_id, body, attachment_file, created_at)
                VALUES (:application_id, :sender_user_id, :body, :attachment_file, NOW())";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'application_id' => $data['application_id'],
            'sender_user_id' => $data['sender_user_id'],
            'body' => $data['body'],
            'attachment_file' => $data['attachment_file'] ?? null
        ]);

        return (int)$this->db->lastInsertId();
    }

    /**
     * 応募のメッセージ一覧取得
     */
    public function getByApplicationId(int $applicationId): array {
        $sql = "SELECT m.*,
                u.email as sender_email, u.role as sender_role,
                CASE
                    WHEN u.role = 'doctor' THEN CONCAT(d.last_name, ' ', d.first_name)
                    WHEN u.role = 'clinic' THEN c.contact_person_name
                    ELSE '管理者'
                END as sender_name,
                CASE
                    WHEN u.role = 'doctor' THEN d.profile_photo
                    WHEN u.role = 'clinic' THEN c.logo_image
                    ELSE NULL
                END as sender_photo
                FROM messages m
                JOIN users u ON m.sender_user_id = u.id
                LEFT JOIN doctors d ON u.id = d.user_id
                LEFT JOIN clinics c ON u.id = c.user_id
                WHERE m.application_id = :application_id
                ORDER BY m.created_at ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['application_id' => $applicationId]);
        return $stmt->fetchAll();
    }

    /**
     * メッセージを既読にする
     */
    public function markAsRead(int $applicationId, int $userId): bool {
        $sql = "UPDATE messages
                SET is_read = 1, read_at = NOW()
                WHERE application_id = :application_id
                AND sender_user_id != :user_id
                AND is_read = 0";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'application_id' => $applicationId,
            'user_id' => $userId
        ]);
    }

    /**
     * 未読メッセージ数取得
     */
    public function getUnreadCount(int $userId): int {
        $sql = "SELECT COUNT(*) FROM messages m
                JOIN applications a ON m.application_id = a.id
                JOIN jobs j ON a.job_id = j.id
                JOIN doctors d ON a.doctor_id = d.id
                JOIN clinics c ON j.clinic_id = c.id
                WHERE m.is_read = 0
                AND m.sender_user_id != :user_id
                AND (d.user_id = :user_id2 OR c.user_id = :user_id3)";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'user_id' => $userId,
            'user_id2' => $userId,
            'user_id3' => $userId
        ]);
        return (int)$stmt->fetchColumn();
    }

    /**
     * 最新メッセージ取得
     */
    public function getLatestByApplicationId(int $applicationId): ?array {
        $sql = "SELECT * FROM messages
                WHERE application_id = :application_id
                ORDER BY created_at DESC
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['application_id' => $applicationId]);
        $result = $stmt->fetch();
        return $result ?: null;
    }
}
