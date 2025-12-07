<?php

namespace app\models\forms;

use app\models\validators\UserDataValidator;

class UserForm extends BaseForm
{
    public int $user_id; // Вычисляемое свойство, на основе $external_id

    public int $external_id;
    public int $service_id;

    public function rules(): array
    {
        return [
            ['user_id', 'safe'],
            [['external_id', 'service_id'], 'required'],
            [['external_id', 'service_id'], 'integer'],
            [['external_id'], UserDataValidator::class],
        ];
    }
}
