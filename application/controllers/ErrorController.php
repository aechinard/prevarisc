<?php

class ErrorController extends Zend_Controller_Action
{
    public function errorAction()
    {
        $this->_helper->layout->setLayout('error');

        $errors = $this->getParam('error_handler');

        if (!$errors || !$errors instanceof ArrayObject) {
            $this->view->message = 'Vous avez atteint la page d\'erreur';
            return;
        }

        // On envoie le bon code erreur en fonction du type
        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                $this->getResponse()->setHttpResponseCode(404);
                $this->view->message = 'Page introuvable';
                break;

            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_OTHER:
                if ($errors->exception->getCode() == 401) {
                    $this->getResponse()->setHttpResponseCode(401);
                    $this->render('not-allowed');
                }
                break;

            default:
                $this->getResponse()->setHttpResponseCode(500);
                $this->view->message = 'L\'application a levée une erreur';
                break;
        }

        // On log l'erreur
        $logger = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('logger');
        $logger->addError('Erreur ' . $this->view->message, array(
            'params' => $errors->request->getParams(),
            'exception' => $errors->exception
        ));

        // Si l'affichage des exceptions est activé, on envoie un message
        if (getenv('PREVARISC_DEBUG_ENABLED')) {
            $this->view->exception = $errors->exception;
        }

        // On envoie la requête de l'erreur sur la vue
        $this->view->request = $errors->request;
    }
}
