<?php

return array(
    'controllers' => array(
        'invokables' => array(
            'LosUser' => 'LosUser\Controller\UserController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'losuser' => array(
                'type' => 'Literal',
                'priority' => 1000,
                'options' => array(
                    'route' => '/losuser',
                    'defaults' => array(
                        'controller' => 'LosUser',
                        'action'     => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'login' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/login',
                            'defaults' => array(
                                'controller' => 'LosUser',
                                'action'     => 'login',
                            ),
                        ),
                    ),
                    'authenticate' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/authenticate',
                            'defaults' => array(
                                'controller' => 'LosUser',
                                'action'     => 'authenticate',
                            ),
                        ),
                    ),
                    'logout' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/logout',
                            'defaults' => array(
                                'controller' => 'LosUser',
                                'action'     => 'logout',
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'LosUser' => __DIR__.'/../view',
        ),
    ),
);
