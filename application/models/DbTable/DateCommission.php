<?php
    class Model_DbTable_DateCommission extends Zend_Db_Table_Abstract
    {
        protected $_name="datecommission"; // Nom de la base
        protected $_primary = "ID_DATECOMMISSION"; // Cl� primaire

        public function addDateComm($date,$heureD,$heureF,$idComm,$type,$libelle)
        {
            $new = $this->createRow();
            $new->DATE_COMMISSION= $date;
            $new->HEUREDEB_COMMISSION= $heureD;
            $new->HEUREFIN_COMMISSION= $heureF;
            $new->COMMISSION_CONCERNE = $idComm;
            $new->ID_COMMISSIONTYPEEVENEMENT = $type;
            $new->LIBELLE_DATECOMMISSION = $libelle;
            $new->save();

            return $new->ID_DATECOMMISSION;
        }

        public function addDateCommLiee($date,$heureD,$heureF,$idCommOrigine,$type,$idComm,$libelle)
        {
            $new = $this->createRow();
            $new->DATE_COMMISSION= $date;
            $new->HEUREDEB_COMMISSION= $heureD;
            $new->HEUREFIN_COMMISSION= $heureF;
            $new->DATECOMMISSION_LIEES = $idCommOrigine;
            $new->ID_COMMISSIONTYPEEVENEMENT = $type;
            $new->COMMISSION_CONCERNE = $idComm;
            $new->LIBELLE_DATECOMMISSION = $libelle;
            $new->save();

            return $new->ID_DATECOMMISSION;
        }

        public function getFirstCommission($idCommission,$debut,$fin)
        {
            $select = "SELECT *
                FROM datecommission
                WHERE COMMISSION_CONCERNE = '".$idCommission."'
                AND DATE_COMMISSION BETWEEN '".$debut."'	AND '".$fin."'
            ";
            //echo $select;
            return $this->getAdapter()->fetchAll($select);
        }
        public function getNextCommission($date, $next_date)
        {
            $select = "SELECT *
                FROM datecommission d
                LEFT JOIN commission c ON d.COMMISSION_CONCERNE = c.ID_COMMISSION
                WHERE DATE_COMMISSION BETWEEN '".date('Y-m-d', $date)."' AND '".date('Y-m-d', $next_date)."'
                ORDER BY DATE_COMMISSION, HEUREDEB_COMMISSION";

            return $this->getAdapter()->fetchAll($select);
        }

        public function getMonthCommission($mois,$annee,$idcom)
        {
            $select = "SELECT *
                FROM datecommission
                WHERE MONTH(DATE_COMMISSION) = '".$mois."'  AND   YEAR(DATE_COMMISSION) = '".$annee."'
                AND COMMISSION_CONCERNE = '".$idcom."'";

            return $this->getAdapter()->fetchAll($select);
        }

        public function getCommissionsLiees($idCommissionOrigine,$debut,$fin)
        {
            $select = "SELECT *
                FROM datecommission
                WHERE DATECOMMISSION_LIEES = '".$idCommissionOrigine."'
                AND DATE_COMMISSION BETWEEN '".$debut."'	AND '".$fin."'
            ";
            //echo $select;
            return $this->getAdapter()->fetchAll($select);
        }

        public function getCommissionsQtypListing($idComm)
        {
            $select = "SELECT *
                FROM datecommission
                WHERE ( ID_DATECOMMISSION = '".$idComm."'
                OR DATECOMMISSION_LIEES = '".$idComm."' )
                ORDER BY DATE_COMMISSION
            ";
            //echo $select;
            return $this->getAdapter()->fetchAll($select);
        }

        public function dateCommUpdateLibelle($idComm, $libelle)
        {
            $select = "UPDATE datecommission
                SET LIBELLE_DATECOMMISSION = '".$libelle."'
                WHERE ( ID_DATECOMMISSION = '".$idComm."' OR DATECOMMISSION_LIEES = '".$idComm."' )
            ";
            //echo $select;
            return $this->getAdapter()->query($select);
        }

        public function dateCommUpdateType($idComm, $idNewType)
        {
            $select = "UPDATE datecommission
                SET ID_COMMISSIONTYPEEVENEMENT = '".$idNewType."'
                WHERE ( ID_DATECOMMISSION = '".$idComm."' OR DATECOMMISSION_LIEES = '".$idComm."' )
            ";
            //echo $select;
            return $this->getAdapter()->query($select);
        }

        public function changeMasterDateComm($oldComm, $newComm)
        {
            $select = "UPDATE datecommission
                SET DATECOMMISSION_LIEES = '".$newComm."'
                WHERE ( ID_DATECOMMISSION = '".$oldComm."' OR DATECOMMISSION_LIEES = '".$oldComm."' )
            ";
            //echo $select;
            return $this->getAdapter()->query($select);
        }

        //pour la gestion des ordres du jour r�cup des date li�es
        public function getCommissionsDateLieesMaster($idComm)
        {
            $select = "SELECT *
                FROM datecommission
                WHERE ( ID_DATECOMMISSION = '".$idComm."'
                OR DATECOMMISSION_LIEES = '".$idComm."' )
                ORDER BY DATE_COMMISSION
            ";
            //echo $select;
            return $this->getAdapter()->fetchAll($select);
        }

        public function informationsDossiers($id_datecommission)
        {
            $select = $this->select()
                ->setIntegrityCheck(false)
                ->from("dossieraffectation", null)
                ->joinInner("dossier", 'dossieraffectation.ID_DOSSIER_AFFECT = dossier.ID_DOSSIER', 'ID_DOSSIER')
                ->joinInner("dossiernature", "dossier.ID_DOSSIER = dossiernature.ID_DOSSIER", "ID_NATURE")
                ->joinleft("etablissementdossier", "dossier.ID_DOSSIER = etablissementdossier.ID_DOSSIER", null)
                ->joinleft("etablissementinformations", "etablissementdossier.ID_ETABLISSEMENT = etablissementinformations.ID_ETABLISSEMENT AND etablissementinformations.DATE_ETABLISSEMENTINFORMATIONS = ( SELECT MAX(etablissementinformations.DATE_ETABLISSEMENTINFORMATIONS) FROM etablissementinformations WHERE etablissementinformations.ID_ETABLISSEMENT = etablissementdossier.ID_ETABLISSEMENT )", array('ID_CATEGORIE', 'ID_CLASSE', 'ID_TYPEACTIVITE', 'ID_GENRE'))
                ->joinLeft("etablissementadresse", "etablissementdossier.ID_ETABLISSEMENT = etablissementadresse.ID_ETABLISSEMENT", "NUMINSEE_COMMUNE AS NUMINSEE_COMMUNE_DEFAULT")
                ->joinLeft(array("etablissementadressesite" => "etablissementadresse"), "etablissementadressesite.ID_ETABLISSEMENT = (SELECT ID_FILS_ETABLISSEMENT FROM etablissementlie WHERE ID_ETABLISSEMENT = etablissementdossier.ID_ETABLISSEMENT LIMIT 1)", "NUMINSEE_COMMUNE AS NUMINSEE_COMMUNE_SITE")
                ->joinLeft(array("etablissementadressecell" => "etablissementadresse"), "etablissementadressecell.ID_ETABLISSEMENT = (SELECT ID_ETABLISSEMENT FROM etablissementlie WHERE ID_FILS_ETABLISSEMENT = etablissementdossier.ID_ETABLISSEMENT LIMIT 1)", "NUMINSEE_COMMUNE AS NUMINSEE_COMMUNE_CELL")
                ->joinleft("groupementcommune", "groupementcommune.NUMINSEE_COMMUNE = etablissementadresse.NUMINSEE_COMMUNE", "groupementcommune.ID_GROUPEMENT AS ID_GROUPEMENT_DEFAULT")
                ->joinleft(array("groupementcommunesite" => "groupementcommune"), "groupementcommunesite.NUMINSEE_COMMUNE = etablissementadressesite.NUMINSEE_COMMUNE", "groupementcommunesite.ID_GROUPEMENT AS ID_GROUPEMENT_SITE")
                ->joinleft(array("groupementcommunecell" => "groupementcommune"), "groupementcommunecell.NUMINSEE_COMMUNE = etablissementadressecell.NUMINSEE_COMMUNE", "groupementcommunecell.ID_GROUPEMENT AS ID_GROUPEMENT_CELL")
                ->where('dossieraffectation.ID_DATECOMMISSION_AFFECT = ?', $id_datecommission);

            return $this->getAdapter()->fetchAll($select);
        }

    }
