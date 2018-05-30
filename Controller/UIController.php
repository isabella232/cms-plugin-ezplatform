<?php

namespace Siteimprove\Bundle\SiteimproveBundle\Controller;

use eZ\Bundle\EzPublishCoreBundle\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class UIController.
 */
class UIController extends Controller
{
    /**
     * @param null $locationId
     * @param null $contentId
     * @param null $language
     *
     * @Route("/o/{contentId}/{language}")
     * @Route("/l/{locationId}/{language}")
     * @Route("/t")
     *
     * @return JsonResponse
     */
    public function dataAction(?int $locationId = null, ?int $contentId = null, ?string $language = null): JsonResponse
    {
        $data     = [
            'token' => $this->get("siteimprove.token.fetcher")->getToken(),
            'url'   => $this->get('siteimprove.url.computer')->getURL($locationId, $contentId, $language)
        ];
        $response = new JsonResponse();
        $response->setData($data);
        $response->setPrivate();

        return $response;
    }
}
