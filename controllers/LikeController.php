<?php
namespace Controllers;

use Models\LikeModel;

class LikeController extends Controller
{
    public function like(int $postId)
    {
        // 1) Vérifier si l’utilisateur est connecté
        if (empty($_SESSION['user_id'])) {
            header('Location: /CookinCrew/connexion');
            exit;
        }
        $userId = $_SESSION['user_id'];

        // 2) Charger le LikeModel
        $likeModel = new LikeModel($this->db);

        // 3) Ajouter le like (ou faire un toggle)
        $likeModel->addLike($userId, $postId);

        // 4) Rediriger vers la page d’accueil ou la liste des posts
        header('Location: /CookinCrew/');
        exit;
    }

    // Variante "toggle" (si on veut liker/déliker)
    public function toggleLike(int $postId)
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /CookinCrew/connexion');
            exit;
        }
        $userId = $_SESSION['user_id'];

        $likeModel = new LikeModel($this->db);

        if ($likeModel->userHasLiked($userId, $postId)) {
            $likeModel->removeLike($userId, $postId);
        } else {
            $likeModel->addLike($userId, $postId);
        }

        header('Location: /CookinCrew/');
        exit;
    }
    public function unlike(int $postId)
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /CookinCrew/connexion');
            exit;
        }
        $userId = $_SESSION['user_id'];
    
        $likeModel = new LikeModel($this->db);
        $likeModel->removeLike($userId, $postId);
    
        // Redirection 
        header('Location: /CookinCrew/');
        exit;
    }
}
