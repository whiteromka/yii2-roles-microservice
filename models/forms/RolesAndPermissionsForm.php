<?php

namespace app\models\forms;

use app\models\validators\UserDataValidator;
use app\models\validators\RolesAndPermissionsValidator;

class RolesAndPermissionsForm extends BaseForm
{
    public ?int $user_id = null; // Вычисляемое свойство, на основе $external_id

    public ?int $external_id = null;
    public ?int $service_id = null;
    public array $roles = [];
    public array $permissions = [];

    public function rules(): array
    {
        return [
            ['user_id', 'safe'],
            [['external_id', 'service_id'], 'required'],
            [['external_id', 'service_id'], 'integer'],
            [['external_id'], UserDataValidator::class],
            [
                ['roles', 'permissions'], RolesAndPermissionsValidator::class
            ],
        ];
    }
}
