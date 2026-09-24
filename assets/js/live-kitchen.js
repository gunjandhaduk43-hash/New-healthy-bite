/**
 * Healthy Bite — Live Kitchen Status Tracker
 */

(function () {
    'use strict';

    window.refreshLiveKitchenStatus = async function () {
        const tableId = (window.State && window.State.table && window.State.table.id) ? window.State.table.id : 4;
        const restaurantId = (window.State && window.State.restaurant && window.State.restaurant.id) ? window.State.restaurant.id : 1;

        try {
            const res = await fetch(`/api/orders/table-latest?table_id=${tableId}&restaurant_id=${restaurantId}`);
            if (!res.ok) return;
            const json = await res.json();
            if (!json.data) return;

            const order = json.data;
            const status = order.order_status; // placed, accepted, preparing, ready, completed
            const orderTime = new Date(order.created_at);
            const timeStr = orderTime.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

            const stepPlaced = document.getElementById('step-placed');
            const stepAccepted = document.getElementById('step-accepted');
            const stepPreparing = document.getElementById('step-preparing');
            const stepReady = document.getElementById('step-ready');
            const stepCompleted = document.getElementById('step-completed');

            const timePlaced = document.getElementById('time-placed');

            if (timePlaced) timePlaced.textContent = timeStr;

            const stages = ['placed', 'accepted', 'preparing', 'ready', 'completed'];
            const currentIndex = stages.indexOf(status);

            const steps = [
                { el: stepPlaced, idx: 0 },
                { el: stepAccepted, idx: 1 },
                { el: stepPreparing, idx: 2 },
                { el: stepReady, idx: 3 },
                { el: stepCompleted, idx: 4 }
            ];

            steps.forEach(({ el, idx }) => {
                if (!el) return;
                el.classList.remove('completed', 'active', 'pending');
                if (idx < currentIndex) {
                    el.classList.add('completed');
                } else if (idx === currentIndex) {
                    el.classList.add('active');
                } else {
                    el.classList.add('pending');
                }
            });
        } catch (e) {
            // Keep default display
        }
    };

    // Bind event listeners on DOM load
    document.addEventListener('DOMContentLoaded', () => {
        window.refreshLiveKitchenStatus();
        setInterval(window.refreshLiveKitchenStatus, 10000);
    });
})();
