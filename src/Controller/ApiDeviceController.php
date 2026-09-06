<?php
namespace App\Controller;

use App\Repository\DeviceRepository;

class ApiDeviceController
{
    private DeviceRepository $deviceRepo;

    public function __construct(DeviceRepository $deviceRepo)
    {
        $this->deviceRepo = $deviceRepo;
    }

    public function search(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        
        $keyword = trim($_GET['q'] ?? '');
        
        $devices = $this->deviceRepo->getDevicesPaginated($keyword, 0, '', 10, 0);

        echo json_encode([
            'ok' => true,
            'data' => $devices
        ], JSON_UNESCAPED_UNICODE);
    }
}