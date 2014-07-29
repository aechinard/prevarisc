<?php

class Plugin_ACL extends Zend_Controller_Plugin_Abstract
{
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        if (!in_array($request->getControllerName(), array('error', 'auth'))) {

            // Préparation de l'helper redirector
            $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');

            // Restriction des accès aux utilisateurs non connectés
            if (!Zend_Auth::getInstance()->hasIdentity()) {
                $redirector->gotoRouteAndExit(array(), 'login', true);
            }

        }
    }
}
