<?php

namespace Controllers;

use PDO;

class HomeController extends Controller
{
    public function __construct(PDO $database)
    {
        parent::__construct($database);
    }

    public function index()
    {
        $data = [
            "title" => "Page d'accueil",
            "h1" => "Bienvenue",
            "content" => "Ceci est la page d'accueil.",
            "mail" => $_SESSION["email"] ?? null,
            "username"=> $_SESSION["username"] ?? null,

        ];

        $this->render("home.html.twig", $data);
    }
}
