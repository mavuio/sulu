#!/command/with-contenv bash

# Exit on error
set -e

echo "UPDATING PNPM..."
cd /var/www/html/fe_assets 
pnpm install
cd /var/www/html/fe_assets2 
pnpm install
echo "UPDATING PNPM... done"

exit 0
