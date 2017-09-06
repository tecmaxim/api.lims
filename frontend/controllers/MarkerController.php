<?php

namespace frontend\controllers;

use Yii;
use PHPExcel;
use PHPExcel_Style_Fill;
use PHPExcel_IOFactory;
use PHPExcel_Cell;
use common\models\Marker;
use common\models\MarkerSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use frontend\models\UploadFile;
use yii\web\UploadedFile;
use yii\db\Query;
use yii\filters\AccessControl;

/**
 * MarkerController implements the CRUD actions for Marker model.
 */
class MarkerController extends Controller
{
    public $enableCsrfValidation = false; 
    
    public function behaviors()
    {
         
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                       
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                     
                ],
           ],
            'verbs' => [
                'class' => VerbFilter::className(),
                //'actions' => [
                //    'delete' => ['post'],
                //],
            ],
        ];
    }

    /**
     * Lists all Snp models.
     * @return mixed
     */
    public function actionIndex()
    {
        if(Yii::$app->user->getIdentity()->itemName != "admin")
            return $this->redirect( Yii::$app->homeUrl);
        
        $searchModel = new MarkerSearch();
        if(Yii::$app->request->queryParams)
        {
            $searchModel->load(Yii::$app->request->queryParams);
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
            $dataProvider->key = "Marker_Id";
            $dataProvider->setPagination(["pageSize" => Yii::$app->request->queryParams == null ? 100 : Yii::$app->request->queryParams['MarkerSearch']['Pagination'] ]);
        }else
            $dataProvider = null;
        $fromDashboard = false;
        if(isset($_GET['fromDashboard']))
            $fromDashboard = true;

        //print_r(Yii::$app->request->queryParams); exit;
        
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'fromDashboard' => $fromDashboard,
        ]);
    }
    
    public function actionIndex2()
    {
        $searchModel = new MarkerSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $searchModel->load(Yii::$app->request->queryParams);
        //print_r(Yii::$app->request->queryParams); exit;
        //$dataProvider->setPagination(["pageSize" => Yii::$app->request->queryParams == null ? 100 : Yii::$app->request->queryParams['SnpSearch']['Pagination'] ]);
        return $this->render('index_2', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    

    /**
     * Displays a single Snp model.
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
         
        return $this->renderAjax('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Snp model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Marker();
      
        $this->layout = false;

        if (Yii::$app->request->isAjax  && $model->load(Yii::$app->request->post())) 
        {
            $isSubmit = !is_null(Yii::$app->request->post('submit'));
            $model->IsActive = 1;
            if($isSubmit)
            {
                //print_r("asdasd"); exit;
                $model->save();
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return $this->renderAjax('view', ['model' => $model,]);
            }else{
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return \yii\widgets\ActiveForm::validate($model);
            }
            
            
        }
            else
        {
            return $this->renderAjax('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Snp model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $this->layout = false;

        if ($model->load(Yii::$app->request->post())) 
        {
            $model->save();
            
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return $this->renderAjax('view', ['model' => $model]);
        } else {
            return $this->renderAjax('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Snp model.
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
     * Finds the Snp model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Snp the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Marker::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionImport()
    {
        set_time_limit(60*5); //5 minutos
        ini_set ( "memory_limit" , -1 );

        $model = new UploadFile();

        if (Yii::$app->request->isPost)
        {
            $model->file = UploadedFile::getInstance($model, 'file');

            if ($model->file && $model->validate()) 
            { 
                $path = 'uploads/' .$model->file->baseName .'.' .$model->file->extension;
                if($model->file->saveAs($path))
                {
                    $markers = $this->importmarker($path);
                    return $this->render('importSummary', ['snps' => $snps]);
                }
            }
        }

        if(Yii::$app->request->queryParams)
        {
            $get = Yii::$app->request->queryParams;
            return $this->render('import', ['model' => $model, "error" => $get['error']]);
        }
        else
            return $this->render('import', ['model' => $model]);
    }

    
    public function actionExcel()
    {
        $model=new MarkerSearch();
        $model->IsActive=1;
        
        $oExcel = new PHPExcel();

        // Add some data
        $oExcel->setActiveSheetIndex(0);
        $oSheet = $oExcel->getActiveSheet();

        $sTitle = "Markers (".date("Y-m-d").")";

        $oExcel->getProperties()->setTitle("$sTitle");

        $vHeaders = array("Name","ShortSequence","PublicLinkageGroup","PublicCm","AdvLinkageGroup","AdvCm","PhysicalPosition");

        //HEADERS
        $charCol = 65;
        $row = 1;
        foreach ($vHeaders as $key => $lbl){
                $oSheet->getColumnDimension(chr($charCol))->setAutoSize(false);
                $oSheet->SetCellValue(chr($charCol++).$row, $lbl);
        }
        $oSheet->getStyle(chr(65)."$row:".chr($charCol-1)."$row")->applyFromArray(
                array('fill' 	=> array(	'type'		=> PHPExcel_Style_Fill::FILL_SOLID,
                                                'color'		=> array('rgb' => 'FFFF00')
                                        )
        ));

        $row++;
        if(isset(Yii::$app->request->queryParams['vCheck']))
            $vMarker = $model->searchByIds(Yii::$app->request->queryParams['vCheck'])->getModels();
        else
            $vMarker = $model->search(Yii::$app->request->queryParams)->getModels();
             ini_set ( "memory_limit" , - 1 );
        //$vSnp->pagination = false;
        //$vSnp->getModels();
            
        //search()->getData();
        if (is_array($vMarker)){
                foreach ($vMarker as $op){
                        $charCol = 65;
                       
                        $oSheet->SetCellValue(chr($charCol++).$row, $op['Name']);
                        $oSheet->SetCellValue(chr($charCol++).$row, $op['ShortSequence']);
                        $oSheet->SetCellValue(chr($charCol++).$row, $op['PublicLinkageGroup']);
                        $oSheet->SetCellValue(chr($charCol++).$row, $op['PublicCm']);
                        $oSheet->SetCellValue(chr($charCol++).$row, $op['AdvLinkageGroup']);
                        $oSheet->SetCellValue(chr($charCol++).$row, $op['AdvCm']);
                        $oSheet->SetCellValue(chr($charCol++).$row, $op['PhysicalPosition']);
                                         
                        $row++;
                }
        }

        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=\"$sTitle.xls\"");
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($oExcel, 'Excel5');
        $objWriter->save('php://output');
    }

    public function actionDeleteSelection()
    {
        if(Yii::$app->request->queryParams['vCheck'])
            Marker::deleteByIds(Yii::$app->request->queryParams['vCheck']);
        return $this->actionIndex();
    }
    
    public function actionDownloadTemplate()
    {
        $oExcel = new PHPExcel();

        // Add some data
        $oExcel->setActiveSheetIndex(0);
        $oSheet = $oExcel->getActiveSheet();

        $sTitle = "Markers Template";

        $oExcel->getProperties()->setTitle("$sTitle");

        $vHeaders = array("Marker original name",
		"Short sequence",
		"Long sequence",
		);

        //HEADERS
        $charCol = 65;
        $row = 1;
        foreach ($vHeaders as $key => $lbl){
                $oSheet->getColumnDimension(chr($charCol))->setAutoSize(true);
                $oSheet->SetCellValue(chr($charCol++).$row, $lbl);
        }
        $oSheet->getStyle(chr(65)."$row:".chr($charCol-1)."$row")->applyFromArray(
                array('fill' 	=> array(	'type'		=> PHPExcel_Style_Fill::FILL_SOLID,
                                                'color'		=> array('rgb' => 'FFFF00')
                                        )
        ));
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=\"$sTitle.xls\"");
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($oExcel, 'Excel5');
        $objWriter->save('php://output');       
    }
    
    public function actionGetMarkersByKendo()
    {
        if (Yii::$app->request->queryParams) 
        {
           if(isset(Yii::$app->request->queryParams['filter']))
           {
               $filter = Yii::$app->request->queryParams['filter']['filters'];
               
           }
           
                
            $crop = Yii::$app->request->queryParams['crop'];

            $connection = \Yii::$app->dbGdbms;
            $Query = "SELECT m.Name, m.Marker_Id FROM marker m
             WHERE m.IsActive=1 and m.Crop_Id=".$crop ;
            if(isset($filter))
            {
                $Query .= " and m.Name like '%".$filter[0]['value']."%'";
            }
            $Query .= " LIMIT 150";

            $marks = $connection->createCommand($Query)->queryAll();

             if($marks)
            {
                 $i =0;
                foreach($marks as $m)
                {
                    $mAvilables[$i]["name"] = $m['Name'];
                    $mAvilables[$i]["id"] = $m['Marker_Id'];
                    $i++;
                }
                //print_r($mAvilables); exit;
            } else
                return false;

            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return  $mAvilables;
            
        }
    }
	
}
