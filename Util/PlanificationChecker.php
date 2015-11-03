<?php

/**
 *
 * @author Pierre Feyssaguet <pfeyssaguet@gmail.com>
 * @since 01/11/15 15:29
 */

namespace DspSofts\CronManagerBundle\Util;

class PlanificationChecker
{
    const CRONTAB_MODE_MINUTE = 0;
    const CRONTAB_MODE_HEURE = 1;
    const CRONTAB_MODE_JOUR = 2;
    const CRONTAB_MODE_MOIS = 3;
    const CRONTAB_MODE_NUMJOUR = 4;

    /**
     * @var \DateTime
     */
    private $timestamp;

    public function __construct(\DateTime $timestamp = null)
    {
        if (!isset($timestamp)) {
            $timestamp = new \DateTime();
        }
        $this->timestamp = $timestamp;
    }

    /**
     * Checks whether execution must be done or not for given planification string.
     *
     * @param string $planification Planification string.
     * @return boolean
     */
    public function isExecutionDue($planification)
    {
        $exec = array(
            self::CRONTAB_MODE_MINUTE => false,
            self::CRONTAB_MODE_HEURE => false,
            self::CRONTAB_MODE_JOUR => false,
            self::CRONTAB_MODE_MOIS => false,
            self::CRONTAB_MODE_NUMJOUR => false,
        );
        $listPlanif = explode(' ', $planification);
        foreach ($listPlanif as $i => $planification) {
            $listPlanifCourante = explode(',', $planification);
            foreach ($listPlanifCourante as $planifCourante) {
                if ($this->checkPlanif($planifCourante, $i)) {
                    $exec[$i] = true;
                    break;
                }
            }
        }

        $result = false;
        if ($exec[self::CRONTAB_MODE_MINUTE]
            && $exec[self::CRONTAB_MODE_HEURE]
            && $exec[self::CRONTAB_MODE_JOUR]
            && $exec[self::CRONTAB_MODE_MOIS]
            && $exec[self::CRONTAB_MODE_NUMJOUR]
        ) {
            $result = true;
        }

        return $result;
    }

    /**
     * Checks a planification entry.
     *
     * @param string $planification Planification entry
     * @param int $mode Planification mode (one of the CRONTAB_MODE_* consts )
     *
     * @return boolean
     */
    private function checkPlanif($planification, $mode)
    {
        $result = false;

        $planifAnalyse = $planification;

        // Si on a juste une étoile, ça veut dire TOUT donc on renvoie directement true
        if ($planifAnalyse == '*') {
            $result = true;
        } else {
            // On regarde si on a une virgule dans le critère
            if (strstr($planifAnalyse, ',')) {
                $listePlanifTemps = explode(',', $planifAnalyse);
            } else {
                $listePlanifTemps[] = $planifAnalyse;
            }

            foreach ($listePlanifTemps as $planifTemps) {
                $planifTempsCourante = $planifTemps;

                // On regarde si on a un / dans le critère
                $moduloTemps = 1;
                if (strstr($planifTempsCourante, '/')) {
                    $aPlanif = explode('/', $planifTempsCourante);
                    $planifTempsCourante = $aPlanif[0];
                    $moduloTemps = $aPlanif[1];
                }

                // On regarde si on a un tiret dans le critère
                $aPlanifTempsCourante = array();
                if (strstr($planifTempsCourante, '-')) {
                    $aPlanif = explode('-', $planifTempsCourante);
                    foreach ($aPlanif as $i => $temps) {
                        if ($i == 0) {
                            $tempsMin = $temps;
                        } elseif ($i == 1) {
                            $tempsMax = $temps;
                        }
                    }
                    for ($i = $tempsMin; $i <= $tempsMax; $i++) {
                        $aPlanifTempsCourante[] = $i;
                    }
                } else {
                    $aPlanifTempsCourante[] = $planifTempsCourante;
                }

                // On récupère l'unité de temps du début
                $tempsDebut = $aPlanifTempsCourante[0];
                if ($tempsDebut == '*') {
                    $tempsDebut = 0;
                }

                // On peut maintenant parcourir les temps planifiés
                foreach ($aPlanifTempsCourante as $temps) {
                    switch ($mode) {
                        case self::CRONTAB_MODE_MINUTE:
                            $tempsCourant = $this->timestamp->format('i');
                            break;
                        case self::CRONTAB_MODE_HEURE:
                            $tempsCourant = $this->timestamp->format('H');
                            break;
                        case self::CRONTAB_MODE_JOUR:
                            $tempsCourant = $this->timestamp->format('d');
                            break;
                        case self::CRONTAB_MODE_MOIS:
                            $tempsCourant = $this->timestamp->format('m');
                            break;
                        case self::CRONTAB_MODE_NUMJOUR:
                            $tempsCourant = $this->timestamp->format('w');
                            break;
                        default:
                            throw new \InvalidARgumentException('Invalid mode ' . $mode);
                    }

                    if ($temps == '*') {
                        $temps = $tempsCourant;
                    }

                    if ($tempsCourant == $temps) {
                        if (($tempsCourant - $tempsDebut) % $moduloTemps == 0) {
                            $result = true;
                            break;
                        }
                    }
                }
            }
        }

        return $result;
    }
}
