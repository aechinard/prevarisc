<?php

class Api_Service_Commission
{
    /**
     * Retourne le calendrier de ou des commissions renseignées
     *
     * @param  array    $ids
     * @param  int      $start
     * @param  int      $end
     * @return string
     */
    public function calendrier($ids, $start = null, $end = null)
    {
        if(!is_array($ids)) {
            $ids = array($ids);
        }

        $service_commission = new Service_Commission;

        $calendriers = array();

        foreach($ids as $id) {
            $calendriers = array_merge($calendriers, $service_commission->getCalendrier($id, $start, $end));
        }

        return $calendriers;
    }
}
