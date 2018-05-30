<?php

namespace Siteimprove\Bundle\SiteimproveBundle\Slot;

use eZ\Publish\Core\SignalSlot\Slot as BaseSlot;
use eZ\Publish\Core\SignalSlot\Signal;
use Siteimprove\Bundle\SiteimproveBundle\Core\Publisher;

/**
 * Class SiteimproveUpdateOnPublish.
 */
class SiteimproveUpdateOnPublish extends BaseSlot
{
    /**
     * @var Publisher
     */
    protected $publisher;

    /**
     * SiteimproveUpdateOnPublish constructor.
     *
     * @param Publisher $publisher
     */
    public function __construct(Publisher $publisher)
    {
        $this->publisher = $publisher;
    }

    /**
     * @param Signal $signal
     */
    public function receive(Signal $signal): void
    {
        // see: https://jira.ez.no/browse/EZP-29252
        if ("cli" === PHP_SAPI) {
            return;
        }
        if (!$signal instanceof Signal\ContentService\PublishVersionSignal) {
            return;
        }
        $this->publisher->update($signal->contentId);
    }
}
