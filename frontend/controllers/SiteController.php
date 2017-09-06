<?php
namespace frontend\controllers;

use Yii;
use common\models\LoginForm;
use common\models\AuthItem;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use common\models\Crop;
use common\models\Cropbyuser;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Controller;
/*******/
use PHPExcel;
use PHPExcel_Style_Fill;
use PHPExcel_IOFactory;
use PHPExcel_Style_Border;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public $enableCsrfValidation = false;
    
    public function behaviors()
    {
        // Yii::$app->session->removeAll();
        
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup','about'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['@'],
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
                    'logout' => ['get'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
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

    public function actionIndex()
    {
         if (\Yii::$app->user->isGuest == 1)
             return $this->actionLogin();
         else
         {
            //return $this->render('select-interface');
             $crop = new Crop();
             return $this->render('index',['model' => $crop]);   
               
        }
    }
    
    public function actionIndexGdbms()
    {
        $crop = new Cropbyuser();
             if(isset(\Yii::$app->session["cropId"]))
                 $crop->Crop_Id = \Yii::$app->session["cropId"];
            return $this->render('index' ,['model'=>$crop]); 
    }

    public function actionLogin()
    {      
	if (!Yii::$app->user->isGuest ) 
	{
            return $this->goHome();
	}
	
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            if(Yii::$app->user->getIdentity()->ItemName == 'breeder')
            {
                return $this->redirect(["project/index"]);
                //return $this->goHome();
            }else{
                
                return $this->goBack();
            }
            	
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();
		return $this->goHome();
    }

    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending email.');
            }

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }
   
    public function actionSignup()
    {
        $model = new SignupForm();
        
        $role = AuthItem::find()
                ->where(['isActive' => 1])
                ->all();
        $crop = Crop::find()
                ->where(["IsActive" => 1])
                ->all();
        $this->layout = false;
        	
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) 
        {
            $isSubmit = !is_null(Yii::$app->request->post('submit'));
            
            if($isSubmit)
            {
                if ($user = $model->signup()) 
                {
                    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                   
                    return $this->renderAjax('../user/view', ['model' => $user->findOne($user->UserId)]);
                }else
                {
                    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                    return \yii\widgets\ActiveForm::validate($model);
                }
            }
            else
            {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return \yii\widgets\ActiveForm::validate($model);
            }
        }else
        {
            return $this->renderAjax('signup', [
            'model' => $model, 'role' => $role, 'crop' => $crop
            ]);
        }
    }

    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->getSession()->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            } else {
                Yii::$app->getSession()->setFlash('error', 'Sorry, we are unable to reset password for email provided.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->getSession()->setFlash('success', 'New password was saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    public function actionDashboard()
    {
        $this->layout = false;
        $cropId = $_GET['cropId'];
        $dateType = $_GET['DateType'];
        $dateFrom = $_GET['DateFrom'];
        $dateTo = $_GET['DateTo'];
        
        if($cropId != '' && $dateFrom != '' && $dateTo != '')
        {
            $categories = \common\components\Operations::getCategoriesByDateType($dateType, $dateFrom, $dateTo);      
            
            $series[0]['name'] = 'DNA';
            $series[0]['data'] = \common\models\SamplesByPlate::findSamplesExtracted($cropId, $dateFrom, $dateTo, $dateType, $categories);
            
            $series[1]['name'] = 'Raw Datapoints';
            $series[1]['data'] = \common\models\RawData::getRawDatapoint($cropId, $dateFrom, $dateTo, $dateType, $categories);
            
            $series[2]['name'] = 'Datapoints Counts';
            $series[2]['data'] = \common\models\DoubleEntryTable::getDatapoint($cropId, $dateFrom, $dateTo, $dateType, $categories);
            
            $series[3]['name'] = 'Datapoints Fails';
            $series[3]['data'] = \common\models\DoubleEntryTable::getDatapoint($cropId, $dateFrom, $dateTo, $dateType, $categories, $fails = true);
            
            return $this->renderAjax('_dashboard', ['categories' => $categories, "series" => $series]);
        }
    }
    
    public function actionDownloadDashboardXls()
    {
        
        if(Yii::$app->request->post() != null )
        {
            $postData = Yii::$app->request->post();
            //print_r($postData); exit;
            if($postData['form']['Crop[cropId'] == 0)
                $crop = 'All';
            else
                $crop = Crop::findOne($postData['form']['Crop[cropId'])->Name;
              
            $sTitle = "Dahboard__".$crop."__".$postData['form']['Crop[DateFrom']."__to__".$postData['form']['Crop[DateTo'];
            
            $oExcel = new PHPExcel();
            // Add some data
            $oExcel->setActiveSheetIndex(0);
            $oSheet = $oExcel->getActiveSheet();
            $oExcel->getProperties()->setTitle($sTitle);

            //HEADERS
            $charCol = 65;
            $row = 1;
            $this->setBordersInCell($oSheet, $charCol, $row);
            $vHeaders = array("Time","ADN","Raw Datapoints","Datapoints Count","Datapoint Fails");

            foreach ($vHeaders as $key => $lbl){
                    $oSheet->getColumnDimension(chr($charCol))->setAutoSize(true);
                    $oSheet->SetCellValue(chr($charCol++).$row, $lbl);
            }
            $oSheet->getStyle(chr(65)."$row:".chr($charCol-1)."$row")
                    ->applyFromArray(array(
                                            'fill' => array(	
                                                            'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                                                            'color'	=> array('rgb' => 'FFFF00')
                                                            )
                                            )
                                    );

            $row++;
            
            $charCol = 65; //A
            foreach($postData['cat'] as $key => $date)
            {
                $this->setBordersInCell($oSheet, $charCol, $row);
                $oSheet->SetCellValue(chr($charCol).$row++, $date );
          
            }
            
            
            foreach($postData['series'] as $elements)
            {
                $charCol++;
                $row =2;
                if(isset($elements['data']) && $elements['data'] != "")
                {
                    foreach($elements['data'] as $key => $val)
                    {
                        $this->setBordersInCell($oSheet, $charCol, $row);
                        $oSheet->SetCellValue(chr($charCol).$row++, $val );
                    }
                }else{
                    foreach($postData['cat'] as $key => $date)
                    {
                        $this->setBordersInCell($oSheet, $charCol, $row);
                        $oSheet->SetCellValue(chr($charCol).$row++, '0' );
                    }
                }
            }
            
            $name = 'C:/php/'.$sTitle.'.xls';
            $objWriter = PHPExcel_IOFactory::createWriter($oExcel, 'Excel5');
            $objWriter->save($name);
            
            return $name;
        }else 
            return false;
    }
    
    public function actionPlates()
    {
        return $this->render('plate');
    }
    
    public function actionPlateLoad()
    {   
        echo "<pre>";
        print_r(Yii::$app->request->queryParams);
        exit;
    }
    
     /* This method define the borders styles in each cells */
    private function setBordersInCell($oSheet, $charCol, $col) {
        $char = ($charCol == 91 || $charCol == 'AA') ? 'AA':chr($charCol);
        
        $oSheet->getStyle( $char . $col)
                ->getBorders()
                ->getTop()
                ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $oSheet->getStyle( $char . $col)
                ->getBorders()
                ->getBottom()
                ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $oSheet->getStyle( $char . $col)
                ->getBorders()
                ->getLeft()
                ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $oSheet->getStyle( $char . $col)
                ->getBorders()
                ->getRight()
                ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    }
    
    public function actionExcelSaved() 
    {
        $file = \Yii::$app->request->queryParams['pat'];
        $nameDownload = substr($file, 7);
        if (file_exists($file) && is_readable($file)) {
            header('Content-Type: application/vnd.ms-excel');
            header("Content-Disposition: attachment;filename=".$nameDownload);
            header('Cache-Control: max-age=0');
            readfile($file);
            unlink($file);
        }else
            return false;
       
    }
}
