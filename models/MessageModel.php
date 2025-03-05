<?php
namespace Models;

use PDO;

class MessageModel extends Model
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'messages');
    }

    public function createMessage(int $userId, string $titre, string $description, ?string $imagePath): ?int
    {
        $stmt = $this->db->prepare("
            INSERT INTO {$this->table} (utilisateur_id, titre, description, image)
            VALUES (:user_id, :titre, :description, :image)
        ");
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':titre', $titre);
        $stmt->bindValue(':description', $description);
        $stmt->bindValue(':image', $imagePath);

        if ($stmt->execute()) {
            return (int)$this->db->lastInsertId();
        }
        return null;
    }

    public function getAllMessages(): array
    {
        $stmt = $this->db->query("
            SELECT * FROM {$this->table}
            ORDER BY date_poste DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function getMessageById(int $id)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table} WHERE id = :id
        ");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }
}
