<?php

namespace app\modules\manage\controllers;

use Yii;
use app\modules\manage\models\Service;
use app\modules\manage\models\ServiceSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\common\base\BaseController;
use yii\helpers\ArrayHelper;

/**
 * SiteController implements the CRUD actions for Service model.
 */
class IndexController extends BaseController
{
    public function behaviors()
    {
        
       $current =  [
           
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
       $parent = parent::behaviors();
       return ArrayHelper::merge($current, $parent);
    }
   
    /**
     * Lists all Service models.
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

}
