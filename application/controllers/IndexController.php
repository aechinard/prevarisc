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

        $this->view->etablissements = $data['etablissements'];
        $this->view->dossiers = $data['dossiers'];
        $this->view->commissions = $data['commissions'];
        $this->view->erpSansPreventionniste =  $data['erpSansPreventionniste'];
        $this->view->etablissementAvisDefavorable =  $data['etablissementAvisDefavorable'];
        $this->view->dossierCommissionEchu =  $data['dossierCommissionEchu'];
        $this->view->CourrierSansReponse =  $data['CourrierSansReponse'];
        $this->view->prochainesCommission =  $data['prochainesCommission'];
        $this->view->NbrDossiersAffect =  $data['NbrDossiersAffect'];
        $this->view->ErpSansProchaineVisitePeriodeOuvert =  $data['ErpSansProchaineVisitePeriodeOuvert'];
    }
}
