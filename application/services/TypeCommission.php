<?php

class Service_TypeCommission extends Service_Abstract
{
    /**
     * Récupération des types de commissions
     *
     * @return array
     */
    public function getAll()
    {
        $model_typesDesCommissions = new Model_DbTable_CommissionType;
        return $model_typesDesCommissions->fetchAll()->toArray();
    }
}
