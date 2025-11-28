<?php

namespace app\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "auth_item".
 *
 * @property string $name
 * @property int $type
 * @property string|null $description
 * @property string|null $rule_name
 * @property resource|null $data
 * @property int|null $created_at
 * @property int|null $updated_at
 *
 * @property AuthAssignment[] $authAssignments Назначения ролей пользователям
 * @property AuthItemChild[] $parentRelations Связи где этот элемент родитель
 * @property AuthItemChild[] $childRelations Связи где этот элемент потомок
 * @property AuthItem[] $childItems Дочерние элементы (роли/разрешения)
 * @property AuthItem[] $parentItems Родительские элементы (роли/разрешения)
 * @property AuthRule $rule Связанное правило
 * @property User[] $users Пользователи с этой ролью/разрешением
 */
class AuthItem extends ActiveRecord
{
    public const TYPE_ROLE = 1;
    public const TYPE_PERMISSION = 2;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'auth_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'type'], 'required'],
            [['type', 'created_at', 'updated_at'], 'integer'],
            [['description', 'data'], 'string'],
            [['name', 'rule_name'], 'string', 'max' => 64],
            [['name'], 'unique'],
            [['rule_name'], 'exist', 'skipOnError' => true, 'targetClass' => AuthRule::class, 'targetAttribute' => ['rule_name' => 'name']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'name' => 'Название',
            'type' => 'Тип',
            'description' => 'Описание',
            'rule_name' => 'Правило',
            'data' => 'Данные',
            'created_at' => 'Создано',
            'updated_at' => 'Обновлено',
        ];
    }

    /**
     * Назначения этой роли/разрешения пользователям
     *
     * @return ActiveQuery
     */
    public function getAuthAssignments()
    {
        return $this->hasMany(AuthAssignment::class, ['item_name' => 'name']);
    }

    /**
     * Связи где этот элемент является родителем
     *
     * @return ActiveQuery
     */
    public function getParentRelations()
    {
        return $this->hasMany(AuthItemChild::class, ['parent' => 'name']);
    }

    /**
     * Связи где этот элемент является потомком
     *
     * @return ActiveQuery
     */
    public function getChildRelations()
    {
        return $this->hasMany(AuthItemChild::class, ['child' => 'name']);
    }

    /**
     * Дочерние роли и разрешения (через связи)
     *
     * @return ActiveQuery
     */
    public function getChildItems()
    {
        return $this->hasMany(AuthItem::class, ['name' => 'child'])
            ->viaTable('auth_item_child', ['parent' => 'name']);
    }

    /**
     * Родительские роли и разрешения (через связи)
     *
     * @return ActiveQuery
     */
    public function getParentItems()
    {
        return $this->hasMany(AuthItem::class, ['name' => 'parent'])
            ->viaTable('auth_item_child', ['child' => 'name']);
    }

    /**
     * Связанное правило доступа
     *
     * @return ActiveQuery
     */
    public function getRule()
    {
        return $this->hasOne(AuthRule::class, ['name' => 'rule_name']);
    }

    /**
     * Пользователи с этой ролью или разрешением
     *
     * @return ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::class, ['id' => 'user_id'])
            ->viaTable('auth_assignment', ['item_name' => 'name']);
    }
}