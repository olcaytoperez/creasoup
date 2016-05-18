<?php
/**
 * Created by PhpStorm.
 * User: olcay
 * Date: 18.05.16
 * Time: 16:27
 */

    namespace v1\Factory;

    use Zend\ServiceManager\FactoryInterface;
    use Zend\ServiceManager\ServiceLocatorInterface;
    use v1\Controller\TasksController as TasksController;

    class TasksControllerFactory implements FactoryInterface
    {
        public function createService(ServiceLocatorInterface $serviceLocator)
        {
            $objectManager = $serviceLocator->getServiceLocator()->get('Doctrine\ORM\EntityManager');
            return new TasksController($objectManager);
        }
    }