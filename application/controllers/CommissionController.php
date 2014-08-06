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
        $service_commission = new Service_Commission;

        $commission = $service_commission->get($this->_request->id);
        $calendrier = $service_commission->get($this->_request->id);

        $this->view->commission = $commission;
        $this->view->calendrier = $calendrier;
    }

    public function editAction()
    {
        $service_commission = new Service_Commission;
        $model_typesDesCommissions = new Model_DbTable_CommissionType;

        $commission = $service_commission->get($this->_request->id);

        $this->view->commission = $commission;
        $this->view->typesDesCommissions = $model_typesDesCommissions->fetchAll()->toArray();
        $this->view->add = false;

        if ($this->_request->isPost()) {
            try {
                $post = $this->_request->getPost();
                $service_commission->save($post['name'], $post['id_type'], $post['active'], $this->_request->id);
                $this->_helper->flashMessenger(array('context' => 'success', 'title' => 'Mise à jour réussie !', 'message' => 'La commission a bien été mise à jour.'));
                $this->_helper->redirector->gotoRoute(array('id' => $this->_request->id), 'comm', true);
            } catch (Exception $e) {
                $this->_helper->flashMessenger(array('context' => 'danger','title' => 'Erreur d\'enregistrement','message' => 'La commission n\'a pas été mise à jour. Veuillez rééssayez. (' . $e->getMessage() . ')'));
            }
        }
    }

    public function addAction()
    {
        $service_commission = new Service_Commission;
        $model_typesDesCommissions = new Model_DbTable_CommissionType;

        $this->view->typesDesCommissions = $model_typesDesCommissions->fetchAll()->toArray();
        $this->view->add = true;

        if ($this->_request->isPost()) {
            try {
                $post = $this->_request->getPost();
                $service_commission->save($post['name'], $post['id_type'], $post['active'], $this->_request->id);
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
        $this->_helper->viewRenderer->setNoRender(true);

        $service_commission = new Service_Commission;

        $commission = $service_commission->get($this->_request->id);

        try {
            $service_commission->delete($this->_request->id);
            $this->_helper->flashMessenger(array('context' => 'success', 'title' => 'Suppression réussie !', 'message' => 'La commission a bien été supprimée.'));
        } catch (Exception $e) {
            $this->_helper->flashMessenger(array('context' => 'danger','title' => 'Erreur d\'enregistrement','message' => 'La commission n\'a pas été supprimée. Veuillez rééssayez. (' . $e->getMessage() . ')'));
        }

        $this->_helper->redirector->gotoRoute(array('id' => $this->_request->id), 'comm_edit', true);
    }

    public function competencesAction()
    {
        $service_commission = new Service_Commission;

        $this->view->commission = $service_commission->get($this->_request->id);
        $this->view->competences = $service_commission->getCompetences($this->_request->id);
    }

    public function editCompetencesAction()
    {
        $service_commission = new Service_Commission;
        $service_adresse = new Service_Adresse;
        $service_groupement = new Service_GroupementCommunes;

        $this->view->groupements = $service_groupement->findAll();
        $this->view->communes = $service_adresse->getAllCommunes();
        $this->view->commission = $service_commission->get($this->_request->id);
        $this->view->competences = $service_commission->getCompetences($this->_request->id);

        if ($this->_request->isPost()) {
            try {
                $post = $this->_request->getPost();
                $service_commission->saveCompetences($this->_request->id, $post);
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

        $service_commission = new Service_Commission;

        try {
            $service_commission->addCompetence($this->_request->id);
            $this->_helper->flashMessenger(array('context' => 'success','title' => 'Ajout réussi !','message' => 'Une compétence a bien été ajoutée.'));
        } catch (Exception $e) {
            $this->_helper->flashMessenger(array('context' => 'danger','title' => 'Ajout annulé','message' => 'La compétence n\'a pas été ajoutée. Veuillez rééssayez. (' . $e->getMessage() . ')'));
        }

        $this->_helper->redirector->gotoRoute(array('id' => $this->_request->id), 'comm_competences_edit', true);
    }

    public function deleteCompetenceAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);

        $service_commission = new Service_Commission;

        try {
            $service_commission->deleteCompetence($this->_request->id, $this->_request->id_competence);
            $this->_helper->flashMessenger(array('context' => 'success','title' => 'Suppression réussie !','message' => 'La compétence de la commission a bien été supprimée.'));
        } catch (Exception $e) {
            $this->_helper->flashMessenger(array('context' => 'danger','title' => 'Suppression annulée','message' => 'La compétence de la commission n\'a pas été supprimée. Veuillez rééssayez. (' . $e->getMessage() . ')'));
        }

        $this->_helper->redirector->gotoRoute(array('id' => $this->_request->id), 'comm_competences_edit', true);
    }

    public function membresAction()
    {
        $service_commission = new Service_Commission;

        $this->view->commission = $service_commission->get($this->_request->id);
        $this->view->membres = $service_commission->getMembres($this->_request->id);
    }

    public function editMembresAction()
    {
        $service_commission = new Service_Commission;
        $service_groupement = new Service_GroupementCommunes;

        $this->view->commission = $service_commission->get($this->_request->id);
        $this->view->membres = $service_commission->getMembres($this->_request->id);
        $this->view->groupements = $service_groupement->findAll();

        if ($this->_request->isPost()) {
            try {
                $post = $this->_request->getPost();
                $service_commission->saveMembres($this->_request->id, $post);
                $this->_helper->flashMessenger(array('context' => 'success','title' => 'Mise à jour réussie !','message' => 'Les membres de la commission ont bien été mis à jour.'));
            } catch (Exception $e) {
                $this->_helper->flashMessenger(array('context' => 'danger','title' => 'Mise à jour annulée','message' => 'Les membres de la commission n\'ont pas été mis à jour. Veuillez rééssayez. (' . $e->getMessage() . ')'));
            }

            $this->_helper->redirector->gotoRoute(array('id' => $this->_request->id), 'comm_membres', true);
        }
    }

    public function addMembreAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);

        $service_commission = new Service_Commission;

        try {
            $service_commission->addMembre($this->_request->id);
            $this->_helper->flashMessenger(array('context' => 'success','title' => 'Ajout réussi !','message' => 'Un membre a bien été ajouté.'));
        } catch (Exception $e) {
            $this->_helper->flashMessenger(array('context' => 'danger','title' => 'Ajout annulé','message' => 'Le membre n\'a pas été ajouté. Veuillez rééssayez. (' . $e->getMessage() . ')'));
        }

        $this->_helper->redirector->gotoRoute(array('id' => $this->_request->id), 'comm_membres_edit', true);
    }

    public function deleteMembreAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);

        $service_commission = new Service_Commission;

        try {
            $service_commission->deleteMembre($this->_request->id, $this->_request->id_membre);
            $this->_helper->flashMessenger(array('context' => 'success','title' => 'Suppression réussie !','message' => 'Le membre de la commission a bien été supprimé.'));
        } catch (Exception $e) {
            $this->_helper->flashMessenger(array('context' => 'danger','title' => 'Suppression annulée','message' => 'Le membre de la commission n\'a pas été supprimé. Veuillez rééssayez. (' . $e->getMessage() . ')'));
        }

        $this->_helper->redirector->gotoRoute(array('id' => $this->_request->id), 'comm_membres_edit', true);
    }

    public function documentsAction()
    {
        $service_commission = new Service_Commission;

        $this->view->commission = $service_commission->get($this->_request->id);
        $this->view->documents = $service_commission->getDocumentsTypes($this->_request->id);
    }

    public function editDocumentsAction()
    {
        $service_commission = new Service_Commission;

        $this->view->commission = $service_commission->get($this->_request->id);
        $this->view->documents = $service_commission->getDocumentsTypes($this->_request->id);

        if ($this->_request->isPost()) {
            try {
                $files = $_FILES;
                $service_commission->saveDocumentsTypes($this->_request->id, $files['documents'], $files['courriers']);
                $this->_helper->flashMessenger(array('context' => 'success','title' => 'Mise à jour réussie !','message' => 'Les documents types ont bien été mis à jour.'));
            } catch (Exception $e) {
                $this->_helper->flashMessenger(array('context' => 'danger','title' => 'Mise à jour annulée','message' => 'Les documents n\'ont pas été mis à jour. Veuillez rééssayez. (' . $e->getMessage() . ')'));
            }

            $this->_helper->redirector->gotoRoute(array('id' => $this->_request->id), 'comm_documents', true);
        }
    }

    public function contactsAction()
    {
        $service_commission = new Service_Commission;

        $commission = $service_commission->get($this->_request->id);

        $this->view->commission = $commission;
        $this->view->contacts = $service_commission->getAllContacts($this->_request->id);
    }

    public function editContactsAction()
    {
        $service_commission = new Service_Commission;

        $this->view->commission = $service_commission->get($this->_request->id);
        $this->view->contacts = $service_commission->getAllContacts($this->_request->id);
    }

    public function addContactAction()
    {
        $service_commission = new Service_Commission;
        $service_contact = new Service_Contact;

        $this->view->fonctions = $service_contact->getFonctions();

        if ($this->_request->isPost()) {
            try {
                $post = $this->_request->getPost();
                $service_commission->addContact($this->_request->id, $post['firstname'], $post['lastname'], $post['id_fonction'], $post['societe'], $post['fixe'], $post['mobile'], $post['fax'], $post['mail'], $post['adresse'], $post['web']);
                $this->_helper->flashMessenger(array('context' => 'success', 'title' => 'Mise à jour réussie !', 'message' => 'Le contact a bien été ajouté.'));
            } catch (Exception $e) {
                $this->_helper->flashMessenger(array('context' => 'danger', 'title' => 'Mise à jour annulée', 'message' => 'Le contact n\'a été ajouté. Veuillez rééssayez. (' . $e->getMessage() . ')'));
            }

            $this->_helper->redirector->gotoRoute(array('id' => $this->_request->id), 'comm_contacts_edit');
        }
    }

    public function addContactExistantAction()
    {
        $service_commission = new Service_Commission;

        if ($this->_request->isPost()) {
            try {
                $post = $this->_request->getPost();
                $service_commission->addContactExistant($this->_request->id, $this->_request->id_contact);
                $this->_helper->flashMessenger(array('context' => 'success', 'title' => 'Mise à jour réussie !', 'message' => 'Le contact a bien été ajouté.'));
            } catch (Exception $e) {
                $this->_helper->flashMessenger(array('context' => 'danger', 'title' => 'Mise à jour annulée', 'message' => 'Le contact n\'a été ajouté. Veuillez rééssayez. (' . $e->getMessage() . ')'));
            }

            $this->_helper->redirector->gotoRoute(array('id' => $this->_request->id), 'comm_contacts_edit');
        }
    }

    public function deleteContactAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);

        $service_commission = new Service_Commission;

        if ($this->_request->isGet()) {
            try {
                $post = $this->_request->getPost();
                $service_commission->deleteContact($this->_request->id, $this->_request->id_contact);
                $this->_helper->flashMessenger(array('context' => 'success', 'title' => 'Suppression réussie !', 'message' => 'Le contact a bien été supprimé de la commission.'));
            } catch (Exception $e) {
                $this->_helper->flashMessenger(array('context' => 'danger', 'title' => 'Suppression annulée', 'message' => 'Le contact n\'a été supprimé. Veuillez rééssayez. (' . $e->getMessage() . ')'));
            }

            $this->_helper->redirector->gotoRoute(array('id' => $this->_request->id), 'comm_contacts_edit');
        }
    }

    public function calendrierAction()
    {
        $service_commission = new Service_Commission;

        $this->view->commission = $service_commission->get($this->_request->id);
    }
}
