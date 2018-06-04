<?php
namespace NethServer\Module\IpsecTunnels;

/*
 * Copyright (C) 2012 Nethesis S.r.l.
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
 * Modify IPSEC tunnels
 *
 * @author Giacomo Sanchietti <giacomo.sanchietti@nethesis.it>
 */
class Modify extends \Nethgui\Controller\Table\Modify
{
    private $ciphers = array('3des', 'aes128', 'aes192', 'aes256');
    private $hashes = array('md5', 'sha1', 'sha2_256', 'sha2_384', 'sha2_512');
    private $pfsgroups = array('modp1024','modp1536', 'modp2048', 'modp3072', 'modp4096', 'modp6144', 'modp8192');

    private function getNetworkInterfaces()
    {
        static $interfaces;

        if (isset($interfaces)) {
            return $interfaces;
        }

        $interfaces = array_filter($this->getPlatform()->getDatabase('networks')->getAll(), function ($props) {
            if ( isset($props['role'], $props['type'])
                    && $props['role'] === 'red'
                    && in_array($props['type'], array('ethernet', 'vlan', 'bridge', 'bond', 'xdsl'))
                    ) {
                return TRUE;
            }

            return FALSE;
        });

        return $interfaces;
    }

    public function initialize()
    {
        $yn = $this->createValidator()->memberOf(array('yes', 'no'));
        $ac = $this->createValidator()->memberOf(array('auto', 'custom'));
        
        $i_names = array();
        foreach (array_keys($this->getNetworkInterfaces()) as $key) {
            $i_names[] = "%$key";
        }
        $lc = $this->createValidator()->memberOf($i_names);
        $rv = $this->createValidator()->orValidator($this->createValidator(Validate::HOSTADDRESS), $this->createValidator()->equalTo('%any'));
        $idv = $this->createValidator()->maxLength(63);

        $parameterSchema = array(
            array('name', Validate::USERNAME, \Nethgui\Controller\Table\Modify::KEY),
            array('left', $lc, \Nethgui\Controller\Table\Modify::FIELD),
            array('leftsubnets', Validate::NOTEMPTY, \Nethgui\Controller\Table\Modify::FIELD),
            array('leftid', $idv, \Nethgui\Controller\Table\Modify::FIELD),
            array('right', $rv, \Nethgui\Controller\Table\Modify::FIELD),
            array('rightsubnets', Validate::NOTEMPTY, \Nethgui\Controller\Table\Modify::FIELD),
            array('rightid', $idv, \Nethgui\Controller\Table\Modify::FIELD),
            array('psk', $this->createValidator()->minLength(6), \Nethgui\Controller\Table\Modify::FIELD),
            array('ikelifetime', Validate::POSITIVE_INTEGER, \Nethgui\Controller\Table\Modify::FIELD),
            array('salifetime', Validate::POSITIVE_INTEGER, \Nethgui\Controller\Table\Modify::FIELD),
            array('ike', $ac, \Nethgui\Controller\Table\Modify::FIELD),
            array('ikecipher', $this->createValidator()->memberOf($this->ciphers), \Nethgui\Controller\Table\Modify::FIELD),
            array('ikehash', $this->createValidator()->memberOf($this->hashes), \Nethgui\Controller\Table\Modify::FIELD),
            array('ikepfsgroup', $this->createValidator()->memberOf($this->pfsgroups), \Nethgui\Controller\Table\Modify::FIELD),
            array('esp', $ac, \Nethgui\Controller\Table\Modify::FIELD),
            array('espcipher', $this->createValidator()->memberOf($this->ciphers), \Nethgui\Controller\Table\Modify::FIELD),
            array('esphash', $this->createValidator()->memberOf($this->hashes), \Nethgui\Controller\Table\Modify::FIELD),
            array('esppfsgroup', $this->createValidator()->memberOf($this->pfsgroups), \Nethgui\Controller\Table\Modify::FIELD),
            array('status', Validate::SERVICESTATUS, \Nethgui\Controller\Table\Modify::FIELD),
            array('pfs', $yn, \Nethgui\Controller\Table\Modify::FIELD),
            array('compress', $yn, \Nethgui\Controller\Table\Modify::FIELD),
            array('dpdaction', $this->createValidator()->memberOf(array('restart','hold')), \Nethgui\Controller\Table\Modify::FIELD),
        );
        
        $this->setSchema($parameterSchema);
        $this->setDefaultValue('status', 'enabled');
        $this->setDefaultValue('ike', 'auto');
        $this->setDefaultValue('esp', 'auto');
        $this->setDefaultValue('compress', 'no');
        $this->setDefaultValue('pfs', 'yes');
        $this->setDefaultValue('dpdaction', 'hold');
        $this->setDefaultValue('ikelifetime', '86400');
        $this->setDefaultValue('salifetime', '3600');
        $this->setDefaultValue('leftsubnets', implode(",",$this->readNetworks()));
        $this->setDefaultValue('psk', bin2hex(openssl_random_pseudo_bytes(32)));

        parent::initialize();
    }

