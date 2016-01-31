#!/bin/bash

export ID=`id -u`

function y {
	yum install -y $1
}
function ck {
	ST="on"
	
	if [ "$2" != "" ];then
		ST="$2"
	fi
	chkconfig $1 $ST
}


if [ "$ID" -eq "0" ]; then
	
	echo "Installing Dev Packages"
	y kernel-devel
	y kernel-headers
	y gcc
	
	echo "Installing server packages"
	echo "RHEL 6 packages"
	y mysql-server	
	y php-mysqli
	
	echo "RHEL 7 Packages"
	y php
	y mariadb-server
	y php-mysql
	
	echo "Generic release Packages"
	y dos2unix 
	y expect
	y memcached
	y php-pecl-memcache
	y httpd 
	
	
	echo "Configuring dev system"
	ck iptables off
	systemctl disable firewalld
	rm -f /etc/selinux/config.old
	cp /etc/selinux/config /etc/selinux/config.old
	cat /etc/selinux/config.old |sed 's/^SELINUX=enforcing/SELINUX=disabled/' >/etc/selinux/config
	service mysqld start
	service mariadb start
	/usr/bin/mysqladmin -u root password 'steve554'
	#/usr/bin/mysqladmin -u root -h ppg-dev1 password 'steve554'
	cat sql/schema.sql |mysql -p'steve554'
	
	echo "Configuring server" 
	ck httpd
	ck memcached 
	ck mysqld
	ck mariadb 
	echo "Installing utils"
	cp utils/* /bin
	
	init 6
fi