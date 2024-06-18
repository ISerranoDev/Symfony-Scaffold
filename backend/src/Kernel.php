<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    const DEV_ENV = 'dev';
    const PROD_ENV = 'prod';

    use MicroKernelTrait;
}
