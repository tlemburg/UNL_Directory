<?php
UNL_Templates::setCachingService(new UNL_Templates_CachingService_Null());
UNL_Templates::$options['version'] = 4.0;
$page = UNL_Templates::factory('Fixed');
$savvy->addGlobal('page', $page);

$page->doctitle = '<title>Directory | University of Nebraska–Lincoln</title>';
$page->titlegraphic = 'Directory';

$classes = ['hide-navigation', 'hide-breadcrumbs'];

$page->__params['class']['value'] = implode(' ', $classes);

$page->head .= $savvy->render(null, 'static/head.tpl.php');

if ($context->options['view'] != 'alphalisting' && UNL_Officefinder::getUser()
    && 
        (
        UNL_Officefinder::isAdmin(UNL_Officefinder::getUser()) || count(new UNL_Officefinder_User_Departments(array('uid'=>UNL_Officefinder::getUser())))
        )
    ) {
    $page->head .= '
    <script type="text/javascript">
        WDN.initializePlugin("jqueryui", [function () {
            WDN.loadJS("'.UNL_Peoplefinder::getURL().'scripts/edit_functions.js");
            WDN.loadCSS("'.UNL_Peoplefinder::getURL().'css/editing.css?v=4.0");
        }]);
    </script>';
    $page->titlegraphic .= '<div id="userDepts"><a class="mydepts" href="'.UNL_Officefinder::getURL().'?view=mydepts">My Departments</a></div>';
}

if (isset($context->options['q']) || isset($context->options['uid']) || isset($context->options['cn']) || isset($context->options['sn'])) {
    // Don't let search engines index these pages
    $page->head .= '<meta name="robots" content="NOINDEX, NOFOLLOW" />';
}

$page->breadcrumbs = $savvy->render(null, 'static/breadcrumbs.tpl.php');
$page->navlinks = '';
$page->pagetitle = '';

$isOutputError = $context->getRaw('output') instanceof Exception;
$outputTemplate = null;
if ($isOutputError) {
    $outputTemplate = 'Exception.tpl.php';
}

if (in_array($context->options['view'], array('instructions', 'search'))) {
    //Don't wrap the home page, because we want it to use bands
    $page->maincontentarea = $savvy->render($context->output, $outputTemplate);
} else {
    //Wrap everything else
    $page->maincontentarea = '<div class="wdn-band results-container"><div class="wdn-inner-wrapper wdn-inner-padding-sm">' . $savvy->render($context->output, $outputTemplate) . '</div></div>';
}

$page->contactinfo = $savvy->render(null, 'static/contact-info.tpl.php');

$page->leftcollinks = $savvy->render(null, 'static/op-footer.tpl.php');

$page->footercontent = $savvy->render(null, 'static/footer.tpl.php');
$page->footercontent .= $savvy->render($context, 'CorrectionForm.tpl.php');

echo $page;
