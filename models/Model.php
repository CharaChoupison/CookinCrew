<?php
namespace Models;

use PDO;

class Model
{
    protected $db;
    protected $table;

    public function __construct(PDO $db, string $table)
    {
        $this->db   = $db;
        $this->table = $table;
    }

    // Méthode générique de création si besoin
    public function create(array $columns, ...$values): ?int
    {
        $cols = implode(',', $columns);
        $placeholders = implode(',', array_fill(0, count($columns), '?'));

        $sql = "INSERT INTO {$this->table} ($cols) VALUES ($placeholders)";
        $stmt = $this->db->prepare($sql);

        if ($stmt->execute($values)) {
            return (int)$this->db->lastInsertId();
        }
        return null;
    }

    public function get_last_id(): int
    {
        return (int) $this->db->lastInsertId();
    }
}
