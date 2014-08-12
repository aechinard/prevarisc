<?php

class IndexController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $data = Service_User::getDashboardData(Zend_Auth::getInstance()->getIdentity()['ID_UTILISATEUR']);

        $this->view->user = Service_User::find(Zend_Auth::getInstance()->getIdentity()['ID_UTILISATEUR']);
        $this->view->flux = Service_Feed::get(Zend_Auth::getInstance()->getIdentity()['group']['ID_GROUPE']);
        $this->view->listeErpAvisDef = !array_key_exists('listeErpAvisDef', $data) ? array() : $data['listeErpAvisDef'];
        $this->view->listeDossiersDateEchue = !array_key_exists('listeDossiersDateEchue', $data) ? array() : $data['listeDossiersDateEchue'];
        $this->view->listeErpSansVP = !array_key_exists('listeErpSansVP', $data) ? array() : $data['listeErpSansVP'];
        $this->view->listeDossiersAvisDiff =  !array_key_exists('listeDossiersAvisDiff', $data) ? array() : $data['listeDossiersAvisDiff'];
        $this->view->listeCourriersSansRep = !array_key_exists('listeCourriersSansRep', $data) ? array() : $data['listeCourriersSansRep'];
        $this->view->listeERPSansPrev =  !array_key_exists('listeERPSansPrev', $data) ? array() : $data['listeERPSansPrev'];
        $this->view->listeDossiersSuivis =  !array_key_exists('listeDossiersSuivis', $data) ? array() : $data['listeDossiersSuivis'];
        $this->view->listeERPSuivis =  !array_key_exists('listeERPSuivis', $data) ? array() : $data['listeERPSuivis'];
    }

    public function addMessageAction()
    {
        $this->view->groupes = Service_User::getAllGroupes();

        if ($this->_request->isPost()) {
            try {
                Service_Feed::addMessage($this->_request->getParam('type'), $this->_request->getParam('text'), Zend_Auth::getInstance()->getIdentity()['ID_UTILISATEUR'], $this->_request->getParam('conf') );
                $this->_helper->flashMessenger(array('context' => 'success','title' => 'Message ajouté !','message' => 'Le message a bien été ajouté.'));
            } catch (Exception $e) {
                $this->_helper->flashMessenger(array('context' => 'danger','title' => 'Erreur !','message' => 'Erreur lors de l\'ajout du message : ' . $e->getMessage()));
            }

            $this->_helper->redirector->gotoRoute(array(), 'home', true);
        }
    }

    public function deleteMessageAction()
    {
        try {
            Service_Feed::deleteMessage($this->_request->getParam('id'));
            $this->_helper->flashMessenger(array('context' => 'success','title' => 'Message supprimé !','message' => 'Le message a bien été supprimé.'));
        } catch (Exception $e) {
            $this->_helper->flashMessenger(array('context' => 'danger','title' => 'Erreur !','message' => 'Erreur lors de la suppression du message : ' . $e->getMessage()));
        }

        $this->_helper->redirector->gotoRoute(array(), 'home', true);
    }
}
