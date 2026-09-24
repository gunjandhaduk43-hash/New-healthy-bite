<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Middleware\AdminMiddleware;
use App\Repositories\UserRepository;

class UserController extends Controller
{
    private UserRepository $userRepo;

    public function __construct()
    {
        $this->userRepo = new UserRepository();
    }

    public function index(): void
    {
        $adminUser = AdminMiddleware::handle();

        $users = $this->userRepo->getAll(100);

        $flashSuccess = Session::getFlash('success');
        $flashError   = Session::getFlash('error');

        echo $this->render('admin/users', [
            'title'        => 'Users & Access Management — Platform Admin',
            'activeNav'    => 'users',
            'user'         => $adminUser,
            'users'        => $users,
            'flashSuccess' => $flashSuccess,
            'flashError'   => $flashError,
        ], 'admin');
    }

    public function updateStatus(string|int $id): void
    {
        $adminUser = AdminMiddleware::handle();
        $targetUserId = (int)$id;

        if ($targetUserId === (int)$adminUser['id']) {
            Session::setFlash('error', 'You cannot change your own account status.');
            Response::redirect('/admin/users');
        }

        $status = (string)Request::get('status', '');
        $validStatuses = ['active', 'inactive', 'suspended'];
        if (!in_array($status, $validStatuses, true)) {
            Session::setFlash('error', 'Invalid user status specified.');
            Response::redirect('/admin/users');
        }

        $this->userRepo->updateStatus($targetUserId, $status);

        Session::setFlash('success', "User #{$targetUserId} status updated to " . ucfirst($status) . ".");
        Response::redirect('/admin/users');
    }

    public function create(): void
    {
        AdminMiddleware::handle();

        $name         = trim((string)Request::get('name', ''));
        $email        = trim((string)Request::get('email', ''));
        $roleId       = (int)Request::get('role_id', 2);
        $restaurantId = !empty(Request::get('restaurant_id')) ? (int)Request::get('restaurant_id') : null;
        $password     = trim((string)Request::get('password', 'Secret@123'));

        if (empty($name) || empty($email)) {
            Session::setFlash('error', 'Name and email are required.');
            Response::redirect('/admin/users');
        }

        try {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $db = \App\Core\Database::getConnection();
            $stmt = $db->prepare("
                INSERT INTO users (role_id, restaurant_id, name, email, password, status)
                VALUES (:role_id, :restaurant_id, :name, :email, :password, 'active')
            ");
            $stmt->execute([
                ':role_id'       => $roleId,
                ':restaurant_id' => $restaurantId,
                ':name'          => $name,
                ':email'         => $email,
                ':password'      => $hash
            ]);
            Session::setFlash('success', "User {$name} created successfully.");
        } catch (\Throwable $e) {
            Session::setFlash('error', 'Could not create user. Email may already exist.');
        }

        Response::redirect('/admin/users');
    }
}
