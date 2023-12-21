<?php

namespace App\Controller\Dashboard;

use App\Service\Dashboard\DashboardService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{

    private DashboardService $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    #[Route(path: '/', name: 'app_dashboard')]
    public function dashboard(): Response
    {
        return $this->dashboardService->dashboard();
    }


}
