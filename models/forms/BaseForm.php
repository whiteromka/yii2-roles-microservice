<?php

namespace api\models\forms;

use api\models\traits\ModelGetErrorTrait;
use yii\base\Model;

abstract class BaseForm  extends Model
{
    use ModelGetErrorTrait;
}