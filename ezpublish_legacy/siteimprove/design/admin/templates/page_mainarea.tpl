{symfony_render( symfony_controller( 'SiteimproveBundle:Legacy:data', hash(
        'moduleResult',$module_result,
        'contentId', cond(is_set($object),$object.id),
        'language', cond(is_set($object),$object.current_language)
)))}
<div class="span{$inner_column_size} main-content">
    <!-- Main area content: START with Siteimprove Plugins -->
    {$module_result.content}
    <!-- Main area content: END with Siteimprove Plugins -->
</div>
