<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Services\MenuService;
use App\Services\QrService;

class CheckoutController extends Controller
{
    private MenuService $menuService;
    private QrService $qrService;

    public function __construct()
    {
        $this->menuService = new MenuService();
        $this->qrService = new QrService();
    }

    public function index(): void
    {
        $qrToken = (string)Request::getQuery('token', '');
        $tableContext = null;
        $restaurantId = (int)config('app.default_restaurant_id', 1);

        if ($qrToken !== '') {
            $tableContext = $this->qrService->resolveToken($qrToken);
            if ($tableContext) {
                $restaurantId = (int)$tableContext['restaurant_id'];
            }
        } else {
            $restaurantId = (int)Request::getQuery('restaurant_id', (string)$restaurantId);
        }

        $restaurant = $this->menuService->getRestaurant($restaurantId);
        if (!$restaurant) {
            echo $this->render('customer/errors/restaurant-unavailable', [], 'minimal');
            return;
        }

        $branch = (new \App\Repositories\RestaurantRepository())->getBranch(
            $restaurantId,
            $tableContext ? (int)$tableContext['branch_id'] : null
        );

        $branchRepo = new \App\Repositories\BranchRepository();
        $availableTables = $branchRepo->getTablesWithQr($restaurantId, (int)($branch['id'] ?? 1));

        echo $this->render('customer/checkout', [
            'restaurant'      => $restaurant,
            'branch'          => $branch,
            'tableContext'    => $tableContext,
            'availableTables' => $availableTables,
            'qrToken'         => $qrToken,
            'title'           => "Checkout — {$restaurant['name']}"
        ]);
    }
}
