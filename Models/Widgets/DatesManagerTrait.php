<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace   Splash\Models\Widgets;

use DateInterval;
use DateTime;
use Exception;

/**
 * Date Management for Splash Widgets.
 *
 * @author      B. Paquier <contact@splashsync.com>
 */
trait DatesManagerTrait
{
    /**
     * @var null|string
     */
    protected ?string $dateStart = null;

    /**
     * @var null|string
     */
    protected ?string $dateEnd = null;

    /**
     * @var DateInterval
     */
    protected DateInterval $dateInterval;

    /**
     * @var string
     */
    protected string $groupBy;

    /**
     * @var string
     */
    protected string $labelFormat;

    //====================================================================//
    //  DATES MANAGEMENT
    //====================================================================//

    /**
     * @param array $params
     *
     * @return void
     */
    protected function importDates(array $params)
    {
        //====================================================================//
        //  Import Dates Parameters
        if (isset($params["DateStart"]) && !empty($params["DateStart"])) {
            $this->dateStart = $params["DateStart"];
        } else {
            $this->dateStart = (new DateTime("first day of this month"))->format(SPL_T_DATECAST);
        }
        if (isset($params["DateEnd"]) && !empty($params["DateEnd"])) {
            $this->dateEnd = $params["DateEnd"];
        } else {
            $this->dateEnd = (new DateTime("last day of this month"))->format(SPL_T_DATECAST);
        }
        if (isset($params["GroupBy"]) && !empty($params["GroupBy"])) {
            $this->groupBy = $params["GroupBy"];
        } else {
            $this->groupBy = "d";
        }

        //====================================================================//
        //  Generate Dates Formater String
        $this->importDatesFormat();
    }

    /**
     * @param array $inputs
     *
     * @throws Exception
     *
     * @return array
     */
    protected function parseDatedData(array $inputs): array
    {
        $outputs = array();
        if (!isset($this->dateStart) || !isset($this->dateEnd)) {
            return $outputs;
        }

        $start = $current = new DateTime($this->dateStart);
        $end = new DateTime($this->dateEnd);

        while ($current < $end) {
            $key = $current->format($this->groupBy);

            $outputs[] = array(
                "label" => $current->format($this->labelFormat),
                "value" => (isset($inputs[$key]) ? $inputs[$key] : 0),
            );

            $current = $start->add($this->dateInterval);
        }

        return $outputs;
    }

    /**
     * @return void
     */
    private function importDatesFormat()
    {
        //====================================================================//
        //  Generate Dates Formater String
        switch ($this->groupBy) {
            case "m":
            default:
                $this->labelFormat = "Y-m";
                $this->dateInterval = new \DateInterval("P1M");

                break;
            case "d":
                $this->labelFormat = "Y-m-d";
                $this->dateInterval = new \DateInterval("P1D");

                break;
            case "h":
                $this->labelFormat = "Y-m-d H:00";
                $this->dateInterval = new \DateInterval("PT1H");

                break;
        }
    }
}
