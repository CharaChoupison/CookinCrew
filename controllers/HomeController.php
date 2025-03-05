<?php
namespace Controllers;

use Models\MessageModel;

class HomeController extends Controller
{
    public function index()
    {
        $messageModel = new MessageModel($this->db);

        // 1. Vérifier si on est en POST => quelqu'un a soumis le formulaire de création
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Vérifier si l'utilisateur est connecté
            if (empty($_SESSION['user_id'])) {
                // Redirige pour éviter la création sans être connecté
                header('Location: /CookinCrew/connexion');
                exit;
            }

            // Récupérer les champs
            $titre       = $_POST['titre']       ?? '';
            $description = $_POST['description'] ?? '';
            $userId      = $_SESSION['user_id'];
            $imagePath   = null;

            // Gérer l'upload (si champ 'image' existe)
            if (!empty($_FILES['image']['name'])) {
                $fileName   = time() . '_' . basename($_FILES['image']['name']);
                $targetDir  = __DIR__ . '/../../public/uploads/';

                // Crée le dossier s'il n'existe pas
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }

                $targetFile = $targetDir . $fileName;
                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                    $imagePath = 'uploads/' . $fileName;
                }
            }

            // Insertion en base
            $newId = $messageModel->createMessage($userId, $titre, $description, $imagePath);

            // On redirige pour rafraîchir la page
            header('Location: /CookinCrew/');
            exit;
        }

        // 2. Sinon (GET), on affiche la page d'accueil
        $allMessages = $messageModel->getAllMessages();

        $this->render('home.html.twig', [
            'title'       => 'Accueil',
            'h1'          => 'Bienvenue sur CookinCrew',
            'messages'    => $allMessages,
            'isConnected' => !empty($_SESSION['user_id']),
            'username'    => $_SESSION['username'] ?? null
        ]);
    }
}
