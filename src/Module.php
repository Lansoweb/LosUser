<?php

namespace LosUser;

class Module
{
    public function getServiceConfig()
    {
        return [
            'invokables' => array(
                'LosUser\Authentication\Adapter\Doctrine' => 'LosUser\Authentication\Adapter\Doctrine',
                'LosUser\Authentication\Storage\Doctrine' => 'LosUser\Authentication\Storage\Doctrine',
                'losuser_user_service' => 'LosUser\Service\User',
            ),
            'factories' => [
                'losuser_module_options' => function ($sm) {
                    $config = $sm->get('Config');

                    return new Options\ModuleOptions(isset($config['losuser']) ? $config['losuser'] : array());
                },
                'losuser_auth_service' => function ($sm) {
                    return new \Zend\Authentication\AuthenticationService($sm->get('LosUser\Authentication\Storage\Doctrine'), $sm->get('LosUser\Authentication\Adapter\Doctrine'));
                },
                'losuser_login_form' => function ($sm) {
                    $options = $sm->get('losuser_module_options');
                    $form = new Form\Login(null, $options);
                    $form->setInputFilter(new Form\LoginFilter($options));

                    return $form;
                },
            ],
        ];
    }

    public function getControllerPluginConfig()
    {
        return array(
            'factories' => array(
                'losUserAuthentication' => function ($sm) {
                    $serviceLocator = $sm->getServiceLocator();
                    $authService = $serviceLocator->get('losuser_auth_service');
                    $authAdapter = $serviceLocator->get('LosUser\Authentication\Adapter\Doctrine');
                    $controllerPlugin = new Controller\Plugin\LosUserAuthentication();
                    $controllerPlugin->setAuthService($authService);
                    $controllerPlugin->setAuthAdapter($authAdapter);

                    return $controllerPlugin;
                },
            ),
        );
    }

    public function getViewHelperConfig()
    {
        return array(
            'factories' => array(
                'losUserIdentity' => function ($sm) {
                    $locator = $sm->getServiceLocator();
                    $viewHelper = new View\Helper\LosUserIdentity();
                    $viewHelper->setAuthService($locator->get('losuser_auth_service'));

                    return $viewHelper;
                },
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__.'/../config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__.'/../src/'.__NAMESPACE__,
                ),
            ),
        );
    }
}
