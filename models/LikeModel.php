<?php
namespace Models;

use PDO;

class LikeModel extends Model
{
    public function __construct(PDO $db)
    {
        // 'likes' = nom de ta table en base
        parent::__construct($db, 'likes');
    }

    /**
     * Vérifier si un utilisateur a déjà liké un post.
     */
    public function userHasLiked(int $userId, int $postId): bool
    {
        $stmt = $this->db->prepare("
            SELECT 1 
            FROM {$this->table}
            WHERE user_id = :u AND post_id = :p
        ");
        $stmt->execute([
            ':u' => $userId,
            ':p' => $postId
        ]);
        return (bool) $stmt->fetch();
    }

    /**
     * Ajouter un like (si pas déjà présent).
     */
    public function addLike(int $userId, int $postId): bool
    {
        // si la contrainte UNIQUE (ou PK composite) est posée, 
        // un 2e insert identique sera ignoré par MySQL (ou retournera une erreur)
        if ($this->userHasLiked($userId, $postId)) {
            return false;
        }

        $stmt = $this->db->prepare("
            INSERT INTO {$this->table} (user_id, post_id)
            VALUES (:u, :p)
        ");
        return $stmt->execute([':u' => $userId, ':p' => $postId]);
    }

    /**
     * Retirer un like.
     */
    public function removeLike(int $userId, int $postId): bool
    {
        $stmt = $this->db->prepare("
            DELETE FROM {$this->table}
            WHERE user_id = :u AND post_id = :p
        ");
        return $stmt->execute([
            ':u' => $userId,
            ':p' => $postId
        ]);
    }
}
