<?php

namespace app\repositories;

use app\models\Service;

class ServiceRepository
{
    public function findByName(string $name): ?Service
    {
        return Service::findOne(['name' => $name]);
    }
}
