<?php

namespace app\models\forms;

use app\models\validators\ServiceEitherValidator;

class UserCreateForm extends BaseForm
{
    public string $name;
    public string $last_name;
    public ?int $external_id = null;
    public ?int $service_id = null;
    public ?string $service_name = null;
    public ?int $status = null;
    public ?string $email = null;

    public function rules(): array
    {
        return [
            [['name', 'last_name'], 'required'],
            [['external_id', 'service_id', 'status'], 'integer'],
            [['name', 'last_name', 'service_name', 'email'], 'string'],
            ['email', 'email'],
            [
                ['service_id', 'service_name'], ServiceEitherValidator::class
            ],
        ];
    }
}
