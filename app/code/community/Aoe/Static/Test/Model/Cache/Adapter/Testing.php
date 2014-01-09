<?php

class Aoe_Static_Test_Model_Cache_Adapter_Testing implements Aoe_Static_Model_Cache_Adapter_Interface
{
    public function purgeAll()
    {
        Mage::unregister('_aoestatic_testadapter_purgeall');
        Mage::register('_aoestatic_testadapter_purgeall', 1);
        return array('purged all');
    }

    public function purge(array $urls)
    {
        Mage::unregister('_aoestatic_testadapter_purge');
        Mage::register('_aoestatic_testadapter_purge', count($urls));
        return array('purged: ' . implode(', ', $urls));
    }

    public function purgeTags(array $tags)
    {
        Mage::unregister('_aoestatic_testadapter_purgetags');
        Mage::register('_aoestatic_testadapter_purgetags', count($tags));
        return array('purgedTags: ' . implode(', ', $tags));
    }

    public function setConfig($config)
    {}
}
