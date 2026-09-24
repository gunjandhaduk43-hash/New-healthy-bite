<?php
declare(strict_types=1);

namespace App\Controllers\Owner;

use App\Core\Controller;
use App\Core\Response;
use App\Core\Session;
use App\Middleware\RestaurantMiddleware;
use App\Repositories\RestaurantRepository;

class SettingsController extends Controller
{
    private RestaurantRepository $restaurantRepo;

    public function __construct()
    {
        $this->restaurantRepo = new RestaurantRepository();
    }

    public function index(): void
    {
        $authContext  = RestaurantMiddleware::handle();
        $user         = $authContext['user'];
        $restaurantId = $authContext['restaurant_id'];

        $restaurant   = $this->restaurantRepo->findAnyById($restaurantId);
        $flashSuccess = Session::getFlash('success');
        $flashError   = Session::getFlash('error');

        echo $this->render('owner/settings', [
            'title'        => 'Settings — ' . ($restaurant['name'] ?? 'Dashboard'),
            'activeNav'    => 'settings',
            'user'         => $user,
            'restaurant'   => $restaurant,
            'flashSuccess' => $flashSuccess,
            'flashError'   => $flashError,
        ], 'owner');
    }

    public function updatePassword(): void
    {
        $authContext  = RestaurantMiddleware::handle();
        $user         = $authContext['user'];
        $userId       = (int)$user['id'];

        $csrfToken = (string)\App\Core\Request::get('_csrf_token');
        if (!\App\Core\Csrf::validateToken($csrfToken)) {
            Session::setFlash('error', 'Security token expired. Please try again.');
            Response::redirect('/owner/settings');
        }

        $currentPassword = (string)\App\Core\Request::get('current_password', '');
        $newPassword     = (string)\App\Core\Request::get('new_password', '');
        $confirmPassword = (string)\App\Core\Request::get('confirm_password', '');

        if ($currentPassword === '' || $newPassword === '') {
            Session::setFlash('error', 'Please enter both current and new password.');
            Response::redirect('/owner/settings');
        }

        if (strlen($newPassword) < 6) {
            Session::setFlash('error', 'New password must be at least 6 characters.');
            Response::redirect('/owner/settings');
        }

        if ($newPassword !== $confirmPassword) {
            Session::setFlash('error', 'New password and confirmation do not match.');
            Response::redirect('/owner/settings');
        }

        $db = \App\Core\Database::getConnection();
        $stmt = $db->prepare("SELECT password FROM users WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $userId]);
        $storedHash = (string)$stmt->fetchColumn();

        if (!password_verify($currentPassword, $storedHash)) {
            Session::setFlash('error', 'Current password entered is incorrect.');
            Response::redirect('/owner/settings');
        }

        $newHash = password_hash($newPassword, PASSWORD_BCRYPT);
        $updateStmt = $db->prepare("UPDATE users SET password = :password WHERE id = :id");
        $updateStmt->execute([':password' => $newHash, ':id' => $userId]);

        Session::setFlash('success', 'Password updated successfully.');
        Response::redirect('/owner/settings');
    }
}

