<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

class HomeController extends Controller
{
    public function index(): void
    {
        echo $this->render('customer/welcome', [
            'appName'     => config('app.name', 'Healthy Bite'),
            'restaurant'  => 'Greenhouse Kitchen',
            'branch'      => 'Indiranagar, Bengaluru',
        ]);
    }
}
