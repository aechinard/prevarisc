<?php

class Service_GroupementCommunes extends Service_Abstract
{
    /**
     * Récupération de tous les groupements
     *
     * @param int numinsee Optionnel
     * @return array
     */
    public function findAll($num_insee = null)
    {
        $model_groupement = new Model_DbTable_Groupement;

        if ($num_insee !== null) {
            return $model_groupement->getGroupementParVille($num_insee);
        }

        return $model_groupement->get()->toArray();
    }

    /**
     * Récupération d'un groupement de commune
     *
     * @param int id Optionnel
     * @return array
     */
    public function get($id)
    {
        $model_groupement = new Model_DbTable_Groupement;
        return $model_groupement->find($id)->current()->toArray();
    }
}
