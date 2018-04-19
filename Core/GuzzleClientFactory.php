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
    public function createWithProxy(?string $host = null, ?string $port = null, ?string $auth = null): Client
    {
        $options = ['proxy' => null];
        if (null !== $host) {
            $options['proxy'] = $host;
            if (null !== $port) {
                $options['proxy'] .= ':'.$port;
            }
            if (null !== $auth) {
                $options['proxy'] = $auth.'@'.$options['proxy'];
            }
        }

        return new Client($options);
    }
}
