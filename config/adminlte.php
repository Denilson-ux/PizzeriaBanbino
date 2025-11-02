<?php

return [
    'title' => 'Pizzería Admin',
    'title_prefix' => '',
    'title_postfix' => '',
    'bottom_title' => 'Sistema de Gestión',
    'current_user' => false,
    'dashboard_url' => 'admin',
    'logout_url' => 'logout',
    'login_url' => 'login',
    'register_url' => 'register',
    'password_reset_url' => 'password/reset',
    'password_email_url' => 'password/email',
    'profile_url' => false,
    'setting_url' => false,
    'right_sidebar_slide' => true,
    'right_sidebar_push' => false,
    'right_sidebar_scrollbar_theme' => 'os-theme-light',
    'right_sidebar_scrollbar_auto_hide' => 'l',
    'use_route_url' => false,
    'layout_topnav' => null,
    'layout_boxed' => null,
    'layout_fixed_sidebar' => null,
    'layout_fixed_navbar' => null,
    'layout_fixed_footer' => null,
    'layout_dark_mode' => null,
    'classes_body' => '',
    'classes_brand' => '',
    'classes_brand_text' => '',
    'classes_content_wrapper' => '',
    'classes_content_header' => '',
    'classes_content' => '',
    'classes_sidebar' => 'sidebar-dark-primary elevation-4',
    'classes_sidebar_nav' => '',
    'classes_topnav' => 'navbar-white navbar-light',
    'classes_topnav_nav' => 'navbar-expand',
    'classes_topnav_container' => 'container',
    'sidebar_mini' => 'lg',
    'sidebar_collapse' => false,
    'sidebar_collapse_auto_size' => false,
    'sidebar_collapse_remember' => false,
    'sidebar_collapse_remember_no_transition' => true,
    'sidebar_scrollbar_theme' => 'os-theme-light',
    'sidebar_scrollbar_auto_hide' => 'l',
    'sidebar_nav_accordion' => true,
    'sidebar_nav_animation_speed' => 300,
    'right_sidebar' => false,
    'right_sidebar_icon' => 'fas fa-cogs',
    'right_sidebar_theme' => 'dark',
    'right_sidebar_slide' => true,
    'right_sidebar_push' => false,
    'right_sidebar_scrollbar_theme' => 'os-theme-light',
    'right_sidebar_scrollbar_auto_hide' => 'l',
    'enabled_laravel_mix' => false,
    'laravel_mix_css_path' => 'css/app.css',
    'laravel_mix_js_path' => 'js/app.js',

    'menu' => [
        ['type' => 'fullscreen-widget', 'topnav_right' => true],

        ['header' => 'Ventas'],
        [
            'text' => 'Ventas',
            'icon' => 'fas fa-fw fa-tag',
            'submenu' => [
                [ 'text' => 'Nueva venta','url' => 'admin/nota-venta-create','icon' => 'fas fa-fw fa-dollar-sign','can' => ['ventas','role:Administrador'] ],
                [ 'text' => 'Ventas','url' => 'admin/nota-venta','icon' => 'fas fa-fw fa-list-ul','can' => ['ventas','role:Administrador'] ],
                [ 'text' => 'Métodos de pago','url' => 'admin/tipo-pago','icon' => 'fas fa-fw fa-credit-card','can' => ['ventas','role:Administrador'] ],
            ]
        ],

        ['header' => 'Pedidos'],
        [
            'text' => 'Pedidos',
            'icon' => 'fas fa-fw fa-shopping-cart',
            'submenu' => [
                [ 'text' => 'Pedidos','url' => 'admin/pedido','icon' => 'fas fa-fw fa-shopping-cart','can' => ['pedidos','role:Administrador'] ],
                [ 'text' => 'Mis pedidos','url' => 'admin/mispedidos','icon' => 'fas fa-fw fa-receipt','can' => ['pedidos','role:Administrador'] ],
            ]
        ],

        ['header' => 'Compras y Proveedores'],
        [
            'text' => 'Compras',
            'icon' => 'fas fa-fw fa-shopping-basket',
            'submenu' => [
                [ 'text' => 'Gestionar Compras','url' => 'admin/compras','icon' => 'fas fa-fw fa-list','can' => ['compras','role:Administrador'] ],
            ]
        ],
        [
            'text' => 'Proveedores',
            'icon' => 'fas fa-fw fa-truck',
            'submenu' => [
                [ 'text' => 'Gestionar Proveedores','url' => 'admin/proveedores','icon' => 'fas fa-fw fa-list','can' => ['proveedores','role:Administrador'] ],
            ]
        ],

        ['header' => 'Inventario y Almacenes'],
        [
            'text' => 'Almacenes',
            'icon' => 'fas fa-fw fa-warehouse',
            'submenu' => [
                [ 'text' => 'Gestionar Almacenes','url' => 'admin/almacenes','icon' => 'fas fa-fw fa-list','can' => ['almacenes','role:Administrador'] ],
            ]
        ],
        [
            'text' => 'Ingredientes',
            'icon' => 'fas fa-fw fa-seedling',
            'submenu' => [
                [ 'text' => 'Gestionar Ingredientes','url' => 'admin/ingredientes','icon' => 'fas fa-fw fa-list','can' => ['ingredientes','role:Administrador'] ],
            ]
        ],

        ['header' => 'Producto menú'],
        [
            'text' => 'Menú e Producto',
            'icon' => 'fas fa-fw fa-utensils',
            'submenu' => [
                [ 'text' => 'Menú','url' => 'admin/menu','icon' => 'fas fa-fw fa-book','can' => ['items_menu','role:Administrador'] ],
                [ 'text' => 'Producto','url' => 'admin/item-menu','icon' => 'fas fa-fw fa-hamburger','can' => ['items_menu','role:Administrador'] ],
                [ 'text' => 'Tipo','url' => 'admin/tipo-menu','icon' => 'fas fa-fw fa-hamburger','can' => ['items_menu','role:Administrador'] ],
            ]
        ],

        ['header' => 'Personas y usuarios'],
        [
            'text' => 'Personas',
            'icon' => 'fas fa-fw fa-user-friends',
            'submenu' => [
                [ 'text' => 'Clientes','icon' => 'fas fa-fw fa-user','url' => 'admin/cliente','can' => ['personas','role:Administrador'] ],
                [ 'text' => 'Empleados','icon' => 'fas fa-fw fa-briefcase','url' => 'admin/empleado','can' => ['personas','role:Administrador'] ],
                [ 'text' => 'Repartidores','icon' => 'fas fa-fw fa-motorcycle','url' => 'admin/repartidor','can' => ['personas','role:Administrador'] ],
            ]
        ],
        [
            'text' => 'Usuarios',
            'icon' => 'fas fa-fw fa-users',
            'submenu' => [
                [ 'text' => 'Usuarios','icon' => 'fas fa-fw fa-user','url' => 'admin/user','can' => ['usuarios','role:Administrador'] ],
                [ 'text' => 'Roles','icon' => 'fas fa-fw fa-lock','url' => 'admin/rol','can' => ['usuarios','role:Administrador'] ],
                [ 'text' => 'Asignación Roles-Permisos','icon' => 'fas fa-fw fa-user-shield','url' => 'admin/asignacion-roles-permisos','can' => ['usuarios','role:Administrador'] ],
            ]
        ],

        ['header' => 'Vehículos'],
        [
            'text' => 'Vehículos',
            'icon' => 'fas fa-fw fa-car',
            'submenu' => [
                [ 'text' => 'Vehículos','icon' => 'fas fa-fw fa-car','url' => 'admin/vehiculo','can' => ['vehiculos','role:Administrador'] ],
                [ 'text' => 'Tipo Vehículos','icon' => 'fas fa-fw fa-car-side','url' => 'admin/tipo-vehiculo','can' => ['vehiculos','role:Administrador'] ],
            ]
        ],

        ['header' => 'Configuración'],
        [ 'text' => 'Restaurante','icon' => 'fas fa-fw fa-store','url' => 'admin/restaurante','can' => ['restaurante','role:Administrador'] ],
    ],

    'filters' => [
        JeroenNoten\LaravelAdminLte\Menu\Filters\GateFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\HrefFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\SearchFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ActiveFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ClassesFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\LangFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\DataFilter::class,
    ],

    'plugins' => [ ],

    'iframe' => [ ],

    'livewire' => false,
];