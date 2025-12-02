<?php

namespace app\repositories;

use app\models\User;


class UserRepository extends BaseRepository
{
    protected function modelClass(): string
    {
        return User::class;
    }

    public function findByExternalId(int $id): ?User
    {
        return User::find()->andWhere(['external_id' => $id])->one();
    }
}
