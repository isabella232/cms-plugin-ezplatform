<?php

namespace Siteimprove\Bundle\SiteimproveBundle\Core;

use GuzzleHttp\Client;

/**
 * Class GuzzleClientFactory.
 *
 * Guzzle Client Factory used to generate client with optional HTTP proxy URI
 */
class GuzzleClientFactory
{
    /**
     * Factory method for Guzzle Client.
     *
     * This method will create a Guzzle client with optional proxy
     *
     * @param null $host An HTTP proxy host
     * @param null $port An HTTP proxy port
     * @param null $auth An HTTP proxy auth string (ie: "user:pass")
     *
     * @return Client A configured Guzzle client object (configured with optional proxy)
     */
    public function createWithProxy($host = null, $port = null, $auth = null)
    {
        $options = ['proxy' => null];
        if (!empty($host)) {
            $options['proxy'] = $host;
            if (!empty($port)) {
                $options['proxy'] .= ':'.$port;
            }
            if (!empty($auth)) {
                $options['proxy'] = $auth.'@'.$options['proxy'];
            }
        }

        return new Client($options);
    }
}
