<?php

use App\Services\PackageManager;

$this->set('router.prefix', '/');
$this->set('router.middlewares', ['web']);
$this->set('router.subdomain', null);


$this->section('security_policy');
    $this->set('roles', null);
    $this->set('permissions', null);
    $this->set('not_authenticated_page', null);
    $this->set('not_authorized_page', null);
$this->close();

if ($this->getEnv() == PackageManager::LOCAL_ENV) {

}
