<?php

namespace app\models;

use app\models\validators\ServiceNameValidator;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property int $external_id Внешний ID пользователя
 * @property int $service_id
 * @property string|null $name
 * @property string|null $last_name
 * @property string|null $email
 * @property int $status
 * @property string $created_at
 * @property string|null $updated_at
 *
 * @property AuthAssignment[] $authAssignments
 * @property AuthItem[] $itemNames
 * @property Service $service
 */
class User extends BaseModel
{
    /**
     * @var string|null Виртуальное поле - название сервиса
     */
    public ?string $service_name;

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
            [['service_name'], 'string', 'max' => 255],
            [['service_name'], 'validateServiceName'],
            [['status', 'external_id', 'service_id'], 'integer'],
            [['status'], 'default', 'value' => 1],
            [['external_id', 'service_id'], 'required'],
            [['status', 'service_id', 'external_id'], 'default', 'value' => null],
            [['status', 'service_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'last_name'], 'string', 'max' => 100],
            [['email'], 'string', 'max' => 255],
            [['service_id'], 'exist', 'skipOnError' => true, 'targetClass' => Service::class, 'targetAttribute' => ['service_id' => 'id']],
            [['service_name'], 'string', 'max' => 255],
            [['service_name'], ServiceNameValidator::class],
        ];
    }

    /**
     * Проверяет существование сервиса с указанным названием
     *
     * @param string $attribute
     * @param array $params
     */
    public function validateServiceName($attribute, $params)
    {
        if (!$this->hasErrors() && !empty($this->$attribute)) {
            $service = Service::findOne(['name' => $this->$attribute]);
            if ($service) {
                $this->service_id = $service->id;
            } else {
                $this->addError($attribute, "Сервис с названием '{$this->$attribute}' не существует");
            }
        }
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
