<?php

namespace app\repositories;

use app\models\User;


class UserRepository extends BaseRepository
{
    protected function modelClass(): string
    {
        return User::class;
    }
}
