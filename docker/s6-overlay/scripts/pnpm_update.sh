#!/command/with-contenv bash

# Exit on error
set -e

echo "INSTALL PNPM"
cd /var/www/html/fe_assets 
pnpm install
cd /var/www/html/fe_assets2 
pnpm install

exit 0
