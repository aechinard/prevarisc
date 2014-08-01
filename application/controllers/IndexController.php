<?php

class IndexController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $service_feed = new Service_Feed;
        $service_user = new Service_User;

        $this->view->user = $service_user->find(Zend_Auth::getInstance()->getIdentity()['ID_UTILISATEUR']);
        $this->view->flux = $service_feed->get(Zend_Auth::getInstance()->getIdentity()['group']['ID_GROUPE']);

        $data = $service_user->getDashboardData(Zend_Auth::getInstance()->getIdentity()['ID_UTILISATEUR']);

        $this->view->listeErpAvisDef = @$data['listeErpAvisDef'];
        $this->view->listeDossiersDateEchue = @$data['listeDossiersDateEchue'];
        $this->view->listeErpSansVP = @$data['listeErpSansVP'];
        $this->view->listeDossiersAvisDiff =  @$data['listeDossiersAvisDiff'];
        $this->view->listeCourriersSansRep = @$data['listeCourriersSansRep'];
        $this->view->listeERPSansPrev =  @$data['listeERPSansPrev'];
        $this->view->listeDossiersSuivis =  @$data['listeDossiersSuivis'];
        $this->view->listeERPSuivis =  @$data['listeERPSuivis'];
    }
}
