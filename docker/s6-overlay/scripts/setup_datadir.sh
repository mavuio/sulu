#!/command/with-contenv bash

# Exit on error
set -e

echo "SETUP DATA-DIR"

cd /var/www/html
mkdir -p data/var
mkdir -p data/public/uploads
mkdir -p data/public/fe_assets2
chown -R webuser:webgroup data
exit 0
