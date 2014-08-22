<?php

class EtablissementController extends Zend_Controller_Action
{
    public function init()
    {
        if ($this->_request->id) {
            $this->view->nav_side_items = array(
                array("text" => "Général", "icon" => "info-sign", "link" => $this->view->url(array('id' => $this->_request->id), 'ets')),
                array("text" => "Textes applicables", "icon" => "align-center", "link" => $this->view->url(array('id' => $this->_request->id), 'ets_textes_applicables')),
                array("text" => "Descriptifs", "icon" => "book", "link" => $this->view->url(array('id' => $this->_request->id), 'ets_descriptifs')),
                array("text" => "Pièces jointes", "icon" => "share", "link" => $this->view->url(array('id' => $this->_request->id), 'ets_pieces_jointes')),
                array("text" => "Contacts", "icon" => "user", "link" => $this->view->url(array('id' => $this->_request->id), 'ets_contacts')),
                array("text" => "Dossiers", "icon" => "folder-open", "link" => $this->view->url(array('id' => $this->_request->id), 'ets_dossiers')),
                array("text" => "Afficher l'historique", "icon" => "repeat", "link" => $this->view->url(array('id' => $this->_request->id), 'ets_historique')),
                array("text" => "Ajouter un dossier", "icon" => "plus", "link" => $this->view->url(array('id' => $this->_request->id), 'doss_add')),
            );
        }
    }

    public function indexAction()
    {
        $etablissement = Service_Etablissement::get($this->_request->id);

        $this->view->couches_cartographiques = Service_Carto::getAll();
        $this->view->key_ign = getenv('PREVARISC_PLUGIN_IGNKEY');
        $this->view->key_googlemap = getenv('PREVARISC_PLUGIN_GOOGLEMAPKEY');

        $contacts = Service_Etablissement::getAllContacts($this->_request->id);
        $contacts_dus = array();

        foreach($contacts as $contact) {
            if($contact['ID_FONCTION'] == 8) {
                $contacts_dus[] = $contact;
            }
        }

        $this->view->presence_dus = count($contacts_dus) > 0;
        $this->view->etablissement = $etablissement;
        $this->view->groupements_de_communes = count($etablissement['adresses']) == 0 ? array() : Service_GroupementCommunes::findAll($etablissement['adresses'][0]["NUMINSEE_COMMUNE"]);
    }

    public function editAction()
    {
        $this->view->etablissement = Service_Etablissement::get($this->_request->id);
        $this->view->DB_genre = Service_Genre::getAll();
        $this->view->DB_statut = Service_Statut::getAll();
        $this->view->DB_avis = Service_Avis::getAll();
        $this->view->DB_categorie = Service_Categorie::getAll();
        $this->view->DB_type = Service_Type::getAll();
        $this->view->DB_activite = Service_TypeActivite::getAll();
        $this->view->DB_commission = Service_Commission::getAll();
        $this->view->DB_typesplan = Service_TypePlan::getAll();
        $this->view->DB_famille = Service_Famille::getAll();
        $this->view->DB_classe = Service_Classe::getAll();

        if ($this->_request->isPost()) {
            try {
                $post = $this->_request->getPost();
                Service_Etablissement::save($post['ID_GENRE'], $post, $this->_request->id, date("Y-m-d"));
                $this->_helper->flashMessenger(array('context' => 'success', 'title' => 'Mise à jour réussie !', 'message' => 'L\'établissement a bien été mis à jour.'));
                $this->_helper->redirector->gotoRoute(array('id' => $this->_request->id), 'ets', true);
            } catch (Exception $e) {
                $this->_helper->flashMessenger(array('context' => 'danger','title' => 'Erreur d\'enregistrement','message' => 'L\'établissement n\'a pas été mis à jour. Veuillez rééssayez. (' . $e->getMessage() . ')'));
            }
        }
    }

    public function addAction()
    {
        $this->view->DB_genre = Service_Genre::getAll();
        $this->view->DB_statut = Service_Statut::getAll();
        $this->view->DB_avis = Service_Avis::getAll();
        $this->view->DB_categorie = Service_Categorie::getAll();
        $this->view->DB_type = Service_Type::getAll();
        $this->view->DB_activite = Service_TypeActivite::getAll();
        $this->view->DB_commission = Service_Commission::getAll();
        $this->view->DB_typesplan = Service_TypePlan::getAll();
        $this->view->DB_famille = Service_Famille::getAll();
        $this->view->DB_classe = Service_Classe::getAll();

        if ($this->_request->isPost()) {
            try {
                $post = $this->_request->getPost();
                $id_etablissement = Service_Etablissement::save($post['ID_GENRE'], $post);
                $this->_helper->flashMessenger(array('context' => 'success', 'title' => 'Ajout réussi !', 'message' => 'L\'établissement a bien été ajouté.'));
                if ($post['ID_GENRE'] == 1 && count($post['ID_FILS_ETABLISSEMENT']) == 1) {
                    $this->_helper->flashMessenger(array('context' => 'warning', 'title' => 'Ajout des établissements enfants', 'message' => "Les droits d'accès au site sont déterminés par les droits d'accès aux établissements qui le compose. Veillez à ajouter des établissements afin de garantir l'accès au site dans Prevarisc."));
                    $this->_helper->redirector->gotoRoute(array('id' => $this->_request->id), 'ets_edit', true);
                } else {
                    $this->_helper->redirector->gotoRoute(array('id' => $this->_request->id), 'ets', true);
                }
            } catch (Exception $e) {
                $this->_helper->flashMessenger(array('context' => 'danger','title' => 'Erreur d\'enregistrement','message' => 'L\'établissement n\'a pas été ajouté. Veuillez rééssayez. (' . $e->getMessage() . ')'));
            }
        }

        $this->render('edit');
    }

