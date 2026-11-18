<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class Branch
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function all(): array
    {
        $sql = "
            SELECT b.*, COUNT(s.id) AS schedule_count
            FROM branches b
            LEFT JOIN schedules s ON b.id = s.branch_id
            GROUP BY b.id
            ORDER BY b.id ASC
        ";
        return $this->db->query($sql)->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM branches WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare("INSERT INTO branches (branch_name, address) VALUES (?, ?)");
        $stmt->execute([
            trim($data['branch_name']),
            trim($data['address'])
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare("UPDATE branches SET branch_name = ?, address = ? WHERE id = ?");
        return $stmt->execute([
            trim($data['branch_name']),
            trim($data['address']),
            $id
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM branches WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
