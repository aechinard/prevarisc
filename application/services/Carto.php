<?php

class Service_Carto extends Service_Abstract
{
    /**
     * Récupération de toutes les couches cartographiques
     *
     * @return array
     */
    public function getAll()
    {
        $cache = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('cache');
        $repository = $model_couchecarto = new Model_DbTable_CoucheCarto;

        if (($couches_carto = unserialize($cache->load('couches_cartographiques'))) === false) {
            // On récupère l'ensemble des couches
            $couches_carto = $repository->fetchAll()->toArray();

            // On stocke en cache
            $cache->save(serialize($couches_carto));
        }

        return $couches_carto;
    }

    /**
     * Récupération d'une couche cartographique
     *
     * @param  int   $id_couche_cartographique
     * @return array
     */
    public function findById($id_couche_cartographique)
    {
        $cache = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('cache');
        $repository = $model_couchecarto = new Model_DbTable_CoucheCarto;

        return $repository->find($id_couche_cartographique)->current()->toArray();
    }

    /**
     * Édition d'une couche cartographique
     *
     * @param  array $data
     * @param  int   $id_couche_cartographique Optionnel
     * @return array
     */
    public function save($data, $id_couche_cartographique = null)
    {
        $cache = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('cache');
        $repository = $model_couchecarto = new Model_DbTable_CoucheCarto;

        $couche_cartographique = $id_couche_cartographique == null ? $repository->createRow() : $repository->find($id_couche_cartographique)->current();
        $couche_cartographique->setFromArray(array_intersect_key($data, $repository->info('metadata')))->save();
        $cache->remove('couches_cartographiques');
    }

    /**
     * Suppression d'une couche cartographique
     *
     * @param  int   $id_couche_cartographique
     * @return array
     */
    public function delete($id_couche_cartographique)
    {
        $cache = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('cache');
        $repository = $model_couchecarto = new Model_DbTable_CoucheCarto;

        $repository->delete("ID_COUCHECARTO = " . $id_couche_cartographique);
        $cache->remove('couches_cartographiques');
    }
}
