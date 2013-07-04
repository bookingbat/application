<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return array(
    'router' => array(
        'routes' => array(

            // home page route
            'home' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Services',
                        'action'     => 'choose',
                    ),
                ),
            ),

            // login
            'login' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/login',
                    'defaults' => array(
                        'controller' => 'Application\Controller\User',
                        'action'     => 'login',
                    ),
                ),
            ),

            // logout
            'logout' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/logout',
                    'defaults' => array(
                        'controller' => 'Application\Controller\User',
                        'action'     => 'logout',
                    ),
                ),
            ),

            // choose services route
            'services' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/services',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Services',
                        'action'     => 'choose',
                    ),
                ),
            ),

            // calendar route
            'calendar' => array(
                'type' => 'segment',
                'options' => array(
                    'route'    => '/calendar/service/:service[/:month][-:year]',
                    'constraints'=>array(
                        'service'=>'[0-9]+',
                        'month'=>'[0-9]+',
                        'year'=>'[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Calendar',
                        'action'     => 'index',
                    ),
                ),
            ),

            // make booking route
            'make-booking' => array(
                'type' => 'segment',
                'options' => array(
                    'route'    => '/make-booking/:action/:service[/:day][/:duration][/:time][/:staff]',
                    'constraints'=>array(
                        'service'=>'[0-9]+',
                        'month'=>'[0-9]+',
                        'year'=>'[0-9]+',

                        'duration'=>'[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Makebooking',
                        'action'     => 'booking',
                    ),
                ),
            ),

            // manage staff route
            'manage-staff' => array(
                'type' => 'segment',
                'options' => array(
                    'route'    => '/manage-staff[/:action][/:id]',
                    'defaults' => array(
                        'controller' => 'Application\Controller\User',
                        'action'     => 'manage',
                    ),
                ),
            ),

            // staff availability route
            'staff-availability' => array(
                'type' => 'segment',
                'options' => array(
                    'route'    => '/manage-staff-availability/:staff',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Availability',
                        'action'     => 'index',
                    ),
                ),
            ),

            // staff services route
            'staff-services' => array(
                'type' => 'segment',
                'options' => array(
                    'route'    => '/manage-staff-services/:staff',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Services',
                        'action'     => 'assign',
                    ),
                ),
            ),

            // manage services
            'manage-services' => array(
                'type' => 'segment',
                'options' => array(
                    'route'    => '/manage-services',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Services',
                        'action'     => 'manage',
                    ),
                ),
            ),

            // edit service
            'edit-service' => array(
                'type' => 'segment',
                'options' => array(
                    'route'    => '/edit-service/:id',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Services',
                        'action'     => 'edit',
                    ),
                ),
            ),

            'appointments' => array(
                'type' => 'segment',
                'options' => array(
                    'route'    => '/appointments/',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Appointments',
                        'action'     => 'index',
                    ),
                ),
            ),

            // "abstract route"
            'application' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/application',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                ),
            ),

        ),
    ),
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'aliases' => array(
            'translator' => 'MvcTranslator',
        ),
    ),
    'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Application\Controller\Availability' => 'Application\Controller\AvailabilityController',
            'Application\Controller\Appointments' => 'Application\Controller\AppointmentsController',
            'Application\Controller\Index' => 'Application\Controller\IndexController',
            'Application\Controller\Calendar' => 'Application\Controller\CalendarController',
            'Application\Controller\Makebooking' => 'Application\Controller\MakebookingController',
            'Application\Controller\Services' => 'Application\Controller\ServicesController',
            'Application\Controller\User' => 'Application\Controller\UserController',
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    'view_helpers'=>array(
        'invokables'=>array(
            'time'=>'\Application\Helper\Time',
            'datetime'=>'\Application\Helper\Datetime',
            'duration'=>'\Application\Helper\Duration',
            'email'=>'\Application\Helper\Email',
            'phone'=>'\Application\Helper\Phone',
        )
    )
);
