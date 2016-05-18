<?php
/**
 * Created by PhpStorm.
 * User: olcay
 * Date: 16.05.16
 * Time: 15:59
 */

    namespace Application\Factory;

    use Zend\ServiceManager\FactoryInterface;
    use Zend\ServiceManager\ServiceLocatorInterface;
    use Application\Controller\RestController as RestController;

    class RestControllerFactory implements FactoryInterface
    {
        public function createService(ServiceLocatorInterface $serviceLocator)
        {
            $objectManager = $serviceLocator->getServiceLocator()->get('Doctrine\ORM\EntityManager');
            return new RestController($objectManager);
        }
    }