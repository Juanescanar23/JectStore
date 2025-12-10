<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Shop Theme Configuration
    |--------------------------------------------------------------------------
    |
    | All the configurations are related to the shop themes.
    |
    */

    // Tema por defecto de tienda
    'shop-default' => 'default',

    'shop' => [
        'jectstore' => [
            'name'        => 'JectStore Theme',
            'assets_path' => 'public/themes/shop/jectstore',
            'views_path'  => 'resources/themes/jectstore/views',

            'vite'        => [
                'hot_file'                 => 'public/themes/shop/jectstore/hot',
                'build_directory'          => 'public/themes/shop/jectstore/build',
                'package_assets_directory' => 'themes/jectstore',
            ],
        ],

        'default' => [
            'name'        => 'Default',
            'assets_path' => 'public/themes/shop/default',
            'views_path'  => 'resources/themes/default/views',

            'vite'        => [
                'hot_file'                 => 'shop-default-vite.hot',
                'build_directory'          => 'themes/shop/default/build',
                'package_assets_directory' => 'src/Resources/assets',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Admin Theme Configuration
    |--------------------------------------------------------------------------
    |
    | All the configurations are related to the admin themes.
    |
    */

    'admin-default' => 'default',

    'admin' => [
        'default' => [
            'name'        => 'Default',
            'assets_path' => 'public/themes/admin/default',
            'views_path'  => 'resources/admin-themes/default/views',

            'vite'        => [
                'hot_file'                 => 'admin-default-vite.hot',
                'build_directory'          => 'themes/admin/default/build',
                'package_assets_directory' => 'src/Resources/assets',
            ],
        ],
    ],
];
