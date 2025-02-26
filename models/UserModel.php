<?php
namespace Models;

use PDO;

class UserModel extends Model
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'users');
    }

    /**
     * Inscrire un utilisateur.
     *
     * @param string $username
     * @param string $email
     * @param string $password
     * @return int|null L'ID de l'utilisateur créé ou null en cas d'échec.
     */
    public function get_user_by_mail(string $email,bool $fetchAsObject = true){

        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = :email");
        $stmt->execute([':email' => $email]);
        return $fetchAsObject ? $stmt->fetch(PDO::FETCH_OBJ) : $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function get_user_by_id(string $id,bool $fetchAsObject = true){

        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $fetchAsObject ? $stmt->fetch(PDO::FETCH_OBJ) : $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function register(string $username, string $email, string $password): ?int
    {
        // Hashage du mot de passe avant l'insertion
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        return $this->create(['username', 'email', 'password'], $username, $email, $hashedPassword);
    }

    public function connexion(string $email, string $password): ?int
    {   
        $user = $this->get_user_by_mail($email, true);

        if ($user && password_verify($password, $user->password)) {
            return 1;
        } else {
            return 0;
        }
    }
}