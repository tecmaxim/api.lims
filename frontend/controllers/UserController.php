<?php

namespace frontend\controllers;

use Yii;
use common\models\User;
use common\models\Crop;
use common\models\Cropbyuser;
use common\models\UserSearch;
use frontend\models\SignupForm;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\AuthItem;
use PHPMailer;


/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
{
    public $enableCsrfValidation = false; 
    
    public function behaviors()
    {
      // Yii::$app->user->geGItemName()
        
        //if(Yii::$app->user->isGuest) return $this->goHome();
           return [
		'access' => [
                'class' => AccessControl::className(),
		'only'	=> ['create','index'],
                'rules' => [
                            [
                                'actions' => ['create'],
                                'allow' => Yii::$app->session['role'] == 'admin' ? true : false ,
                                'roles' => ['admin'],
                            ],
                            [
                                'actions' => ['index'],
                                'allow' => true,
                                'roles' => ['@'],
                            ],
                            
                            		
                           ],
		],
		
                'verbs' => [
                'class' => VerbFilter::className(),
                //'actions' => [
                //    'delete' => ['get'],
                ///			],
            ],
			
        ];
        
    }

    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $role = AuthItem::find()
			->where(['isActive' => 1])->all();
        
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'role' => $role,
            //'success' => $success,
        ]);
    }

    /**
     * Displays a single User model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }
    
    public function actionViewAjax($id)
    {
        $this->layout = false;
        
         //echo "<pre>";
        //print_r(Yii::$app->request); exit;
        //Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $this->renderAjax('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new User();
        $role = AuthItem::find()
				->where(['isActive' => 1])
				->all();
        if ($model->load(Yii::$app->request->post()) &&  $model->saveUser()) {
            return $this->redirect(['view', 'id' => $model->UserId]);
        } else {
            return $this->render('create', [
                'model' => $model, 'role' => $role,
            ]);
        }
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $this->layout = false;
        $model = $this->findModel($id);
        $role = AuthItem::find()
				->where(['isActive' => 1])
				->all();
        $crop = Crop::find()
                ->where(["IsActive" => 1])
                ->all();
        $model->PasswordConfirm = $model->PasswordHash;
       //$model->Crop = Cropbyuser::getCropsByUser();
        $crops = Cropbyuser::getCropsByUser($id);
       
        if($crops != null)
        {
            foreach($crops as $c)
                $arr[]=  $c->Crop_Id;
            $model->Crop =  $arr;
        }
         
        if ( Yii::$app->request->isAjax && $model->load(Yii::$app->request->post()))
        {
            $isSubmit = !is_null(Yii::$app->request->post('submit'));
            if($isSubmit)
            {   
                if(isset(Yii::$app->request->post()['User']['ItemName']))
                    $model->Role = Yii::$app->request->post()['User']['ItemName'];
                $model->saveUser();
                
                if($model->Crop != null)
                    $this->saveCropsByUser($model->Crop, $model->UserId);
                
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return $this->renderAjax('view', ['model' => $model]);
                // return $this->redirect(['view', 'id' => $model->UserId]);
            }
            else
            {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return \yii\widgets\ActiveForm::validate($model);
            }
           
        } else 
        {
            //  print_r("sdfdsf"); exit;     
            return $this->renderAjax('update', [
                'model' => $model, 'role' => $role, 'crop' => $crop
            ]);
        }
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->layout = false;
        $model = $this->findModel($id);
        if (Yii::$app->request->isAjax)
        {
            //print_r("aassad"); exit;
            $isSubmit = !is_null(Yii::$app->request->post('submit'));
            if($isSubmit)
            {     
                $model->deleteLogic();
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return $this->renderAjax('delete', [ 'ok' => true]);
            }
        }
        return $this->renderAjax('delete', ['model'=>$model]);   
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
	
    public function actionRequest()
    {
        $this->layout = false;
       
            $model = new User();
            
            if(Yii::$app->request->isAjax && $model->load(Yii::$app->request->post()))
            {
                $isSubmit = !is_null(Yii::$app->request->post('submit'));
                if($isSubmit)
                {                  
                    if($pass = $model->findPasswordByEmail($model->Email))
                    {
                            if($this->sendEmail($pass, $model->Email))
                            {
                                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                                return  $this->renderAjax('forgot', ['success' => 1, "model" =>$model]);
                            }
                            else 
                            {	
                                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                                return  $this->renderAjax('forgot', ['success' => 0, "model" =>$model]);
                            }
                    }else
                    {
                      
                        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                        return $this->renderAjax('forgot', ['success' => 0, "model" =>$model]);
                    }
                }else
                {
                    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                    return \yii\widgets\ActiveForm::validate($model);
                }
            }
		
	return $this->renderAjax('forgot', [
        'model' => $model
				]);	
    }
	
    public function sendEmail($pass, $email)
    {
          
         				
					$link = 'Http://'.$_SERVER['HTTP_HOST'].Yii::$app->urlManager->baseUrl;
                                        
					$mail = new PHPMailer();
					$mail->Mailer="smtp";
					$mail->SMTPAuth=true;
					//$mail->SMTPDebug = 2;
					$mail->Host = "smtp.gmail.com";
					$mail->Port = 465;
					$mail->IsHTML(true);
					$mail->Username = "admin@theseedguru.com";
					$mail->Password = "!QAZ2wsx";
					$mail->From = "admin@theseedguru.com";
					$mail->FromName = "Advanta Semillas";
					$mail->Subject = utf8_decode("Reseteo de contraseña: GDBMS");
                                      
					$mail->SMTPAuth = true;
					$mail->SMTPSecure = "ssl";
					$mail->AddAddress($email,"");
					$body  = "Hola!<br>";
					$body .= "Su contraseña ha sido reiniciada.<br><br>Su nueva contraseña es:".$pass;
					$body .= "<br>Pruebe ingresar al sistema: <a href='".$link."' target='_blank'>".$link."</a><br>";
					$body .= "<br><br><font color='grey'>Saludos</font>";
					$mail->Body = $body;
					
					$state = $mail->Send();
					return $state;
		
    }
    
    private function saveCropsByUser($Crops, $UserId)
    {
        foreach($Crops as $k => $crop)
        {
            $cropbyUser = Cropbyuser::find()
                                ->where(["Crop_Id"=>$crop,"UserId"=>$UserId])
                                ->one();
            if($cropbyUser)
            {
                $cropbyUser->Crop_Id   = $crop;
                $cropbyUser->UserId    = $UserId;
                $cropbyUser->IsActive  = 1;
                $cropbyUser->update();
            }else
            {
                $cropbyUser = new Cropbyuser();

                $cropbyUser->Crop_Id   = $crop;
                $cropbyUser->UserId    = $UserId;
                $cropbyUser->IsActive  = 1;
                $cropbyUser->save();
            }
        }
        $query="DELETE FROM cropbyuser WHERE UserId=".$UserId;
        foreach($Crops as $k => $crop)
        {
            $query .= " and Crop_Id<>".$crop;
        }
        
        $connection = \Yii::$app->dbGdbms;
        try
        {
            $connection->createCommand($query)->execute();
        }catch(Exception $e){	print_r($e); exit;	};
    }
    
    public function actionGetUsersByJson()
    {
        $users = User::find()->select('UserId, Username') ->where(["IsActive"=>1])->orderBy("Username", SORT_ASC)->asArray()->all();
        
         Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $users;
    }
	
    public function actionGetAllUsers()
    {
        $array_users = [];
        $users = User::find()->where(["IsActive"=>1])->asArray()->orderBy(["Username" => SORT_ASC])->all();
        if($users)
            {
                foreach($users as $user)
                {
                    $array_users[] = ["name" => $user['Username'], "id" => $user['UserId']];
                }
            }
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return ["output" => $array_users];
    }
    
    public function actionTestRest()
    {
        /*
         * 
         * Verbos HTTP rest
        GET /users: list all users page by page;
        HEAD /users: show the overview information of user listing;
        POST /users: create a new user;
        GET /users/123: return the details of the user 123;
        HEAD /users/123: show the overview information of user 123;
        PATCH /users/123 and PUT /users/123: update the user 123;
        DELETE /users/123: delete the user 123;
        OPTIONS /users: show the supported verbs regarding endpoint /users;
        OPTIONS /users/123: show the supported verbs regarding endpoint /users/123.
         * 
         */
        
        //Obtengo el usuario Id=10
        $ch = curl_init("http://localhost/advanta.lims/api/web/users/10");
        
        //Primera Pagina de resultados
        //$ch = curl_init("http://localhost/advanta.lims/api/web/users");
        
        //Agregar un nuevo usuario
        //$ch = curl_init("http://localhost/advanta.lims/api/web/users/create");
        
        //$myKey = Yii::$app->user->getIdentity()->getAuthKey();
        //$authorization = "Authorization: Bearer $myKey";
        //curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization ));
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        
        /*
        //Post        
         
        $data = array("Username" => 'Prueba Test',  
                      "AuthKey"=> User::generateAuthKey(),
                      "PasswordHash" => '123456',
                      "PasswordResetToken" => User::generateAuthKey(),
                      "Email"   =>  'example@gmail.com',
                      "CreatedAt"  => date('Y-m-d h:m:s'),
                      "IsActive"   => 1,
                      "ViewNews"   => 0,
                        );  
        
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");  
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));  
        */
        $response = curl_exec($ch);
        curl_close($ch);
                
        print_r($response); exit;
    }
}
