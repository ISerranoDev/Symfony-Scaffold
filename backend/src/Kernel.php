<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    const DEV_ENV = 'dev';
    const TEST_ENV = 'test';
    const PROD_ENV = 'prod';

    use MicroKernelTrait;
}
