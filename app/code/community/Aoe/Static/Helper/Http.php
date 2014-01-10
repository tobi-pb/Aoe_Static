<?php

class Aoe_Static_Helper_Http extends Mage_Core_Helper_Abstract
{
    /**
     * holds an array of all requests (basically the curl-options for each request)
     *
     * @var array
     */
    protected $_requests = array();

    /**
     * holds an array of all curl-options which will be applied to a request before the request-specific options (like CURLOPT_CUSTOMREQUEST)
     *
     * @var array
     */
    protected $_globalOptions = array();

    /**
     * resets requests and options
     *
     * @return Aoe_Static_Helper_Http
     */
    public function reset()
    {
        $this->_requests = array();
        $this->_globalOptions = array();

        return $this;
    }

    /**
     * add request to the execution queue
     *
     * @param array $request
     * @return $this
     */
    public function addRequests(array $request)
    {
        $this->_requests[] = $request;

        return $this;
    }

    /**
     * set global options array
     *
     * @param array $globalOptions
     * @return $this
     */
    public function setGlobalOptions(array $globalOptions)
    {
        $this->_globalOptions = $globalOptions;

        return $this;
    }

    /**
     * set specified curl option
     *
     * @param $option
     * @param $value
     * @return $this
     */
    public function setGlobalOption($option, $value)
    {
        $this->_globalOptions[$option] = $value;

        return $this;
    }

    /**
     * get requests
     *
     * @return array
     */
    public function getRequests()
    {
        return $this->_requests;
    }

    /**
     * get global options
     *
     * @return array
     */
    public function getGlobalOptions()
    {
        return $this->_globalOptions;
    }

    /**
     * execute requests, return errors, clean queue if wanted (by using $this->reset())
     *
     * @param bool $clean
     * @return array
     */
    public function execute($clean = true)
    {
        // Cannot be tested due to the curl-stuff. Sorry.
        // @codeCoverageIgnoreStart
        $errors = array();

        // Init curl handler
        $curlHandlers = array(); // keep references for clean up
        $multiHandler = curl_multi_init();

        foreach ($this->_requests as $request)
        {
            $curlHandler = curl_init();

            foreach ($this->_globalOptions as $option => $value) {
                curl_setopt($curlHandler, $option, $value);
            }

            foreach ($request as $option => $value) {
                curl_setopt($curlHandler, $option, $value);
            }

            curl_multi_add_handle($multiHandler, $curlHandler);
            $curlHandlers[] = $curlHandler;
        }

        do {
            curl_multi_exec($multiHandler, $active);
        } while ($active);

        // Error handling and clean up
        foreach ($curlHandlers as $curlHandler) {
            $info = curl_getinfo($curlHandler);

            if (curl_errno($curlHandler)) {
                $errors[] = "Error at {$info['url']}: " . curl_error($curlHandler);
            } else if ($info['http_code'] != 200 && $info['http_code'] != 404) {
                $errors[] = "Curl Error: {$info['url']}, http code: {$info['http_code']}. error: " . curl_error($curlHandler);
            }

            curl_multi_remove_handle($multiHandler, $curlHandler);
            curl_close($curlHandler);
        }
        curl_multi_close($multiHandler);

        if ($clean) {
            $this->reset();
        }

        return $errors;
        // @codeCoverageIgnoreEnd
    }
}
