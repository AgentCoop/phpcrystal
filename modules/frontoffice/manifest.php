<?php

use App\Services\PackageManager;

$this->set('router.prefix', '/');
$this->set('router.middlewares', ['web']);
$this->set('router.subdomain', null);

//$this->set('security_policy.roles', ['admin', 'manager']);

if ($this->getEnv() == PackageManager::LOCAL_ENV) {

}
