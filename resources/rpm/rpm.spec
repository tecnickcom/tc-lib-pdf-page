Name:      %{_package}
Version:   %{_version}
Release:   %{_release}%{?dist}
Summary:   Provides tc-lib-pdf-page: PHP library containing PDF page formats and definitions

Group:     Development/Libraries/PHP
License:   GNU-LGPL v3
URL:       https://github.com/tecnickcom/tc-lib-pdf-page

BuildRoot: %{_tmppath}/%{name}-%{version}-%{release}-%(%{__id_u} -n)
BuildArch: noarch

Requires:  php >= 5.3.3

%description
Provides tc-lib-pdf-page: PHP library containing PDF page formats and definitions

%build
(cd %{_current_directory} && make build)

%install
rm -rf $RPM_BUILD_ROOT
(cd %{_current_directory} && make install DESTDIR=$RPM_BUILD_ROOT)

%clean
rm -rf $RPM_BUILD_ROOT
(cd %{_current_directory} && make clean)

%files
%attr(-,root,root) %{_libpath}
%attr(-,root,root) %{_docpath}
%docdir %{_docpath}

%changelog

* Thu May 14 2015 Nicola Asuni <info@tecnick.com> 1.0.0-1
- Initial Commit
