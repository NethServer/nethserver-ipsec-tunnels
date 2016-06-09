<?php
namespace NethServer\Module;

/*
 * Copyright (C) 2013 Nethesis S.r.l.
 *
 * This script is part of NethServer.
 *
 * NethServer is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * NethServer is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with NethServer.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Retrieve IPsec tunnel status
 *
 * @author Giacomo Sanchietti
 */
class IpsecTunnelsStatus extends \Nethgui\Controller\AbstractController
{
 
    private $status;

    protected function initializeAttributes(\Nethgui\Module\ModuleAttributesInterface $base)
    {
        return \Nethgui\Module\SimpleModuleAttributesProvider::extendModuleAttributes($base, 'Status');
    }
    
    private function readStatus()
    {
        return json_decode($this->getPlatform()->exec('sudo /usr/libexec/nethserver/ipsec-status')->getOutput(), true);
    }

    public function prepareView(\Nethgui\View\ViewInterface $view)
    {
        if (!$this->status) {
            $this->status = $this->readStatus();
        }

        $view['status'] = $this->status;
    }
}
