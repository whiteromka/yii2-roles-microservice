<?php

namespace app\models\validators;

use yii\validators\Validator;

class RolesAndPermissionsValidator extends Validator
{
    public function validateAttribute($model, $attribute): void
    {
        $roles = $model->roles ?? [];
        $permissions = $model->permissions ?? [];

        if (!$roles && !$permissions) {
            $this->addError($model, $attribute, 'Необходимо передать roles или permissions');
        }
    }
}
