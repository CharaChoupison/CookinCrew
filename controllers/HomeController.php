<?php
namespace Controllers;

use Models\MessageModel;
use Models\LikeModel;

class HomeController extends Controller
{
    public function index()
    {
        // Instancier le modèle des messages
        $messageModel = new MessageModel($this->db);
        // Instancier le modèle des likes (pour vérifier si l’utilisateur a déjà liké)
        $likeModel    = new LikeModel($this->db);

        // Si on reçoit un formulaire en POST -> on crée un nouveau message
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Vérifier que l’utilisateur est connecté
            if (empty($_SESSION['user_id'])) {
                header('Location: /CookinCrew/connexion');
                exit;
            }

            // Récupérer les champs
            $titre       = $_POST['titre']       ?? '';
            $description = $_POST['description'] ?? '';
            $userId      = $_SESSION['user_id'];
            $imagePath   = null;

            // Gestion upload image
            if (!empty($_FILES['image']['name'])) {
                $fileName  = time() . '_' . basename($_FILES['image']['name']);
                // Ton dossier uploads/posts :
                $targetDir = __DIR__ . '/../uploads/posts/';

                // Créer le dossier s’il n’existe pas
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }

                $targetFile = $targetDir . $fileName;
                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                    $imagePath = 'uploads/posts/' . $fileName;
                }
            }

            // Insérer le message en base
            $newId = $messageModel->createMessage($userId, $titre, $description, $imagePath);

            // Rediriger pour éviter une double-submission
            if ($newId) {
                header('Location: /CookinCrew/');
                exit;
            }
        }

        // 1) Récupérer tous les messages
        //    (si tu veux aussi la count de likes dans ce listing, tu peux faire 
        //    un subselect ou un LEFT JOIN dans getAllMessages())
        $allMessages = $messageModel->getAllMessages();

        // 2) Récupérer le “top 5” des posts les plus likés
        $topMessages = $messageModel->getMostLikedPosts(5);

        // 3) Ajouter le champ userHasLiked pour chaque message si l’utilisateur est connecté
        $userId = $_SESSION['user_id'] ?? 0; // 0 => pas connecté
        foreach ($allMessages as $msg) {
            $msg->userHasLiked = $likeModel->userHasLiked($userId, $msg->id);
        }
        // Pareil pour le top 5
        foreach ($topMessages as $msg) {
            $msg->userHasLiked = $likeModel->userHasLiked($userId, $msg->id);
        }

        // 4) Envoyer tout à la vue
        $this->render('home.html.twig', [
            'title'       => 'CookinCrew',
            'h1'          => 'CookinCrew',
            'messages'    => $allMessages,  // liste complète
            'topMessages' => $topMessages,  // top 5
            'isConnected' => !empty($_SESSION['user_id']),
            'username'    => $_SESSION['username'] ?? null
        ]);
    }
}
