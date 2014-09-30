<?php

    class Model_DbTable_CommissionMembre extends Zend_Db_Table_Abstract
    {
        protected $_name="commissionmembre"; // Nom de la base
        protected $_primary = array("ID_COMMISSIONMEMBRE"); // Cl� primaire

        private function fullJoinRegle($first_table, $second_table, $key, $id_membre)
        {
            // On fait une union entre ce qu'il y a dans la base et les crit�res enregistr�
            $return = $this->fetchAll($this->select()->union(array(
                $this->select()->setIntegrityCheck(false)->from($first_table)->joinLeft($second_table, "$first_table.$key = $second_table.$key AND ID_COMMISSIONMEMBRE = $id_membre"),
                $this->select()->setIntegrityCheck(false)->from($first_table)->joinRight($second_table, "$first_table.$key = $second_table.$key AND ID_COMMISSIONMEMBRE = $id_membre")
            )))->toArray();

            // Requete sur la table finale
            $primary = $this->fetchAll($this->select()->setIntegrityCheck(false)->from($first_table))->toArray();

            // On limite les resultats
            $return = array_slice($return, 0, count($primary));

            // On rajoute les valeurs de toutes les cl� primaires
            foreach($return as $pos => $item) : $return[$pos][$key] = $primary[$pos][$key]; endforeach;

            // Clé primaire en clé du tableau associatif
            $return = $this->mapResult($return, $key);

            // On envoi le tout
            return $return;
        }

        // Formaliser les resultats envoy�s
        private function mapResult($array, $key)
        {
            $result = array();

            // On parcours le tableau
            foreach ($array as $value) {
                $result[$value[$key]] = $value;
            }

            return $result;
        }

        public function get($id_commission)
        {
            // Mod�le de la commission
            $model_commission = new Model_DbTable_Commission;
            $model_commune = new Model_DbTable_AdresseCommune;
            $model_utilisateurInformations = new Model_DbTable_UtilisateurInformations;
            $model_groupement = new Model_DbTable_Groupement;

            // On r�cup�re les membres de la commission
            $rowset_membresDeLaCommission = $this->fetchAll("ID_COMMISSION = " . $id_commission)->toArray();

            // On initialise le tableau qui contiendra l'ensemble des crit�res
            $array_membres = array();

            // Pour chaques r�gles, on va chercher les crit�res
            foreach ($rowset_membresDeLaCommission as $row_membreDeLaCommission) {

                $array_membres[] = array(
                    "id_membre" => $row_membreDeLaCommission["ID_COMMISSIONMEMBRE"],
                    "presence" => $row_membreDeLaCommission["PRESENCE_COMMISSIONMEMBRE"],
                    "groupement" => $row_membreDeLaCommission["ID_GROUPEMENT"] == null ? null : $model_groupement->get($row_membreDeLaCommission["ID_GROUPEMENT"])->toArray(),
                    "contact" => null,
                    "contacts" => $model_utilisateurInformations->getContact("commission", $id_commission),
                    "libelle" => $row_membreDeLaCommission["LIBELLE_COMMISSIONMEMBRE"],
                    "categories" => $this->fullJoinRegle("categorie", "commissionmembrecategorie", "ID_CATEGORIE", $row_membreDeLaCommission["ID_COMMISSIONMEMBRE"]),
                    "classes" => $this->fullJoinRegle("classe", "commissionmembreclasse", "ID_CLASSE", $row_membreDeLaCommission["ID_COMMISSIONMEMBRE"]),
                    "types" => $this->fullJoinRegle("typeactivite", "commissionmembretypeactivite", "ID_TYPEACTIVITE", $row_membreDeLaCommission["ID_COMMISSIONMEMBRE"]),
                    "dossiertypes" => $this->fullJoinRegle("dossiertype", "commissionmembredossiertype", "ID_DOSSIERTYPE", $row_membreDeLaCommission["ID_COMMISSIONMEMBRE"]),
                    "dossiernatures" => $this->fullJoinRegle("dossiernatureliste", "commissionmembredossiernature", "ID_DOSSIERNATURE", $row_membreDeLaCommission["ID_COMMISSIONMEMBRE"]),
                    "courriers" => array(
                        "id_membre" => $row_membreDeLaCommission["ID_COMMISSIONMEMBRE"],
                        "COURRIER_CONVOCATIONVISITE" => $row_membreDeLaCommission["COURRIER_CONVOCATIONVISITE"],
                        "COURRIER_CONVOCATIONSALLE" => $row_membreDeLaCommission["COURRIER_CONVOCATIONSALLE"],
                        "COURRIER_ODJ" => $row_membreDeLaCommission["COURRIER_ODJ"],
                        "COURRIER_PV" => $row_membreDeLaCommission["COURRIER_PV"]
                    )
                );
            }

            return $array_membres;
        }

    }
