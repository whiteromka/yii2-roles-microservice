<?php

namespace app\models\validators;

use yii\validators\Validator;
use app\repositories\ServiceRepository;
use Yii;

/**
 * Валидатор названия сервиса.
 */
class ServiceNameValidator extends Validator
{
    private ServiceRepository $serviceRepository;

    public function __construct(ServiceRepository $serviceRepository, $config = [])
    {
        $this->serviceRepository = $serviceRepository;
        parent::__construct($config);
    }

    /**
     * Проверяет наличие сервиса по имени сервиса
     */
    public function validateAttribute($model, $attribute)
    {
        $value = $model->$attribute;

        if (empty($value)) {
            return;
        }

        $service = $this->serviceRepository->findByName($value);

        if (!$service) {
            $this->addError($model, $attribute, "Сервис '{$value}' не найден");
            return;
        }

        $model->service_id = $service->id;
    }
}
