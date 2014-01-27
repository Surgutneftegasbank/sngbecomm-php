echo "==> copy source into Apache directory"
rsync -rv --exclude=.git ../sngbecomm-php /var/www/ecm-cli/ 
php test/SNGBEcomm.php
