<?php

class Plugin_View extends Zend_Controller_Plugin_Abstract
{
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        if ($request->getModuleName() == 'default' || $request->getModuleName() == '') {

            // On récupère la vue
            $view = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('view');

            // Chemins vers les vues, les helpers ..
            $view->setScriptPath(APPLICATION_PATH . DS . 'views' . DS . 'scripts');
            $view->addScriptPath(APPLICATION_PATH . DS . 'views');

            // JS
            $view->inlineScript()->appendFile("/assets/bower_components/jquery/dist/jquery.min.js");
            $view->inlineScript()->appendFile("/assets/bower_components/bootstrap/dist/js/bootstrap.min.js");
            $view->inlineScript()->appendFile("/assets/bower_components/bootstrap-multiselect/js/bootstrap-multiselect.js");
            $view->inlineScript()->appendFile("/assets/bower_components/dropzone/downloads/dropzone.min.js");
            $view->inlineScript()->appendFile("/assets/bower_components/fullcalendar/fullcalendar.min.js");
            $view->inlineScript()->appendFile("/assets/bower_components/jquery.tablesorter/js/jquery.tablesorter.min.js");
            $view->inlineScript()->appendFile("/assets/bower_components/packery/dist/packery.pkgd.js");
            $view->inlineScript()->appendFile("/assets/bower_components/selectize/dist/js/standalone/selectize.min.js");
            $view->inlineScript()->appendFile("/assets/bower_components/jQuery.Marquee/jquery.marquee.min.js");
            $view->inlineScript()->appendFile("/assets/js/plugins.js");
            $view->inlineScript()->appendFile("/assets/js/main.js");

            // CSS
            $view->headLink()->appendStylesheet('/assets/bower_components/bootstrap/dist/css/bootstrap.min.css', 'all');
            $view->headLink()->appendStylesheet('/assets/css/main.css', 'all');
            $view->headLink()->appendStylesheet('/assets/bower_components/bootstrap-multiselect/css/bootstrap-multiselect.css', 'all');
            $view->headLink()->appendStylesheet('/assets/bower_components/dropzone/downloads/css/basic.css', 'all');
            $view->headLink()->appendStylesheet('/assets/bower_components/dropzone/downloads/css/dropzone.css', 'all');
            $view->headLink()->appendStylesheet('/assets/bower_components/fullcalendar/fullcalendar.css', 'all');
            $view->headLink()->appendStylesheet('/assets/bower_components/jquery.tablesorter/css/theme.default.css', 'all');
            $view->headLink()->appendStylesheet('/assets/bower_components/animate.css/animate.min.css', 'all');
            $view->headLink()->appendStylesheet('/assets/bower_components/selectize/dist/css/selectize.bootstrap3.css', 'all');
            $view->headLink()->appendStylesheet('/assets/css/components/nav-side.css', 'all');
            $view->headLink()->appendStylesheet('/assets/css/components/search-list.css', 'all');
            $view->headLink()->appendStylesheet('/assets/css/components/panel-body-heightlimit.css', 'all');
            $view->headLink()->appendStylesheet('/assets/css/components/dashboard.css', 'all');
            $view->headLink()->appendStylesheet('/assets/css/components/avis.css', 'all');
            $view->headLink()->appendStylesheet('/assets/css/components/muted.css', 'all');
            $view->headLink()->appendStylesheet('/assets/css/components/etablissement.css', 'all');
            $view->headLink()->appendStylesheet('/assets/css/components/contact.css', 'all');

            // Chargement des aides de vue
            $view->registerHelper(new SDIS62_View_Helper_FlashMessenger, 'flashMessenger');

            // Définition du partial de vue à utiliser pour le rendu d'une recherche
            Zend_View_Helper_PaginationControl::setDefaultViewPartial('search' . DIRECTORY_SEPARATOR . 'pagination_control.phtml');

            // On charge la vue correctement configurée dans le viewRenderer
            $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
            $viewRenderer->setView($view);

        }
    }
}
