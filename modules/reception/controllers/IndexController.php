<?php

namespace app\modules\reception\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use app\common\base\BaseController;
use yii\filters\VerbFilter;
use yii\base\Object;
use yii\db\Query;
use app\modules\spot_set\models\Schedule;
use yii\helpers\Html;
use app\modules\schedule\models\Scheduling;
/**
 * IndexController implements the CRUD actions for Nurse model.
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
     * Lists all Nurse models.
     * @return mixed
     */
    public function actionIndex()
    {
       // $searchModel = new ReceptionSearch();
        $schedule = Scheduling::getScheduleList(['id','shift_name'],['status' => 1]);
        return $this->render('index',[
//            'searchModel' => $searchModel,
              'schedule' =>$schedule
        ]);
    }

    public function actionReception()
    {
        $schedule = Scheduling::getScheduleList(['id','shift_name'],['status' => 1]);
       // $searchModel = new ReceptionSearch();
        return $this->render('reception',[
//            'searchModel' => $searchModel,
            'schedule' =>$schedule
        ]);
    }


}
