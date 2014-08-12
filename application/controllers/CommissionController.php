<?php

class CommissionController extends Zend_Controller_Action
{
    public function init()
    {
        if ($this->_request->id) {
            $this->view->nav_side_items = array(
                array("text" => "Général", "icon" => "info-sign", "link" => $this->view->url(array('id' => $this->_request->id), 'comm')),
                array("text" => "Compétences", "icon" => "filter", "link" => $this->view->url(array('id' => $this->_request->id), 'comm_competences')),
                array("text" => "Membres types", "icon" => "user", "link" => $this->view->url(array('id' => $this->_request->id), 'comm_membres')),
                array("text" => "Documents et courriers types", "icon" => "book", "link" => $this->view->url(array('id' => $this->_request->id), 'comm_documents')),
                array("text" => "Contacts", "icon" => "user", "link" => $this->view->url(array('id' => $this->_request->id), 'comm_contacts')),
                array("text" => "Calendrier", "icon" => "calendar", "link" => $this->view->url(array('id' => $this->_request->id), 'comm_calendrier'))
            );
        }
    }

    public function indexAction()
    {
        $this->view->commission = Service_Commission::get($this->_request->id);
        $this->view->calendrier = Service_Commission::getCalendrier($this->_request->id);
    }

    public function editAction()
    {
        $this->view->commission = Service_Commission::get($this->_request->id);
        $this->view->typesDesCommissions = Service_TypeCommission::getAll();

        if ($this->_request->isPost()) {
            try {
                $post = $this->_request->getPost();
                Service_Commission::save($post['name'], $post['id_type'], $post['active'], $this->_request->id);
                $this->_helper->flashMessenger(array('context' => 'success', 'title' => 'Mise à jour réussie !', 'message' => 'La commission a bien été mise à jour.'));
                $this->_helper->redirector->gotoRoute(array('id' => $this->_request->id), 'comm', true);
            } catch (Exception $e) {
                $this->_helper->flashMessenger(array('context' => 'danger','title' => 'Erreur d\'enregistrement','message' => 'La commission n\'a pas été mise à jour. Veuillez rééssayez. (' . $e->getMessage() . ')'));
            }
        }
    }

    public function addAction()
    {
        $this->view->typesDesCommissions = Service_TypeCommission::getAll();

        if ($this->_request->isPost()) {
            try {
                $post = $this->_request->getPost();
                Service_Commission::save($post['name'], $post['id_type'], $post['active'], $this->_request->id);
                $this->_helper->flashMessenger(array('context' => 'success', 'title' => 'Ajout réussi !', 'message' => 'La commission a bien été ajoutée.'));
                $this->_helper->redirector->gotoRoute(array('id' => $this->_request->id), 'comm', true);
            } catch (Exception $e) {
                $this->_helper->flashMessenger(array('context' => 'danger','title' => 'Erreur d\'enregistrement','message' => 'La commission n\'a pas été ajoutée. Veuillez rééssayez. (' . $e->getMessage() . ')'));
            }
        }

        $this->render('edit');
    }

    public function deleteAction()
    {
        try {
            Service_Commission::delete($this->_request->id);
            $this->_helper->flashMessenger(array('context' => 'success', 'title' => 'Suppression réussie !', 'message' => 'La commission a bien été supprimée.'));
        } catch (Exception $e) {
            $this->_helper->flashMessenger(array('context' => 'danger','title' => 'Erreur d\'enregistrement','message' => 'La commission n\'a pas été supprimée. Veuillez rééssayez. (' . $e->getMessage() . ')'));
        }

        $this->_helper->redirector->gotoRoute(array('id' => $this->_request->id), 'comm_edit', true);
    }

    public function competencesAction()
    {
        $this->view->commission = Service_Commission::get($this->_request->id);
        $this->view->competences = Service_Commission::getCompetences($this->_request->id);
    }

