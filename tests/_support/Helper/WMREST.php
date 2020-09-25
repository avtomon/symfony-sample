<?php

namespace Helper;

use Codeception\Module\REST as CodeceptionREST;

/**
 * Class WMREST
 *
 * @package Helper
 */
class WMREST extends CodeceptionREST
{
    /**
     * @param $method
     * @param $parameters
     *
     * @return false|mixed|string
     */
    protected function encodeApplicationJson($method, $parameters)
    {
        if ($method !== 'GET' && array_key_exists('Content-Type', $this->connectionModule->headers)
            && ($this->connectionModule->headers['Content-Type'] === 'application/json'
                || preg_match('!^application/.+\+json$!', $this->connectionModule->headers['Content-Type'])
            )
        ) {
            if ($parameters instanceof \JsonSerializable) {
                return \json_encode($parameters, JSON_PRESERVE_ZERO_FRACTION);
            }
            if (is_array($parameters) || $parameters instanceof \ArrayAccess) {
                $parameters = $this->scalarizeArray($parameters);
                return \json_encode($parameters, JSON_PRESERVE_ZERO_FRACTION);
            }
        }
        return $parameters;
    }

    /**
     * @param $url
     * @param array $params
     * @param array $files
     * @param array $options
     *
     * @throws \Codeception\Exception\ModuleConfigException
     * @throws \Codeception\Exception\ModuleException
     */
    public function sendPOSTWithOptions($url, $params = [], $files = [], $options = [])
    {
        $data = $this->_getConfig();
        $newData = $data;

        foreach ($newData as $key => $value) {
            if (array_key_exists($key, $options)) {
                $newData[$key] = $options[$key];
            }
        }

        $this->_setConfig($newData);

        $result = $this->execute('POST', $url, $params, $files);

        $this->_setConfig($data);

        return $result;
    }
}
