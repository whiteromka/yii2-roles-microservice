<?php

namespace api\models\forms;

use app\models\traits\ModelGetErrorTrait;
use yii\base\Model;

abstract class BaseForm  extends Model
{
    use ModelGetErrorTrait;
}