    public function editCompetencesAction()
    {
        $this->view->groupements = Service_GroupementCommunes::findAll();
        $this->view->communes = Service_Adresse::getAllCommunes();
        $this->view->commission = Service_Commission::get($this->_request->id);
        $this->view->competences = Service_Commission::getCompetences($this->_request->id);

        if ($this->_request->isPost()) {
            try {
                Service_Commission::saveCompetences($this->_request->id, $this->_request->getPost());
                $this->_helper->flashMessenger(array('context' => 'success','title' => 'Mise à jour réussie !','message' => 'Les compétences de la commission ont bien été mise à jour.'));
            } catch (Exception $e) {
                $this->_helper->flashMessenger(array('context' => 'danger','title' => 'Mise à jour annulée','message' => 'Les compétences de la commission n\'ont pas été mise à jour. Veuillez rééssayez. (' . $e->getMessage() . ')'));
            }

            $this->_helper->redirector->gotoRoute(array('id' => $this->_request->id), 'comm_competences', true);
        }
    }

    public function addCompetenceAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);

        try {
            Service_Commission::addCompetence($this->_request->id);
            $this->_helper->flashMessenger(array('context' => 'success','title' => 'Ajout réussi !','message' => 'Une compétence a bien été ajoutée.'));
        } catch (Exception $e) {
            $this->_helper->flashMessenger(array('context' => 'danger','title' => 'Ajout annulé','message' => 'La compétence n\'a pas été ajoutée. Veuillez rééssayez. (' . $e->getMessage() . ')'));
        }

        $this->_helper->redirector->gotoRoute(array('id' => $this->_request->id), 'comm_competences_edit', true);
    }

    public function deleteCompetenceAction()
    {
        try {
            Service_Commission::deleteCompetence($this->_request->id, $this->_request->id_competence);
            $this->_helper->flashMessenger(array('context' => 'success','title' => 'Suppression réussie !','message' => 'La compétence de la commission a bien été supprimée.'));
        } catch (Exception $e) {
            $this->_helper->flashMessenger(array('context' => 'danger','title' => 'Suppression annulée','message' => 'La compétence de la commission n\'a pas été supprimée. Veuillez rééssayez. (' . $e->getMessage() . ')'));
        }

        $this->_helper->redirector->gotoRoute(array('id' => $this->_request->id), 'comm_competences_edit', true);
    }

    public function membresAction()
    {
        $this->view->commission = Service_Commission::get($this->_request->id);
        $this->view->membres = Service_Commission::getMembres($this->_request->id);
    }

    public function editMembresAction()
    {
        $this->view->commission = Service_Commission::get($this->_request->id);
        $this->view->membres = Service_Commission::getMembres($this->_request->id);
        $this->view->groupements = Service_GroupementCommunes::findAll();

        if ($this->_request->isPost()) {
            try {
                Service_Commission::saveMembres($this->_request->id, $this->_request->getPost());
                $this->_helper->flashMessenger(array('context' => 'success','title' => 'Mise à jour réussie !','message' => 'Les membres de la commission ont bien été mis à jour.'));
            } catch (Exception $e) {
                $this->_helper->flashMessenger(array('context' => 'danger','title' => 'Mise à jour annulée','message' => 'Les membres de la commission n\'ont pas été mis à jour. Veuillez rééssayez. (' . $e->getMessage() . ')'));
            }

            $this->_helper->redirector->gotoRoute(array('id' => $this->_request->id), 'comm_membres', true);
        }
    }

    public function addMembreAction()
    {
        try {
            Service_Commission::addMembre($this->_request->id);
            $this->_helper->flashMessenger(array('context' => 'success','title' => 'Ajout réussi !','message' => 'Un membre a bien été ajouté.'));
        } catch (Exception $e) {
            $this->_helper->flashMessenger(array('context' => 'danger','title' => 'Ajout annulé','message' => 'Le membre n\'a pas été ajouté. Veuillez rééssayez. (' . $e->getMessage() . ')'));
        }

        $this->_helper->redirector->gotoRoute(array('id' => $this->_request->id), 'comm_membres_edit', true);
    }

    public function deleteMembreAction()
    {
        try {
            Service_Commission::deleteMembre($this->_request->id, $this->_request->id_membre);
            $this->_helper->flashMessenger(array('context' => 'success','title' => 'Suppression réussie !','message' => 'Le membre de la commission a bien été supprimé.'));
        } catch (Exception $e) {
            $this->_helper->flashMessenger(array('context' => 'danger','title' => 'Suppression annulée','message' => 'Le membre de la commission n\'a pas été supprimé. Veuillez rééssayez. (' . $e->getMessage() . ')'));
        }

        $this->_helper->redirector->gotoRoute(array('id' => $this->_request->id), 'comm_membres_edit', true);
    }

    public function documentsAction()
    {
        $this->view->commission = Service_Commission::get($this->_request->id);
        $this->view->documents = Service_Commission::getDocumentsTypes($this->_request->id);
    }

    public function editDocumentsAction()
    {
        $this->view->commission = Service_Commission::get($this->_request->id);
        $this->view->documents = Service_Commission::getDocumentsTypes($this->_request->id);

        if ($_FILES) {
            try {
                Service_Commission::saveDocumentsTypes($this->_request->id, $_FILES['documents'], $_FILES['courriers']);
                $this->_helper->flashMessenger(array('context' => 'success','title' => 'Mise à jour réussie !','message' => 'Les documents types ont bien été mis à jour.'));
            } catch (Exception $e) {
                $this->_helper->flashMessenger(array('context' => 'danger','title' => 'Mise à jour annulée','message' => 'Les documents n\'ont pas été mis à jour. Veuillez rééssayez. (' . $e->getMessage() . ')'));
            }

            $this->_helper->redirector->gotoRoute(array('id' => $this->_request->id), 'comm_documents', true);
        }
    }

    public function contactsAction()
    {
        $this->view->commission = Service_Commission::get($this->_request->id);
        $this->view->contacts = Service_Commission::getAllContacts($this->_request->id);
    }

    public function editContactsAction()
    {
        $this->view->commission = Service_Commission::get($this->_request->id);
        $this->view->contacts = Service_Commission::getAllContacts($this->_request->id);
    }

    public function addContactAction()
    {
        $this->view->fonctions = Service_Contact::getFonctions();

        if ($this->_request->isPost()) {
            try {
                $post = $this->_request->getPost();
                Service_Commission::addContact($this->_request->id, $post['firstname'], $post['lastname'], $post['id_fonction'], $post['societe'], $post['fixe'], $post['mobile'], $post['fax'], $post['mail'], $post['adresse'], $post['web']);
                $this->_helper->flashMessenger(array('context' => 'success', 'title' => 'Mise à jour réussie !', 'message' => 'Le contact a bien été ajouté.'));
            } catch (Exception $e) {
                $this->_helper->flashMessenger(array('context' => 'danger', 'title' => 'Mise à jour annulée', 'message' => 'Le contact n\'a été ajouté. Veuillez rééssayez. (' . $e->getMessage() . ')'));
            }

            $this->_helper->redirector->gotoRoute(array('id' => $this->_request->id), 'comm_contacts_edit');
        }
    }

    public function addContactExistantAction()
    {
        if ($this->_request->isPost()) {
            try {
                Service_Commission::addContactExistant($this->_request->id, $this->_request->id_contact);
                $this->_helper->flashMessenger(array('context' => 'success', 'title' => 'Mise à jour réussie !', 'message' => 'Le contact a bien été ajouté.'));
            } catch (Exception $e) {
                $this->_helper->flashMessenger(array('context' => 'danger', 'title' => 'Mise à jour annulée', 'message' => 'Le contact n\'a été ajouté. Veuillez rééssayez. (' . $e->getMessage() . ')'));
            }

            $this->_helper->redirector->gotoRoute(array('id' => $this->_request->id), 'comm_contacts_edit');
        }
    }

    public function deleteContactAction()
    {
        if ($this->_request->isGet()) {
            try {
                Service_Commission::deleteContact($this->_request->id, $this->_request->id_contact);
                $this->_helper->flashMessenger(array('context' => 'success', 'title' => 'Suppression réussie !', 'message' => 'Le contact a bien été supprimé de la commission.'));
            } catch (Exception $e) {
                $this->_helper->flashMessenger(array('context' => 'danger', 'title' => 'Suppression annulée', 'message' => 'Le contact n\'a été supprimé. Veuillez rééssayez. (' . $e->getMessage() . ')'));
            }

            $this->_helper->redirector->gotoRoute(array('id' => $this->_request->id), 'comm_contacts_edit');
        }
    }

    public function calendrierAction()
    {
        $this->view->commission = Service_Commission::get($this->_request->id);
    }
}
