<!-- Header Section -->
<div class="hb-header-row">
    <div>
        <h1 class="hb-page-title">Staff Management</h1>
        <p class="hb-page-subtitle">Manage access for your restaurant team.</p>
    </div>
    <div>
        <button type="button" onclick="openModal('addStaffModal')" class="hb-btn hb-btn-primary">
            <i class="bi bi-plus-lg"></i> Add Staff
        </button>
    </div>
</div>

<!-- Filter Bar -->
<div class="hb-filter-bar">
    <div class="hb-search-wrapper" style="flex: 1;">
        <i class="bi bi-search hb-search-icon"></i>
        <input type="text" id="staffSearch" placeholder="Search staff" class="hb-search-input" onkeyup="filterStaffTable()">
    </div>
    <select id="staffRoleFilter" class="hb-filter-select" onchange="filterStaffTable()">
        <option value="">All roles</option>
        <option value="Restaurant Owner">Restaurant Owner</option>
        <option value="Manager">Manager</option>
        <option value="Kitchen Staff">Kitchen Staff</option>
        <option value="Waiter">Waiter</option>
    </select>
    <select id="staffStatusFilter" class="hb-filter-select" onchange="filterStaffTable()">
        <option value="">Active / Inactive</option>
        <option value="Active">Active</option>
        <option value="Inactive">Inactive</option>
    </select>
</div>

<!-- Staff Table Card -->
<div class="hb-table-card-container">
    <table class="hb-table" id="staffTable">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Joined Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($staff)): ?>
                <tr>
                    <td colspan="6" style="text-align: center; color: var(--color-gray-500); padding: 32px;">
                        No staff members found. Click "Add Staff" to create an account.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($staff as $s): ?>
                    <?php 
                        $statusText = ucfirst($s['status'] ?? 'active');
                        $roleName = $s['role_name'] ?? 'Kitchen Staff';
                        $isOwner = strtolower($roleName) === 'restaurant owner' || strtolower($roleName) === 'owner';
                    ?>
                    <tr data-name="<?= strtolower(e($s['name'])) ?>" data-email="<?= strtolower(e($s['email'])) ?>" data-role="<?= e($roleName) ?>" data-status="<?= $statusText ?>">
                        <td style="font-weight: 600; color: var(--color-gray-900);"><?= e($s['name']) ?></td>
                        <td style="color: var(--color-gray-600);"><?= e($s['email']) ?></td>
                        <td style="color: var(--color-gray-700);"><?= e($roleName) ?></td>
                        <td>
                            <?php if (strtolower($statusText) === 'active'): ?>
                                <span style="color: var(--color-gray-900); font-weight: 500;">Active</span>
                            <?php else: ?>
                                <span style="color: var(--color-gray-500); font-weight: 500;">Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td style="color: var(--color-gray-600);"><?= date('d M Y', strtotime($s['created_at'] ?? 'now')) ?></td>
                        <td>
                            <div style="display: inline-flex; align-items: center; gap: 8px;">
                                <button type="button" onclick="openEditStaffModal(<?= (int)$s['id'] ?>, '<?= e(addslashes($s['name'])) ?>', <?= (int)($s['role_id'] ?? 4) ?>)" class="hb-action-link" style="background:none; border:none; padding:0; cursor:pointer;">
                                    Edit
                                </button>
                                <?php if (!$isOwner): ?>
                                    <span style="color: var(--color-gray-300);">·</span>
                                    <form action="<?= url('/owner/staff/' . (int)$s['id'] . '/toggle') ?>" method="POST" style="display:inline;">
                                        <?= \App\Core\Csrf::field() ?>
                                        <button type="submit" class="hb-action-link" style="background:none; border:none; padding:0; cursor:pointer;">
                                            <?= strtolower($statusText) === 'active' ? 'Deactivate' : 'Activate' ?>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal: Add Staff Member -->
