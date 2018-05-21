<?php

namespace Siteimprove\Bundle\SiteimproveBundle\Core;

use eZ\Publish\Core\MVC\Symfony\Routing\Generator\RouteReferenceGeneratorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use eZ\Publish\API\Repository\Repository;

/**
 * Class URLComputer.
 */
class URLComputer
{

    /**
     * @var Repository
     */
    protected $repository;

    /**
     * @var UrlGeneratorInterface
     */
    protected $router;

    /**
     * @var RouteReferenceGeneratorInterface
     */
    protected $routeRefGenerator;

    /**
     * @var string
     */
    protected $defaultSiteAccessName;

    /**
     * URLComputer constructor.
     *
     * @param Repository                       $repository
     * @param UrlGeneratorInterface            $router
     * @param RouteReferenceGeneratorInterface $routeRefGenerator
     * @param string                           $defaultSiteAccessName
     */
    public function __construct(
        Repository $repository,
        UrlGeneratorInterface $router,
        RouteReferenceGeneratorInterface $routeRefGenerator,
        $defaultSiteAccessName
    ) {
        $this->repository            = $repository;
        $this->router                = $router;
        $this->routeRefGenerator     = $routeRefGenerator;
        $this->defaultSiteAccessName = $defaultSiteAccessName;
    }

    /**
     * @param null $locationId
     * @param null $contentId
     * @param null $lang
     *
     * @return string
     */
    public function getURL(?int $locationId = null, ?int $contentId = null, ?string $lang = null): string
    {
        try {
            if ($locationId === null) {
                $content  = $this->repository->getContentService()->loadContent($contentId);
                $location = $this->repository->getLocationService()->loadLocation(
                    $content->contentInfo->mainLocationId
                );
            } else {
                $location = $this->repository->getLocationService()->loadLocation(
                    $locationId
                );
            }

            $currentMainUrl = $this->router->generate(
                $location,
                ['siteaccess' => $this->defaultSiteAccessName],
                UrlGeneratorInterface::ABSOLUTE_URL
            );
            if ($lang !== null) {
                $routeRef = $this->routeRefGenerator->generate($location, ['language' => $lang]);

                return $this->router->generate($routeRef, [], UrlGeneratorInterface::ABSOLUTE_URL);
            }

            return $currentMainUrl;

        } catch (\Exception $e) {
            return '';
        }
    }

}
