<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

return array(

    // View config
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),

    // Doctrine config
//    'doctrine' => array(
//        'driver' => array(
//            __NAMESPACE__ . '_driver' => array(
//                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
//                'cache' => 'array',
//                'paths' => array(__DIR__ . '/../src/' . __NAMESPACE__ . '/Entity')
//            ),
//            'orm_default' => array(
//                'drivers' => array(
//                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver'
//                )
//            )
//        ),
//    ),

    'controllers' => array(
        'invokables' => array(
            'Application\Controller\Index' => 'Application\Controller\IndexController',
            //'Application\Controller\Rest' => 'Application\Controller\RestController'
        ),
    ),

//    'controllers' => array(
//        'factories' => array(
//            'Application\Controller\Rest' => 'Application\Factory\RestControllerFactory',
//        )
//    ),

    // Routing config
    'router' => array(
        'routes' => array(

            // The following is a route to simplify getting started creating
            // new controllers and actions without needing to create a new
            // module. Simply drop new controllers in, and you can access them
            // using the path /application/:controller/:action
            'home' => array(
                'type' => 'Literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'index',
                    ),
                ),

            )


//            'lists' => array(
//                'type'    => 'Literal',
//                'options' => array(
//                    'route'    => '/v1/lists',
//                    'constraints' => array(
//                        'controller' => 'Application\Controller\Rest',
//                        'action'     => 'index',
//                    ),
//                    'defaults' => array(
//                        'controller' => 'Application\Controller\Rest',
//                        'action'     => 'index',
//                    ),
//                ),
//            ),
//
//            'list' => array(
//                'type'    => 'Segment',
//                'options' => array(
//                    'route'    => '/v1/lists[/:id]',
//                    'constraints' => array(
//                        'id'     => '[0-9]+',
//                    ),
//                    'defaults' => array(
//                        'controller' => 'v1\Controller\Lists',
//                        'action'        => 'index',
//                    ),
//                ),
//            ),
//
//            'tasks' => array(
//                'type'    => 'Segment',
//                'options' => array(
//                    'route'    => '/v1/lists[/:id]/tasks',
//                    'constraints' => array(
//                        'id'     => '[0-9]+',
//                    ),
//                    'defaults' => array(
//                        'controller' => 'Application\Controller\Rest',
//                        'action'        => 'index',
//                    ),
//                ),
//            ),
//
//            'task' => array(
//                'type'    => 'Segment',
//                'options' => array(
//                    'route'    => '/v1/lists[/:id]/tasks[/:taskid]',
//                    'constraints' => array(
//                        'id'     => '[0-9]+',
//                        'taskid' => '[0-9]+',
//                    ),
//                    'defaults' => array(
//                        'controller' => 'Application\Controller\Rest',
//                        'action'        => 'index',
//                    ),
//                ),
//            ),
        ),
    ),
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'factories' => array(
            'translator' => 'Zend\Mvc\Service\TranslatorServiceFactory',
        ),
    ),
    'translator' => array(
        'locale' => 'tr_TR',
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ),

    // Placeholder for console routes
    'console' => array(
        'router' => array(
            'routes' => array(
            ),
        ),
    ),
);
