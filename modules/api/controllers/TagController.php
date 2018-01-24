<?php

namespace app\modules\api\controllers;
use Yii;
use yii\web\NotAcceptableHttpException;
use app\modules\spot\models\Tag;
use yii\web\Response;
use yii\helpers\Html;
use app\modules\spot\models\AdviceTagRelation;

class TagController extends CommonController{
    
    
    public function actionSearch(){
        
        $request = Yii::$app->request;
        Yii::$app->response->format = Response::FORMAT_JSON;
        if($request->isAjax){
            $discountTagChecked = $request->get('discountTagChecked');
            $commonTagChecked = $request->get('commonTagChecked');
            $tagList = Tag::getTagList(['id','name','type']);
            $discountTag = [];
            $commonTag = [];
            if(!empty($tagList)){
                foreach ($tagList as $v){
                    if($v['type'] == 1){
                        $discountTag[] = ['id' => $v['id'],'name' => $v['name']];
                    }
                    if($v['type'] == 2){
                        $commonTag[] = ['id' => $v['id'],'name' => $v['name']];
                    }
                }   
            }
            
            $model = new AdviceTagRelation();
            if($discountTagChecked){
                $model->discountTag = explode(',', $discountTagChecked);
            }
            if($commonTagChecked){
                $model->commonTag = explode(',', $commonTagChecked);
            }
            return [
                'title' => '请选择药品标签',
                'content' => $this->renderAjax('@spotAdviceTagSearch',[
                    'model' => $model,
                    'discountTag' => $discountTag,
                    'commonTag' => $commonTag
                ]),
                'footer' =>  Html::button('取消', ['class' => 'btn btn-cancel btn-form', 'data-dismiss' => "modal"]) .
                    Html::button('保存', ['class' => 'btn btn-default btn-form advice-tag-search-button'])
            ];
            
            
        }else{
            throw new NotAcceptableHttpException();
        }
        
    }
}