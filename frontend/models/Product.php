<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "product".
 *
 * @property int $id
 * @property int $product_id
 * @property string $sku
 * @property string $name
 * @property float $price
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 *
 * @property ProductDescription[] $productDescriptions
 */
class Product extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            //[['product_id', 'sku', 'name', 'price', 'created_at', 'updated_at'], 'required'],
            [['product_id', 'status'], 'integer'],
            [['price'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['sku'], 'string', 'max' => 45],
            [['name'], 'string', 'max' => 100],
            [['sku'], 'unique'],
            [['product_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'product_id' => 'Product ID',
            'sku' => 'Sku',
            'name' => 'Name',
            'price' => 'Price',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[ProductDescriptions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProductDescriptions()
    {
        return $this->hasOne(ProductDescription::className(), ['product_id' => 'id']);
    }
}
