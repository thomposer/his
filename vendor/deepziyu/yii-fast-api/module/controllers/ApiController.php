<?php
namespace deepziyu\yii\rest\module\controllers;

use deepziyu\yii\rest\models\Route;
use Yii;
use yii\web\Controller;
use yii\web\NotAcceptableHttpException;

/**
 * Site controller
 */
class ApiController extends Controller
{
    public function init()
    {
        parent::init();
        Yii::$app->response->format = 'html';
    }

    /**
     * Displays homepage.
     * @param int $id
     * @return array
     */
    public function actionIndex()
    {
        if(!YII_DEBUG){
            throw new NotAcceptableHttpException('权限不足');
        }
        $model = new Route();
        
        $routes =  $model->getAppRoutes('api');
        unset($routes['/route/*'],$routes['/*']);
        $routes = array_reverse($routes);
        return $this->renderPartial('index',[
            'routes'=>$routes
        ]);
    }

    public function actionRoutes()
    {

    }

}
