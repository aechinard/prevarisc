<?php

class Service_Commission extends Service_Abstract implements Service_Interface_Commission
{
    /**
     * Récupération de l'ensemble des commissions
     *
     * @return array
     */
    public function getAll()
    {
        // Modèle de données
        $model_typesDesCommissions = new Model_DbTable_CommissionType();
        $model_commission = new Model_DbTable_Commission();

        // On cherche tous les types de commissions
        $rowset_typesDesCommissions = $model_typesDesCommissions->fetchAll();

        // Tableau de résultats
        $array_commissions = array();

        // Pour tous les types, on cherche leur commission
        foreach ($rowset_typesDesCommissions as $row_typeDeCommission) {
            $array_results = $model_commission->fetchAll("ID_COMMISSIONTYPE = " . $row_typeDeCommission->ID_COMMISSIONTYPE )->toArray();
            $array_results2 = array();
            foreach ($array_results as $item) {
              $array_results2[] = array(
                "ID_COMMISSION" => $item["ID_COMMISSION"],
                "LIBELLE_COMMISSION" => $item["LIBELLE_COMMISSION"],
                "DOCUMENT_CR" => $item["DOCUMENT_CR"],
                "ID_COMMISSIONTYPE" => $item["ID_COMMISSIONTYPE"],
                "LIBELLE_COMMISSIONTYPE" => $row_typeDeCommission->LIBELLE_COMMISSIONTYPE
              );
            }
            $array_commissions[$row_typeDeCommission->ID_COMMISSIONTYPE] = array(
                "LIBELLE" => $row_typeDeCommission->LIBELLE_COMMISSIONTYPE,
                "ARRAY" => $array_results2
            );
        }

        return $array_commissions;
    }

    /**
     * Récupération d'une commission
     *
     * @param  int   $id_commission
     * @throws Exception Si la commission n'existe pas
     * @return array
     */
    public function get($id_commission)
    {
        $model_commission = new Model_DbTable_Commission();
        $model_typesDesCommissions = new Model_DbTable_CommissionType();

        $result = $model_commission->find($id_commission)->current()->toArray();

        if ($result == null) {
            throw new Exception('Commission inconnue.');
        }

        // Ajout du type de la commission
        $result['LIBELLE_COMMISSIONTYPE'] = $model_typesDesCommissions->fetchRow("ID_COMMISSIONTYPE = " . $result['ID_COMMISSIONTYPE'])['LIBELLE_COMMISSIONTYPE'];

        return $result;
    }

    /**
     * Récupération de l'ensemble des commissions
     *
     * @param  string   $name
     * @param  int   $id_type
     * @param  bool   $actif
     * @param  int   $id_commission optionnel
     * @return array
     */
    public function save($name, $id_type, $actif, $id_commission = null)
    {
        $model_commissions = new Model_DbTable_Commission;

        $item = $id_commission == null ? $model_commissions->createRow() : $model_commissions->find($id_commission)->current();
        $item->ID_COMMISSIONTYPE = $id_type;
        $item->LIBELLE_COMMISSION = $name;
        // $item->ACTIVE_COMMISSION = $actif;
        $item->save();
    }

    /**
     * Suppression d'une commission
     *
     * @param  int   $id_commission
     */
    public function delete($id_commission)
    {
        $model_commission = new Model_DbTable_Commission;
        $model_commission->find($id_commission)->current()->delete();
    }

    /**
     * Récupération des compétences de la commission
     *
     * @param  int   $id_commission
     * @return array
     */
    public function getCompetences($id_commission)
    {
        $model_regles = new Model_DbTable_CommissionRegle;
        return $model_regles->get($id_commission);
    }

    /**
     * Ajout d'une ligne de compétence à la commission
     *
     * @param  int   $id_commission
     */
    public function addCompetence($id_commission)
    {
        // Les modèles
        $model_regles = new Model_DbTable_CommissionRegle;

        // On ajoute une règle
        $row_regle = $model_regles->createRow();
        $row_regle->ID_COMMISSION = $id_commission;
        $row_regle->save();
    }

