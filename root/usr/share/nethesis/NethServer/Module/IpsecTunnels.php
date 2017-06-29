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

use Nethgui\System\PlatformInterface as Validate;

/**
 * Manage VPN accounts.
 *
 * @author Giacomo Sanchietti <giacomo.sanchietti@nethesis.it>
 * @since 1.0
 */
class IpsecTunnels extends \Nethgui\Controller\TableController
{
    protected function initializeAttributes(\Nethgui\Module\ModuleAttributesInterface $base)
    {   
        return \Nethgui\Module\SimpleModuleAttributesProvider::extendModuleAttributes($base, 'Gateway');
    }

    public function initialize()
    {

        $columns = array(
            'Key',
            'leftsubnets',
            'rightsubnets',
            'state',
            'Actions'
        );

        $this
            ->setTableAdapter($this->getPlatform()->getTableAdapter('vpn','ipsec-tunnel'))
            ->setColumns($columns)
            ->addTableAction(new \NethServer\Module\IpsecTunnels\Modify('create'))
            ->addTableAction(new \Nethgui\Controller\Table\Help('Help'))
            ->addRowAction(new \NethServer\Module\IpsecTunnels\Modify('update'))
            ->addRowAction(new \NethServer\Module\IpsecTunnels\Modify('delete'))
            ->addRowAction(new \NethServer\Module\IpsecTunnels\TunnelCtl('enable'))
            ->addRowAction(new \NethServer\Module\IpsecTunnels\TunnelCtl('disable'))
        ;

        parent::initialize();
    }

    public function prepareViewForColumnActions(\Nethgui\Controller\Table\Read $action, \Nethgui\View\ViewInterface $view, $key, $values, &$rowMetadata)
    {
        $cellView = $action->prepareViewForColumnActions($view, $key, $values, $rowMetadata);

        if (!isset($values['status']) || ($values['status'] == "disabled")) {
            unset($cellView['disable']);
        } else {
            unset($cellView['enable']);
        }

        return $cellView;
    }

    private function readStatus()
    {
        static $status;

        if (!isset($status)) {
            $status = json_decode($this->getPlatform()->exec('sudo /usr/libexec/nethserver/ipsec-status')->getOutput(), true);
        }
        return $status;
    }

    public function prepareViewForColumnState(\Nethgui\Controller\Table\Read $action, \Nethgui\View\ViewInterface $view, $key, $values, &$rowMetadata)
    {
        if (!isset($values['status']) || ($values['status'] == "disabled")) {
            $rowMetadata['rowCssClass'] = trim($rowMetadata['rowCssClass'] . ' user-locked');
        }

        $status = $this->readStatus();
        if (!isset($status[$key])) {
            return '-';
        }

        if (($status[$key]['status'])) {
           return '<i class="fa fa-check-circle" style="color: green; font-size: 150%"></i>';
        } else {
           return '<i class="fa fa-warning" style="color: red; font-size: 150%"></i>';
        }
    }
}
