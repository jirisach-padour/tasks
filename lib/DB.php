<?php
class DB {
    private static ?PDO $pdo = null;

    public static function get(): PDO {
        if (!self::$pdo) {
            self::$pdo = new PDO(
                'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
                DB_USER, DB_PASS,
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]
            );
        }
        return self::$pdo;
    }

    public static function q(string $sql, array $params = []): PDOStatement {
        $stmt = self::get()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public static function insert(string $table, array $data): int {
        $cols = implode(',', array_keys($data));
        $placeholders = implode(',', array_fill(0, count($data), '?'));
        self::q("INSERT INTO {$table} ({$cols}) VALUES ({$placeholders})", array_values($data));
        return (int) self::get()->lastInsertId();
    }

    public static function update(string $table, array $data, int $id): void {
        $set = implode(',', array_map(fn($k) => "{$k}=?", array_keys($data)));
        self::q("UPDATE {$table} SET {$set} WHERE id=?", [...array_values($data), $id]);
    }
}
