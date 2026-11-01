<?php

return [
    [
        'type' => 'header',
        'title' => 'Principal',
    ],
    [
        'type' => 'link',
        'title' => 'Dashboard',
        'icon' => 'fa-solid fa-gauge',
        'route' => 'admin.dashboard',
        'active' => ['admin.dashboard'],
    ],
    [
        'type' => 'group',
        'title' => 'Inventario',
        'icon' => 'fa-solid fa-boxes-stacked',
        'active' => ['admin.categories.*', 'admin.products.*', 'admin.warehouses.*'],
        'items' => [
            [
                'type' => 'link',
                'title' => 'Categorías',
                'route' => 'admin.categories.index',
                'active' => 'admin.categories.*',
                'can' => [
                    'create-categories',
                    'read-categories',
                    'update-categories',
                    'delete-categories',
                ]
            ],
            [
                'type' => 'link',
                'title' => 'Productos',
                'route' => 'admin.products.index',
                'active' => 'admin.products.*',
                'can' => [
                    'create-products',
                    'read-products',
                    'update-products',
                    'delete-products',
                ]
            ],
            [
                'type' => 'link',
                'title' => 'Almacenes',
                'route' => 'admin.warehouses.index',
                'active' => 'admin.warehouses.*',
                'can' => [
                    'create-warehouses',
                    'read-warehouses',
                    'update-warehouses',
                    'delete-warehouses',
                ]
            ],
        ],
    ],
    [
        'type' => 'group',
        'title' => 'Compras',
        'icon' => 'fa-solid fa-cart-shopping',
        'active' => ['admin.suppliers.*', 'admin.purchase-orders.*', 'admin.purchases.*'],
        'items' => [
            [
                'type' => 'link',
                'title' => 'Proveedores',
                'icon' => 'fa-solid fa-users',
                'route' => 'admin.suppliers.index',
                'active' => 'admin.suppliers.*',
                'can' => [
                    'create-suppliers',
                    'read-suppliers',
                    'update-suppliers',
                    'delete-suppliers',
                ]
            ],
            [
                'type' => 'link',
                'title' => 'Órdenes de Compra',
                'route' => 'admin.purchase-orders.index',
                'active' => 'admin.purchase-orders.*',
                'can' => [
                    'create-purchase-orders',
                    'read-purchase-orders',
                    'update-purchase-orders',
                    'delete-purchase-orders',
                ]
            ],
            [
                'type' => 'link',
                'title' => 'Compras',
                'route' => 'admin.purchases.index',
                'active' => 'admin.purchases.*',
                'can' => [
                    'create-purchases',
                    'read-purchases',
                    'update-purchases',
                    'delete-purchases',
                ]
            ],
        ],
    ],
    [
        'type' => 'group',
        'title' => 'Ventas',
        'icon' => 'fa-solid fa-cash-register',
        'active' => ['admin.customers.*', 'admin.quotes.*', 'admin.sales.*'],
        'items' => [
            [
                'type' => 'link',
                'title' => 'Clientes',
                'icon' => 'fa-solid fa-users',
                'route' => 'admin.customers.index',
                'active' => 'admin.customers.*',
                'can' => [
                    'create-customers',
                    'read-customers',
                    'update-customers',
                    'delete-customers',
                ]
            ],
            [
                'type' => 'link',
                'title' => 'Cotizaciones',
                'route' => 'admin.quotes.index',
                'active' => 'admin.quotes.*',
                'can' => [
                    'create-quotes',
                    'read-quotes',
                    'update-quotes',
                    'delete-quotes',
                ]
            ],
            [
                'type' => 'link',
                'title' => 'Ventas',
                'route' => 'admin.sales.index',
                'active' => 'admin.sales.*',
                'can' => [
                    'create-sales',
                    'read-sales',
                    'update-sales',
                    'delete-sales',
                ]
            ],
        ],
    ],
    [
        'type' => 'group',
        'title' => 'Movimientos',
        'icon' => 'fa-solid fa-exchange-alt',
        'active' => ['admin.movements.*', 'admin.transfers.*'],
        'items' => [
            [
                'type' => 'link',
                'title' => 'Entradas y Salidas',
                'route' => 'admin.movements.index',
                'active' => 'admin.movements.*',
                'can' => [
                    'create-movements',
                    'read-movements',
                    'update-movements',
                    'delete-movements',
                ]
            ],
            [
                'type' => 'link',
                'title' => 'Transferencias',
                'route' => 'admin.transfers.index',
                'active' => 'admin.transfers.*',
                'can' => [
                    'create-transfers',
                    'read-transfers',
                    'update-transfers',
                    'delete-transfers',
                ]
            ],
        ],
    ],
    [
        'type' => 'group',
        'title' => 'Reportes',
        'icon' => 'fa-solid fa-chart-line',
        'active' => ['admin.reports.top-products', 'admin.reports.top-customers', 'admin.reports.low-stock'],
        'items' => [
            [
                'type' => 'link',
                'title' => 'Productos top',
                'route' => 'admin.reports.top-products',
                'active' => 'admin.reports.top-products',
                'can' => [
                    'read-top-products',
                ]
            ],
            [
                'type' => 'link',
                'title' => 'Clientes frecuentes',
                'route' => 'admin.reports.top-customers',
                'active' => 'admin.reports.top-customers',
                'can' => [
                    'read-top-customers',
                ]
            ],
            [
                'type' => 'link',
                'title' => 'Stock bajo',
                'route' => 'admin.reports.low-stock',
                'active' => 'admin.reports.low-stock',
                'can' => [
                    'read-low-stock',
                ]
            ],
        ],
    ],
    [
        'type' => 'group',
        'title' => 'Sunat',
        'icon' => 'fa-solid fa-exchange-alt',
        'active' => ['admin.sunat.invoice.*', 'admin.sunat.note.*'],
        'items' => [
            [
                'type' => 'link',
                'title' => 'Facturas',
                'route' => 'admin.sunat.invoice',
                'active' => 'admin.sunat.invoice',
                'can' => [
                    'read-invoice',
                    'send-invoice',
                    'xml-invoice',
                    'pdf-invoice',
                ]
            ],
            [
                'type' => 'link',
                'title' => 'Notas de crédito',
                'route' => 'admin.sunat.note',
                'active' => 'admin.sunat.note',
                'can' => [
                    'read-note',
                    'send-note',
                    'xml-note',
                    'pdf-note',
                ]
            ],
        ],
    ],
    [
        'type' => 'header',
        'title' => 'Configuración',
        'can' => [
            'read-users',
            'read-roles',
            'read-settings',
        ]
    ],
    [
        'type' => 'link',
        'title' => 'Compañías',
        'icon' => 'fa-solid fa-building',
        'route' => 'admin.companies.index',
        'active' => 'admin.companies.*',
        'can' => [
            'create-companies',
            'read-companies',
            'update-companies',
            'delete-companies',
        ]
    ],
    [
        'type' => 'link',
        'title' => 'Usuarios',
        'icon' => 'fa-solid fa-users-cog',
        'route' => 'admin.users.index',
        'active' => 'admin.users.*',
        'can' => [
            'create-users',
            'read-users',
            'update-users',
            'delete-users',
        ]
    ],
    [
        'type' => 'link',
        'title' => 'Roles',
        'icon' => 'fa-solid fa-user-shield',
        'route' => 'admin.roles.index',
        'active' => 'admin.roles.*',
        'can' => [
            'create-roles',
            'read-roles',
            'update-roles',
            'delete-roles',
        ]
    ],
    [
        'type' => 'link',
        'title' => 'Ajustes',
        'icon' => 'fa-solid fa-cogs',
        'can' => [
            'read-settings',
            'update-settings',
        ]
    ],
];
