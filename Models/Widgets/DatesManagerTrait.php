<?php
/*
 * This file is part of SplashSync Project.
 *
 * Copyright (C) Splash Sync <www.splashsync.com>
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace   Splash\Models\Widgets;

/**
 * @abstract    Date Management for Splash Widgets.
 * @author      B. Paquier <contact@splashsync.com>
 */
trait DatesManagerTrait
{
    protected $DateStart;
    protected $DateEnd;
    protected $DateInterval;
    protected $GroupBy;
    protected $LabelFormat;
    
    //====================================================================//
    //  DATES MANAGEMENT
    //====================================================================//

    protected function importDates($params)
    {
        
        //====================================================================//
        //  Import Dates Parameters
        if (isset($params["DateStart"]) && !empty($params["DateStart"])) {
            $this->DateStart    =   $params["DateStart"];
        } else {
            $this->DateStart    =   (new \DateTime("first day of this month"))->format(SPL_T_DATECAST);
        }
        if (isset($params["DateEnd"]) && !empty($params["DateEnd"])) {
            $this->DateEnd      =   $params["DateEnd"];
        } else {
            $this->DateEnd      =   (new \DateTime("last day of this month"))->format(SPL_T_DATECAST);
        }
        if (isset($params["GroupBy"]) && !empty($params["GroupBy"])) {
            $this->GroupBy      =   $params["GroupBy"];
        } else {
            $this->GroupBy      =   "d";
        }

        //====================================================================//
        //  Generate Dates Formater String
        $this->importDatesFormat();
    }
    
    private function importDatesFormat()
    {
        //====================================================================//
        //  Generate Dates Formater String
        switch ($this->GroupBy) {
            case "m":
                $this->LabelFormat  = "Y-m";
                $this->DateInterval = new \DateInterval("P1M");
                break;
            case "d":
                $this->LabelFormat  = "Y-m-d";
                $this->DateInterval = new \DateInterval("P1D");
                break;
            case "h":
                $this->LabelFormat  = "Y-m-d H:00";
                $this->DateInterval = new \DateInterval("PT1H");
                break;
            default:
                $this->LabelFormat  = "Y-m";
                $this->DateInterval = new \DateInterval("P1M");
                break;
        }
    }
    
    protected function parseDatedData($In)
    {
        $Out       = array();
        if (!isset($this->DateStart) || !isset($this->DateEnd)) {
            return $Out;
        }
        
        $Start      = $Current    = new \DateTime($this->DateStart);
        $End        = new \DateTime($this->DateEnd);
        
        while ($Current < $End) {
            $Key = $Current->format($this->GroupBy);

            $Out[] = array(
                "label" =>  $Current->format($this->LabelFormat),
                "value" =>  (isset($In[$Key]) ? $In[$Key] : 0),
            );
                    
            $Current    = $Start->add($this->DateInterval);
        }

        return $Out;
    }
}
