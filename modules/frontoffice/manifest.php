<?php

use App\Services\Package\Manager as PackageManager;

$this->set('router.prefix', '/');
$this->set('router.middlewares', ['web']);
$this->set('router.subdomain', null);

if ($this->getEnv() == PackageManager::LOCAL_ENV) {

}
