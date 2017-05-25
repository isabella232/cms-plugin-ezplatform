<?php

namespace Siteimprove\Bundle\SiteimproveBundle\Controller;

use eZ\Bundle\EzPublishCoreBundle\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class LegacyController.
 */
class LegacyController extends Controller
{
    /**
     * @param array        $moduleResult
     * @param null|integer $contentId
     * @param null|integer $language
     *
     * @Template
     *
     * @return array
     */
    public function dataAction($moduleResult = [], $contentId = null, $language = null)
    {
        unset($moduleResult['content']);
        if ($contentId <= 0 && isset($moduleResult['content_info'])) {
            $contentId = $moduleResult['content_info']['object_id'];
        }

        $data = [
            'token' => $this->get("siteimprove.token.fetcher")->getToken(),
            'url'   => $this->get('siteimprove.url.computer')->getURL(null, $contentId, $language)
        ];

        return ['data' => $data];
    }
}
