<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "product_description".
 *
 * @property int $id
 * @property string|null $description
 * @property string|null $short_description
 * @property string $image
 * @property int $product_id
 *
 * @property Product $product
 */
class ProductDescription extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product_description';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['description', 'short_description'], 'string'],
            //[['image', 'product_id'], 'required'],
            [['product_id'], 'integer'],
            [['image'], 'string', 'max' => 255],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::className(), 'targetAttribute' => ['product_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'description' => 'Description',
            'short_description' => 'Short Description',
            'image' => 'Image',
            'product_id' => 'Product ID',
        ];
    }

    /**
     * Gets query for [[Product]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['id' => 'product_id']);
    }
}
