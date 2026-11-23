<?php

class PricingController
{
    private $pricingModel;
    private $settingModel;

    public function __construct(PDO $db)
    {
        require_once __DIR__ . '/../Models/PricingModel.php';
        require_once __DIR__ . '/../Models/SettingModel.php';
        $this->pricingModel = new PricingModel($db);
        $this->settingModel = new SettingModel($db);
    }

    public function index()
    {
        $settings = $this->settingModel->getAll();
        $plans = $this->pricingModel->getAllActive();

        $pageTitle = 'Bảng giá dịch vụ';
        $currentPage = 'pricing';

        require __DIR__ . '/../Views/Frontends/pricing/index.php';
    }
}
