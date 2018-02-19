<?php

use PhpCrystal\Core\Services\Package\Manager as PackageManager;

$this->set('router.prefix', null);
$this->set('router.middlewares', ['admin']);

if ($this->getEnv() == PackageManager::LOCAL_ENV) {
    $this->set('router.subdomain', 'admin.localhost');
} else {
    $this->set('router.subdomain', 'your.production.server');
}