    /**
     * Suppression d'une ligne de compétence
     *
     * @param  int   $id_commission
     * @param  int   $id_competence
     */
    public function deleteCompetence($id_commission, $id_competence)
    {
        // Les modèles
        $model_regles = new Model_DbTable_CommissionRegle;
        $model_reglesTypes = new Model_DbTable_CommissionRegleType;
        $model_reglesClasses = new Model_DbTable_CommissionRegleClasse;
        $model_reglesCategories = new Model_DbTable_CommissionRegleCategorie;
        $model_reglesEtudeVisite = new Model_DbTable_CommissionRegleEtudeVisite;
        $model_reglesLocalSommeil = new Model_DbTable_CommissionRegleLocalSommeil;

        // On supprime la règle
        $model_reglesTypes->delete("ID_REGLE = " .  $id_competence);
        $model_reglesCategories->delete("ID_REGLE = " .  $id_competence);
        $model_reglesClasses->delete("ID_REGLE = " .  $id_competence);
        $model_reglesLocalSommeil->delete("ID_REGLE = " .  $id_competence);
        $model_reglesEtudeVisite->delete("ID_REGLE = " .  $id_competence);
        $model_regles->delete("ID_REGLE = " .  $id_competence);
    }

    /**
     * Sauvegarde des compétences de la commission
     *
     * @param  int   $id_commission
     * @param  array   $data
     * @return array
     */
    public function saveCompetences($id_commission, $data)
    {
        // Les modèles
        $model_commission = new Model_DbTable_Commission;
        $model_regles = new Model_DbTable_CommissionRegle;
        $model_reglesTypes = new Model_DbTable_CommissionRegleType;
        $model_reglesClasses = new Model_DbTable_CommissionRegleClasse;
        $model_reglesCategories = new Model_DbTable_CommissionRegleCategorie;
        $model_reglesEtudeVisite = new Model_DbTable_CommissionRegleEtudeVisite;
        $model_reglesLocalSommeil = new Model_DbTable_CommissionRegleLocalSommeil;

        // On spécifi l'ID de la règle à null
        $id_regle = null;
        $rowset_regle = null;

        // On regarde dans quelle commission nous sommes
        $row_commission = $model_commission->find($id_commission)->current();

        // On analyse toutes les données envoyé en POST
        foreach ($data["ID_REGLE"] as $id_regle) {

            // Mise à jour de la règle à sauvegarder
            // On récupère la ligne
            $rowset_regle = $model_regles->find($id_regle)->current();

            // On supprime les porteuses de la règle
            $model_reglesTypes->delete("ID_REGLE = $id_regle");
            $model_reglesClasses->delete("ID_REGLE = $id_regle");
            $model_reglesCategories->delete("ID_REGLE = $id_regle");
            $model_reglesLocalSommeil->delete("ID_REGLE = $id_regle");
            $model_reglesEtudeVisite->delete("ID_REGLE = $id_regle");

            // On met à jour la commune et le groupement
            $rowset_regle->NUMINSEE_COMMUNE = ($row_commission->ID_COMMISSIONTYPE == 2 ) ? $data[$id_regle."_NUMINSEE_COMMUNE"] : null;
            $rowset_regle->ID_GROUPEMENT = ($row_commission->ID_COMMISSIONTYPE != 2 ) ? $data[$id_regle."_ID_GROUPEMENT"] : null;

            // On sauvegarde la règle
            $rowset_regle->save();

            // On sauvegarde la catégorie
            foreach ($data[$id_regle."_ID_CATEGORIE"] as $categorie) {
                $model_reglesCategories->insert(array(
                    "ID_REGLE" => $id_regle,
                    "ID_CATEGORIE" => $categorie
                ));
            }

            // On sauvegarde les types d'activités
            foreach ($data[$id_regle."_ID_TYPE"] as $type) {
                $model_reglesTypes->insert(array(
                    "ID_REGLE" => $id_regle,
                    "ID_TYPE" => $type
                ));
            }

            // On sauvegarde les classes IGH
            foreach ($data[$id_regle."_ID_CLASSE"] as $classe) {
                $model_reglesClasses->insert(array(
                    "ID_REGLE" => $id_regle,
                    "ID_CLASSE" => $classe
                ));
            }

            // Local sommeil
            foreach ($data[$id_regle."_LOCALSOMMEIL"] as $localsommeil) {
                $model_reglesLocalSommeil->insert(array(
                    "ID_REGLE" => $id_regle,
                    "LOCALSOMMEIL" => $localsommeil
                ));
            }

            // Etude visite
            foreach ($data[$id_regle."_ETUDEVISITE"] as $etudevisite) {
                $model_reglesEtudeVisite->insert(array(
                    "ID_REGLE" => $id_regle,
                    "ETUDEVISITE" => $etudevisite
                ));
            }
        }
    }

