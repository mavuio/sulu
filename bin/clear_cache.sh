#!/bin/bash
sudo runuser -u www-data -- php bin/websiteconsole cache:clear  --env=prod
sudo runuser -u www-data -- php bin/adminconsole cache:clear  --env=prod
sudo runuser -u www-data -- php bin/adminconsole fos:httpcache:clear  --env=prod
