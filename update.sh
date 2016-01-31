#!/bin/bash
set +x
echo "Starting Update"

ID=`id -u`
function e { 
	echo "$1"
}
function err {
	if [ "$1" -ne "$?" ];then 
		e "ERROR: $2 "
	fi
}



export SD="/media/sf_ppg2"
export DD="/var/www/html"

if [ ! -d $DD ];then
	mkdir $DD
	chown root:root $DD
	chmod 755 $DD
fi
cp $SD/*.html $DD
cp $SD/*.png  $DD        

if [ ! -d $DD/partials ];then
	mkdir $DD/partials
fi
cp $SD/partials/*.html /var/www/html/partials
if [ ! -d $DD/php ];then
	mkdir $DD/php
fi
cp $SD/php/*.php $DD/php
if [ ! -d $DD/sql ];then
	mkdir $DD/sql
fi
cp $SD/sql/*.sql $DD/sql
if [ ! -d $DD/js ];then
	mkdir $DD/js
fi
if [ ! -d $DD/js/libs ];then
	mkdir $DD/js/libs
fi
if [ ! -d $DD/fonts ];then
	mkdir $DD/fonts
fi
if [ ! -d $DD/css ];then
	mkdir $DD/css
fi

cat $SD/libarray.sh |dos2unix >/tmp/liblist
echo "/* one ring to rule them all */" >| $DD/js/main.js
while IFS='' read line || [[ -n $line ]]; do
  echo "/* auto generated start of $line file */" >> $DD/js/main.js
  cat $SD/$line >> $DD/js/main.js
  echo "/* end of auto generate js - $line file*/" >> $DD/js/main.js
done < /tmp/liblist
rm -f /tmp/liblist

cp $SD/js/*.js $DD/js
cp $SD/js/libs/*.js $DD/js/libs
cp $SD/js/libs/*.map $DD/js/libs
cp $SD/*.sh $DD
cp $SD/css/*.css $DD/css
cp $SD/fonts/* $DD/fonts

cp $SD/update.sh $DD
cp $SD/update.sh /root/update.sh
cp $SD/utils/* /bin

chown -R apache $DD/*


echo "Update Completed"
