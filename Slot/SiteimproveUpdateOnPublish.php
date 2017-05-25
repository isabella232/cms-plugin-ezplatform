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

    public function receive(Signal $signal)
    {
        if (!$signal instanceof Signal\ContentService\PublishVersionSignal) {
            return;
        }
        $this->publisher->update($signal->contentId);
    }
}
