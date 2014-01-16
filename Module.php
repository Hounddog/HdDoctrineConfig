<?php

namespace HD\Doctrine\Config;

use Zend\Config\Writer\PhpArray as PhpArrayWriter;
use ZF\Configuration\ConfigResource;
use Zend\ModuleManager\ModuleManager;
use Zend\EventManager\StaticEventManager;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;

class Module
{

    public function onBootstrap($e) {
        $sm = $e->getApplication()->getServiceManager();
        $headScript = $sm->get('viewhelpermanager')->get('headScript');
        $headScript->appendFile('/hd-doctrine-config/js/doctrine-config.js');
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getConfig($env = null)
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getServiceConfig()
    {
        return array(
            'factories' =>  array(
                'HD\Doctrine\Config\Model\DoctrineAdapterModel' => function ($services) {
                    if (!$services->has('Config')) {
                        throw new ServiceNotCreatedException(
                            'Cannot create HD\Doctrine\Config\Model\DoctrineAdapterModel service because Config service is not present'
                        );
                    }
                    $config = $services->get('Config');
                    $writer = new PhpArrayWriter();

                    $global = new ConfigResource($config, 'config/autoload/global.php', $writer);
                    $local  = new ConfigResource($config, 'config/autoload/local.php', $writer);
                    return new \HD\Doctrine\Config\Model\DoctrineAdapterModel($global, $local);
                },
                'HD\Doctrine\Config\Model\DoctrineAdapterResource' => function ($services) {
                    if (!$services->has('HD\Doctrine\Config\Model\DoctrineAdapterModel')) {
                        throw new ServiceNotCreatedException(
                            'Cannot create HD\Doctrine\Config\Model\DoctrineAdapterResource service because HD\Doctrine\Config\Model\DoctrineAdapterModel service is not present'
                        );
                    }
                    $model = $services->get('HD\Doctrine\Config\Model\DoctrineAdapterModel');
                    return new Model\DoctrineAdapterResource($model);
                },
            ),
        );
    }
}
