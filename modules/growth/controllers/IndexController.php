<?php

namespace app\modules\growth\controllers;

use Yii;
use yii\filters\VerbFilter;
use app\common\base\BaseController;

/**
 * IndexController implements the CRUD actions for Inspect model.
 */
class IndexController extends BaseController
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Inspect models.
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->render('_weight');
    }

   
}