    public function bind(\Nethgui\Controller\RequestInterface $request)
    {
        parent::bind($request);
        if($request->isMutation()) {
            if($this->parameters['leftid'] === '') {
                $this->parameters['leftid'] = sprintf('@%s.local', $this->parameters['name']);
            }
            if($this->parameters['rightid'] === '') {
                $this->parameters['rightid'] = sprintf('@%s.remote', $this->parameters['name']);
            }
        }
    }

    public function prepareView(\Nethgui\View\ViewInterface $view)
    {
        parent::prepareView($view);
        $templates = array(
            'create' => 'NethServer\Template\IpsecTunnels\Modify',
            'update' => 'NethServer\Template\IpsecTunnels\Modify',
            'delete' => 'Nethgui\Template\Table\Delete',
        );
        $view->setTemplate($templates[$this->getIdentifier()]);


        $view['ikecipherDatasource'] =  array_map(function($fmt) use ($view) {
            return array($fmt, $view->translate($fmt . '_label'));
        }, $this->ciphers);
        $view['ikehashDatasource'] =  array_map(function($fmt) use ($view) {
            return array($fmt, $view->translate($fmt . '_label'));
        }, $this->hashes);
        $view['ikepfsgroupDatasource'] =  array_map(function($fmt) use ($view) {
            return array($fmt, $view->translate($fmt . '_label'));
        }, $this->pfsgroups);

        $view['espcipherDatasource'] =  array_map(function($fmt) use ($view) {
            return array($fmt, $view->translate($fmt . '_label'));
        }, $this->ciphers);
        $view['esphashDatasource'] =  array_map(function($fmt) use ($view) {
            return array($fmt, $view->translate($fmt . '_label'));
        }, $this->hashes);
        $view['esppfsgroupDatasource'] =  array_map(function($fmt) use ($view) {
            return array($fmt, $view->translate($fmt . '_label'));
        }, $this->pfsgroups);

        $left = array();
        foreach ($this->getNetworkInterfaces() as $key => $props) {
            if (isset($props['ipaddr']) && $props['ipaddr']) {
                $label = "$key - {$props['ipaddr']}";
            } elseif(isset($props['bootproto']) && $props['bootproto'] === 'dhcp') {
                $label = "$key - DHCP";
            } elseif(isset($props['type']) && $props['type'] === 'xdsl') {
                $label = "$key - PPPoE";
            } else {
                continue;
            }
            $left[] = array("%$key", $label);
        }
        $view['leftDatasource'] = $left;
    }

    private function maskToCidr($mask){
        $long = ip2long($mask);
        $base = ip2long('255.255.255.255');
        return 32-log(($long ^ $base)+1,2);
    }

    private function readNetworks()
    {
        $ret = array();
        $interfaces = $this->getPlatform()->getDatabase('networks')->getAll();
        foreach ($interfaces as $interface => $props) {
            if(isset($props['role']) && isset($props['ipaddr']) && $props['role'] == 'green') {
                $net = long2ip(ip2long($props['ipaddr']) & ip2long($props['netmask']));
                $cidr = $this->maskToCidr($props['netmask']); 
                $ret[] = "$net/$cidr";
            }
        }
        return $ret;
    }

    public function validate(\Nethgui\Controller\ValidationReportInterface $report)
    {
        if ( ! $this->getRequest()->isMutation()) {
            return;
        }
        elseif  ($this->parameters['left'] === NULL) {
            $report->addValidationErrorMessage($this, 'left', 'ExpectAtLeastOneRedNic');
        }
        parent::validate($report);
    }

    protected function onParametersSaved($changedParameters)
    {
        $this->getPlatform()->signalEvent('nethserver-ipsec-tunnels-save');
    }

}
