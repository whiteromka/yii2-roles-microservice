<?php

namespace app\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $external_id Внешний ID пользователя
 * @property string|null $name
 * @property string|null $last_name
 * @property string|null $email
 * @property int $status
 * @property string $created_at
 * @property string|null $updated_at
 * @property int $service_id
 *
 * @property AuthAssignment[] $authAssignments
 * @property AuthItem[] $itemNames
 * @property Service $service
 */
class User extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'last_name', 'email', 'updated_at'], 'default', 'value' => null],
            [['status'], 'default', 'value' => 1],
            [['external_id', 'service_id'], 'required'],
            [['status', 'service_id'], 'default', 'value' => null],
            [['status', 'service_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['external_id'], 'string', 'max' => 500],
            [['name', 'last_name'], 'string', 'max' => 100],
            [['email'], 'string', 'max' => 255],
            [['external_id'], 'unique'],
            [['service_id'], 'exist', 'skipOnError' => true, 'targetClass' => Service::class, 'targetAttribute' => ['service_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'external_id' => 'External ID',
            'name' => 'Name',
            'last_name' => 'Last Name',
            'email' => 'Email',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'service_id' => 'Service ID',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getAuthAssignments()
    {
        return $this->hasMany(AuthAssignment::class, ['user_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getItemNames()
    {
        return $this->hasMany(AuthItem::class, ['name' => 'item_name'])->viaTable('auth_assignment', ['user_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getService()
    {
        return $this->hasOne(Service::class, ['id' => 'service_id']);
    }
}