    /**
     * Récupération des membres de la commission
     *
     * @param  int   $id_commission
     * @return array
     */
    public function getMembres($id_commission)
    {
        // Les modèles
        $model_types = new Model_DbTable_Type;
        $model_membres = new Model_DbTable_CommissionMembre;

        // On récupère les règles de la commission
        $array_membres = $model_membres->get($id_commission);

        // On met le libellé du type dans le tableau des activités
        $types = $model_types->fetchAll()->toArray();
        $types_sort = array();

        foreach ($types as $_type) {
            $types_sort[$_type['ID_TYPE']] = $_type;
        }

        foreach ($array_membres as &$membre) {
            $type_sort = array();

            foreach ($membre['types'] as $type) {
                if (!array_key_exists($types_sort[$type["ID_TYPE"]]['LIBELLE_TYPE'], $type_sort)) {
                    $type_sort[$types_sort[$type["ID_TYPE"]]['LIBELLE_TYPE']] = array();
                }

                $type_sort[$types_sort[$type["ID_TYPE"]]['LIBELLE_TYPE']][] = $type;
            }

            $membre['types'] = $type_sort;
        }

        return $array_membres;
    }

    /**
     * Ajout d'un membre à la commission
     *
     * @param  int   $id_commission
     * @return array
     */
    public function addMembre($id_commission)
    {
        // Les modèles
        $model_membres = new Model_DbTable_CommissionMembre;

        // On ajoute une règle
        $row_membre = $model_membres->createRow();
        $row_membre->ID_COMMISSION = $id_commission;
        $row_membre->LIBELLE_COMMISSIONMEMBRE = '';
        $row_membre->PRESENCE_COMMISSIONMEMBRE = 0;
        $row_membre->save();
    }

    /**
     * Suppression d'un membre de la commission
     *
     * @param  int   $id_commission
     * @param  int   $id_membre
     * @return array
     */
    public function deleteMembre($id_commission, $id_membre)
    {
        // Les modèles
        $model_membres = new Model_DbTable_CommissionMembre;
        $model_membresTypes = new Model_DbTable_CommissionMembreTypeActivite;
        $model_membresClasses = new Model_DbTable_CommissionMembreClasse;
        $model_membresCategories = new Model_DbTable_CommissionMembreCategorie;
        $model_membresDossierNatures = new Model_DbTable_CommissionMembreDossierNature;
        $model_membresDossierTypes = new Model_DbTable_CommissionMembreDossierType;

        // On supprime les courriers
        $row_membre = $model_membres->find($id_membre)->current();
        //unlink("./data/uploads/courriers/" . Service_Commission::_request->id_membre . "ODJ" . $row_membre->COURRIER_ODJ);
        //unlink("./data/uploads/courriers/" . Service_Commission::_request->id_membre . "CONVOCATIONVISITE" . $row_membre->COURRIER_CONVOCATIONVISITE);
        //unlink("./data/uploads/courriers/" . Service_Commission::_request->id_membre . "CONVOCATIONSALLE" . $row_membre->COURRIER_CONVOCATIONSALLE);

        // On supprime la règle
        $model_membresTypes->delete("ID_COMMISSIONMEMBRE = " .  $id_membre);
        $model_membresCategories->delete("ID_COMMISSIONMEMBRE = " .  $id_membre);
        $model_membresClasses->delete("ID_COMMISSIONMEMBRE = " .  $id_membre);
        $model_membresDossierNatures->delete("ID_COMMISSIONMEMBRE = " .  $id_membre);
        $model_membresDossierTypes->delete("ID_COMMISSIONMEMBRE = " .  $id_membre);
        $row_membre->delete();
    }

