#
# Copyright (C) 2015 Nethesis S.r.l.
# http://www.nethesis.it - nethserver@nethesis.it
#
# This script is part of NethServer.
#
# NethServer is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 3 of the License,
# or any later version.
#
# NethServer is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with NethServer.  If not, see COPYING.
#

use strict;

package NethServer::Firewall::IpSec;
use NethServer::Firewall qw(register_callback);
use esmith::DB::db;
use esmith::util;

register_callback(\&ipsec_tunnels);

#
# Search inside IPsec tunnel network
#
sub ipsec_tunnels
{
    my $value = shift;

    my $vpn_db = esmith::DB::db->open_ro('vpn');
    foreach ($vpn_db->get_all_by_prop('type' => 'ipsec-tunnel')) {
        my $subnets = $_->prop('rightsubnets') || 'next';
        foreach my $net (split(/,/, $subnets)) {
            if (Net::IPv4Addr::ipv4_in_network($net, $value)) {
                return 'ivpn';
            }
        }
    }

    return '';
}
