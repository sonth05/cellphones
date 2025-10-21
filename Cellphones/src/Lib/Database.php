<?php
namespace App\Lib;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $pdo = null;
    private static ?DbConnection $instance = null;

    public static function init(array $config): void
    {
        if (self::$pdo !== null) {
            return;
        }
        $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s',
            $config['host'], $config['port'], $config['name'], $config['charset'] ?? 'utf8mb4'
        );
        try {
            self::$pdo = new PDO($dsn, $config['user'], $config['pass'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo 'Database connection failed.';
            exit;
        }
    }

    public static function pdo(): PDO
    {
        if (!self::$pdo) {
            throw new \RuntimeException('Database not initialized');
        }
        return self::$pdo;
    }

    // Backwards-compatible singleton used by models in this project
    public static function getInstance(): DbConnection
    {
        if (!self::$pdo) {
            throw new \RuntimeException('Database not initialized');
        }
        if (!self::$instance) {
            self::$instance = new DbConnection(self::$pdo);
        }
        return self::$instance;
    }
}

// Lightweight wrapper to standardize prepared queries and transactions
class DbConnection
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function query(string $sql, array $params = [])
    {
        if (empty($params)) {
            return $this->pdo->query($sql);
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function beginTransaction(): bool
    {
        return $this->pdo->beginTransaction();
    }

    public function commit(): bool
    {
        return $this->pdo->commit();
    }

    public function rollback(): bool
    {
        return $this->pdo->rollBack();
    }

    public function lastInsertId(): string
    {
        return $this->pdo->lastInsertId();
    }
}
?>


