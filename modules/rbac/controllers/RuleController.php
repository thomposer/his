<?php

namespace app\modules\rbac\controllers;

use Yii;
use app\modules\rbac\models\RuleForm;
use app\common\base\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use tests\codeception\_pages\AboutPage;
use app\modules\rbac\rule\RbacRule;
/**
 * RuleController implements the CRUD actions for Rule model.
 */
class RuleController extends BaseController
{
    public $manager;
    public function init(){
        parent::init();
        $this->manager = \yii::$app->authManager;
    }
    
    public function execute($user, $item, $params){
        
    }
    /**
     * Lists all Rule models.
     * @return mixed
     */
    public function actionIndex()
    {
       
       

       return $this->render('index');
    }

    /**
     * Displays a single Rule model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }
    
    /**
     * Creates a new Rule model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new RuleForm();
        $rule = new RbacRule();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            
            $rule->name = $model->name;
            $rule->data = $model->data;
            
            $result = $this->manager->add($rule);
            if($result){
                return $this->redirect('index', ['id' => $model->name]);
            }
            
        } 
            
            return $this->render('index', [
                'model' => $model,
            ]);
        
    }

    /**
     * Updates an existing Rule model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->name]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Rule model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Rule model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Rule the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Rule::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
