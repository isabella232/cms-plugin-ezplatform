<?php

namespace Siteimprove\Bundle\SiteimproveBundle\Controller;

use eZ\Bundle\EzPublishCoreBundle\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Class AdminUIController.
 */
class AdminUIController extends Controller
{
    /**
     * @param int|null    $locationId
     * @param int|null    $contentId
     * @param null|string $language
     *
     * @Template
     *
     * @return array
     */
    public function dataAction(?int $locationId = null, ?int $contentId = null, ?string $language = null): array
    {
        $data = [
            'token' => $this->get("siteimprove.token.fetcher")->getToken(),
            'url'   => $this->get('siteimprove.url.computer')->getURL($locationId, $contentId, $language)
        ];

        return ['data' => $data];
    }
}