    /**
     * Sauvegarde de l'ensemble des membres de la commission
     *
     * @param  int   $id_commission
     * @param  array   $data
     */
    public function saveMembres($id_commission, $data)
    {
        // Les modèles
        $model_membres = new Model_DbTable_CommissionMembre;
        $model_membresTypes = new Model_DbTable_CommissionMembreTypeActivite;
        $model_membresClasses = new Model_DbTable_CommissionMembreClasse;
        $model_membresCategories = new Model_DbTable_CommissionMembreCategorie;
        $model_membresDossierNatures = new Model_DbTable_CommissionMembreDossierNature;
        $model_membresDossierTypes = new Model_DbTable_CommissionMembreDossierType;
        $model_dossiernature = new Model_DbTable_DossierNatureliste;

        // On spécifie l'ID de la règle à null
        $id_membre = null;
        $rowset_membre = null;

        // On analyse toutes les données envoyé en POST
        foreach ($data["ID_COMMISSIONMEMBRE"] as $id_membre) {

            // Mise à jour de la règle à sauvegarder
            // On récupère la ligne
            $rowset_membre = $model_membres->find($id_membre)->current();

            // On supprime les porteuses de la règle
            $model_membresTypes->delete("ID_COMMISSIONMEMBRE = $id_membre");
            $model_membresClasses->delete("ID_COMMISSIONMEMBRE = $id_membre");
            $model_membresCategories->delete("ID_COMMISSIONMEMBRE = $id_membre");
            $model_membresDossierNatures->delete("ID_COMMISSIONMEMBRE = $id_membre");
            $model_membresDossierTypes->delete("ID_COMMISSIONMEMBRE = $id_membre");

            // On met à jour la commune et le groupement
            $rowset_membre->LIBELLE_COMMISSIONMEMBRE = $data[$id_membre."_LIBELLE_COMMISSIONMEMBRE"];
            $rowset_membre->PRESENCE_COMMISSIONMEMBRE = $data[$id_membre."_PRESENCE_COMMISSIONMEMBRE"];
            $rowset_membre->ID_UTILISATEURINFORMATIONS = null;
            $rowset_membre->ID_GROUPEMENT = null;

            switch ($data[$id_membre."_typemembre"]) {

                case 1:
                    $rowset_membre->ID_GROUPEMENT = $data[$id_membre."_ID_GROUPEMENT"];
                    break;

                case 2:
                    $rowset_membre->ID_UTILISATEURINFORMATIONS = $data[$id_membre."_ID_UTILISATEURINFORMATIONS"];
                    break;
            }

            // On sauvegarde la règle
            $rowset_membre->save();

            // On sauvegarde la catégorie
            foreach ($data[$id_membre."_ID_CATEGORIE"] as $categorie) {
                $model_membresCategories->insert(array(
                    "ID_COMMISSIONMEMBRE" => $id_membre,
                    "ID_CATEGORIE" => $categorie
                ));
            }

            // On sauvegarde les types d'activités
            foreach ($data[$id_membre."_ID_TYPEACTIVITE"] as $type) {
                $model_membresTypes->insert(array(
                    "ID_COMMISSIONMEMBRE" => $id_membre,
                    "ID_TYPEACTIVITE" => $type
                ));
            }

            // On sauvegarde les classes IGH
            foreach ($data[$id_membre."_ID_CLASSE"] as $classe) {
                $model_membresClasses->insert(array(
                    "ID_COMMISSIONMEMBRE" => $id_membre,
                    "ID_CLASSE" => $classe
                ));
            }

            // On sauvegarde les natures du dossier
            if (count($data[$id_membre."_ID_DOSSIERNATURE"]) > 0) {

                foreach ($data[$id_membre."_ID_DOSSIERNATURE"] as $type) {
                    $id_type = array();
                    $model_membresDossierNatures->insert(array("ID_COMMISSIONMEMBRE" => $id_membre,"ID_DOSSIERNATURE" => $type));
                    $nature = $model_dossiernature->find($type)->current();
                    $id_type[] = $nature->ID_DOSSIERTYPE;
                }

                $id_type = array_unique($id_type);

                foreach($id_type as $id) {
                    $model_membresDossierTypes->insert(array("ID_COMMISSIONMEMBRE" => $id_membre,"ID_DOSSIERTYPE" => $id));
                }
            }
        }
    }

