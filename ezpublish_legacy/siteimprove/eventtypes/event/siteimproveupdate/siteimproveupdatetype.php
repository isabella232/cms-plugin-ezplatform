<?php

/**
 * Class siteimproveupdatetype.
 */
class SiteimproveUpdateType extends eZWorkflowEventType
{
    const WORKFLOW_TYPE_STRING = "siteimproveupdate";

    public function __construct()
    {
        parent::eZWorkflowEventType(SiteimproveUpdateType::WORKFLOW_TYPE_STRING, 'Siteimprove Update');
    }

    public function execute($process, $event)
    {
        $parameters = $process->attribute('parameter_list');

        if ($parameters['module_name'] == 'content' && $parameters['module_function'] == 'publish') {
            $container = ezpKernel::instance()->getServiceContainer();
            $container->get('siteimprove.publisher')->update($parameters['object_id']);
        }
        return eZWorkflowType::STATUS_ACCEPTED;
    }
}

eZWorkflowEventType::registerEventType(SiteimproveUpdateType::WORKFLOW_TYPE_STRING, 'siteimproveupdatetype');
