<?php

return [
    '__name' => 'admin-product-collateral',
    '__version' => '0.0.1',
    '__git' => 'git@github.com:getmim/admin-product-collateral.git',
    '__license' => 'MIT',
    '__author' => [
        'name' => 'Iqbal Fauzi',
        'email' => 'iqbalfawz@gmail.com',
        'website' => 'https://iqbalfn.com/'
    ],
    '__files' => [
        'modules/admin-product-collateral' => ['install','update','remove'],
        'theme/admin/product/collateral' => ['install','update','remove']
    ],
    '__dependencies' => [
        'required' => [
            [
                'admin' => NULL
            ],
            [
                'product-collateral' => NULL
            ],
            [
                'lib-form' => NULL
            ],
            [
                'lib-pagination' => NULL
            ],
            [
                'lib-formatter' => NULL
            ]
        ],
        'optional' => []
    ],
    'autoload' => [
        'classes' => [
            'AdminProductCollateral\\Controller' => [
                'type' => 'file',
                'base' => 'modules/admin-product-collateral/controller'
            ]
        ],
        'files' => []
    ],
    'routes' => [
        'admin' => [
            'adminProductCollateral' => [
                'path' => [
                    'value' => '/product/collateral'
                ],
                'method' => 'GET',
                'handler' => 'AdminProductCollateral\\Controller\\Collateral::index'
            ],
            'adminProductCollateralEdit' => [
                'path' => [
                    'value' => '/product/collateral/(:id)',
                    'params' => [
                        'id'  => 'number'
                    ]
                ],
                'method' => 'GET|POST',
                'handler' => 'AdminProductCollateral\\Controller\\Collateral::edit'
            ],
            'adminProductCollateralRemove' => [
                'path' => [
                    'value' => '/product/collateral/(:id)/remove',
                    'params' => [
                        'id'  => 'number'
                    ]
                ],
                'method' => 'GET',
                'handler' => 'AdminProductCollateral\\Controller\\Collateral::remove'
            ]
        ]
    ],
    'adminUi' => [
        'sidebarMenu' => [
            'items' => [
                'product' => [
                    'label' => 'Product',
                    'icon' => '<i class="fas fa-box-open"></i>',
                    'priority' => 0,
                    'filterable' => false,
                    'children' => [
                        'collateral' => [
                            'label' => 'Collateral',
                            'icon'  => '<i></i>',
                            'route' => ['adminProductCollateral'],
                            'perms' => 'manage_product_collateral'
                        ]
                    ]
                ]
            ]
        ]
    ],
    'libForm' => [
        'forms' => [
            'admin.product.edit' => [
                'collateral' => [
                    'label' => 'Collateral',
                    'type' => 'checkbox-group',
                    'rules' => []
                ]
            ],
            'admin.product-collateral.edit' => [
                'name' => [
                    'label' => 'Name',
                    'type' => 'text',
                    'rules' => [
                        'required' => true
                    ]
                ]
            ],
            'admin.product-collateral.index' => [
                'q' => [
                    'label' => 'Search',
                    'type' => 'search',
                    'nolabel' => true,
                    'rules' => []
                ]
            ]
        ]
    ]
];