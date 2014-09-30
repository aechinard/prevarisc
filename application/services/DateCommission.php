<?php

class Service_DateCommission extends Service_Abstract implements Service_Interface_DateCommission
{
    /**
     * Récupération d'une date de passage en commission
     *
     * @param  int   $id_datecommission
     * @return array
     */
    public function get($id_datecommission)
    {
        $dbDateComm = new Model_DbTable_DateCommission;
        return $dbDateComm->find($id_datecommission)->current()->toArray();
    }

    /**
     * Récupération des dates de passage en commission liées
     *
     * @param  int   $id_datecommission
     * @return array
     */
    public function getDatesLiees($id_datecommission)
    {
        $dbDateComm = new Model_DbTable_DateCommission;

        $datecommission = Service_DateCommission::get($id_datecommission);

        $id_datecommission = $datecommission['DATECOMMISSION_LIEES'] != null ? $datecommission['DATECOMMISSION_LIEES'] : $id_datecommission;

        return $dbDateComm->getCommissionsDateLieesMaster($id_datecommission);
    }

    /**
     * Récupération de l'ordre du jour de la date de passage en commission
     *
     * @param  int   $id_datecommission
     * @return array
     */
    public function getODJ($id_datecommission)
    {
        $dbDossierAffectation = new Model_DbTable_DossierAffectation;
        return $dbDossierAffectation->getDossiers($id_datecommission);
    }

    /**
     * Récupération des membres concernés par ce passage en commission
     *
     * @param  int   $id_datecommission
     * @return array
     */
    public function getListeMembresConcernes($id_datecommission)
    {
        $dbDateComm = new Model_DbTable_DateCommission;
        $dbUserInfos = new Model_DbTable_UtilisateurInformations;
        $infos_dossiers = $dbDateComm->informationsDossiers($id_datecommission);
        $date_commision = Service_DateCommission::get($id_datecommission);
        $membres_types = Service_Commission::getMembres($date_commision['COMMISSION_CONCERNE']);
        $db_adresse = new Model_DbTable_AdresseCommune;

        $membres = array();

        foreach($membres_types as $membre_type) {

            $membre_type_categories = call_user_func(function() use ($membre_type) {
                $result = array();
                foreach($membre_type['categories'] as $categorie)
                    if($membre_type['id_membre'] == $categorie['ID_COMMISSIONMEMBRE'])
                        $result[] = $categorie['ID_CATEGORIE'];
                return $result;
            });

            $membre_type_classes = call_user_func(function() use ($membre_type) {
                $result = array();
                foreach($membre_type['classes'] as $classe)
                    if($membre_type['id_membre'] == $classe['ID_COMMISSIONMEMBRE'])
                        $result[] = $classe['ID_CLASSE'];
                return $result;
            });

            $membre_type_natures = call_user_func(function() use ($membre_type) {
                $result = array();
                foreach($membre_type['dossiernatures'] as $nature)
                    if($membre_type['id_membre'] == $nature['ID_COMMISSIONMEMBRE'])
                        $result[] = $nature['ID_DOSSIERNATURE'];
                return $result;
            });

            $membre_type_typesactivites = call_user_func(function() use ($membre_type) {
                $result = array();
                foreach($membre_type['types'] as $types)
                    foreach($types as $type)
                        if($membre_type['id_membre'] == $type['ID_COMMISSIONMEMBRE'])
                            $result[] = $type['ID_TYPEACTIVITE'];
                return $result;
            });

            $membre_type_groupement = is_array($membre_type['groupement']) ? $membre_type['groupement']['ID_GROUPEMENT'] : null;

            $membres_potentiels = array();

            foreach($infos_dossiers as $dossier) {

                switch($dossier['ID_GENRE']) {
                    case 1:
                        $commune_id = $dossier['NUMINSEE_COMMUNE_SITE'];
                        $groupement_id = $dossier['ID_GROUPEMENT_SITE'];
                        break;
                    case 3:
                        $commune_id = $dossier['NUMINSEE_COMMUNE_CELL'];
                        $groupement_id = $dossier['ID_GROUPEMENT_CELL'];
                        break;
                    default:
                        $commune_id = $dossier['NUMINSEE_COMMUNE_DEFAULT'];
                        $groupement_id = $dossier['ID_GROUPEMENT_DEFAULT'];
                }

                if(
                    (in_array($dossier['ID_CATEGORIE'], $membre_type_categories) || in_array($dossier['ID_CLASSE'], $membre_type_classes)) &&
                    in_array($dossier['ID_NATURE'], $membre_type_categories) &&
                    in_array($dossier['ID_TYPEACTIVITE'], $membre_type_typesactivites)
                ) {

                    $id_user_infos = $membre_type_groupement == null ? $db_adresse->find($commune_id)->current()->toArray()['ID_UTILISATEURINFORMATIONS'] : Service_GroupementCommunes::get($groupement_id)['ID_UTILISATEURINFORMATIONS'];

                    if($id_user_infos != null) {
                        if(array_key_exists($id_user_infos, $membres_potentiels)) {
                            if(!in_array($dossier['ID_DOSSIER'], $membres_potentiels[$id_user_infos]['dossiers_concernes'])) {
                                $membres_potentiels[$id_user_infos]['dossiers_concernes'][] = $dossier['ID_DOSSIER'];
                            }
                        }
                        else {
                            $fiche_contact = $dbUserInfos->get($id_user_infos);
                            if($fiche_contact != null)
                                $membres_potentiels[$fiche_contact['ID_UTILISATEURINFORMATIONS']] = array_merge($fiche_contact, array('dossiers_concernes' => array($dossier['ID_DOSSIER'])));
                        }
                    }
                }

            }

            if(count($membres_potentiels) > 0) {
                $membres[] = array(
                    'ID_COMMISSIONMEMBRE' => $membre_type['id_membre'],
                    'LIBELLE_COMMISSIONMEMBRE' => $membre_type['libelle'],
                    'presence' => $membre_type['presence'],
                    'membres' => $membres_potentiels
                );
            }
        }

        return $membres;
    }

