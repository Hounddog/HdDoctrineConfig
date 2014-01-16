<?php 

return array(
    'asset_manager' => array(
        'resolver_configs' => array(
            'paths' => array(
                __DIR__ . '/../assets',
            ),
        ),
    ),

	'navigation' => array(
		'ag-sidenav' => array(
            array(
                'type' => 'ZF\Apigility\Admin\Navigation\Page',
                'label' => 'Doctrine Adapter',
                'uri' => 'global/doctrine-adapters',
                'section' => 'doctrine-adapter',
            ),
        )
	),

    'router' => array(
        'routes' => array(
            'zf-apigility-admin' => array(
                'child_routes' => array(
                    'api' => array(
                       'child_routes' => array(
                            'doctrine-adapter' => array(
                                'type' => 'segment',
                                'options' => array(
                                    'route' => '/doctrine-adapter[/:adapter_name]',
                                    'defaults' => array(
                                        'controller' => 'HD\Doctrine\Config\Controller\DoctrineAdapter',
                                    ),
                                ),
                            ), 
                        ),
                    ),
                ),
            ),
        ), 
    ),

    'zf-content-negotiation' => array(
        'controllers' => array(
            'HD\Doctrine\Config\Controller\DoctrineAdapter' => 'HalJson',
        ),
        'accept-whitelist' => array(
            'HD\Doctrine\Config\Controller\DoctrineAdapter' => array(
                'application/json',
                'application/*+json',
            ),
        ),
        'content-type-whitelist' => array(
            'HD\Doctrine\Config\Controller\DoctrineAdapter' => array(
                'application/json',
                'application/*+json',
            ),
        )
    ),
    'zf-rest' => array(
        'HD\Doctrine\Config\Controller\DoctrineAdapter' => array(
            'listener'                => 'HD\Doctrine\Config\Model\DoctrineAdapterResource',
            'route_name'              => 'zf-apigility-admin/api/doctrine-adapter',
            'route_identifier_name'   => 'adapter_name',
            'entity_class'            => 'HD\Doctrine\Config\Model\DoctrineAdapterEntity',
            'resource_http_methods'   => array('GET', 'PATCH', 'DELETE'),
            'collection_http_methods' => array('GET', 'POST'),
            'collection_name'         => 'doctrine_adapter',
        ),
    ),
    'zf-hal' => array(
        'metadata_map' => array(
            'HD\Doctrine\Config\Model\DoctrineAdapterEntity' => array(
                'hydrator'        => 'ArraySerializable',
                'route_identifier_name' => 'adapter_name',
                'route_name'      => 'zf-apigility-admin/api/doctrine-adapter',
            ),
        ),
    ),
);