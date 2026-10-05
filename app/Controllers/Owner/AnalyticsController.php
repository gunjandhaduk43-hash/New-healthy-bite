<?php
declare(strict_types=1);

namespace App\Controllers\Owner;

use App\Core\Controller;
use App\Core\Response;
use App\Core\Session;
use App\Middleware\RestaurantMiddleware;
use App\Repositories\OrderRepository;
use App\Repositories\RestaurantRepository;

class AnalyticsController extends Controller
{
    private OrderRepository $orderRepo;
    private RestaurantRepository $restaurantRepo;

    public function __construct()
    {
        $this->orderRepo      = new OrderRepository();
        $this->restaurantRepo = new RestaurantRepository();
    }

    public function index(): void
    {
        $authContext  = RestaurantMiddleware::handle();
        $user         = $authContext['user'];
        $restaurantId = $authContext['restaurant_id'];

        $restaurant   = $this->restaurantRepo->findAnyById($restaurantId);

        $period    = isset($_GET['period']) ? sanitize($_GET['period']) : 'month';
        $startDate = isset($_GET['start_date']) ? sanitize($_GET['start_date']) : null;
        $endDate   = isset($_GET['end_date']) ? sanitize($_GET['end_date']) : null;

        $analytics    = $this->orderRepo->getFilteredAnalytics($restaurantId, $period, $startDate, $endDate);
        $todayStats   = $this->orderRepo->getTodayStats($restaurantId);

        $flashSuccess = Session::getFlash('success');
        $flashError   = Session::getFlash('error');

        echo $this->render('owner/analytics', [
            'title'        => 'Sales & Macro Analytics — ' . ($restaurant['name'] ?? 'Dashboard'),
            'activeNav'    => 'analytics',
            'user'         => $user,
            'restaurant'   => $restaurant,
            'period'       => $analytics['period'],
            'startDate'    => $analytics['start_date'],
            'endDate'      => $analytics['end_date'],
            'analytics'    => $analytics,
            'summary'      => $analytics['summary'],
            'macroStats'   => $analytics['macro_stats'],
            'paymentStats' => $analytics['payment_stats'],
            'trends'       => $analytics['trends'],
            'topItems'     => $analytics['top_items'],
            'todayStats'   => $todayStats,
            'flashSuccess' => $flashSuccess,
            'flashError'   => $flashError,
        ], 'owner');
    }

