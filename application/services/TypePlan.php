<?php

class Service_TypePlan extends Service_Abstract
{
    /**
     * Récupération de l'ensemble des types de plan
     *
     * @return array
     */
    public function getAll()
    {
        $DB_typesplan = new Model_DbTable_TypePlan;

        return $DB_typesplan->fetchAllPK();
    }
}
