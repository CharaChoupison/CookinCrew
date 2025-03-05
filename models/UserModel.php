<?php
namespace Models;

use PDO;

class UserModel extends Model
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'users');
    }

    public function get_user_by_mail(string $email, bool $fetchAsObject = true)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table}
            WHERE email = :email
        ");
        $stmt->execute([':email' => $email]);
        return $fetchAsObject ? $stmt->fetch(PDO::FETCH_OBJ) : $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function get_user_by_id(int $id, bool $fetchAsObject = true)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table}
            WHERE id = :id
        ");
        $stmt->execute([':id' => $id]);
        return $fetchAsObject ? $stmt->fetch(PDO::FETCH_OBJ) : $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Inscription
    public function register(string $username, string $email, string $password): ?int
    {
        // Hashage
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        return $this->create(['username', 'email', 'password'], $username, $email, $hashedPassword);
    }

    // Connexion
    public function connexion(string $email, string $password): bool
    {
        $user = $this->get_user_by_mail($email, true);
        if ($user && password_verify($password, $user->password)) {
            return true;
        }
        return false;
    }
}