    public function export(): void
    {
        $authContext  = RestaurantMiddleware::handle();
        $restaurantId = $authContext['restaurant_id'];
        $restaurant   = $this->restaurantRepo->findAnyById($restaurantId);

        $period    = isset($_GET['period']) ? sanitize($_GET['period']) : 'month';
        $startDate = isset($_GET['start_date']) ? sanitize($_GET['start_date']) : null;
        $endDate   = isset($_GET['end_date']) ? sanitize($_GET['end_date']) : null;
        $type      = strtolower(isset($_GET['type']) ? sanitize($_GET['type']) : 'csv');

        $analytics = $this->orderRepo->getFilteredAnalytics($restaurantId, $period, $startDate, $endDate);

        if ($type === 'csv') {
            $filename = 'sales_report_' . ($restaurant['slug'] ?? 'restaurant') . '_' . $analytics['start_date'] . '_to_' . $analytics['end_date'] . '.csv';
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            $out = fopen('php://output', 'w');

            fputcsv($out, ['HEALTHY BITE — SALES & MACRO ANALYTICS REPORT']);
            fputcsv($out, ['Restaurant', $restaurant['name'] ?? 'Healthy Bite']);
            fputcsv($out, ['Filter Period', ucfirst($analytics['period']) . " ({$analytics['start_date']} to {$analytics['end_date']})"]);
            fputcsv($out, ['Generated At', date('Y-m-d H:i:s')]);
            fputcsv($out, []);

            fputcsv($out, ['EXECUTIVE SUMMARY']);
            fputcsv($out, ['Total Revenue (INR)', 'Net Sales (INR)', 'Total Orders', 'Average Order Value (INR)', 'Active Orders', 'Completed Orders']);
            fputcsv($out, [
                number_format((float)($analytics['summary']['total_revenue'] ?? 0), 2, '.', ''),
                number_format((float)($analytics['summary']['net_sales'] ?? 0), 2, '.', ''),
                (int)($analytics['summary']['total_orders'] ?? 0),
                number_format((float)($analytics['summary']['aov'] ?? 0), 2, '.', ''),
                (int)($analytics['summary']['active_orders'] ?? 0),
                (int)($analytics['summary']['completed_orders'] ?? 0),
            ]);
            fputcsv($out, []);

            fputcsv($out, ['NUTRITIONAL & MACRO TOTALS']);
            fputcsv($out, ['Total Calories (kcal)', 'Total Protein (g)', 'Total Carbs (g)', 'Total Fat (g)', 'Total Sugar (g)', 'Total Caffeine (mg)']);
            fputcsv($out, [
                round((float)($analytics['macro_stats']['total_calories'] ?? 0)),
                round((float)($analytics['macro_stats']['total_protein'] ?? 0), 1),
                round((float)($analytics['macro_stats']['total_carbs'] ?? 0), 1),
                round((float)($analytics['macro_stats']['total_fat'] ?? 0), 1),
                round((float)($analytics['macro_stats']['total_sugar'] ?? 0), 1),
                round((float)($analytics['macro_stats']['total_caffeine'] ?? 0)),
            ]);
            fputcsv($out, []);

            fputcsv($out, ['DETAILED ORDERS BREAKDOWN']);
            fputcsv($out, ['Order Number', 'Date & Time', 'Customer', 'Table', 'Type', 'Order Status', 'Payment Status', 'Items Ordered', 'Subtotal (INR)', 'Tax (INR)', 'Total Amount (INR)']);
            foreach ($analytics['orders'] as $ord) {
                fputcsv($out, [
                    $ord['order_number'],
                    $ord['created_at'],
                    $ord['customer_name'] ?? 'Guest',
                    $ord['table_number'] ? 'Table ' . $ord['table_number'] : 'Takeaway',
                    ucfirst($ord['order_type'] ?? 'Dine-in'),
                    ucfirst($ord['order_status'] ?? 'Placed'),
                    ucfirst($ord['payment_status'] ?? 'Pending'),
                    $ord['items_summary'] ?? 'N/A',
                    number_format((float)$ord['subtotal'], 2, '.', ''),
                    number_format((float)$ord['tax'], 2, '.', ''),
                    number_format((float)$ord['total_amount'], 2, '.', ''),
                ]);
            }
            fclose($out);
            exit;
        }

        // Printable HTML / PDF view
        $this->renderPrintReport($restaurant, $analytics);
        exit;
    }

