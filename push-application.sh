rsync -e ssh -vramlHP --exclude 'var' --numeric-ids --delay-updates --delete-after ./ root@2vnr-bvnh.accessdomain.com:/var/www/application/
