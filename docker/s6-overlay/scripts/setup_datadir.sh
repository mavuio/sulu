#!/command/with-contenv bash

# Exit on error
set -e

echo "SETUP DATA-DIR"

cd /var/www/html
mkdir -p data/var
mkdir -p data/public/uploads
if [ ! -L public/uploads ]; then
    ln -s ../data/public/uploads public/uploads
fi
mkdir -p data/public/fe_assets2
if [ ! -L public/fe_assets2 ]; then
    ln -s ../data/public/fe_assets2 public/fe_assets2
fi

chown -R webuser:webgroup data
exit 0
