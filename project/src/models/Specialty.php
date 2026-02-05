<?php
/**
 * Specialtyモデル（診療科目マスタ）
 */

require_once __DIR__ . '/../config/database.php';

class Specialty {
    private PDO $db;

    public function __construct() {
        $this->db = getDBConnection();
    }

    /**
     * 全診療科目取得
     */
    public function getAll(bool $activeOnly = true): array {
        $sql = "SELECT * FROM specialties";
        if ($activeOnly) {
            $sql .= " WHERE is_active = 1";
        }
        $sql .= " ORDER BY sort_order, id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * IDで取得
     */
    public function findById(int $id): ?array {
        $sql = "SELECT * FROM specialties WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * 診療科目作成
     */
    public function create(array $data): int {
        $sql = "INSERT INTO specialties (name, sort_order, is_active, created_at)
                VALUES (:name, :sort_order, :is_active, NOW())";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'name' => $data['name'],
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $data['is_active'] ?? 1
        ]);

        return (int)$this->db->lastInsertId();
    }

    /**
     * 診療科目更新
     */
    public function update(int $id, array $data): bool {
        $fields = [];
        $params = ['id' => $id];

        if (isset($data['name'])) {
            $fields[] = "name = :name";
            $params['name'] = $data['name'];
        }
        if (isset($data['sort_order'])) {
            $fields[] = "sort_order = :sort_order";
            $params['sort_order'] = $data['sort_order'];
        }
        if (isset($data['is_active'])) {
            $fields[] = "is_active = :is_active";
            $params['is_active'] = $data['is_active'];
        }

        if (empty($fields)) {
            return false;
        }

        $sql = "UPDATE specialties SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }
}
