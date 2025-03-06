<?php
namespace Controllers;

use Models\UserModel;

class UserController extends Controller
{
    public function index()
    {
        // Affichage du formulaire de connexion
        $this->render("connexion.html.twig", [
            'title' => 'Connexion',
            'h1'    => 'Connexion',
            // error si tu veux afficher une erreur récupérée en session, etc.
        ]);
    }

    public function inscription()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $email    = $_POST['mail'] ?? '';
            $password = $_POST['password'] ?? '';

            if (!empty($username) && !empty($email) && !empty($password)) {
                try {
                    $userModel = new UserModel($this->db);
                    $insertId  = $userModel->register($username, $email, $password);

                    if ($insertId) {
                        // Récupération de l'utilisateur
                        $user = $userModel->get_user_by_id($insertId);
                        $_SESSION['user_id']  = $user->id;
                        $_SESSION['username'] = $user->username;
                        $_SESSION['mail']     = $user->email;

                        // Redirection vers l'accueil
                        header('Location: /CookinCrew/');
                        exit;
                    } else {
                        $error = 'Impossible de créer l\'utilisateur.';
                    }
                } catch (\PDOException $e) {
                    $error = 'Erreur lors de l\'inscription : ' . $e->getMessage();
                }
            } else {
                $error = 'Tous les champs sont obligatoires.';
            }
        }

        $this->render('inscription.html.twig', [
            'error' => $error ?? null,
        ]);
    }

    public function connexion()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email    = $_POST['mail'] ?? '';
        $password = $_POST['password'] ?? '';

        if (!empty($email) && !empty($password)) {
            $userModel = new UserModel($this->db);
            $connected = $userModel->connexion($email, $password);

            if ($connected) {
                // Succès => On charge l'user pour récupérer son ID, etc.
                $user = $userModel->get_user_by_mail($email);
                $_SESSION['user_id']  = $user->id;
                $_SESSION['username'] = $user->username;
                $_SESSION['mail']     = $user->email;

                header('Location: /CookinCrew/');
                exit;
            } else {
                // Erreur => On prépare un message
                $error = 'Identifiants incorrects.';
            }
        }
    }

    // On rend la vue (si GET ou si on a eu un échec)
    $this->render('connexion.html.twig', [
        'title' => 'Connexion',
        'h1'    => 'Connexion',
        'error' => $error ?? null,
    ]);
}


    public function admin()
    {
        // Sécuriser l'accès admin
        if (empty($_SESSION['user_id'])) {
            header('Location: /CookinCrew/connexion');
            exit;
        }

        $data = [
            'mail'     => $_SESSION['mail'] ?? '',
            'username' => $_SESSION['username'] ?? '',
            'h1'       => 'Admin'
        ];

        $this->render("admin.html.twig", $data);
    }

    public function logout()
    {
        session_destroy();
        header('Location: /CookinCrew/');
        exit;
    }
}
