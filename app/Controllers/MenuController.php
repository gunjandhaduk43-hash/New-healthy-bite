<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Services\MenuService;
use App\Services\QrService;
use App\Repositories\BranchRepository;

class MenuController extends Controller
{
    private MenuService $menuService;
    private QrService $qrService;
    private BranchRepository $branchRepo;

    public function __construct()
    {
        $this->menuService = new MenuService();
        $this->qrService   = new QrService();
        $this->branchRepo  = new BranchRepository();
    }

    public function index(): void
    {
        $qrToken = (string)Request::getQuery('token', '');
        $tableContext = null;
        $restaurantId = (int)config('app.default_restaurant_id', 1);
        $branchId = null;
        $tokenError = null;

        if ($qrToken !== '') {
            $tableContext = $this->qrService->resolveToken($qrToken);
            if ($tableContext) {
                $restaurantId = (int)$tableContext['restaurant_id'];
                $branchId = (int)$tableContext['branch_id'];
            } else {
                $tokenError = 'The scanned QR code is inactive, invalid, or expired. Switched to standard digital menu.';
            }
        }

        if (!$tableContext) {
            $restaurantId = (int)Request::getQuery('restaurant_id', (string)$restaurantId);
        }

        $restaurant = $this->menuService->getRestaurant($restaurantId);
        if (!$restaurant) {
            echo $this->render('customer/errors/restaurant-unavailable', [], 'minimal');
            return;
        }

        $branch = (new \App\Repositories\RestaurantRepository())->getBranch($restaurantId, $branchId);
        $resolvedBranchId = (int)($branch['id'] ?? 1);

        if (!$tableContext) {
            $firstTable = $this->branchRepo->getFirstAvailableTable($restaurantId, $resolvedBranchId);
            if ($firstTable) {
                $tableContext = [
                    'id'            => (int)$firstTable['id'],
                    'table_number'  => $firstTable['table_number'],
                    'restaurant_id' => $restaurantId,
                    'branch_id'     => $resolvedBranchId,
                    'token'         => $firstTable['qr_token'] ?? null,
                ];
            }
        }

        $categories = $this->menuService->getCategories($restaurantId);
        $foods = $this->menuService->getFoods($restaurantId);

        echo $this->render('customer/menu', [
            'restaurant'   => $restaurant,
            'branch'       => $branch,
            'tableContext' => $tableContext,
            'qrToken'      => $qrToken,
            'tokenError'   => $tokenError,
            'categories'   => $categories,
            'foods'        => $foods,
            'title'        => "{$restaurant['name']} — Digital Menu & Food Ordering"
        ]);
    }

    public function resolveTableNumber(string $tableNumber): void
    {
        $restaurantId = (int)Request::getQuery('restaurant_id', (string)config('app.default_restaurant_id', 1));
        $table = $this->branchRepo->findTableByNumber($restaurantId, $tableNumber);

        if ($table && !empty($table['qr_token'])) {
            Response::redirect('/menu?token=' . urlencode($table['qr_token']));
            return;
        }

        if ($table) {
            $token = $this->branchRepo->ensureQrTokenForTable($restaurantId, (int)$table['branch_id'], (int)$table['id']);
            Response::redirect('/menu?token=' . urlencode($token));
            return;
        }

        Response::redirect('/menu');
    }

    public function resolveQr(string $token): void
    {
        Response::redirect('/menu?token=' . urlencode(trim($token)));
    }
}