    public function descriptifAction()
    {
        $descriptifs = Service_Etablissement::getDescriptifs($this->_request->id);

        $this->view->etablissement = Service_Etablissement::get($this->_request->id);
        $this->view->descriptif = $descriptifs['descriptif'];
        $this->view->historique = $descriptifs['historique'];
        $this->view->derogations = $descriptifs['derogations'];
        $this->view->champs_descriptif_technique = $descriptifs['descriptifs_techniques'];
    }

    public function editDescriptifAction()
    {
        $descriptifs = Service_Etablissement::getDescriptifs($this->_request->id);

        $this->view->etablissement = Service_Etablissement::get($this->_request->id);
        $this->view->descriptif = $descriptifs['descriptif'];
        $this->view->historique = $descriptifs['historique'];
        $this->view->derogations = $descriptifs['derogations'];
        $this->view->champs_descriptif_technique = $descriptifs['descriptifs_techniques'];

        if ($this->_request->isPost()) {
            try {
                $post = $this->_request->getPost();
                Service_Etablissement::saveDescriptifs($this->_request->id, $post['historique'], $post['descriptif'], $post['derogations'], $post['descriptifs_techniques']);
                $this->_helper->flashMessenger(array('context' => 'success', 'title' => 'Mise à jour réussie !', 'message' => 'Les descriptifs ont bien été mis à jour.'));
            } catch (Exception $e) {
                $this->_helper->flashMessenger(array('context' => 'danger', 'title' => 'Mise à jour annulée', 'message' => 'Les descriptifs n\'ont pas été mis à jour. Veuillez rééssayez. (' . $e->getMessage() . ')'));
            }

            $this->_helper->redirector->gotoRoute(array('id' => $this->_request->id), 'ets_descriptifs', true);
        }
    }

    public function textesApplicablesAction()
    {
        $this->view->etablissement = Service_Etablissement::get($this->_request->id);
        $this->view->textes_applicables_de_etablissement = Service_Etablissement::getAllTextesApplicables($this->_request->id);
    }

    public function editTextesApplicablesAction()
    {
        $this->view->etablissement = Service_Etablissement::get($this->_request->id);
        $this->view->textes_applicables_de_etablissement = Service_Etablissement::getAllTextesApplicables($this->_request->id);
        $this->view->textes_applicables = Service_TextesApplicables::getAll();

        if ($this->_request->isPost()) {
            try {
                $post = $this->_request->getPost();
                Service_Etablissement::saveTextesApplicables($this->_request->id, $post['textes_applicables']);
                $this->_helper->flashMessenger(array('context' => 'success','title' => 'Mise à jour réussie !','message' => 'Les textes applicables ont bien été mis à jour.'));
            } catch (Exception $e) {
                $this->_helper->flashMessenger(array('context' => 'danger','title' => 'Mise à jour annulée','message' => 'Les textes applicables n\'ont pas été mis à jour. Veuillez rééssayez. (' . $e->getMessage() . ')'));
            }

            $this->_helper->redirector->gotoRoute(array('id' => $this->_request->id), 'ets_textes_applicables', true);
        }
    }

    public function piecesJointesAction()
    {
        $this->view->etablissement = Service_Etablissement::get($this->_request->id);
        $this->view->pieces_jointes = Service_Etablissement::getAllPJ($this->_request->id);
    }

    public function editPiecesJointesAction()
    {
        $this->view->etablissement = Service_Etablissement::get($this->_request->id);
        $this->view->pieces_jointes = Service_Etablissement::getAllPJ($this->_request->id);
    }

    public function addPieceJointeAction()
    {
        if ($this->_request->isPost()) {
            try {
                $post = $this->_request->getPost();
                Service_Etablissement::addPJ($this->_request->id, $_FILES['file'], $post['name'], $post['description'], $post['mise_en_avant']);
                $this->_helper->flashMessenger(array('context' => 'success', 'title' => 'Mise à jour réussie !', 'message' => 'La pièce jointe a bien été ajoutée.'));
            } catch (Exception $e) {
                $this->_helper->flashMessenger(array('context' => 'danger', 'title' => 'Mise à jour annulée', 'message' => 'La pièce jointe n\'a été ajoutée. Veuillez rééssayez. (' . $e->getMessage() . ')'));
            }

            $this->_helper->redirector->gotoRoute(array('id' => $this->_request->id), 'ets_pieces_jointes_edit');
        }
    }

