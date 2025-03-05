<?php
namespace Controllers;

use Models\MessageModel;

class MessageController extends Controller
{
    public function index()
    {
        $messageModel = new MessageModel($this->db);
        $messages = $messageModel->getAllMessages();

        $this->render('messages/list.html.twig', [
            'messages' => $messages
        ]);
    }

    public function create()
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /CookinCrew/connexion');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titre       = $_POST['titre'] ?? '';
            $description = $_POST['description'] ?? '';
            $userId      = $_SESSION['user_id'] ?? 0;
            $imagePath   = null;

            if (!empty($_FILES['image']['name'])) {
                // upload
                $fileName   = time() . '_' . basename($_FILES['image']['name']);
                $targetDir  = __DIR__ . '/../../public/uploads/';
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }
                $targetFile = $targetDir . $fileName;

                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                    $imagePath = 'uploads/' . $fileName;
                }
            }

            $msgModel = new MessageModel($this->db);
            $newId    = $msgModel->createMessage($userId, $titre, $description, $imagePath);

            if ($newId) {
                header('Location: /CookinCrew/');
                exit;
            } else {
                $error = 'Impossible d\'enregistrer le message.';
            }
        }

        $this->render('messages/create.html.twig', [
            'error' => $error ?? null
        ]);
    }
}
