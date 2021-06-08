<?php
namespace frontend\controllers;

use app\models\Product;
use app\models\ProductDescription;
use frontend\models\ResendVerificationEmailForm;
use frontend\models\VerifyEmailForm;
use Yii;
use yii\base\BaseObject;
use yii\base\InvalidArgumentException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use GuzzleHttp\Client;
use yii\data\Pagination;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $products = Product::find();

        $pages = new Pagination([
            'defaultPageSize' => 8,
            'totalCount' => $products->count()
        ]);

        $products = $products
            ->offset($pages->offset)
            ->limit($pages->limit)
            ->all();

        return $this->render('index', ['products' => $products, 'pages' => $pages, 'maxPrice' => "", 'minPrice' => "", 'sortOrder' => ""]);
    }

    /**
     * Displays products ordered.
     *
     * @return mixed
     */
    public function actionProductOrder()
    {
        $request = Yii::$app->request;
        $products = Product::find();

        $maxPrice = $request->get('max-price');
        $minPrice = $request->get('min-price');
        $sortOrder = $request->get('sort-order');

        $field = explode('_', $sortOrder);
        $fields_allowed = array('name', 'price');
        $orders_allowed = array('asc', 'desc');

        if (isset($field[0], $field[1])){
            if (in_array($field[0], $fields_allowed, true) && in_array($field[1], $orders_allowed, true)) {
                $order_by = $field[0] . ' ' . $field[1];
            }
        } else {
            $order_by = 'id';
        }

        if (empty($maxPrice) && empty($minPrice)) {
            $minPrice = "";
            $maxPrice = "";
        }

        $countProducts = $products
            ->filterwhere(['between', 'price', $minPrice, $maxPrice ])
            ->count();

        $pages = new Pagination([
            'defaultPageSize' => 8,
            'totalCount' => $countProducts
        ]);

        $products = $products
            ->offset($pages->offset)
            ->limit($pages->limit)
            ->orderBy($order_by)
            ->all();

        return $this->render('index', ['products' => $products, 'pages' => $pages, 'maxPrice' => $maxPrice, 'minPrice' => $minPrice, 'sortOrder' => $sortOrder]);
    }

    public function actionSaveProducts() {
        $url = "https://demo-opensource.toogas.com/media/catalog/sampledata.json";

        $myClient = new Client([
            'headers' => ['Content-Type' => 'application/json; charset=utf-8'],
        ]);

        $response = $myClient->request('GET', $url);

        if ($response->getStatusCode() === 200) {
            if ($response->hasHeader('content-length')) {
                $contentLength = $response->getHeader('content-length')[0];
                //echo "downloaded $contentLength bytes of data";
            }

            $body = $response->getBody();
            $response = json_decode($body);
        }

        foreach ($response->items as $rsp) {
            $product = new Product();
            $product->product_id = $rsp->id;
            $product->sku = $rsp->sku;
            $product->name = $rsp->name;
            $product->price = $rsp->price;
            $product->status = $rsp->status;
            $product->created_at = $rsp->created_at;
            $product->updated_at = $rsp->updated_at;
            $product->save();
            $productDescription = new ProductDescription();
            $productDescription->product_id = $product->id;
            foreach ($rsp->custom_attributes as $r) {
                if ($r->attribute_code === 'image') {
                    $productDescription->image = $r->value;
                } elseif ($r->attribute_code === 'description'){
                    $productDescription->description = $r->value;
                } elseif ($r->attribute_code === 'short_description') {
                    $productDescription->short_description = $r->value;
                }
            }
            $productDescription->save();
        }
    }
}
