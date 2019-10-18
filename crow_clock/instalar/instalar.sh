
#if [ -d /usr/sbin/crow/ ];
#then 
 #rm /usr/sbin/crow/ -R  
#fi

 #mkdir /usr/sbin/crow/ 

cp ../crow_clock /usr/sbin/crow/crow_clock


cp crowclock.service /lib/systemd/system/
