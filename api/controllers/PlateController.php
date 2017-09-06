<?php

namespace api\controllers;

use common\models\Plate;
use yii\web\NotFoundHttpException;
use yii\rest\ActiveController;
use yii\filters\auth\HttpBasicAuth;
//use yii\web\Response;
use yii\filters\auth\QueryParamAuth;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use common\components\QueryParamsComponent;

/**
 * PlateController implements the CRUD actions for Plate model.
 */
class PlateController extends ActiveController
{

    public $enableCsrfValidation = false;
    
    public $modelClass = 'common\models\Plate';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        
        $behaviors['authenticator'] = [
            //'class' => QueryParamAuth::className(),
            'class' => HttpBearerAuth::className(),
            //'class' => QueryParamsComponent::className(),
            //'authMehtods' => function()
        ];
        return $behaviors;
        
        //$behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_HTML;
        return $behaviors;

    }
    
    public function actions()
    {
        $actions = parent::actions();
        unset($actions['view']);
        
        //$actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
         
        return $actions;
    }

    public function actionView($id)
    {
        $data = ['message' => $id,'message2' => $id];
        return $data;

    }
    
    public function prepareDataProvider()
    {
        //print_r($this->); exit;
        
    }
    
    public function init()
    {
        parent::init();
        \Yii::$app->user->enableSession = false;
    }

   

    /**
     * Finds the Plate model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Plate the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Plate::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    /**
    * Checks the privilege of the current user.
    *
    * This method should be overridden to check whether the current user has the privilege
    * to run the specified action against the specified data model.
    * If the user does not have access, a [[ForbiddenHttpException]] should be thrown.
    *
    * @param string $action the ID of the action to be executed
    * @param \yii\base\Model $model the model to be accessed. If `null`, it means no specific model is being accessed.
    * @param array $params additional parameters
    * @throws ForbiddenHttpException if the user does not have access
    */
   public function checkAccess($action, $model = null, $params = [])
   {
       // check if the user can access $action and $model
       // throw ForbiddenHttpException if access should be denied
       //print_r($model); exit;
       if ($action === 'update' || $action === 'delete') {
           if ($model->author_id !== \Yii::$app->user->id)
               throw new \yii\web\ForbiddenHttpException(sprintf('You can only %s articles that you\'ve created.', $action));
       }
   }

}
