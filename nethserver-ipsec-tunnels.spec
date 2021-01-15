Summary: NethServer VPN IPsec tunnels
Name: nethserver-ipsec-tunnels
Version: 1.2.4
Release: 1%{?dist}
License: GPL
URL: %{url_prefix}/%{name} 
Source0: %{name}-%{version}.tar.gz
BuildArch: noarch

Requires: openswan
Requires: nethserver-firewall-base
Requires: nethserver-vpn-ui

BuildRequires: nethserver-devtools 

%description
Configures VPN tunnels based on IPsec protocol

%prep
%setup

%build
%{makedocs}
perl createlinks
mkdir -p root%{perl_vendorlib}
mv -v lib/perl/NethServer root%{perl_vendorlib}

%install
rm -rf %{buildroot}
(cd root; find . -depth -print | cpio -dump %{buildroot})
%{genfilelist} %{buildroot} | sed '
\|^%{_sysconfdir}/sudoers.d/20_nethserver_ipsec$| d
' > %{name}-%{version}-filelist

%files -f %{name}-%{version}-filelist
%defattr(-,root,root)
%doc COPYING
%dir %{_nseventsdir}/%{name}-update
%config %attr (0440,root,root) %{_sysconfdir}/sudoers.d/20_nethserver_ipsec
%config %ghost %attr (0644,root,root) %{_sysconfdir}/ipsec.d/tunnels.conf
%config %ghost %attr (0600,root,root) %{_sysconfdir}/ipsec.d/tunnels.secrets


%changelog
* Fri Jan 15 2021 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.2.4-1
- Delete conntrack connection with ipsec tunnel - NethServer/dev#6393

* Tue Jan 12 2021 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.2.3-1
- VPN: IPSEC tunnels are not deleted with cockpit - Bug NethServer/dev#6389

* Tue Apr 07 2020 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.2.2-1
- Restore configuration without network override - NethServer/dev#6099

* Mon Mar 30 2020 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.2.1-1
- IPsec and WAN in DHCP - Bug NethServer/dev#6096

* Wed Jun 19 2019 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.2.0-1
- VPN Cockpit UI - NethServer/dev#5760
- save event: avoid double firewall restart
- firewall library: do not break on empty value

* Mon Jan 21 2019 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.1.6-1
- Bad label on IPsec Tunnel configuration (DH-24 instead of DH-14) - Bug NethServer/dev#5699

* Thu Jan 17 2019 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.1.5-1
- ipsec tunnels stop to work if invalid PFS policy is applied - Bug NethServer/dev#5696

* Fri Jun 08 2018 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.1.4-1
- Ipsec: sort the cipher order from weak to strong - NethServer/dev#5507

* Mon Jun 04 2018 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.1.3-1
- Ipsec: harden the PSK key - NethServer/dev#5504

* Mon May 21 2018 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.1.2-1
- IPSec tunnel: no connection after update to CentOS 7.5 - Bug NethServer/dev#5501
- Blank dropdown 'local IP' in IPsec setting page - Bug NethServer/dev#5502

* Thu Jul 06 2017 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.1.1-1
- IPSec tunnels: UI tweaks - NethServer/dev#5323

* Thu Jun 01 2017 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.1.0-1
- IPSec tunnel: green network address not updated - Bug NethServer/dev#5291

* Thu Jul 21 2016 Davide Principi <davide.principi@nethesis.it> - 1.0.1-1
 - ipsec tunnel: properly reject unencrypted traffic - Bug NethServer/dev#5048

* Thu Jul 07 2016 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.0.0-1
- First NS7 release