    /**
     * Récupération des documents types de la commission
     *
     * @param  int   $id_commission
     * @return array
     */
    public function getDocumentsTypes($id_commission)
    {
        return array(
            'commission' => Service_Commission::get($id_commission),
            'membres' => call_user_func(function() use ($id_commission) {
                foreach(Service_Commission::getMembres($id_commission) as $membre)
                    $courriers = array();
                    if($membre["libelle"] != null)
                        $courriers[$membre["libelle"]] = $membre["courriers"];
                return $courriers;
            })
        );
    }

    /**
     * Sauvegarde des documents types de la commission
     *
     * @param  int   $id_commission
     * @param  array   $documents_commission
     * @param  array   $courriers_membres
     * @return array
     */
    public function saveDocumentsTypes($id_commission, $documents_commission, $courriers_membres)
    {
        Zend_Debug::Dump($documents_commission);
        Zend_Debug::Dump($courriers_membres);
        die();
        //
    }

    /**
     * Récupération des contacts d'une commission
     *
     * @param  int   $id_commission
     * @return array
     */
    public function getAllContacts($id_commission)
    {
        $DB_contact = new Model_DbTable_UtilisateurInformations();

        return $DB_contact->getContact('commission', $id_commission);
    }

    /**
     * Ajout d'un contact à une commission
     *
     * @param int    $id_commission
     * @param string $nom
     * @param string $prenom
     * @param int    $id_fonction
     * @param string $societe
     * @param string $fixe
     * @param string $mobile
     * @param string $fax
     * @param string $email
     * @param string $adresse
     * @param string $web
     */
    public function addContact($id_commission, $nom, $prenom, $id_fonction, $societe, $fixe, $mobile, $fax, $email, $adresse, $web)
    {
        $DB_informations = new Model_DbTable_UtilisateurInformations();

        $id_contact = $DB_informations->createRow(array(
            'NOM_UTILISATEURINFORMATIONS' => (string) $nom,
            'PRENOM_UTILISATEURINFORMATIONS' => (string) $prenom,
            'TELFIXE_UTILISATEURINFORMATIONS' => (string) $fixe,
            'TELPORTABLE_UTILISATEURINFORMATIONS' => (string) $mobile,
            'TELFAX_UTILISATEURINFORMATIONS' => (string) $fax,
            'MAIL_UTILISATEURINFORMATIONS' => (string) $email,
            'SOCIETE_UTILISATEURINFORMATIONS' => (string) $societe,
            'WEB_UTILISATEURINFORMATIONS' => (string) $web,
            'OBS_UTILISATEURINFORMATIONS' => (string) $adresse,
            'ID_FONCTION' => (string) $id_fonction
        ))->save();

        Service_Commission::addContactExistant($id_commission, $id_contact);
    }

