<?php
namespace Controllers;

use Models\MessageModel;

class HomeController extends Controller
{
    public function index()
    {
        $messageModel = new MessageModel($this->db);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Vérifier que l’utilisateur est connecté (facultatif selon ton besoin)
            if (empty($_SESSION['user_id'])) {
                header('Location: /CookinCrew/connexion');
                exit;
            }

            // Récupérer les champs
            $titre       = $_POST['titre']       ?? '';
            $description = $_POST['description'] ?? '';
            $userId      = $_SESSION['user_id'];
            $imagePath   = null;

            // Gestion upload
            if (!empty($_FILES['image']['name'])) {
                $fileName   = time() . '_' . basename($_FILES['image']['name']);
                // Dossier "uploads" dans CookinCrew/
                $targetDir  = __DIR__ . '/../uploads/posts/';
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }
                $targetFile = $targetDir . $fileName;

                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                    $imagePath = 'uploads/posts/' . $fileName;
                }
            }

            // Insertion en base
            $newId = $messageModel->createMessage($userId, $titre, $description, $imagePath);

            // Rediriger pour éviter de re-soumettre le formulaire en cas de refresh
            if ($newId) {
                header('Location: /CookinCrew/');
                exit;
            }
        }

        // Si GET ou après redirection, on affiche la liste
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
