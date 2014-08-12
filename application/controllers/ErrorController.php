<?php

class ErrorController extends Zend_Controller_Action
{
    public function errorAction()
    {
        $message = "";
        $errors = $this->getParam('error_handler');

        // Gestion du message de l'erreur
        if (!$errors || !$errors instanceof ArrayObject) {
            $message = "L'application s'est arrêtée brutalement.";
        } else {
            switch ($errors->type) {
                case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
                case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
                case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                    $message = "Page introuvable.";
                    break;
            }
        }

        // Si l'affichage des exceptions est activé, on ajoute le message d'erreur
        if (getenv('PREVARISC_DEBUG_ENABLED')) {
            $message .= ' (' . $errors->exception->getMessage() . ')';
        }

        // On log l'erreur
        $this->getInvokeArg('bootstrap')->getResource('logger')->addError('Erreur ' . $message, array(
            'params' => $errors->request->getParams(),
            'exception' => $errors->exception
        ));

        // On ajoute le message d'erreur à la stack des messages
        $this->_helper->flashMessenger(array('context' => 'danger','title' => 'Erreur','message' => $message));

        // On retourne sur la home
        $this->_helper->redirector->gotoRoute(array(), 'home', true);
    }
}
