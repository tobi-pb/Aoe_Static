<?php

/**
 * Class Aoe_Static_Model_Cache_Adapter_Varnish
 */
class Aoe_Static_Model_Cache_Adapter_Varnish implements Aoe_Static_Model_Cache_Adapter_Interface
{
    /** @var array  */
    protected $_varnishServers = array();

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_varnishServers = Mage::helper('aoestatic')->trimExplode("\n", Mage::getStoreConfig('dev/aoestatic/servers'), true);
    }

    /**
     * Purges all cache on all Varnish servers.
     *
     * @return array errors if any
     */
    public function purgeAll()
    {
        return $this->purge(array('.*'));
    }

    /**
     * Purge an array of urls on all varnish servers.
     *
     * @param array $urls
     * @return array with all errors
     */
    public function purge(array $urls)
    {

        $httpHelper = Mage::helper('aoestatic/http');
        $httpHelper->reset();

        $options = array(
            CURLOPT_CUSTOMREQUEST   => 'PURGE',
            CURLOPT_RETURNTRANSFER  => 1,
            CURLOPT_SSL_VERIFYPEER  => 0,
            CURLOPT_SSL_VERIFYHOST  => 0,
        );
        $httpHelper->setGlobalOptions($options);

        foreach ($this->_varnishServers as $varnishServer) {
            foreach ($urls as $url) {
                $request = array(
                    CURLOPT_URL         => 'http://' . $varnishServer . '/' . $url,
                );

                $httpHelper->addRequests($request);
            }
        }

        $errors = $httpHelper->execute();

        return $errors;
    }

    /**
     * purges an array of given tags in varnish by using the X-Invalidates header
     *
     * @param array $tags
     * @return array
     */
    public function purgeTags(array $tags)
    {
        $httpHelper = Mage::helper('aoestatic/http');
        $httpHelper->reset();

        $options = array(
            CURLOPT_CUSTOMREQUEST   => 'BAN',
            CURLOPT_RETURNTRANSFER  => 1,
            CURLOPT_SSL_VERIFYPEER  => 0,
            CURLOPT_SSL_VERIFYHOST  => 0,
        );
        $httpHelper->setGlobalOptions($options);

        foreach ($this->_varnishServers as $varnishServer) {
            foreach ($tags as $tag) {
                $request = array(
                    CURLOPT_URL         => 'http://' . $varnishServer,
                    CURLOPT_HTTPHEADER  => array('X-Invalidates: ' . Aoe_Static_Model_Cache_Control::DELIMITER . $tag),
                );

                $httpHelper->addRequests($request);
            }
        }

        $errors = $httpHelper->execute();

        return $errors;
    }

    /**
     * sets varnish server urls
     *
     * @param string|array $config
     */
    public function setConfig($config)
    {
        // This adapter reads its configuration from core_config_data instead
    }
}
