<?php
use yii\widgets\LinkPager;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */

$this->title = 'eStore';
?>
<div class="row" style="margin-top: 80px;">
    <div class="col-sm-12 text-right">
        <?php ActiveForm::begin(['method' => 'GET', 'action' => Url::to(['site/product-order'])]); ?>
            Price: <?= Html::input('number', 'min-price', $minPrice, ['placeholder' => 'min', 'min' => 0, 'style' => 'width: 64px;'])  ?> -
            <?= Html::input('number', 'max-price', $maxPrice, ['placeholder' => 'max', 'min' => 0, 'style' => 'width: 64px;'])  ?>
            <?= Html::dropDownList('sort-order', $sortOrder, [ 'name_asc' => 'Name - Asc', 'name_desc' => 'Name - Desc', 'price_asc' => 'Price - Low To High', 'price_desc' => 'Price - High To Low'], ['style' => 'height: 26px;']) ?>
            <?= Html::submitButton('Filter', ['class' => 'btn btn-primary']) ?>
        <?php ActiveForm::end(); ?>
    </div>
</div>
<div class="row" style="margin-top: 10px;">
    <?php foreach ($products as $product): ?>
        <div class="col-md-3">
            <div class="ibox">
                <div class="ibox-content product-box">
                    <div class="product-imitation">
                        <img class="img-responsive product-image-size" src="https://demo-opensource.toogas.com/media/catalog/product/<?= $product->productDescriptions->image; ?>" alt="<?=$product->name?>" title="<?=$product->name?>">
                    </div>
                    <div class="product-desc">
                    <span class="product-price">
                        <?= $product->price . '€' ?>
                    </span>
                        <a href="#" data-toggle='modal' data-target="#myModal-<?=$product->id?>" class="product-name">
                            <?= ((strlen($product->name)) >= 20 ? mb_substr($product->name, 0, 20) . '...' : $product->name) ?>
                        </a>
                        <small><bold>SKU:</bold> <?= $product->sku ?></small>
                        <div class="small m-t-xs">
                            <?= ((strlen($product->productDescriptions->description)) >= 150 ? mb_substr($product->productDescriptions->description, 0, 150) . '...' : $product->productDescriptions->description) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
            Modal::begin([
                'header' => "<strong>$product->name</strong>",
                'id' => "myModal-{$product->id}",
            ]);?>
            <div class="row">
                <div class="col-md-6 product_img">
                    <img src="https://demo-opensource.toogas.com/media/catalog/product/<?= $product->productDescriptions->image; ?>" class="img-responsive" alt="<?=$product->name?>" title="<?=$product->name?>">
                </div>
                <div class="col-md-6 product_content">
                    <h4>Product Id: <span><?= $product->product_id ?></span></h4>
                    <h6>SKU: <span><bold><?= $product->sku ?></bold></span></h6>
                    <p><?= $product->productDescriptions->description; ?></p>
                    <h3 class="cost">Price: <?= $product->price . '€' ?></h3>
                    <div class="space-ten"></div>
                </div>
            </div>
        <?php Modal::end(); ?>
    <?php endforeach; ?>
</div>
<div class="row">
    <?=
    LinkPager::widget([
        'pagination' => $pages,
        'maxButtonCount' => 4,
        'lastPageLabel' => '»»',
        'firstPageLabel' => '««',
        'options' => ['class' => 'pagination pull-right']
    ])
    ?>
</div>
