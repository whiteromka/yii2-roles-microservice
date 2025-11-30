<?php

namespace app\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "service".
 *
 * @property int $id
 * @property string $name Название сервиса
 * @property string $host Хост сервиса
 * @property string|null $descr Описание сервиса
 * @property string $created_at
 * @property string|null $updated_at
 *
 * @property User[] $users
 */
class Service extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'service';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['descr', 'updated_at'], 'default', 'value' => null],
            [['name', 'host'], 'required'],
            [['descr'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'host'], 'string', 'max' => 255],
            [['name'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'host' => 'Host',
            'descr' => 'Descr',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::class, ['service_id' => 'id']);
    }
}
