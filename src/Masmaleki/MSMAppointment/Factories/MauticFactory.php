<?php

namespace Masmaleki\MSMAppointment\Factories;

use GuzzleHttp\Client;
use Mautic\Auth\ApiAuth;
use Illuminate\Support\Arr;
use Mautic\Auth\OAuthClient;
use Masmaleki\Mautic\Models\MauticConsumer;
use GuzzleHttp\Exception\ClientException;

class MauticFactory
{

    /**
     * Make a new Mautic url.
     *
     * @param string $endpoints
     * @return url
     */
    protected function getMSMAppointmentUrl($endpoints = null)
    {
        if (!empty($endpoints))
            return config('MSMAppointmentFactory.connections.main.baseUrl') . '/' . $endpoints;
        else
            return config('MSMAppointmentFactory.connections.main.baseUrl') . '/';
    }

    /**
     * Check AccessToken Expiration Time
     * @param $expireTimestamp
     * @return bool
     */
    public function checkExpirationTime($expireTimestamp)
    {
        $now = time();

        if ($now > $expireTimestamp)
            return true;
        else
            return false;
    }

    /**
     * Make a new Mautic client.
     *
     * @param array $config
     * @return \MSMAppointment\Config
     */
    public function make(array $config)
    {

        $config = $this->getConfig($config);
        return $this->getClient($config);
    }

    /**
     * Get the configuration data.
     *
     * @param array $config
     *
     * @throws \InvalidArgumentException
     *
     * @return array
     */
    protected function getConfig(array $config)
    {
        $keys = ['clientKey', 'clientSecret'];

        foreach ($keys as $key)
        {
            if (!array_key_exists($key, $config))
            {
                throw new \InvalidArgumentException('The MSMAppointment client requires configuration.');
            }
        }

        return Arr::only($config, ['version', 'baseUrl', 'clientKey', 'clientSecret', 'callback']);
    }



}
