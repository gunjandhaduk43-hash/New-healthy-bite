<?php
declare(strict_types=1);

namespace App\Controllers\Owner;

use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Middleware\RestaurantMiddleware;
use App\Repositories\BranchRepository;
use App\Repositories\RestaurantRepository;

class TableController extends Controller
{
    private BranchRepository $branchRepo;
    private RestaurantRepository $restaurantRepo;

    public function __construct()
    {
        $this->branchRepo     = new BranchRepository();
        $this->restaurantRepo = new RestaurantRepository();
    }

    public function index(): void
    {
        $authContext  = RestaurantMiddleware::handle();
        $user         = $authContext['user'];
        $restaurantId = $authContext['restaurant_id'];

        $restaurant = $this->restaurantRepo->findAnyById($restaurantId);
        $branches   = $this->branchRepo->getBranchesByRestaurant($restaurantId);

        $selectedBranchId = Request::getQuery('branch_id') ? (int)Request::getQuery('branch_id') : null;
        $tables = $this->branchRepo->getTablesWithQr($restaurantId, $selectedBranchId);
        $tableStats = $this->branchRepo->countTablesByRestaurant($restaurantId);

        $flashSuccess = Session::getFlash('success');
        $flashError   = Session::getFlash('error');

        echo $this->render('owner/tables', [
            'title'            => 'Tables & QR Codes — ' . ($restaurant['name'] ?? 'Dashboard'),
            'activeNav'        => 'tables',
            'user'             => $user,
            'restaurant'       => $restaurant,
            'branches'         => $branches,
            'tables'           => $tables,
            'tableStats'       => $tableStats,
            'selectedBranchId' => $selectedBranchId,
            'flashSuccess'     => $flashSuccess,
            'flashError'       => $flashError,
        ], 'owner');
    }

    public function updateStatus(string|int $id): void
    {
        $authContext  = RestaurantMiddleware::handle();
        $restaurantId = $authContext['restaurant_id'];

        $tableId = (int)$id;
        $status  = (string)Request::get('status', '');

        $valid = ['available', 'occupied', 'cleaning', 'out_of_service'];
        if (!in_array($status, $valid, true)) {
            if (Request::isJson()) {
                Response::json(['error' => 'Invalid table status'], 400);
            }
            Session::setFlash('error', 'Invalid table status specified.');
            Response::redirect('/owner/tables');
        }

        $this->branchRepo->updateTableStatus($tableId, $restaurantId, $status);

        if (Request::isJson()) {
            Response::json(['success' => true, 'table_id' => $tableId, 'status' => $status]);
        }

        Session::setFlash('success', 'Table status updated.');
        Response::redirect('/owner/tables');
    }

    public function create(): void
    {
        $authContext  = RestaurantMiddleware::handle();
        $restaurantId = $authContext['restaurant_id'];

        $csrfToken = (string)Request::get('_csrf_token');
        if (!Csrf::validateToken($csrfToken)) {
            Session::setFlash('error', 'Security token expired. Please try again.');
            Response::redirect('/owner/tables');
        }

        $branchId    = (int)Request::get('branch_id', 0);
        $tableNumber = trim((string)Request::get('table_number', ''));

        if ($branchId <= 0 || $tableNumber === '') {
            Session::setFlash('error', 'Branch and table number are required.');
            Response::redirect('/owner/tables');
        }

        try {
            $tableId = $this->branchRepo->createTable($restaurantId, $branchId, $tableNumber);
            Session::setFlash('success', "Table {$tableNumber} added and QR token generated.");
        } catch (\PDOException $e) {
            Session::setFlash('error', 'Table already exists in this branch.');
        }

        Response::redirect('/owner/tables');
    }
}
