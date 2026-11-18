<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class Teacher
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function all(): array
    {
        $sql = "
            SELECT t.*, COUNT(s.id) AS schedule_count
            FROM teachers t
            LEFT JOIN schedules s ON t.id = s.teacher_id
            GROUP BY t.id
            ORDER BY t.full_name ASC
        ";
        return $this->db->query($sql)->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM teachers WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM teachers WHERE email = ?");
        $stmt->execute([trim($email)]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $password = !empty($data['password']) 
            ? password_hash($data['password'], PASSWORD_BCRYPT) 
            : password_hash('123456', PASSWORD_BCRYPT);

        $stmt = $this->db->prepare("
            INSERT INTO teachers (full_name, email, phone, expertise, password)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            trim($data['full_name']),
            trim($data['email']),
            trim($data['phone']),
            trim($data['expertise']),
            $password
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        if (!empty($data['password'])) {
            $stmt = $this->db->prepare("
                UPDATE teachers 
                SET full_name = ?, email = ?, phone = ?, expertise = ?, password = ?
                WHERE id = ?
            ");
            return $stmt->execute([
                trim($data['full_name']),
                trim($data['email']),
                trim($data['phone']),
                trim($data['expertise']),
                password_hash($data['password'], PASSWORD_BCRYPT),
                $id
            ]);
        } else {
            $stmt = $this->db->prepare("
                UPDATE teachers 
                SET full_name = ?, email = ?, phone = ?, expertise = ?
                WHERE id = ?
            ");
            return $stmt->execute([
                trim($data['full_name']),
                trim($data['email']),
                trim($data['phone']),
                trim($data['expertise']),
                $id
            ]);
        }
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM teachers WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getSchedules(int $teacherId): array
    {
        $sql = "
            SELECT s.*, b.branch_name, b.address AS branch_address
            FROM schedules s
            JOIN branches b ON s.branch_id = b.id
            WHERE s.teacher_id = ?
            ORDER BY 
                FIELD(s.day_of_week, 'Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7', 'Chủ Nhật'),
                FIELD(s.shift_or_periods, 'Sáng (Tiết 1-3)', 'Sáng (Tiết 4-6)', 'Chiều (Tiết 7-9)', 'Chiều (Tiết 10-12)', 'Tối (Tiết 13-15)')
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$teacherId]);
        return $stmt->fetchAll();
    }
}