<div class="hb-modal-backdrop" id="addStaffModal">
    <div class="hb-modal">
        <div class="hb-modal-header">
            <h2 class="hb-modal-title">Add Team Member</h2>
            <button type="button" class="hb-modal-close" onclick="closeModal('addStaffModal')">&times;</button>
        </div>
        <form action="<?= url('/owner/staff/create') ?>" method="POST">
            <?= \App\Core\Csrf::field() ?>
            <div class="hb-modal-body">
                <div class="hb-form-group">
                    <label class="hb-form-label">Full Name *</label>
                    <input type="text" name="name" class="hb-form-control" placeholder="e.g. Meera Nair" required>
                </div>
                <div class="hb-form-group">
                    <label class="hb-form-label">Email Address *</label>
                    <input type="email" name="email" class="hb-form-control" placeholder="e.g. meera@greenhouse.in" required>
                </div>
                <div class="hb-form-group">
                    <label class="hb-form-label">Operational Role *</label>
                    <select name="role_id" class="hb-form-control" required>
                        <option value="4">Kitchen Staff</option>
                        <option value="3">Restaurant Manager</option>
                    </select>
                </div>
                <div class="hb-form-group">
                    <label class="hb-form-label">Initial Password *</label>
                    <input type="password" name="password" class="hb-form-control" placeholder="Minimum 6 characters" value="Secret@123" required>
                </div>
            </div>
            <div class="hb-modal-footer">
                <button type="button" class="hb-btn hb-btn-outline" onclick="closeModal('addStaffModal')">Cancel</button>
                <button type="submit" class="hb-btn hb-btn-primary">Add Team Member</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Edit Staff Member -->
<div class="hb-modal-backdrop" id="editStaffModal">
    <div class="hb-modal">
        <div class="hb-modal-header">
            <h2 class="hb-modal-title">Edit Team Member</h2>
            <button type="button" class="hb-modal-close" onclick="closeModal('editStaffModal')">&times;</button>
        </div>
        <form id="editStaffForm" action="" method="POST">
            <?= \App\Core\Csrf::field() ?>
            <div class="hb-modal-body">
                <div class="hb-form-group">
                    <label class="hb-form-label">Full Name *</label>
                    <input type="text" id="editStaffName" name="name" class="hb-form-control" required>
                </div>
                <div class="hb-form-group">
                    <label class="hb-form-label">Operational Role *</label>
                    <select id="editStaffRole" name="role_id" class="hb-form-control" required>
                        <option value="4">Kitchen Staff</option>
                        <option value="3">Restaurant Manager</option>
                        <option value="2">Restaurant Owner</option>
                    </select>
                </div>
                <div class="hb-form-group">
                    <label class="hb-form-label">Reset Password (leave blank to keep current)</label>
                    <input type="password" name="password" class="hb-form-control" placeholder="New password (optional)">
                </div>
            </div>
            <div class="hb-modal-footer">
                <button type="button" class="hb-btn hb-btn-outline" onclick="closeModal('editStaffModal')">Cancel</button>
                <button type="submit" class="hb-btn hb-btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
window.openModal = function(id) {
    const el = document.getElementById(id);
    if (el) {
        el.classList.add('active');
        el.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
};

window.closeModal = function(id) {
    const el = document.getElementById(id);
    if (el) {
        el.classList.remove('active');
        el.classList.add('hidden');
        document.body.style.overflow = '';
    }
};

function openEditStaffModal(id, name, roleId) {
    document.getElementById('editStaffForm').action = '<?= url('/owner/staff/') ?>' + id + '/edit';
    document.getElementById('editStaffName').value = name;
    document.getElementById('editStaffRole').value = roleId;
    openModal('editStaffModal');
}

function filterStaffTable() {
    const searchVal = (document.getElementById('staffSearch').value || '').toLowerCase();
    const roleVal = document.getElementById('staffRoleFilter').value;
    const statusVal = document.getElementById('staffStatusFilter').value;
    const rows = document.querySelectorAll('#staffTable tbody tr');

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
