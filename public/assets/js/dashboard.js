/**
 * Healthy Bite — Unified Dashboard JS
 * Client interactions for Owner & Admin Portals
 */
document.addEventListener('DOMContentLoaded', () => {
    // 1. Mobile Sidebar Navigation Drawer
    const mobileNavToggle = document.querySelector('.mobile-nav-toggle');
    const sidebar = document.querySelector('.dashboard-sidebar');
    if (mobileNavToggle && sidebar) {
        mobileNavToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            sidebar.classList.toggle('mobile-open');
        });

        document.addEventListener('click', (e) => {
            if (sidebar.classList.contains('mobile-open') && !sidebar.contains(e.target)) {
                sidebar.classList.remove('mobile-open');
            }
        });
    }

    // 2. Generic Client-side Table Search & Filters
    const searchInputs = document.querySelectorAll('[data-table-search]');
    searchInputs.forEach(input => {
        const tableSelector = input.getAttribute('data-table-search');
        const table = document.querySelector(tableSelector);
        if (!table) return;

        input.addEventListener('input', () => {
            const query = input.value.toLowerCase().trim();
            const rows = table.querySelectorAll('tbody tr');

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(query) ? '' : 'none';
            });
        });
    });

    // 3. Dropdown Filter for Tables
    const filterSelects = document.querySelectorAll('[data-table-filter]');
    filterSelects.forEach(select => {
        const tableSelector = select.getAttribute('data-table-filter');
        const colIndex = parseInt(select.getAttribute('data-col-index') || '0', 10);
        const table = document.querySelector(tableSelector);
        if (!table) return;

        select.addEventListener('change', () => {
            const val = select.value.toLowerCase().trim();
            const rows = table.querySelectorAll('tbody tr');

            rows.forEach(row => {
                if (!val || val === 'all') {
                    row.style.display = '';
                    return;
                }
                const cell = row.querySelectorAll('td')[colIndex];
                if (!cell) return;
                const cellText = cell.textContent.toLowerCase().trim();
                row.style.display = cellText.includes(val) ? '' : 'none';
            });
        });
    });

    // 4. Modal Helpers
    window.openModal = window.openHbModal = function(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('active');
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    };

    window.closeModal = window.closeHbModal = function(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('active');
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }
    };

    // Close modal on click outside dialog
    document.querySelectorAll('.hb-modal-backdrop, .hb-modal-overlay').forEach(backdrop => {
        backdrop.addEventListener('click', (e) => {
            if (e.target === backdrop) {
                backdrop.classList.remove('active');
                backdrop.classList.add('hidden');
                document.body.style.overflow = '';
            }
        });
    });

    // 5. Kitchen Kanban Order Status Update
    window.advanceOrderStatus = async function(orderId, newStatus, btn) {
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<i class="bi bi-arrow-repeat spin"></i> Updating...';
        }

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const formData = new FormData();
            formData.append('status', newStatus);
            if (csrfToken) {
                formData.append('csrf_token', csrfToken);
            }

            const res = await fetch(`/owner/orders/${orderId}/status`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (res.ok) {
                // Success: reload or update DOM
                window.location.reload();
            } else {
                alert('Could not update order status. Please try again.');
                if (btn) btn.disabled = false;
            }
        } catch (err) {
            console.error('Failed to advance order status:', err);
            window.location.reload();
        }
    };
});
