<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace v1;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\EventManager\EventInterface;
use Zend\Mvc\MvcEvent;
use Zend\Http\Request as HttpRequest;
use Zend\Http\Response as HttpResponse;

class Module implements AutoloaderProviderInterface, ConfigProviderInterface, BootstrapListenerInterface
{
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function onBootstrap(EventInterface $event)
    {

        $application    = $event->getTarget();
        $serviceManager = $application->getServiceManager();

        // delaying instantiation of everything to the latest possible moment
        $application
            ->getEventManager()
            ->attach(MvcEvent::EVENT_DISPATCH, function (MvcEvent $event) use ($serviceManager) {
                $request  = $event->getRequest();
                $response = $event->getResponse();

                if ( ! (
                    $request instanceof HttpRequest
                    && $response instanceof HttpResponse
                )) {
                    return; // we're not in HTTP context - CLI application?
                }

                $authAdapter = $serviceManager->get('v1\AuthControllerFactory');

                $authAdapter->setRequest($request);
                $authAdapter->setResponse($response);

                $result = $authAdapter->authenticate();

                if($result->isValid()) {
                    return; // everything OK
                }

                $response->setContent('Access Denied');
                $response->setStatusCode(HttpResponse::STATUS_CODE_401);

                $event->setResult($response); // short-circuit to application end

                return false; // stop event propagation
            });
    }
}
