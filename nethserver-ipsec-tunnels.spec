Summary: NethServer VPN IPsec tunnels
Name: nethserver-ipsec-tunnels
Version: 1.1.0
Release: 1%{?dist}
License: GPL
URL: %{url_prefix}/%{name} 
Source0: %{name}-%{version}.tar.gz
BuildArch: noarch

Requires: openswan
Requires: nethserver-firewall-base

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
* Thu Jun 01 2017 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.1.0-1
- IPSec tunnel: green network address not updated - Bug NethServer/dev#5291

* Thu Jul 21 2016 Davide Principi <davide.principi@nethesis.it> - 1.0.1-1
 - ipsec tunnel: properly reject unencrypted traffic - Bug NethServer/dev#5048

* Thu Jul 07 2016 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.0.0-1
- First NS7 release

