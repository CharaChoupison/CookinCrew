<?php
namespace Controllers;

use Database\Database;

class Controller
{
    protected $db;
    protected $twig;

    public function __construct($db)
    {
        $this->db = $db;

        // Initialisation de Twig, en pointant vers le dossier "views"
        $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../views');
        $this->twig = new \Twig\Environment($loader);
    }

    protected function render(string $template, array $data = [])
    {
        echo $this->twig->render($template, $data);
    }
}
