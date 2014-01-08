<?php

class Aoe_Static_Test_Model_Cache_Adapter_Testing implements Aoe_Static_Model_Cache_Adapter_Interface
{
    public function purgeAll()
    {
        return array('purged all');
    }

    public function purge(array $urls)
    {
        return array('purged: ' . implode(', ', $urls));
    }

    public function purgeTags(array $tags)
    {
        return array('purgedTags: ' . implode(', ', $tags));
    }

    public function setConfig($config)
    {}
}