    /**
     * Ajout d'un contact existant à une commission
     *
     * @param int $id_commission
     * @param int $id_contact
     */
    public function addContactExistant($id_commission, $id_contact)
    {
        $DB_contact = new Model_DbTable_CommissionContact();

        $DB_contact->createRow(array(
            'ID_COMMISSION' => $id_commission,
            'ID_UTILISATEURINFORMATIONS' => $id_contact
        ))->save();
    }

    /**
     * Suppression d'un contact
     *
     * @param int $id_commission
     * @param int $id_contact
     */
    public function deleteContact($id_commission, $id_contact)
    {
        $DB_current = new Model_DbTable_CommissionContact();
        $DB_informations = new Model_DbTable_UtilisateurInformations();
        $DB_contact = array(
            new Model_DbTable_CommissionContact,
            new Model_DbTable_DossierContact,
            new Model_DbTable_GroupementContact,
            new Model_DbTable_CommissionContact
        );

        // Appartient à d'autre ets ?
        $exist = false;
        foreach ($DB_contact as $key => $model) {
            if (count($model->fetchAll("ID_UTILISATEURINFORMATIONS = " . $id_contact)->toArray()) > (($model == $DB_current) ? 1 : 0) ) {
                $exist = true;
            }
        }

        // Est ce que le contact n'appartient pas à d'autre etablissement ?
        if (!$exist) {
            $DB_current->delete("ID_UTILISATEURINFORMATIONS = " . $id_contact); // Porteuse
            $DB_informations->delete( "ID_UTILISATEURINFORMATIONS = " . $id_contact ); // Contact
        } else {
            $DB_current->delete("ID_UTILISATEURINFORMATIONS = " . $id_contact . " AND ID_COMMISSION = " . $id_commission); // Porteuse
        }
    }

    /**
     * Récupération du calendrier de la commission
     *
     * @param int $id_commission
     * @param int $start optionnel
     * @param int $end optionnel
     * @return array
     */
    public function getCalendrier($id_commission, $start = null, $end = null)
    {
        if($start == null && $end == null) {
            $start = \Datetime::createFromFormat('Y-m-d H:i:s', date('Y') . '-' . date('m') . '-01 ' . '00:00:00')->format('U');
            $end = \Datetime::createFromFormat('Y-m-d H:i:s', date('Y') . '-' . date('m') . '-01 ' . '00:00:00')->add(\DateInterval::createFromDateString('1 month'))->format('U');
        }

        $start = \Datetime::createFromFormat('U', $start)->format('Y-m-d H:i:s');
        $end = \Datetime::createFromFormat('U', $end)->format('Y-m-d H:i:s');

        $dbDateCommission = new Model_DbTable_DateCommission;

        $items = array();
        $requete = "COMMISSION_CONCERNE = '" . $id_commission . "' AND DATE_COMMISSION BETWEEN '" . $start . "'	AND '" . $end . "'";

        foreach ($dbDateCommission->fetchAll($requete)->toArray() as $commissionEvent) {
            $items[] = array(
                "id" => $commissionEvent['ID_DATECOMMISSION'],
                "title" => "   ".$commissionEvent['LIBELLE_DATECOMMISSION'],
                "start" => date($commissionEvent['DATE_COMMISSION']." ".$commissionEvent['HEUREDEB_COMMISSION']),
                "end" => date($commissionEvent['DATE_COMMISSION']." ".$commissionEvent['HEUREFIN_COMMISSION']),
                "url" => "/commission/" . $id_commission . "/date/".$commissionEvent['ID_DATECOMMISSION'],
                "className" => "calendrier-event calendrier-event-".$commissionEvent['ID_COMMISSIONTYPEEVENEMENT'],
                "allDay" => false,
            );
        }

        return $items;
    }
}
