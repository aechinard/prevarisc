<?php

interface Service_Interface_Commission extends Service_Interface_Contact
{
    public function getAll();

    public function get($id_commission);
    public function save($name, $id_type, $actif, $id_commission = null);
    public function delete($id_commission);

    public function getCompetences($id_commission);
    public function addCompetence($id_commission);
    public function deleteCompetence($id_commission, $id_competence);
    public function saveCompetences($id_commission, $data);

    public function getMembres($id_commission);
    public function addMembre($id_commission);
    public function deleteMembre($id_commission, $id_membre);
    public function saveMembres($id_commission, $data);

    public function getDocumentsTypes($id_commission);
    public function saveDocumentsTypes($id_commission, $documents_commission, $courriers_membres);

    public function getCalendrier($id_commission, $start = null, $end = null);
}
