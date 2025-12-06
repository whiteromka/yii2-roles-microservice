<?php

namespace api\models\traits;

/**
 * Только для моделей: AR и Model
 */
trait ModelGetErrorTrait
{
    public function getError(): string
    {
        if (!method_exists($this, 'getFirstErrors')) {
            return '';
        }

        $errors = $this->getFirstErrors();
        return $errors ? reset($errors) : '';
    }
}