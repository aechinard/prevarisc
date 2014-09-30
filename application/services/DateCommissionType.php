<?php

class Service_DateCommissionType extends Service_Abstract
{
    /**
     * Récupération de l'ensemble des types d'evenements
     *
     * @return array
     */
    public function getAll()
    {
        $db = new Model_DbTable_CommissionTypeEvenement;

        return $db->fetchAll()->toArray();
    }
}
