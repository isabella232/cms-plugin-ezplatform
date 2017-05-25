<?php

namespace Siteimprove\Bundle\SiteimproveBundle\Core;

use GuzzleHttp\Client;
use eZ\Publish\Core\Helper\TranslationHelper;
use Psr\Log\LoggerInterface;

/**
 * Class Publisher.
 */
class Publisher
{
    /**
     * @var Client
     */
    protected $httpClient;

    /**
     * @var TokenFetcher
     */
    protected $tokenFetcher;

    /**
     * @var URLComputer
     */
    protected $URLComputer;

    /**
     * @var TranslationHelper
     */
    protected $translationHelper;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Endpoint to rechech server side
     */
    CONST RECHECK_ENDPOINT = "https://api-gateway.siteimprove.com/cms-recheck";

    /**
     * Publisher constructor.
     *
     * @param Client            $httpClient
     * @param TokenFetcher      $tokenFetcher
     * @param URLComputer       $URLComputer
     * @param TranslationHelper $translationHelper
     * @param LoggerInterface   $logger
     */
    public function __construct(
        Client $httpClient,
        TokenFetcher $tokenFetcher,
        URLComputer $URLComputer,
        TranslationHelper $translationHelper,
        LoggerInterface $logger
    ) {
        $this->httpClient        = $httpClient;
        $this->tokenFetcher      = $tokenFetcher;
        $this->URLComputer       = $URLComputer;
        $this->translationHelper = $translationHelper;
        $this->logger            = $logger;
    }

    /**
     * @param $contentId
     */
    public function update($contentId)
    {
        $langs    = $this->translationHelper->getAvailableLanguages();
        $doneURLs = [];
        $token    = $this->tokenFetcher->getToken();
        foreach ($langs as $lang) {
            if ($lang == '') {
                continue;
            }
            $url = $this->URLComputer->getURL(null, $contentId, $lang);
            if (in_array($url, $doneURLs)) {
                continue;
            }
            $doneURLs[] = $url;
            $request    = $this->httpClient->request(
                "POST",
                static::RECHECK_ENDPOINT,
                [
                    'User-Agent' => 'eZ Platform Plugin',
                    'Accept'     => 'application/json',
                    'json'       => [
                        "url"   => $url,
                        "token" => $token,
                        "type"  => "recheck"
                    ]
                ]
            );
            $this->logger->info("SiteImprove($token): Rechecked URL: {$url} - HTTPCODE: {$request->getStatusCode()}");

        }
    }
}
