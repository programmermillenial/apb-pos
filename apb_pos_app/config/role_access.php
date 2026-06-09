<?php

return [
    'menus' => [
        'dashboard' => ['manager', 'cashier', 'logistic', 'picker', 'checker'],

        'product-categories' => ['manager'],
        'brands' => ['manager'],
        'units' => ['manager'],
        'products' => ['manager', 'logistic'],

        'stock-adjustments' => ['manager', 'logistic', 'checker'],
        'stock-opnames' => ['manager', 'logistic', 'picker', 'checker'],
        'purchase-orders' => ['manager', 'logistic'],
        'goods-receipts' => ['manager', 'logistic', 'checker'],
        'stock-transfers' => ['manager', 'logistic', 'picker', 'checker'],

        'sales' => ['manager', 'cashier'],
        'sales-history' => ['manager', 'cashier'],
        'customers' => ['manager', 'cashier'],
        'suppliers' => ['manager', 'logistic'],

        'reports' => ['manager'],

        'setting' => [],
        'outlets' => [],
        'users' => [],
    ],
];
