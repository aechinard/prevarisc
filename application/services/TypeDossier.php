<?php

class Service_TypeDossier extends Service_Abstract
{
    /**
     * Récupération de l'ensemble des types de dossier
     *
     * @return array
     */
    public function getAll()
    {
        $DB_type = new Model_DbTable_DossierType();
        return $DB_type->fetchAll()->toArray();
    }
}
