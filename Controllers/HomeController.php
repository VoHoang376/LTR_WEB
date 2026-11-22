<?php

class HomeController
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function index()
    {
        $pageTitle = 'Trang chủ';
        $fullWidth = true;
        $currentPage = 'home';
        require __DIR__ . '/../Views/Frontends/home/index.php';
    }
}
