<?php

namespace app\models;

use yii\db\ActiveRecord;

class BaseModel extends ActiveRecord
{
    /** Венет первую ошибку в отвалидированной модели  */
    public function getError(): string
    {
        $errors = $this->getErrors();
        if ($errors) {
            $firstKey = array_key_first($errors);
            return $errors[$firstKey][0];
        }
        return '';
    }
}