    public function deletePieceJointeAction()
    {
        if ($this->_request->isGet()) {
            try {
                Service_Etablissement::deletePJ($this->_request->id, $this->_request->id_pj);
                $this->_helper->flashMessenger(array('context' => 'success', 'title' => 'Suppression réussie !', 'message' => 'La pièce jointe a bien été supprimée.'));
            } catch (Exception $e) {
                $this->_helper->flashMessenger(array('context' => 'danger', 'title' => 'Suppression annulée', 'message' => 'La pièce jointe n\'a été supprimée. Veuillez rééssayez. (' . $e->getMessage() . ')'));
            }

            $this->_helper->redirector->gotoRoute(array('id' => $this->_request->id), 'ets_pieces_jointes_edit');
        }
    }

    public function contactsAction()
    {
        $etablissement = Service_Etablissement::get($this->_request->id);

        $contacts_etablissements_parents = count($etablissement['parents']) == 0 ? array() : call_user_func(function () use ($etablissement) {
            $etablissements_parents = array();
            foreach ($etablissement['parents'] as $etablissement_parent)
                    $etablissements_parents = array_merge($etablissements_parents, Service_Etablissement::getAllContacts($etablissement_parent['ID_ETABLISSEMENT']));

            return $etablissements_parents;
        });

        $this->view->etablissement = $etablissement;
        $this->view->contacts = Service_Etablissement::getAllContacts($this->_request->id);
        $this->view->contacts_etablissements_parents = $contacts_etablissements_parents;
    }

    public function editContactsAction()
    {
        $this->view->etablissement = Service_Etablissement::get($this->_request->id);
        $this->view->contacts = Service_Etablissement::getAllContacts($this->_request->id);
    }

    public function addContactAction()
    {
        $this->view->fonctions = Service_Contact::getFonctions();

        if ($this->_request->isPost()) {
            try {
                $post = $this->_request->getPost();
                Service_Etablissement::addContact($this->_request->id, $post['firstname'], $post['lastname'], $post['id_fonction'], $post['societe'], $post['fixe'], $post['mobile'], $post['fax'], $post['mail'], $post['adresse'], $post['web']);
                $this->_helper->flashMessenger(array('context' => 'success', 'title' => 'Mise à jour réussie !', 'message' => 'Le contact a bien été ajouté.'));
            } catch (Exception $e) {
                $this->_helper->flashMessenger(array('context' => 'danger', 'title' => 'Mise à jour annulée', 'message' => 'Le contact n\'a été ajouté. Veuillez rééssayez. (' . $e->getMessage() . ')'));
            }

            $this->_helper->redirector->gotoRoute(array('id' => $this->_request->id), 'ets_contacts_edit');
        }
    }

    public function addContactExistantAction()
    {
        if ($this->_request->isPost()) {
            try {
                Service_Etablissement::addContactExistant($this->_request->id, $this->_request->id_contact);
                $this->_helper->flashMessenger(array('context' => 'success', 'title' => 'Mise à jour réussie !', 'message' => 'Le contact a bien été ajouté.'));
            } catch (Exception $e) {
                $this->_helper->flashMessenger(array('context' => 'danger', 'title' => 'Mise à jour annulée', 'message' => 'Le contact n\'a été ajouté. Veuillez rééssayez. (' . $e->getMessage() . ')'));
            }

            $this->_helper->redirector->gotoRoute(array('id' => $this->_request->id), 'ets_contacts_edit');
        }
    }

    public function deleteContactAction()
    {
        if ($this->_request->isGet()) {
            try {
                Service_Etablissement::deleteContact($this->_request->id, $this->_request->id_contact);
                $this->_helper->flashMessenger(array('context' => 'success', 'title' => 'Suppression réussie !', 'message' => 'Le contact a bien été supprimé de la fiche établissement.'));
            } catch (Exception $e) {
                $this->_helper->flashMessenger(array('context' => 'danger', 'title' => 'Suppression annulée', 'message' => 'Le contact n\'a été supprimé. Veuillez rééssayez. (' . $e->getMessage() . ')'));
            }

            $this->_helper->redirector->gotoRoute(array('id' => $this->_request->id), 'ets_contacts_edit');
        }
    }

    public function dossiersAction()
    {
        $dossiers = Service_Etablissement::getDossiers($this->_request->id);

        $this->view->etablissement = Service_Etablissement::get($this->_request->id);
        $this->view->etudes = $dossiers['etudes'];
        $this->view->visites = $dossiers['visites'];
        $this->view->autres = $dossiers['autres'];
    }

    public function historiqueAction()
    {
        $this->view->etablissement = Service_Etablissement::get($this->_request->id);
        $this->view->historique = Service_Etablissement::getHistorique($this->_request->id);
    }
}
