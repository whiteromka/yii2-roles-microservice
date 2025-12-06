<?php

namespace app\models;

use app\models\traits\ModelGetErrorTrait;
use yii\db\ActiveRecord;

abstract class BaseModel extends ActiveRecord
{
    use ModelGetErrorTrait;
}