    private function renderPrintReport(array $restaurant, array $analytics): void
    {
        $sum = $analytics['summary'];
        $mac = $analytics['macro_stats'];
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <title>Analytics Report — <?= e($restaurant['name'] ?? 'Healthy Bite') ?></title>
            <style>
                body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; padding: 32px; color: #1e293b; background: #fff; }
                .report-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #2e7d32; padding-bottom: 16px; margin-bottom: 24px; }
                .report-title { font-size: 24px; font-weight: 700; color: #1e293b; margin: 0; }
                .badge { display: inline-block; padding: 4px 12px; background: #eaf5ea; color: #2e7d32; border-radius: 9999px; font-weight: 600; font-size: 13px; }
                .kpi-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px; }
                .kpi-card { border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px; }
                .kpi-label { font-size: 12px; text-transform: uppercase; color: #64748b; font-weight: 600; margin-bottom: 6px; }
                .kpi-val { font-size: 20px; font-weight: 700; color: #0f172a; }
                table { width: 100%; border-collapse: collapse; margin-top: 16px; font-size: 13px; }
                th { text-align: left; padding: 10px 12px; background: #f8fafc; border-bottom: 2px solid #e2e8f0; color: #475569; }
                td { padding: 10px 12px; border-bottom: 1px solid #f1f5f9; }
                @media print { .no-print { display: none; } }
            </style>
        </head>
        <body onload="window.print()">
            <div class="no-print" style="margin-bottom: 20px;">
                <button onclick="window.print()" style="padding: 8px 16px; background: #2e7d32; color: #fff; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">Print / Save as PDF</button>
                <a href="/owner/analytics" style="margin-left: 12px; text-decoration: none; color: #64748b;">← Back to Dashboard</a>
            </div>
            <div class="report-header">
                <div>
                    <h1 class="report-title"><?= e($restaurant['name'] ?? 'Healthy Bite') ?></h1>
                    <p style="margin: 4px 0 0 0; color: #64748b;">Executive Sales & Macro Analytics Report</p>
                </div>
                <div style="text-align: right;">
                    <span class="badge"><?= ucfirst($analytics['period']) ?>: <?= $analytics['start_date'] ?> to <?= $analytics['end_date'] ?></span>
                    <p style="margin: 4px 0 0 0; font-size: 12px; color: #94a3b8;">Generated <?= date('M d, Y h:i A') ?></p>
                </div>
            </div>

            <div class="kpi-grid">
                <div class="kpi-card">
                    <div class="kpi-label">Total Revenue</div>
                    <div class="kpi-val"><?= format_price($sum['total_revenue'] ?? 0) ?></div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-label">Net Sales</div>
                    <div class="kpi-val"><?= format_price($sum['net_sales'] ?? 0) ?></div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-label">Total Orders</div>
                    <div class="kpi-val"><?= number_format((int)($sum['total_orders'] ?? 0)) ?></div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-label">Average Order Value</div>
                    <div class="kpi-val"><?= format_price($sum['aov'] ?? 0) ?></div>
                </div>
            </div>

            <h3 style="margin-top: 24px; color: #0f172a;">Nutritional & Macro Highlights</h3>
            <div class="kpi-grid">
                <div class="kpi-card">
                    <div class="kpi-label">Calories Served</div>
                    <div class="kpi-val"><?= number_format((float)($mac['total_calories'] ?? 0)) ?> kcal</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-label">Total Protein</div>
                    <div class="kpi-val"><?= number_format((float)($mac['total_protein'] ?? 0), 1) ?> g</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-label">Total Carbs</div>
                    <div class="kpi-val"><?= number_format((float)($mac['total_carbs'] ?? 0), 1) ?> g</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-label">Total Caffeine</div>
                    <div class="kpi-val"><?= number_format((float)($mac['total_caffeine'] ?? 0)) ?> mg</div>
                </div>
            </div>

            <h3 style="margin-top: 32px; color: #0f172a;">Recent Orders in Period</h3>
            <table>
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Table</th>
                        <th>Items</th>
                        <th>Status</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($analytics['orders'])): ?>
                        <tr><td colspan="7" style="text-align: center; color: #94a3b8; padding: 24px;">No orders found for this time period.</td></tr>
                    <?php else: ?>
                        <?php foreach (array_slice($analytics['orders'], 0, 50) as $ord): ?>
                            <tr>
                                <td><strong><?= e($ord['order_number']) ?></strong></td>
                                <td><?= date('M d, H:i', strtotime($ord['created_at'])) ?></td>
                                <td><?= e($ord['customer_name'] ?? 'Guest') ?></td>
                                <td><?= $ord['table_number'] ? 'Table ' . e((string)$ord['table_number']) : 'Takeaway' ?></td>
                                <td><?= e($ord['items_summary'] ?? 'N/A') ?></td>
                                <td><?= ucfirst(e($ord['order_status'])) ?></td>
                                <td><strong><?= format_price($ord['total_amount']) ?></strong></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </body>
        </html>
        <?php
    }
}
