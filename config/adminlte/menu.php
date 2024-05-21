<?php

return [
    // Navbar items:
    [
        'type' => 'navbar-search',
        'text' => 'search',
        'topnav_right' => true,
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
        'submenu' => [
            [
                'text' => 'Daybook',
                'url' => '/daybook',
                'icon' => 'fa fa-book fa-fw',
            ]
        ],
    ],
];
