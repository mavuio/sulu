#!/command/with-contenv bash

# Exit on error
set -e

echo "UPDATING PNPM..."
cd /var/www/html/fe_assets 
CI=1 pnpm install
cd /var/www/html/fe_assets2 
CI=1 pnpm install
echo "UPDATING PNPM... done"

exit 0
