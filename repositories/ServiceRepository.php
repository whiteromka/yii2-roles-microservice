<?php

namespace app\repositories;

use app\models\Service;

class ServiceRepository extends BaseRepository
{
    protected function modelClass(): string
    {
        return Service::class;
    }
}