    /**
     * Sauvegarde du passage en commission
     *
     * @param  array   $data
     * @param  int   $id_datecommission Optionnel
     * @return int
     */
    public function save($label, $type_id, $jour, $heure_deb, $heure_fin, $id_commission, $id_datecommission_liee = null, $id_datecommission = null)
    {
        $dbDateCommission = new Model_DbTable_DateCommission;

        $datecommission = $id_datecommission == null ? $dbDateCommission->createRow() : $dbDateCommission->find($id_datecommission)->current();

        $datecommission->LIBELLE_DATECOMMISSION = $label;
        $datecommission->COMMISSION_CONCERNE = $id_commission;
        $datecommission->ID_COMMISSIONTYPEEVENEMENT = $type_id;

        $datecommission->DATE_COMMISSION = date_create($jour)->format('Y-m-d');
        $datecommission->HEUREDEB_COMMISSION = date_create($heure_deb)->format('H:i:s');
        $datecommission->HEUREFIN_COMMISSION = date_create($heure_fin)->format('H:i:s');

        $datecommission->DATECOMMISSION_LIEES = $id_datecommission_liee;

        //return $datecommission->save();
    }

    public function addDossier($id_dossier, $date)
    {

    }

    public function removeDossier($id_dossier)
    {

    }

    /**
     * Récupération des pièces jointes d'un établissement
     *
     * @param  int   $id_etablissement
     * @return array
     */
    public function getAllPJ($id_datecommission)
    {
        $DBused = new Model_DbTable_PieceJointe;

        return $DBused->affichagePieceJointe("datecommissionpj", "datecommissionpj.ID_DATECOMMISSION", $id_datecommission);
    }

    /**
     * Ajout d'une pièce jointe pour une date de commission
     *
     * @param int    $id_datecommission
     * @param array  $file
     * @param string $name
     * @param string $description
     * @param int    $mise_en_avant    0 = aucune mise en avant, 1 = diaporama, 2 = plans
     */
    public function addPJ($id_datecommission, $file, $name = '', $description = '', $mise_en_avant = 0)
    {
        $path = APPLICATION_PATH . DS . '..' . DS . 'public' . DS . 'data' . DS . 'uploads' . DS . 'pieces-jointes' . DS;
        $extension = strtolower(strrchr($file['name'], "."));

        $DBpieceJointe = new Model_DbTable_PieceJointe;

        $nouvellePJ = $DBpieceJointe->createRow(array(
            'EXTENSION_PIECEJOINTE' => $extension,
            'NOM_PIECEJOINTE' => $name == '' ? $file['name'] : $name,
            'DESCRIPTION_PIECEJOINTE' => $description,
            'DATE_PIECEJOINTE' => date('Y-m-d')
        ))->save();

        if (!move_uploaded_file($file['tmp_name'], $path . $nouvellePJ . $extension)) {
            throw new Exception('Ne peut pas déplacer le fichier ' . $file['tmp_name']);
        } else {
            $DBsave = new Model_DbTable_DateCommissionPj;

            $linkPj = $DBsave->createRow(array(
                'ID_DATECOMMISSION' => $id_datecommission,
                'ID_PIECEJOINTE' => $nouvellePJ,
                'PLACEMENT_ETABLISSEMENTPJ' => null
            ))->save();

            if (in_array($extension, array(".jpg", ".jpeg", ".png", ".gif"))) {
                GD_Resize::run($path . $nouvellePJ . $extension, $path . "miniatures" . DIRECTORY_SEPARATOR . $nouvellePJ . ".jpg", 450);
            }
        }
    }

    /**
     * Suppression d'une pièce jointe d'une date de commission
     *
     * @param int $id_datecommission
     * @param int $id_pj
     */
    public function deletePJ($id_datecommission, $id_pj)
    {
        $DBpieceJointe = new Model_DbTable_PieceJointe;
        $DBitem = new Model_DbTable_DateCommissionPj;

        $path = APPLICATION_PATH . DS . '..' . DS . 'public' . DS . 'data' . DS . 'uploads' . DS . 'pieces-jointes' . DS;

        $pj = $DBpieceJointe->find($id_pj)->current();

        if ($DBitem != null) {
            if( file_exists($path . $pj->ID_PIECEJOINTE . $pj->EXTENSION_PIECEJOINTE) )                         unlink($path . $pj->ID_PIECEJOINTE . $pj->EXTENSION_PIECEJOINTE);
            if( file_exists($path . "miniatures" . DS . $pj->ID_PIECEJOINTE . $pj->EXTENSION_PIECEJOINTE) )     unlink($path . "miniatures" . DS . $pj->ID_PIECEJOINTE . $pj->EXTENSION_PIECEJOINTE);
            $DBitem->delete("ID_PIECEJOINTE = " . (int) $id_pj);
            $pj->delete();
        }
    }
}
