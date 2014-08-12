<?php

class Service_TypeActivite extends Service_Abstract
{
    /**
     * Récupération de l'ensemble des activités
     *
     * @return array
     */
    public function getAll()
    {
        $DB_activite = new Model_DbTable_TypeActivite;

        return $DB_activite->fetchAll()->toArray();
    }

    /**
     * Récupération de l'ensemble des activités
     *
     * @return array
     */
    public function getAllWithTypes()
    {
        $DB_activite = new Model_DbTable_TypeActivite;

        return $DB_activite->myfetchAll();
    }
}
