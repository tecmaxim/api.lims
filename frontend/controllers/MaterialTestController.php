<?php

namespace frontend\controllers;

use Yii;
use common\models\MaterialTest;
use common\models\MaterialTestSearch;
// use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use PHPExcel;
use common\models\Crop;
use PHPExcel_Style_Fill;
use PHPExcel_IOFactory;
use PHPExcel_Cell;
use yii\filters\AccessControl;

/**
 * MaterialTestController implements the CRUD actions for MaterialTest model.
 */
class MaterialTestController extends ControllerCustom
{
    public $enableCsrfValidation = false; 
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
                'actions' => [
                    // 'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all MaterialTest models.
     * @return mixed
     */
    public function actionIndex()
    {
        if(Yii::$app->user->getIdentity()->itemName != "admin" && Yii::$app->user->getIdentity()->itemName != "importer: maps" && Yii::$app->user->getIdentity()->itemName != "lab")
            return $this->redirect( Yii::$app->homeUrl);
        
        $searchModel = new MaterialTestSearch();
        $searchModel->IsActive = 1;
        
        $sessionCropId = Yii::$app->session->get('cropId');
        if($sessionCropId != null)
            $searchModel->Crop_Id = $sessionCropId;
        
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $crop         = Crop::find()
                                ->where(["crop.IsActive" => 1])
                                ->all();

        $fromDashboard = false;
        if(isset($_GET['fromDashboard']))
            $fromDashboard = true;
        
        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'crop'         => $crop,
            'fromDashboard'         => $fromDashboard,
        ]);
    }

    /**
     * Displays a single MaterialTest model.
     * @param integer $id
     * @return mixed
     */
    public function actionViewAjax($id)
    {
        $this->layout = false;
        return $this->renderAjax('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new MaterialTest model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $this->layout = false;
        $model = new MaterialTest();

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post()))
        {
            
            $model->IsActive = 1;
            
            $isSubmit = !is_null(Yii::$app->request->post('submit'));
            if($isSubmit)
            {   
                $model->save();
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return $this->renderAjax('view', ['model' => $model]);
            }
            else
            {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return \yii\widgets\ActiveForm::validate($model);
            }
        } else {
            
            $parameters = $this->getParameters();

            $parameters['model'] = $model;
            $parameters['crop']  = Crop::find()
                                           ->where(['crop.IsActive' => 1])
                                           ->all();
            return $this->renderAjax('create', $parameters);
        }
    }

    /**
     * Updates an existing MaterialTest model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $this->layout = false;
        $model = $this->findModel($id);
       
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post()))
        {
            $isSubmit = !is_null(Yii::$app->request->post('submit'));
            if($isSubmit)
            {   
                $model->save();
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return $this->renderAjax('view', ['model' => $model]);
            }
            else
            {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return \yii\widgets\ActiveForm::validate($model);
            }
        } else {
            $parameters = $this->getParameters();
            
            
            $parameters['model'] = $model;
            $parameters['crop']  = Crop::find()
                                           ->where(['crop.IsActive' => 1])
                                           ->all();
          
            return $this->renderAjax('update', $parameters);
        }
    }

    /**
     * Deletes an existing MaterialTest model.
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
                $model->IsActive = 0;
                $model->update();
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return $this->renderAjax('delete', ['ok' => true]);
            }
        }
        return $this->renderAjax('delete', ['model'=>$model]);
    }

    /**
     * Finds the MaterialTest model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return MaterialTest the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = MaterialTest::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
    * Build an Array with all the parameter to pass to create/update view
    */
    protected function getParameters()
    {
        $parameters = [];

        return $parameters;
    }
    
    public function actionExcel()
    {
        $model=new MaterialTestSearch();
        $model->IsActive=1;
        
        $oExcel = new PHPExcel();

        // Add some data
        $oExcel->setActiveSheetIndex(0);
        $oSheet = $oExcel->getActiveSheet();

        $sTitle = "Materials (".date("Y-m-d").")";

        $oExcel->getProperties()->setTitle("$sTitle");

        $vHeaders = array("Name","CodeType","OldCode_1", "Owner", "Material", "cms","Pedigree", "Origin");

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
                       
                        $oSheet->SetCellValue(chr($charCol++).$row, $op->Name);
                        $oSheet->SetCellValue(chr($charCol++).$row, $op->CodeType);
                        $oSheet->SetCellValue(chr($charCol++).$row, $op->PreviousCode);
                        $oSheet->SetCellValue(chr($charCol++).$row, $op->Owner);
                        $oSheet->SetCellValue(chr($charCol++).$row, $op->Generation);
                        $oSheet->SetCellValue(chr($charCol++).$row, $op->HeteroticGroup);
                        $oSheet->SetCellValue(chr($charCol++).$row, $op->Pedigree);
                        $oSheet->SetCellValue(chr($charCol++).$row, $op->Type);
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
            MaterialTest::deleteByIds(Yii::$app->request->queryParams['vCheck']);
        return $this->actionIndex();
    }
    
    public function actionDownloadTemplate()
    {
        $oExcel = new PHPExcel();

        // Add some data
        $oExcel->setActiveSheetIndex(0);
        $oSheet = $oExcel->getActiveSheet();

        $sTitle = "Materials Template";

        $oExcel->getProperties()->setTitle("$sTitle");
//Name, Crop_Id, CodeType, PreviousCode, Owner, Generation, HeteroticGroup, Pedigree, Type
        $vHeaders = array("MaterialID",
                            "CodeType",
                            "PreviousCode",
                            "Owner",
                            "Generation",
                            "HeteroticGroup",
                            "Pedigree",
                            "Type",
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
    
    public function actionMaterialsByCropsByKendo($url_crop = null ) {
        //if (Yii::$app->request->queryParams) {
            //print_r(Yii::$app->request->queryParams); exit;
                            
            $parents = Yii::$app->request->queryParams['filter']['filters'];

            if ($parents != null) {

                $crop = $url_crop == null ? $parents[1]['value']:$url_crop;

                $connection = \Yii::$app->dbGdbms;
                $Query = "(SELECT m.Name, m.Material_Test_Id, mf.Fingerprint_Material_Id, mf.TissueOrigin FROM material_test m
                 left join fingerprint_material mf ON mf.Material_Test_Id=m.Material_Test_Id
                 WHERE m.IsActive=1 and m.Crop_Id=" . $crop;
                if (isset($parents[2])) {
                    $Query .= " and m.Name like '%" . $parents[2]['value'] . "%'";
                }
                $Query .= " Limit 50)";
                if ( $parents[0]['value'] != "") {
                     $Query .= " UNION (SELECT m2.Name, m2.Material_Test_Id, mf2.Fingerprint_Material_Id, mf2.TissueOrigin FROM material_test m2
                    left join fingerprint_material mf2 ON mf2.Material_Test_Id=m2.Material_Test_Id
                    WHERE m2.IsActive=1 and m2.Crop_Id=" . $crop.
                    " and m2.Material_Test_Id =".$parents[0]['value'].')';
                };
                
                $materials = $connection->createCommand($Query)->queryAll();

                if ($materials) {
                    $i = 0;
                    foreach ($materials as $m) {
                        $tissueOrigin = $m['TissueOrigin'] == null ? 'NA': $m['TissueOrigin'];
                        $mAvilables[$i]["name"] = $m['Name'] .' ('.$tissueOrigin.')';
                        $mAvilables[$i]["id"] = $m['Material_Test_Id'];
                        $m['Fingerprint_Material_Id'] == NULL ? $mAvilables[$i]["hexa"] = '#F69C35' : $mAvilables[$i]["hexa"] = '#389AE5';
                        $i++;
                    }
                    //print_r($mAvilables); exit;
                } else
                    return false;

                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return $mAvilables;
            }
       //}
    }
    
    public function actionMaterialsByCropsToFp() {
        $mAvilables = [];
        
        if (Yii::$app->request->post() ) {
            //print_r(Yii::$app->request->post()); exit;
                $params = Yii::$app->request->post();
                $crop = $params["depdrop_all_params"]["project-crop_id"];
                        
                $connection = \Yii::$app->dbGdbms;
                $Query = "SELECT m.Material_Test_Id, m.Name, '' as 'TissueOrigin'  FROM material_test m
                            WHERE m.IsActive=1 and m.Crop_Id=". $crop;
                                
                /*if(isset($params['filter']['filters'][0]['value']))
                {
                    $Query .= " AND m.Name like '".$params['filter']['filters'][0]['value']."%'";
                }*/
                $Query .=" UNION
                            SELECT mf.Material_Test_Id, m2.Name, mf.TissueOrigin from fingerprint_material mf 
                            INNER JOIN material_test m2 ON  mf.Material_Test_Id=m2.Material_Test_Id
                            WHERE m2.IsActive=1 and m2.Crop_Id=". $crop;
                /*if(isset($params['filter']['filters'][0]['value']))
                {
                    $Query .= " AND m2.Name like '".$params['filter']['filters'][0]['value']."%'";
                }*/
                            
                $Query .=" ORDER BY Material_Test_Id";
               
                //$Query .= " Limit 20";
               
                $materials = $connection->createCommand($Query)->queryAll();

                if ($materials) {
                    $i = 0;
                    foreach ($materials as $m) {
                        $tissueOrigin = $m['TissueOrigin'] == null ? 'NA': $m['TissueOrigin'];
                        $mAvilables[$i]["name"] = $m['Name'] .' ('.$tissueOrigin.')';
                        $mAvilables[$i]["id"] = $m['Material_Test_Id'];
                        //$m['Fingerprint_Material_Id'] == NULL ? $mAvilables[$i]["hexa"] = '#F69C35' : $mAvilables[$i]["hexa"] = '#389AE5';
                        $i++;
                    }
                    //print_r($mAvilables); exit;
                } else
                    return false;

                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return ["output" => $mAvilables];
            
        }
    }
       
    public function actionGetMaterialsByCropsByAjax() 
    {        
        if (Yii::$app->request->queryParams || Yii::$app->request->isPost) {
            $crop = Yii::$app->request->queryParams['cropId'];
            //this variable will be an array to keep the materials copied succesfully
            $materialsId = null;
                        
            $materials = MaterialTest::find()->where(["IsActive" => 1, "Crop_Id" => $crop])->orderBy('Name')->asArray()->all();
            
            // If the request come from project Update or copyMaterials to FP
           
            if(isset(Yii::$app->request->queryParams['projectId']))
            {
                $materialsId = $this->getIdsMaterialsByProject(Yii::$app->request->queryParams['projectId']);
            }elseif(Yii::$app->request->isPost)
            {
                $materialsId = Yii::$app->request->post()['copyMaterials'];
            }
                
            
            $inputs = '<button type="button" data-toggle="modal" data-target="#modal" data-url="../materialtest/create" class="user export size12 margin-top--30_padding-6 pull-right">'
                        . '<span class="glyphicon glyphicon-import "></span> New Material'
                    . '</button>'
                    . '<button type="button" data-toggle="modal" data-target="#modal" data-url="../materialtest/copy-materials" class="user export size12 margin-top--30_padding-6 pull-right">'
                        . '<span class="glyphicon glyphicon-plus "></span> Copy Materials'
                    . '</button>'
                    . '<a href="javascript: selectAll();" class ="user export margin20 pull-right" > <span class="glyphicon glyphicon-check size12" aria-hidden="true"></span></a>'
                    . '<div class="container-scrolleable">'
                    . '<label class="control-label" for="materials"> Material </label>';
            $i = 0;
            $divs = 0;
            $inputs .= "<div class='scrolleable'>";
            
            foreach ($materials as $m) 
            {
                if ((fmod($i, 50) == 0) or $i == 0) {
                    if ((fmod($divs, 6) == 0) or $divs == 6)
                        $inputs .= "<div class='separator'>";
                    $inputs .= "<div class='cols_materials'>";
                }
                if($materialsId != null)
                {
                    if(in_array($m['Material_Test_Id'], $materialsId))
                    {
                        //set "checked" in the input
                        $inputs .= "<div><span class='alert alert-success padding10-2'><input type='checkbox' value='" . $m['Material_Test_Id'] . "' id='" . $m['Material_Test_Id'] . "' name='Project[vCheck][]' checked/>" . $m['Name'] . "</span></div>"; 
                    }else 
                        $inputs .= "<div><input type='checkbox' value='" . $m['Material_Test_Id'] . "' id='" . $m['Material_Test_Id'] . "' name='Project[vCheck][]' /> " . $m['Name'] . "</div>";
                }else 
                    $inputs .= "<div><input type='checkbox' value='" . $m['Material_Test_Id'] . "' id='" . $m['Material_Test_Id'] . "' name='Project[vCheck][]' /> " . $m['Name'] . "</div>";
                $i++;

                if ((fmod($i, 50) == 0) and $i >= 50) {
                    $divs++;
                    $inputs .= "</div>";
                    if ((fmod($divs, 6) == 0) and $divs >= 6)
                        $inputs .= "</div> <br>";
                }
            }
            $inputs .= '</div></div></div>         
                        </div>';
            return $inputs;
        }
    }
    
    private function getIdsMaterialsByProject($projectId)
    {
        $materialsSelected = \common\models\MaterialsByProject::find()->where([ "ProjectId" => $projectId])->asArray()->all();
        
        
        if($materialsSelected)
        {   
            foreach($materialsSelected as $m)
            {
                $ids[] = $m['Material_Test_Id'];
            }
            return $ids;
        }else
            false;
    }
    
    public function actionCopyMaterials()
    {
        $model = new MaterialTest();
        $this->layout = false;
        $model->scenario = "controlMaterials";
        if(\Yii::$app->request->post())
        {
            $model->Crop_Id = Yii::$app->request->queryParams["crop"];
            //print_r(\Yii::$app->request->post()); exit;
            if ($model->load(\Yii::$app->request->post()) && $model->validate())
            {
                $materilsCopied = $model->normalizeSamplesAsArray($model->CopyMaterials);
                $error = "Some materials dont exist for selected crop";
                //print_r($materilsCopied); exit;
                $existinMaterials = $model->find()
                        ->select("Material_Test_Id, Name")
                        ->where(["Crop_Id" => $model->Crop_Id])
                        ->andWhere(["in", "Name", $materilsCopied])
                        ->asArray()
                        ->all();
                if($existinMaterials)
                {
                    if(count($existinMaterials) != count($materilsCopied))
                    {
                       // $result = array_diff($materilsCopied, $existinMaterials);
                        $result = array_diff($materilsCopied, \common\components\Operations::array_column($existinMaterials, "Name"));
                       
                       
                        $error .= "<ul>";
                       foreach($result as $key => $name)
                       {
                           $error .= "<li>".$name."</li>";
                       }
                       $error .= "</ul>";
                       return $error;
                       
                    }else
                    {
                        $result['materials'] = $existinMaterials;
                        $result['msj'] = 'ok';
                        
                       Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                       return $result;
                    }
                }
                return $error;
            }else{
                print_r($model->getErrors());

            }
            
        }
        
        return $this->render('../project/resources/__form-materials-fp', ["model" => $model]);
        
    }
    
    public function actionExample()
    {  return 1;
        if (Yii::$app->request->queryParams) {
                $crop = Yii::$app->request->queryParams["url_crop"];
                $connection = \Yii::$app->dbGdbms;
                $Query = "SELECT m.Material_Test_Id, m.Name, null as 'TissueOrigin'  FROM material_test m
                            WHERE m.IsActive=1 and m.Crop_Id=". $crop."
                            union
                            select mf.Material_Test_Id, m2.Name, mf.TissueOrigin from fingerprint_material mf 
                            inner join material_test m2 ON  mf.Material_Test_Id=m2.Material_Test_Id
                            Order by Material_Test_Id";
               
                //$Query .= " Limit 50";
               
                $materials = $connection->createCommand($Query)->queryAll();

                if ($materials) {
                    $i = 0;
                    foreach ($materials as $m) {
                        $tissueOrigin = $m['TissueOrigin'] == null ? 'NA': $m['TissueOrigin'];
                        $mAvilables[$i]["name"] = $m['Name'] .' ('.$tissueOrigin.')';
                        $mAvilables[$i]["id"] = $m['Material_Test_Id'];
                        //$m['Fingerprint_Material_Id'] == NULL ? $mAvilables[$i]["hexa"] = '#F69C35' : $mAvilables[$i]["hexa"] = '#389AE5';
                        $i++;
                    }
                    //print_r($mAvilables); exit;
                } else
                    return false;

                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return $mAvilables;
            
        }
    }
}