<?php
namespace Controllers;
use Models\UserModel;

class UserController extends Controller
{
    /**
     * Afficher la page de connexion.
     */
    public function index()
    {
        $data = [
            "title" => "connexion",
            "h1" => "connexion",
            "error" => $_SESSION['error'] ?? null,  // si tu veux afficher l’erreur
        ];
        $this->render("connexion.html.twig", $data);
        
    }

    /**
     * Afficher le formulaire d'inscription et traiter l'inscription.
     */
    public function inscription()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            if (!empty($username) && !empty($email) && !empty($password)) {
                try {
                    $userModel = new UserModel($this->db);
                    $issuccess = $userModel->register($username, $email, $password);

                    if ($issuccess) {
                        $id = $userModel->get_last_id();
                        $user = $userModel->get_user_by_id($id);
                        $_SESSION["email"] = $user->email;
                        $_SESSION["username"] = $user->username;
                        header('Location: /CookinCrew/home');
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


    public function connexion(){

        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') 
        {
           
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            
           
           $userModel =  new UserModel($this->db);
           
            if(!empty($email) && !empty($password)){
                 try{

                    $connected = $userModel->connexion($email,$password);
                   
                    if($connected == 1){
                        
                        $user = $userModel->get_user_by_mail($email);
                        $_SESSION["email"] = $user->mail;
                        $_SESSION["username"] = $user->username;
                        header('Location: /CookinCrew/');
                    }
                    else {
                      $_SESSION['error'] = 'Email ou mot de passe incorrect';
                      header('Location: /CookinCrew/connexion');
                  }
                  

                 }catch(\PDOException $e){
                        $userModel = new UserModel($this->db);
                 }
            }   else{
                $error = 'Tous les champs sont obligatoires.';
                
            }
        }
    }

    public function logout() {
        // 1. Détruire la session
        session_destroy();

        // 2. Rediriger vers l'accueil
        header('Location: /CookinCrew/'); 
        exit;
    }

    public function admin(){

        $data = [
            "mail" => $_SESSION["email"],
            "username"=> $_SESSION["username"],
            "h1" => "Admin",
        ];


        $this->render("admin.html.twig",$data);
    }
}