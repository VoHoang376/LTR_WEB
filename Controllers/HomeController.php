<?php

class HomeController
{
    private $db;
    private $settingModel;
    private $productModel;
    private $postModel;

    public function __construct(PDO $db)
    {
        $this->db = $db;
        require_once __DIR__ . '/../Models/SettingModel.php';
        require_once __DIR__ . '/../Models/ProductModel.php';
        require_once __DIR__ . '/../Models/PostModel.php';
        $this->settingModel = new SettingModel($db);
        $this->productModel = new ProductModel($db);
        $this->postModel = new PostModel($db);
    }

    public function index()
    {
        $settings = $this->settingModel->getAll();
        $featuredProducts = $this->productModel->search([
            'status' => 1,
            'sort' => 'newest',
            'page' => 1,
            'per_page' => 4,
        ])['items'];
        $latestPosts = $this->postModel->getLatestPublished(3);

        $pageTitle = 'Trang chủ';
        $fullWidth = true;
        $currentPage = 'home';
        require __DIR__ . '/../Views/Frontends/home/index.php';
    }
}
