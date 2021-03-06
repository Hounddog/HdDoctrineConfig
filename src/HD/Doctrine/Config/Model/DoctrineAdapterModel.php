<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2013 Zend Technologies USA Inc. (http://www.zend.com)
 */

namespace HD\Doctrine\Config\Model;

use ZF\Configuration\ConfigResource;

class DoctrineAdapterModel
{
    /**
     * @var ConfigResource
     */
    protected $globalConfig;

    /**
     * @var ConfigResource
     */
    protected $localConfig;

    /**
     * @param ConfigResource $globalConfig
     * @param ConfigResource $localConfig
     */
    public function __construct(ConfigResource $globalConfig, ConfigResource $localConfig)
    {
        $this->globalConfig = $globalConfig;
        $this->localConfig = $localConfig;
    }

    /**
     * Create Doctrine adapter configuration
     *
     * @param  mixed $name
     * @param  array $adapterConfig
     * @return DoctrineAdapterEntity
     */
    public function create($name, array $adapterConfig)
    {
        $key = 'doctrine.connection.' . $name . '.params';

        $this->globalConfig->patchKey($key, array());
        $this->localConfig->patchKey($key, $adapterConfig);

        return new DoctrineAdapterEntity($name, $adapterConfig);
    }

    /**
     * Update an existing Doctrine adapter
     *
     * @param  string $name
     * @param  array $adapterConfig
     * @return DoctrineAdapterEntity
     */
    public function update($name, array $adapterConfig)
    {
        return $this->create($name, $adapterConfig);
    }

    /**
     * Remove a named adapter
     *
     * @param  string $name
     * @return true
     */
    public function remove($name)
    {
        $key = 'doctrine.connection.' . $name;
        $this->globalConfig->deleteKey($key);
        $this->localConfig->deleteKey($key);
        return true;
    }

    /**
     * Retrieve all named adapters
     *
     * @return array
     */
    public function fetchAll()
    {
        $config = array();
        $fromConfigFile = $this->localConfig->fetch(true);
        if (isset($fromConfigFile['doctrine'])
            && isset($fromConfigFile['doctrine']['connection'])
            && is_array($fromConfigFile['doctrine']['connection'])
        ) {
            $config = $fromConfigFile['doctrine']['connection'];
        }

        $adapters = array();
        foreach ($config as $name => $adapterConfig) {
            $adapters[] = new DoctrineAdapterEntity($name, $adapterConfig);
        }

        return $adapters;
    }

    /**
     * Fetch configuration details for a named adapter
     *
     * @param  string $name
     * @return DbAdapterEntity
     */
    public function fetch($name)
    {
        echo 'fetch';
        exit;
        $config = $this->localConfig->fetch(true);
        if (!isset($config['db'])
            || !isset($config['db']['adapters'])
            || !is_array($config['db']['adapters'])
            || !isset($config['db']['adapters'][$name])
            || !is_array($config['db']['adapters'][$name])
        ) {
            return false;
        }

        return new DbAdapterEntity($name, $config['db']['adapters'][$name]);
    }
}
