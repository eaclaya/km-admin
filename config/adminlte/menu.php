<?php

return [
    // Navbar items:
    [
        'type' => 'navbar-search',
        'text' => 'search',
        'topnav_right' => true,
        'url' => '#nav-search',
        'input_name' => 'nav_search',
        'id' => 'navbarSearch'
    ],
    [
        'type' => 'fullscreen-widget',
        'topnav_right' => true,
    ],

    // Sidebar items:
    [
        'type' => 'sidebar-menu-search',
        'text' => 'Buscar En Menu',
    ],
    [
        'text' => 'Dashboard',
        'url' => '/dashboard',
        'icon' => 'fa fa-fw fa-home',
        'label' => 'New',
        'label_color' => 'primary',
    ],
    ['header' => 'Modulos'],
    [
        'text' => 'Finance Daybook',
        'icon' => 'fa fa-table fa-fw',
        'submenu' => require __DIR__ . '/modules/FinanceDaybook.php',
    ],
    [
        'text' => 'Invoices Discount',
        'icon' => 'fa fa-table fa-fw',
        'submenu' => require __DIR__ . '/modules/InvoicesDiscount.php',
    ],
    [
        'text' => 'Clone Models',
        'icon' => 'fa fa-table fa-fw',
        'submenu' => require __DIR__ . '/modules/CloneModels.php',
    ],
];
