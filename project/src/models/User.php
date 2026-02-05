<?php
/**
 * Userモデル
 */

require_once __DIR__ . '/../config/database.php';

class User {
    private PDO $db;

    public function __construct() {
        $this->db = getDBConnection();
    }

    /**
     * ユーザー登録
     */
    public function create(array $data): int {
        $sql = "INSERT INTO users (email, password, role, status, created_at)
                VALUES (:email, :password, :role, :status, NOW())";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'email' => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'role' => $data['role'],
            'status' => $data['status'] ?? STATUS_PENDING
        ]);

        return (int)$this->db->lastInsertId();
    }

    /**
     * IDでユーザー取得
     */
    public function findById(int $id): ?array {
        $sql = "SELECT * FROM users WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * メールアドレスでユーザー取得
     */
    public function findByEmail(string $email): ?array {
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['email' => $email]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * メールアドレスの存在チェック
     */
    public function emailExists(string $email): bool {
        $sql = "SELECT COUNT(*) FROM users WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['email' => $email]);
        return (int)$stmt->fetchColumn() > 0;
    }

    /**
     * パスワード検証
     */
    public function verifyPassword(string $password, string $hash): bool {
        return password_verify($password, $hash);
    }

    /**
     * ステータス更新
     */
    public function updateStatus(int $id, string $status): bool {
        $sql = "UPDATE users SET status = :status, updated_at = NOW() WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id, 'status' => $status]);
    }

    /**
     * メール認証済みに更新
     */
    public function markEmailVerified(int $id): bool {
        $sql = "UPDATE users SET email_verified_at = NOW(), updated_at = NOW() WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    /**
     * パスワード更新
     */
    public function updatePassword(int $id, string $password): bool {
        $sql = "UPDATE users SET password = :password, updated_at = NOW() WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'password' => password_hash($password, PASSWORD_DEFAULT)
        ]);
    }

    /**
     * リメンバートークン保存
     */
    public function saveRememberToken(int $id, string $token): bool {
        $sql = "UPDATE users SET remember_token = :token, updated_at = NOW() WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id, 'token' => $token]);
    }

    /**
     * ユーザー一覧取得（管理者用）
     */
    public function getAll(string $role = null, int $page = 1, int $perPage = ITEMS_PER_PAGE): array {
        $offset = ($page - 1) * $perPage;
        $params = [];

        $sql = "SELECT * FROM users";
        if ($role) {
            $sql .= " WHERE role = :role";
            $params['role'] = $role;
        }
        $sql .= " ORDER BY created_at DESC LIMIT :limit OFFSET :offset";

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
     * ユーザー総数取得
     */
    public function count(string $role = null, string $status = null): int {
        $sql = "SELECT COUNT(*) FROM users WHERE 1=1";
        $params = [];

        if ($role) {
            $sql .= " AND role = :role";
            $params['role'] = $role;
        }
        if ($status) {
            $sql .= " AND status = :status";
            $params['status'] = $status;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }
}
