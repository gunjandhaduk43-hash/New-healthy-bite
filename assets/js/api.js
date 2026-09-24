/**
 * HEALTHY BITE — API Client
 */

const Api = {
    async get(endpoint) {
        const response = await fetch(endpoint, {
            headers: {
                'Accept': 'application/json',
            }
        });
        const data = await response.json();
        if (!response.ok) {
            throw new Error(data.message || data.error || 'Network error occurred');
        }
        return data;
    },

    async post(endpoint, payload) {
        const response = await fetch(endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify(payload)
        });
        const data = await response.json();
        if (!response.ok) {
            throw new Error(data.message || data.error || 'Server error occurred');
        }
        return data;
    },

    async fetchFoodDetails(foodId, restaurantId) {
        return this.get(`/api/foods/${foodId}?restaurant_id=${restaurantId}`);
    },

    async validateCart(cartItems, restaurantId) {
        return this.post('/api/cart/validate', {
            restaurant_id: restaurantId,
            items: cartItems
        });
    },

    async submitOrder(orderPayload) {
        return this.post('/api/orders', orderPayload);
    },

    async getOrderStatus(orderNumber) {
        return this.get(`/api/orders/${orderNumber}`);
    },

    async simulatePayment(orderId, paymentMethod) {
        return this.post('/api/payment/simulate', {
            order_id: orderId,
            payment_method: paymentMethod
        });
    },

    async submitReview(orderNumber, rating, comment) {
        return this.post('/api/reviews', {
            order_number: orderNumber,
            rating: rating,
            comment: comment
        });
    }
};

window.Api = Api;
