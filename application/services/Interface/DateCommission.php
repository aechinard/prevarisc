<?php

interface Service_Interface_DateCommission extends Service_Interface_PieceJointe
{
    public function get($id_datecommission);
    public function getDatesLiees($id_datecommission);
    public function getODJ($id_datecommission);
    public function getListeMembresConcernes($id_datecommission);
    public function save($label, $type_id, $jour, $heure_deb, $heure_fin, $id_commission, $id_datecommission_liee = null, $id_datecommission = null);
    public function addDossier($id_dossier, $date);
    public function removeDossier($id_dossier);


}
