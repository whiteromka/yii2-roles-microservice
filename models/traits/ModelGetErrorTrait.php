<?php

namespace app\models\traits;

/**
 * Только для моделей: AR и Model
 */
trait ModelGetErrorTrait
{
    /**
     * Получить первую ошибку строкой
     */
    public function getError(): string
    {
        if (!method_exists($this, 'getFirstErrors')) {
            return '';
        }

        $errors = $this->getFirstErrors();
        return $errors ? reset($errors) : '';
    }

    /**
     * Получить все ошибку в виде строки
     */
    public function getErrorsAsString(): string
    {
        $message = '';
        $errors = $this->getErrors();
        foreach ($errors as $field => $errorsData) {
            // $message .= "Поле $field : "; ToDo нужно ли?
            foreach ($errorsData as $error) {
                $message .= $error . '; ';
            }
        }
        return $message;
    }
}