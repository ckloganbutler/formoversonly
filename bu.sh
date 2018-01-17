#!/bin/sh

#----------------------------------------------------------
# Simple script to backup our databases from malicious individuals.
#----------------------------------------------------------

# (1) set up all the mysqldump variables
FILE=/home/bu/files/FMOBU2000.sql.`date +"%Y%m%d"`
DBSERVER=127.0.0.1
DATABASE=fmo
USER=root
PASS=il2g$bwlm6522B

# (2) in case you run this more than once a day, remove the previous version of the file
find /bu/files -mtime +7 -exec rm -f {} \;

# (3) do the mysql database backup (dump)

mysqldump --opt --user=${USER} --password=${PASS} ${DATABASE} > ${FILE}

gzip $FILE

echo "${FILE}.gz was created:"
ls -l ${FILE}.gz
