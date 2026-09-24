<?php
declare(strict_types=1);

define('ROLE_SUPER_ADMIN', 1);
define('ROLE_RESTAURANT_OWNER', 2);
define('ROLE_MANAGER', 3);
define('ROLE_STAFF', 4);

define('ORDER_TYPE_DINE_IN', 'dine_in');
define('ORDER_TYPE_TAKEAWAY', 'takeaway');

define('ORDER_STATUS_PLACED', 'placed');
define('ORDER_STATUS_ACCEPTED', 'accepted');
define('ORDER_STATUS_PREPARING', 'preparing');
define('ORDER_STATUS_READY', 'ready');
define('ORDER_STATUS_COMPLETED', 'completed');
define('ORDER_STATUS_CANCELLED', 'cancelled');

define('TABLE_STATUS_AVAILABLE', 'available');
define('TABLE_STATUS_OCCUPIED', 'occupied');
define('TABLE_STATUS_CLEANING', 'cleaning');
define('TABLE_STATUS_OUT_OF_SERVICE', 'out_of_service');

define('PAYMENT_STATUS_PENDING', 'pending');
define('PAYMENT_STATUS_COMPLETED', 'completed');
define('PAYMENT_STATUS_FAILED', 'failed');

define('FOOD_TYPE_VEGETARIAN', 'vegetarian');
define('FOOD_TYPE_NON_VEGETARIAN', 'non_vegetarian');
define('FOOD_TYPE_VEGAN', 'vegan');
define('FOOD_TYPE_JAIN', 'jain');
