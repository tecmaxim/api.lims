<?php

namespace frontend\controllers;

use Yii;
use common\models\SnpLab;
use common\models\Marker;
use common\models\SnpLabSearch;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use frontend\models\UploadFile;
use yii\web\UploadedFile;
use PHPExcel;
use PHPExcel_Style_Fill;
use PHPExcel_IOFactory;
use PHPExcel_Cell;
use yii\filters\AccessControl;

/**
 * SnpLabController implements the CRUD actions for SnpLab model.
 */
class SnplabController extends Controller
{
    public function behaviors()
    {
        // Yii::$app->session->removeAll();
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
     * Lists all SnpLab models.
     * @return mixed
     */
    public function actionIndex()
    {
        $mapTypes="";
        if(Yii::$app->user->getIdentity()->itemName != "admin")
            return $this->redirect( Yii::$app->homeUrl);
        
        $searchModel = new SnpLabSearch();
       
        $searchModel->load(Yii::$app->request->queryParams);
        
        $sessionCropId = Yii::$app->session->get('cropId');
        if($sessionCropId != null)
            $searchModel->Crop = $sessionCropId;
        
        if($searchModel->MapTypeId != "")
                {    
                    $mapTypes = \common\models\MapTypeByCrop::find()
                                    ->where(["IsActive"=>1, "Crop_Id"=>$searchModel->Crop])
                                    ->all();
                  
                }
                
        $searchModel->Crop = Yii::$app->session->get('cropId');
        if(Yii::$app->request->queryParams && isset(Yii::$app->request->queryParams['SnpLabSearch']['search']))
        {   
            $dataProvider = $searchModel->search2(Yii::$app->request->queryParams);
           
            $dataProvider->key = "Snp_lab_Id";
            //$dataProvider->setPagination(["pageSize" => Yii::$app->request->queryParams == "" ? 50 : Yii::$app->request->queryParams['SnpLabSearch']['Pagination']]);
        }
        else
            $dataProvider = null;

        $fromDashboard = false;
        if(isset($_GET['fromDashboard']))
            $fromDashboard = true;
       
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'fromDashboard' => $fromDashboard,
            'mapTypes'     => $mapTypes,
        ]);
    }

    /**
     * Displays a single SnpLab model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        //$this->layout = false;
        
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
     * Creates a new SnpLab model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new SnpLab();
        
        ini_set("memory_limit", -1);
        $this->layout = false;
        
        if (Yii::$app->request->isAjax  && $model->load(Yii::$app->request->post())) 
        {
             $isSubmit = !is_null(Yii::$app->request->post('submit'));
             
            if($isSubmit)
            {
                $model->IsActive = 1;
        	if($model->save())

                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return $this->render('view', ['model' => $model]);
            }
            else
            {
               Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
               return \yii\widgets\ActiveForm::validate($model);
            }
        } 
        else             
        {
            $markers = Marker::find()
                    ->where(["IsActive" => 1])
                    ->all();

            return $this->renderAjax('create', [
                'model' => $model,
                'markers' => $markers,
            ]);
        }
    }

    /**
     * Updates an existing SnpLab model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
         ini_set("memory_limit", -1);
        $this->layout = false;
        if ($model->load(Yii::$app->request->post())) 
        {
        	if($model->save());
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            	return $this->redirect(['view', 'id' => $model->Snp_lab_Id]);
        } 
        else 
        {
            $markers = Marker::find()->where(["IsActive" => 1])->all();
            return $this->renderAjax('update', [
                'model' => $model,
                'markers' => $markers,
            ]);
        }
    }

    /**
     * Deletes an existing SnpLab model.
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
     * Finds the SnpLab model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SnpLab the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SnpLab::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function getBarcodes($model)
    {
                    $snp =Barcode::find()
                                                    ->where(['Snp_lab_Id' => $model->Snp_lab_Id])
                                                            ->all();

                    if($snp)
                    {
                            foreach($snp as $bar)
                                    $arraycods[] = $bar->Number;

                            $stringBarcode = implode(',', $arraycods);
                    return $stringBarcode;
                    } else return NULL;
    }

    public function actionImport()
    {
            $model = new UploadFile();

    if (Yii::$app->request->isPost)
    {
        $model->file = UploadedFile::getInstance($model, 'file');

        if ($model->file && $model->validate()) 
                    { 
                            $path = 'uploads/' .$model->file->baseName .'.' .$model->file->extension;
                            $model->file->saveAs($path);
                            // print_r($model);exit();
                            $this->importSnpLab($path);
                            // if($model->file->saveAs('uploads/' . $model->file->baseName . '.' . $model->file->extension))  
                                    // return $this->redirect(['fingerprint/query_string', 'url'=> $model->file]);
                    }
    }

    return $this->render('import', ['model' => $model]);

    }
        
    public function actionExcel()
    {
        $model=new SnpLabSearch();
        $model->IsActive=1;
        
        $oExcel = new PHPExcel();

        // Add some data
        $oExcel->setActiveSheetIndex(0);
        $oSheet = $oExcel->getActiveSheet();

        $sTitle = "SNP LABs (".date("Y-m-d").")";

        $oExcel->getProperties()->setTitle("$sTitle");

        $vHeaders = array("LabName","PurchaseSequence","Quality", "Allele Fam", "Allele Vic Hex","Box","Position In Box");

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
            $vSnp = $model->searchByIds(Yii::$app->request->queryParams['vCheck'])->getModels();
        else
            $vSnp = $model->search(Yii::$app->request->queryParams)->getModels();
        //search()->getData();
        if (is_array($vSnp)){
                foreach ($vSnp as $op){
                        $charCol = 65;
                       
                        $oSheet->SetCellValue(chr($charCol++).$row, $op["LabName"]);
                        $oSheet->SetCellValue(chr($charCol++).$row, $op["PurchaseSequence"]);
                        $oSheet->SetCellValue(chr($charCol++).$row, $op["Quality"]);
                        $oSheet->SetCellValue(chr($charCol++).$row, $op["AlleleFam"]);
                        $oSheet->SetCellValue(chr($charCol++).$row, $op["AlleleVicHex"]);
                        $oSheet->SetCellValue(chr($charCol++).$row, $op["Box"]);
                        $oSheet->SetCellValue(chr($charCol++).$row, $op["PositionInBox"]);
       
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
            SnpLab::deleteByIds(Yii::$app->request->queryParams['vCheck']);
        return $this->actionIndex();
    }
    
    public function actionDownloadTemplate()
    {
        $oExcel = new PHPExcel();

        // Add some data
        $oExcel->setActiveSheetIndex(0);
        $oSheet = $oExcel->getActiveSheet();

        $sTitle = "SNP LABs Template";

        $oExcel->getProperties()->setTitle("$sTitle");

        $vHeaders = array("Marker original name",
                          "SNP lab name",
                          "Purchase sequence",
                          "Allele FAM",
                          "Allele VIC/HEX", 
                          "Barcodes",
                          "Validated",
                          "Quality",
                          "Assay brand",
                          "Box",
                          "Position in box",
                          "PIC");

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
    
    /*******************************************************
     *                                                     *
     *                Refact To Lims                       *
     *                                                     *
     *******************************************************/
    public function actionGetSnplabsByKendo()
    {
        $mAvilables = [];
        if (Yii::$app->request->queryParams) 
        {
           if(isset(Yii::$app->request->queryParams['filter']))
           {
               $filter = Yii::$app->request->queryParams['filter']['filters'];
               
           }
           
            $crop = Yii::$app->request->queryParams['crop'];

            $connection = \Yii::$app->dbGdbms;
            $Query = "SELECT s.LabName, s.Snp_lab_Id FROM snp_lab s"
                    . " INNER JOIN marker m ON m.Marker_Id = s.Marker_Id"
                    . " WHERE s.IsActive=1 and m.IsActive=1 and m.Crop_Id=".$crop ;
            if(isset($filter))
            {
                $Query .= " and s.LabName like '%".$filter[0]['value']."%'";
            }
            $Query .= " LIMIT 150";

            $marks = $connection->createCommand($Query)->queryAll();

             if($marks)
            {
                 $i =0;
                foreach($marks as $m)
                {
                    $mAvilables[$i]["name"] = $m['LabName'];
                    $mAvilables[$i]["id"] = $m['Snp_lab_Id'];
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
