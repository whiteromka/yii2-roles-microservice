<?php

namespace app\models;

use api\models\traits\ModelGetErrorTrait;
use yii\db\ActiveRecord;

abstract class BaseModel extends ActiveRecord
{
    use ModelGetErrorTrait;
}
