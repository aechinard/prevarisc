<?php

class Service_Avis extends Service_Abstract
{
    /**
     * Récupération de l'ensemble des avis
     *
     * @return array
     */
    public function getAll()
    {
        $DB_avis = new Model_DbTable_Avis;

        return $DB_avis->fetchAll()->toArray();
    }
}
