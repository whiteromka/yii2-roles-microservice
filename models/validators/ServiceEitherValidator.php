<?php

namespace app\models\validators;

use yii\validators\Validator;

class ServiceEitherValidator extends Validator
{
    public function validateAttribute($model, $attribute)
    {
        $serviceId = $model->service_id ?? null;
        $serviceName = $model->service_name ?? null;

        if (!$serviceId && !$serviceName) {
            $this->addError($model, $attribute, 'Необходимо передать service_id или service_name');
        }
    }
}
