<?php

namespace api\controllers;

use Yii;
use common\models\User;
use common\models\UserSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\AuthItem;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\QueryParamAuth;
use yii\rest\ActiveController;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends ActiveController
{
    public $enableCsrfValidation = false; 
    
    public $modelClass = 'common\models\User';
    
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        /*
         * $behaviors['authenticator'] = [
           'class' => HttpBearerAuth::className(),
            /*
            'auth' => function ($username, $password) {
                // @var User $user 
                $user = User::findByUsername($username);
                if ($user && $user->validatePassword($password)) {
                    return $user;
                }
            }
         
            ];
         */
                     
        return $behaviors;
    }
    
    
    public function actions()
    {
        $actions = parent::actions();
        unset($actions['view']);
         
        return $actions;
    }
    
    public function actionLord()
    {
        print_r("Hola Estoy sobreescribiendo el método");
    }
    
    
    public function init()
    {
        parent::init();
        \Yii::$app->user->enableSession = false;
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
}
