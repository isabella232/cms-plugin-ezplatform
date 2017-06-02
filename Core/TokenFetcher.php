<?php

namespace Siteimprove\Bundle\SiteimproveBundle\Core;

use GuzzleHttp\Client;
use Doctrine\DBAL\Connection;

/**
 * Class TokenFetcher.
 */
class TokenFetcher
{
    /**
     * Key stored in the ezsite_data table
     */
    CONST EZSITEDATA_KEY = "siteimprove-token";

    /**
     * Site Improve Endpoint
     */

    CONST TOKEN_ENDPOINT = "https://my2.siteimprove.com/auth/token?cms=eZ+Platform+";

    /**
     * @var Client
     */
    protected $httpClient;

    /**
     * @var Connection
     */
    protected $dbalConnection;

    /**
     * TokenFetcher constructor.
     *
     * @param Client     $httpClient
     * @param Connection $dbalConnection
     */
    public function __construct(Client $httpClient, Connection $dbalConnection)
    {
        $this->httpClient     = $httpClient;
        $this->dbalConnection = $dbalConnection;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        $result = $this->dbalConnection->query(
            "SELECT * FROM ezsite_data WHERE name = '".static::EZSITEDATA_KEY."' LIMIT 1"
        )->fetchAll();
        if (count($result) == 1) {
            return $result[0]['value'];
        }

        // get the version
        $resultVersion = $this->dbalConnection->query(
            "SELECT * FROM ezsite_data WHERE name = 'ezpublish-version' LIMIT 1"
        )->fetchAll();

        $version = count($resultVersion) == 1 ? $resultVersion[0]['value'] : "unknown";

        // fetch and store it
        $result = $this->httpClient->request(
            "GET",
            static::TOKEN_ENDPOINT."{$version}",
            [
                'User-Agent' => 'eZ Platform Plugin',
                'Accept'     => 'application/json'
            ]
        );
        if ($result->getStatusCode() == 200) {
            $data = json_decode($result->getBody()->getContents());
            if ($data->token) {
                $stmt = $this->dbalConnection->prepare("INSERT INTO ezsite_data VALUES (?,?)");
                $stmt->bindValue(1, static::EZSITEDATA_KEY);
                $stmt->bindValue(2, $data->token);
                $stmt->execute();
            }

            return $data->token;
        }

        return "Unable to fetch the Siteimprove token";
    }
}
