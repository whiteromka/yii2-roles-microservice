<?php

namespace app\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "auth_item_child".
 *
 * @property string $parent
 * @property string $child
 *
 * @property AuthItem $parentItem Родительская роль/разрешение
 * @property AuthItem $childItem Дочерняя роль/разрешение
 */
class AuthItemChild extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'auth_item_child';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['parent', 'child'], 'required'],
            [['parent', 'child'], 'string', 'max' => 64],
            [['parent', 'child'], 'unique', 'targetAttribute' => ['parent', 'child']],
            [['parent'], 'exist', 'skipOnError' => true, 'targetClass' => AuthItem::class, 'targetAttribute' => ['parent' => 'name']],
            [['child'], 'exist', 'skipOnError' => true, 'targetClass' => AuthItem::class, 'targetAttribute' => ['child' => 'name']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'parent' => 'Родитель',
            'child' => 'Потомок',
        ];
    }

    /**
     * Родительская роль или разрешение
     *
     * @return ActiveQuery
     */
    public function getParentItem()
    {
        return $this->hasOne(AuthItem::class, ['name' => 'parent']);
    }

    /**
     * Дочерняя роль или разрешение
     *
     * @return ActiveQuery
     */
    public function getChildItem()
    {
        return $this->hasOne(AuthItem::class, ['name' => 'child']);
    }
}
