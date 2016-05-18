<?php
/**
 * Created by PhpStorm.
 * User: olcay
 * Date: 18.05.16
 * Time: 00:55
 */

    namespace v1\Factory;

    use Zend\ServiceManager\FactoryInterface;
    use Zend\ServiceManager\ServiceLocatorInterface;
    use Zend\Authentication\Adapter\Http as HttpAdapter;
    use Zend\Authentication\Adapter\Http\FileResolver;

    class AuthControllerFactory implements FactoryInterface
    {
        public function createService(ServiceLocatorInterface $serviceLocator)
        {
            $config         = $serviceLocator->get('Config');
            $authConfig     = $config['my_app']['auth_adapter'];
            $authAdapter    = new HttpAdapter($authConfig['config']);
            $basicResolver  = new FileResolver();
            $digestResolver = new FileResolver();

            $basicResolver->setFile($authConfig['basic_passwd_file']);
            $digestResolver->setFile($authConfig['digest_passwd_file']);
            $authAdapter->setBasicResolver($basicResolver);
            $authAdapter->setDigestResolver($digestResolver);

            return $authAdapter;
        }
    }