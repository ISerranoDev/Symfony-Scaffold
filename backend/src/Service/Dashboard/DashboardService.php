<?php

namespace App\Service\Dashboard;


use App\Utils\AbstractClasses\AbstractService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class DashboardService extends AbstractService
{

    public function __construct(
        EntityManagerInterface $entityManager
    )
    {

    }

    public function dashboard(): Response
    {

        return $this->render('dashboard/index.html.twig', [

        ]);
    }


}
