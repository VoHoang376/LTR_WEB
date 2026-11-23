<?php

class HomeController
{
    private $db;
    private $settingModel;

    public function __construct(PDO $db)
    {
        $this->db = $db;
        require_once __DIR__ . '/../Models/SettingModel.php';
        $this->settingModel = new SettingModel($db);
    }

    public function index()
    {
        $settings = $this->settingModel->getAll();
        $pageTitle = 'Trang chủ';
        $fullWidth = true;
        $currentPage = 'home';
        require __DIR__ . '/../Views/Frontends/home/index.php';
    }
}
