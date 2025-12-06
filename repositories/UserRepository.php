<?php

namespace app\repositories;

use api\models\forms\UserCreateForm;
use app\models\User;
use Exception;

class UserRepository extends BaseRepository
{
    protected function modelClass(): string
    {
        return User::class;
    }

    public function findByExternalIdAndService(int $id, int $serviceId): ?User
    {
        return User::find()
            ->andWhere(['external_id' => $id])
            ->andWhere(['service_id' => $serviceId])
            ->one();
    }

    /**
     * @throws Exception
     */
    public function create(UserCreateForm $form): ?User
    {
        $user = new User();
        $user->attributes = $form->attributes;
        if ($user->save()) {
            return $user;
        } else {
            throw new Exception('Ошибка: ' . $user->getError());
        }
    }
}
