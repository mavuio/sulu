#!/usr/bin/bash
php bin/websiteconsole cache:clear  --env=$APP_ENV
php bin/adminconsole cache:clear  --env=$APP_ENV
php bin/adminconsole fos:httpcache:clear  --env=$APP_ENV
