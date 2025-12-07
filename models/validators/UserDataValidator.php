<?php

namespace app\models\validators;

use app\models\forms\RolesAndPermissionsForm;
use app\repositories\UserRepository;
use yii\validators\Validator;

class UserDataValidator extends Validator
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository, $config = [])
    {
        $this->userRepository = $userRepository;
        parent::__construct($config);
    }

    /**
     * Проверяет наличие пользователя в БД и записывает его настоящий ID в user_id
     *
     * @param RolesAndPermissionsForm $model
     * @param string $attribute
     */
    public function validateAttribute($model, $attribute): void
    {
        $externalId = $model->external_id;
        $serviceId = $model->service_id;

        $user = $this->userRepository->findByExternalIdAndServiceId($externalId, $serviceId);
        if (!$user) {
            $this->addError($model, $attribute, "Не найден пользователь с ID $externalId и serviceID $serviceId");
        }
        $model->user_id = $user->id;
    }
}
