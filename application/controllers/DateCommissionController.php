<?php

class DateCommissionController extends Zend_Controller_Action
{
    public function init()
    {
        if ($this->_request->id) {
            $this->view->nav_side_items = array(
                array("text" => "Général", "icon" => "info-sign", "link" => $this->view->url(array('id' => $this->_request->id, 'id_commission' => $this->_request->id_commission), 'datecomm')),
                array("text" => "Pièces jointes", "icon" => "share", "link" => $this->view->url(array('id' => $this->_request->id, 'id_commission' => $this->_request->id_commission), 'datecomm_pieces_jointes'))
            );
        }
    }

    public function indexAction()
    {
        $this->view->commission = Service_Commission::get($this->_request->id_commission);
        $this->view->datecommission = Service_DateCommission::get($this->_request->id);
        $this->view->odj = Service_DateCommission::getODJ($this->_request->id);
        $this->view->dates_liees = Service_DateCommission::getDatesLiees($this->_request->id);
        $this->view->membres_concernes = Service_DateCommission::getListeMembresConcernes($this->_request->id);
    }

    public function addAction()
    {
        $this->view->commission = Service_Commission::get($this->_request->id_commission);
        $this->view->DB_typesevenements = Service_DateCommissionType::getAll();

        if ($this->_request->isPost()) {
            try {
                $post = $this->_request->getPost();
                $id_commission_liee = null;
                foreach($post['dates'] as $date) {
                    $id = Service_DateCommission::save($post['LIBELLE_DATECOMMISSION'], $post['ID_COMMISSIONTYPEEVENEMENT'], $date['jour'], $date['heure_debut'], $date['heure_fin'], $this->_request->id_commission, $id_commission_liee);
                    $id_commission_liee = $id_commission_liee == null ? $id : $id_commission_liee;
                }
                $this->_helper->flashMessenger(array('context' => 'success', 'title' => 'Mise à jour réussie !', 'message' => 'La date de passage en commission a bien été mise à jour.'));
                $this->_helper->redirector->gotoRoute(array('id' => $this->_request->id), 'ets', true);
            } catch (Exception $e) {
                $this->_helper->flashMessenger(array('context' => 'danger','title' => 'Erreur d\'enregistrement','message' => 'La date de passage en commission n\'a pas été mise à jour. Veuillez rééssayez. (' . $e->getMessage() . ')'));
            }
        }
    }

    public function editAction()
    {
        $this->view->commission = Service_Commission::get($this->_request->id_commission);
        $this->view->datecommission = Service_DateCommission::get($this->_request->id);

        if ($this->_request->isPost()) {
            try {
                $post = $this->_request->getPost();
                Service_DateCommission::save();
                $this->_helper->flashMessenger(array('context' => 'success', 'title' => 'Mise à jour réussie !', 'message' => 'La date de passage en commission a bien été mise à jour.'));
                $this->_helper->redirector->gotoRoute(array('id' => $this->_request->id), 'ets', true);
            } catch (Exception $e) {
                $this->_helper->flashMessenger(array('context' => 'danger','title' => 'Erreur d\'enregistrement','message' => 'La date de passage en commission n\'a pas été mise à jour. Veuillez rééssayez. (' . $e->getMessage() . ')'));
            }
        }
    }

    public function editOdjAction()
    {
        $this->view->commission = Service_Commission::get($this->_request->id_commission);
        $this->view->datecommission = Service_DateCommission::get($this->_request->id);
        $this->view->dates_liees = Service_DateCommission::getDatesLiees($this->_request->id);

        if ($this->_request->isPost()) {
            try {
                $post = $this->_request->getPost();
                Service_DateCommission::save();
                $this->_helper->flashMessenger(array('context' => 'success', 'title' => 'Mise à jour réussie !', 'message' => 'La date de passage en commission a bien été mise à jour.'));
                $this->_helper->redirector->gotoRoute(array('id' => $this->_request->id), 'ets', true);
            } catch (Exception $e) {
                $this->_helper->flashMessenger(array('context' => 'danger','title' => 'Erreur d\'enregistrement','message' => 'La date de passage en commission n\'a pas été mise à jour. Veuillez rééssayez. (' . $e->getMessage() . ')'));
            }
        }
    }

    public function piecesJointesAction()
    {
        $this->view->commission = Service_Commission::get($this->_request->id_commission);
        $this->view->datecommission = Service_DateCommission::get($this->_request->id);
        $this->view->pieces_jointes = Service_DateCommission::getAllPJ($this->_request->id);
    }

    public function editPiecesJointesAction()
    {
        $this->view->commission = Service_Commission::get($this->_request->id_commission);
        $this->view->datecommission = Service_DateCommission::get($this->_request->id);
        $this->view->pieces_jointes = Service_DateCommission::getAllPJ($this->_request->id);
    }

    public function addPieceJointeAction()
    {
        if ($this->_request->isPost()) {
            try {
                $post = $this->_request->getPost();
                Service_DateCommission::addPJ($this->_request->id, $_FILES['file'], $post['name'], $post['description']);
                $this->_helper->flashMessenger(array('context' => 'success', 'title' => 'Mise à jour réussie !', 'message' => 'La pièce jointe a bien été ajoutée.'));
            } catch (Exception $e) {
                $this->_helper->flashMessenger(array('context' => 'danger', 'title' => 'Mise à jour annulée', 'message' => 'La pièce jointe n\'a été ajoutée. Veuillez rééssayez. (' . $e->getMessage() . ')'));
            }

            $this->_helper->redirector->gotoRoute(array('id' => $this->_request->id), 'datecomm_pieces_jointes_edit');
        }
    }

    public function deletePieceJointeAction()
    {
        if ($this->_request->isGet()) {
            try {
                Service_DateCommission::deletePJ($this->_request->id, $this->_request->id_pj);
                $this->_helper->flashMessenger(array('context' => 'success', 'title' => 'Suppression réussie !', 'message' => 'La pièce jointe a bien été supprimée.'));
            } catch (Exception $e) {
                $this->_helper->flashMessenger(array('context' => 'danger', 'title' => 'Suppression annulée', 'message' => 'La pièce jointe n\'a été supprimée. Veuillez rééssayez. (' . $e->getMessage() . ')'));
            }

            $this->_helper->redirector->gotoRoute(array('id' => $this->_request->id), 'datecomm_pieces_jointes_edit');
        }
    }
}
