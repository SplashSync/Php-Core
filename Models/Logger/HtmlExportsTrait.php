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

namespace Splash\Models\Logger;

/**
 * Splash Logger - Html Exports Managment
 */
trait HtmlExportsTrait
{
    /**
     * Return All WebServer current Log WebServer in Html format
     *
     * @param bool $clean true if messages needs to be cleaned after reading
     *
     * @return string
     */
    public function getHtmlLog(bool $clean = false): string
    {
        $html = null;
        //====================================================================//
        // Read All Messages as Html
        $html .= $this->getHtml($this->err, 'Errors', '#FF3300');
        $html .= $this->getHtml($this->war, 'Warning', '#FF9933');
        $html .= $this->getHtml($this->msg, 'Messages', '#006600');
        $html .= $this->getHtml($this->deb, 'Debug', '#003399');
        //====================================================================//
        // Clear Log Buffer If Required
        if ($clean) {
            $this->cleanLog();
        }

        return $html;
    }

    /**
     * Return WebServer Log Item in Html Checklist format
     *
     * @param string      $message Log message
     * @param null|string $type    Message Type
     *
     * @return string
     */
    public function getHtmlListItem(string $message, string $type = null): string
    {
        switch ($type) {
            case 'Error':
                $color = '#FF3300';
                $text = '&nbsp;KO&nbsp;';

                break;
            case 'Warning':
                $color = '#FF9933';
                $text = '&nbsp;WAR&nbsp;';

                break;
            default:
                $color = '#006600';
                $text = '&nbsp;OK&nbsp;';

                break;
        }

        return '[<font color="'.$color.'">'.$text.'</font>]&nbsp;&nbsp;&nbsp;'.$message.PHP_EOL.'</br>';
    }

    /**
     * Return All WebServer current Log WebServer in Html Checklist format
     *
     * @param bool $clean true if messages needs to be cleaned after reading
     *
     * @return string
     */
    public function getHtmlLogList(bool $clean = false): string
    {
        $html = null;
        //====================================================================//
        // Read All Messages as Html
        $html .= $this->getHtmlList($this->err, 'Error');
        $html .= $this->getHtmlList($this->war, 'Warning');
        $html .= $this->getHtmlList($this->msg, 'Message');
        $html .= $this->getHtmlList($this->deb, 'Debug');
        //====================================================================//
        // Clear Log Buffer If Required
        if ($clean) {
            $this->cleanLog();
        }

        return $html;
    }

    /**
     * Return All WebServer current Log WebServer in Html format
     *
     * @param null|array $msgArray
     * @param string     $title
     * @param string     $color
     *
     * @return string
     */
    public function getHtml(?array $msgArray, string $title = '', string $color = '#000000'): string
    {
        $html = '<font color="'.$color.'">';

        if (!empty($msgArray)) {
            //====================================================================//
            // Prepare Title as Bold
            if ($title) {
                $html .= '<u><b>'.$title.'</b></u></br> ';
            }
            //====================================================================//
            // Add Messages
            foreach ($msgArray as $txt) {
                $html .= $txt.'</br>';
            }
        }

        return $html.'</font>';
    }

    /**
     * Return All WebServer current Log WebServer in Html Checklist format
     *
     * @param null|array $msgArray
     * @param string     $type
     *
     * @return null|string
     */
    private function getHtmlList(?array $msgArray, string $type): ?string
    {
        $html = null;
        if (!empty($msgArray)) {
            //====================================================================//
            // Add Messages
            foreach ($msgArray as $message) {
                $html .= $this->getHtmlListItem($message, $type);
            }
        }

        return $html;
    }
}
