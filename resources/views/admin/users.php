<!-- Header Section -->
<div class="hb-header-row">
    <div>
        <h1 class="hb-page-title">Users & Access</h1>
        <p class="hb-page-subtitle">Manage platform roles and restaurant assignments.</p>
    </div>
</div>

<!-- Filter Bar -->
<div class="hb-filter-bar">
    <div class="hb-search-wrapper" style="flex: 1;">
        <i class="bi bi-search hb-search-icon"></i>
        <input type="text" id="userSearch" placeholder="Search user or email" class="hb-search-input" onkeyup="filterUsersTable()">
    </div>
    <select id="userRoleFilter" class="hb-filter-select" onchange="filterUsersTable()">
        <option value="">All roles</option>
        <option value="Super Admin">Super Admin</option>
        <option value="Restaurant Owner">Restaurant Owner</option>
        <option value="Manager">Manager</option>
        <option value="Kitchen Staff">Kitchen Staff</option>
    </select>
    <select id="userStatusFilter" class="hb-filter-select" onchange="filterUsersTable()">
        <option value="">Active / Inactive</option>
        <option value="Active">Active</option>
        <option value="Inactive">Inactive</option>
    </select>
</div>

<!-- Users Table Card -->
<div class="hb-table-card-container">
    <table class="hb-table" id="usersTable">
        <thead>
            <tr>
                <th>User Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Assigned Restaurant</th>
                <th>Status</th>
                <th>Registered Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php 
                $displayUsers = !empty($users) ? $users : [
                    ['id' => 1, 'name' => 'Mira Shah', 'email' => 'mira@healthybite.in', 'role_name' => 'Super Admin', 'restaurant_name' => 'Platform', 'status' => 'active', 'created_at' => '2025-01-04'],
                    ['id' => 2, 'name' => 'Aarav Sharma', 'email' => 'aarav@greenhouse.in', 'role_name' => 'Restaurant Owner', 'restaurant_name' => 'Greenhouse Kitchen', 'status' => 'active', 'created_at' => '2025-01-12'],
                    ['id' => 3, 'name' => 'Meera Nair', 'email' => 'meera@greenhouse.in', 'role_name' => 'Manager', 'restaurant_name' => 'Greenhouse Kitchen', 'status' => 'active', 'created_at' => '2025-03-05'],
                    ['id' => 4, 'name' => 'Rohan Patel', 'email' => 'rohan@greenhouse.in', 'role_name' => 'Kitchen Staff', 'restaurant_name' => 'Greenhouse Kitchen', 'status' => 'inactive', 'created_at' => '2025-06-21'],
                ];
            ?>
            <?php foreach ($displayUsers as $u): ?>
                <?php 
                    $statusText = ucfirst($u['status'] ?? 'active');
                    $assigned = !empty($u['restaurant_name']) ? $u['restaurant_name'] : 'Platform';
                    $role = $u['role_name'] ?? 'Staff';
                    $isSelf = (int)($u['id'] ?? 0) === (int)($user['id'] ?? 0);
                ?>
                <tr data-name="<?= strtolower(e($u['name'])) ?>" data-email="<?= strtolower(e($u['email'])) ?>" data-role="<?= e($role) ?>" data-status="<?= $statusText ?>">
                    <td style="font-weight: 600; color: var(--color-gray-900);"><?= e($u['name']) ?></td>
                    <td style="color: var(--color-gray-600);"><?= e($u['email']) ?></td>
                    <td style="color: var(--color-gray-700);"><?= e($role) ?></td>
                    <td style="color: var(--color-gray-700);"><?= e($assigned) ?></td>
                    <td>
                        <?php if (strtolower($statusText) === 'active'): ?>
                            <span style="color: var(--color-gray-900); font-weight: 500;">Active</span>
                        <?php else: ?>
                            <span style="color: var(--color-gray-500); font-weight: 500;">Inactive</span>
                        <?php endif; ?>
                    </td>
                    <td style="color: var(--color-gray-600);"><?= date('d M Y', strtotime($u['created_at'] ?? 'now')) ?></td>
                    <td>
                        <div style="display: inline-flex; align-items: center; gap: 8px;">
                            <span class="hb-action-link" style="cursor: pointer;">Edit User</span>
                            <?php if (!$isSelf): ?>
                                <span style="color: var(--color-gray-300);">·</span>
                                <form action="<?= url('/admin/users/' . (int)$u['id'] . '/status') ?>" method="POST" style="display:inline;">
                                    <?= \App\Core\Csrf::field() ?>
                                    <input type="hidden" name="status" value="<?= strtolower($statusText) === 'active' ? 'inactive' : 'active' ?>">
                                    <button type="submit" class="hb-action-link" style="background:none; border:none; padding:0; cursor:pointer;">
                                        <?= strtolower($statusText) === 'active' ? 'Deactivate' : 'Activate' ?>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
function filterUsersTable() {
    const searchVal = (document.getElementById('userSearch').value || '').toLowerCase();
    const roleVal = document.getElementById('userRoleFilter').value;
    const statusVal = document.getElementById('userStatusFilter').value;
    const rows = document.querySelectorAll('#usersTable tbody tr');

    rows.forEach(row => {
        const name = row.getAttribute('data-name') || '';
        const email = row.getAttribute('data-email') || '';
        const role = row.getAttribute('data-role') || '';
        const status = row.getAttribute('data-status') || '';

        const matchesSearch = !searchVal || name.includes(searchVal) || email.includes(searchVal);
        const matchesRole = !roleVal || role === roleVal;
        const matchesStatus = !statusVal || status === statusVal;

        row.style.display = (matchesSearch && matchesRole && matchesStatus) ? '' : 'none';
    });
}
</script>
