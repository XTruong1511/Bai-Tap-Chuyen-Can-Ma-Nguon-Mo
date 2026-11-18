<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

$host = $_ENV['DB_HOST'] ?? '127.0.0.1';
$port = $_ENV['DB_PORT'] ?? '3306';
$user = $_ENV['DB_USER'] ?? 'root';
$pass = $_ENV['DB_PASS'] ?? '';
$dbname = $_ENV['DB_NAME'] ?? 'huit_db';

try {
    $pdo = new PDO("mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    echo "Connected to MySQL server database `{$dbname}`.\n";

    // Ensure faculty and department columns exist in teachers table
    $stmt = $pdo->query("SHOW COLUMNS FROM teachers LIKE 'faculty'");
    if (!$stmt->fetch()) {
        $pdo->exec("ALTER TABLE teachers ADD COLUMN faculty VARCHAR(150) NOT NULL DEFAULT 'Khoa Công nghệ Thông tin' COMMENT 'Khoa' AFTER phone");
        echo "Added column `faculty` to `teachers`.\n";
    }

    $stmtDept = $pdo->query("SHOW COLUMNS FROM teachers LIKE 'department'");
    if (!$stmtDept->fetch()) {
        $pdo->exec("ALTER TABLE teachers ADD COLUMN department VARCHAR(150) NOT NULL DEFAULT 'Bộ môn Công nghệ phần mềm' COMMENT 'Bộ môn' AFTER faculty");
        echo "Added column `department` to `teachers`.\n";
    }

    echo "Database `{$dbname}` updated successfully!\n";
} catch (PDOException $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
