<?php

namespace frontend\controllers;

use Yii;

use common\models\Fingerprint;
use common\models\FingerprintMaterial;
use common\models\FingerprintSnp;
use common\models\FingerprintResult;
use common\models\FingerprintSearch;
use common\models\SnpLab;
use common\models\Origin;
use common\models\Protocol;
use common\models\Allele;
use common\models\Material;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Query;
use yii\filters\AccessControl;

/**
 * FingerprintController implements the CRUD actions for Fingerprint model.
 */
class FingerprintController extends Controller
{
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
                /*'actions' => [
                    'delete' => ['post'],
                ],*/
            ],
        ];
    }

    /**
     * Lists all Fingerprint models.
     * @return mixed
     */
    public function actionIndex()
    {
        if(Yii::$app->user->getIdentity()->itemName != "admin")
            return $this->redirect( Yii::$app->homeUrl);
        
        $searchModel = new FingerprintSearch();
        
        $sessionCropId = Yii::$app->session->get('cropId');
        if($sessionCropId != null)
            $searchModel->Crop_Id = $sessionCropId;
                    
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $fromDashboard = false;
        if(isset($_GET['fromDashboard']))
            $fromDashboard = true;
               
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'fromDashboard' => $fromDashboard,
        ]);
    }

    /**
     * Displays a single Fingerprint model.
     * @param string $id
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
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Fingerprint model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Fingerprint();
        
         $this->layout = false;
         
        if (Yii::$app->request->isAjax  && $model->load(Yii::$app->request->post()) ) {
            if($isSubmit)
            {
                $model->save();
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return $this->renderAjax('view', ['model' => $model,]);
            }else{
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return \yii\widgets\ActiveForm::validate($model);
            }
        } else {
            return $this->renderAjax('create', [
                'model' => $model,
            ]);
        }
        
         
    }

    /**
     * Updates an existing Fingerprint model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->Fingerprint_Id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Fingerprint model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
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
     * Finds the Fingerprint model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Fingerprint the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Fingerprint::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
	

	/** DEBUG */
   
    public function actionImport_excel()
	{
		
			$objPHPExcel = new PHPExcel();

			$file = "C:\Advanta\FINGERPRINT.xlsx";
			$Reader = PHPExcel_IOFactory::createReaderForFile($file);
			
			$Reader -> setReadDataOnly (true);
			$ObjXLS = $Reader ->load( $file );
			$ObjXLS->setActiveSheetIndex(0);
		
							
			$lastCol = $ObjXLS->getActiveSheet()->getHighestColumn();
			//$dimensiones =  $ObjXLS->getActiveSheet()->calculateWorksheetDimension();
			$lastRow = $ObjXLS->getActiveSheet()->getHighestRow();			
			$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($lastCol);
		
			
		
				for($row=1; $row <= $lastRow; $row++)
				{
					set_time_limit(60*5); //5 minutos
					ini_set ( "memory_limit" , - 1 );
					
					for($col=0; $col < $highestColumnIndex; $col++) //Iteracion para el control ascii que va de la A a la Z
					{
						$values[$row][$col] = $ObjXLS->getSheet(0)->getCellByColumnAndRow($col, $row)->getValue();
					}
						 		
				}
				
				$ObjXLS->disconnectWorksheets();
				unset($ObjXLS, $objPHPExcel);		
			
			
				$length=count($values); 
				$width=array_map( 'count',  $values); //394
							
				$model 				= new Fingerprint;
				$model->Name		= '';//GetTietle;		
				$model->Project_Id	= '';
				$model->DateCreated =date("Y-m-d H:i:s");
				$model->IsActive = 1;
				if(!$model->save())
				{
					$er = $model->getErrors();
					print_r($er);
				}
				
				ini_set("max_execution_time", "7200");
				
				for($rows=1; $rows <= $length; $rows++)
				{
						
						if($rows == 1) //Si es la primera vuelta, se recogen los snps
						{
							for($col_snp=4; $col_snp <= ($width[$rows]-1); $col_snp++ )
							{
							
								$array_snps[] = $values[$rows][$col_snp]; 
								$snp = SnpLab::find()
													->where(['LabName'=> $values[$rows][$col_snp]])
													->one();
								if($snp)
									$array_snps[$col_snp] = $snp->Snp_lab_Id;
								else
								{
									/*echo "El marcador ".$values[$rows][$col_snps]." no se encuentra cargado en la base de datos."; 
									$array_snps = array(); die();
									*/
										$array_snps[$col_snp]=rand(500, 600)*$col_snp; 
								}
								
							}
												
							
							$rows++; //rows 2
							for($col_qa=4; $col_qa <= ($width[$rows]-1); $col_qa++ )
							{
								if($values[$rows][$col_qa])
								{
									$array_vector= explode(', ', $values[$rows][$col_qa]);
									$array_quality[$col_qa] = json_encode($array_vector);
								}
							}
								
						}else // SI ES MAS DE LA 2da VUELTA, EMPIEZA A RECOGER LOS RESuLTAODS DE LOS FINGERPRINT
						{
							
							$FingerprintMaterial 	= new FingerprintMaterial;
							$FingerprintMaterial->IsActive = 1;
							$FingerprintMaterial->Observation = '';
						
							$Origin 				= new Origin;
							$Protocol 				= new Protocol;
							
							for($col_allele=0; $col_allele <  ($width[$rows]); $col_allele++ ) // rows=3 of fingerprint
							{
								
												
								if($col_allele < 4)	
								{			
								
									$FingerprintMaterial->Fingerprint_Id = $model->Fingerprint_Id;	
									
									if(isset($values[$rows][3])) // Si hay material en e excel
									{
										
											$Material = Material::find()
																->where(['Name'=>$values[$rows][3]])
																->one();
										
										if($Material)
										{
											$Origin = Origin::find()
																->where(['Origin_Id'=> $Material->Origin_Id])
																->one();
																					
											$FingerprintMaterial->Origin_Id = $Origin->Origin_Id==''? $rows : $Origin->Origin_Id;
											$FingerprintMaterial->Material_Id = $Material->Material_Id;
											$FingerprintMaterial->Pedigree	  = $Material->Pedigree;
											$FingerprintMaterial->TissueOrigin  = $Material->TissueOrigin;
										}else
										{									
								
											$FingerprintMaterial->Origin_Id = $values[$rows][$col_allele]==''?$rows*rand(200,400):$values[$rows][$col_allele];
											$FingerprintMaterial->Material_Id = $rows; //Preline
											$FingerprintMaterial->Pedigree	  = $values[$rows][1]; //row_line = 2 
											$FingerprintMaterial->TissueOrigin  = $values[$rows][2]; //row_line = 3 
										}
										
										$col_allele=3;							
									}else
									{						
										$FingerprintMaterial->Origin_Id = $values[$rows][$col_allele]==''?1:$values[$rows][$col_allele];
										$FingerprintMaterial->Material_Id = $rows; //Preline
										$FingerprintMaterial->Pedigree	  = $values[$rows][1]; //row_line = 2 ++
										$FingerprintMaterial->TissueOrigin  = $values[$rows][2]; //row_line = 3 ++
										$col_allele=3;
									}// if que controla la carga e encabezado del material y sus datos								
									if(!$FingerprintMaterial->save())
									{
											$er = $FingerprintMaterial->getErrors();
											print_r($er);
									}
																							
								
								}else
								{// END if si es col_allele es menor o igual a 4
								
								//col_allele=4
									$FingerprintResult = new FingerprintResult();
									
									if($rows == 3)
									{
										$FingerprintSnp	= new FingerprintSnp();
										$FingerprintSnp->Snp_lab_Id 	= $array_snps[$col_allele];
										$FingerprintSnp->Quality 		= $array_quality[$col_allele];
										$FingerprintSnp->Fingerprint_Id = $model->Fingerprint_Id;
										$FingerprintSnp->IsActive		= 1;
										if(!$FingerprintSnp->save())
										{
											$er = $FingerprintSnp->getErrors();
											print_r($er);
										}
																									
										$FingerprintResult->Fingerprint_Snp_Id = $FingerprintSnp->Fingerprint_Snp_Id;
																																			
										$array_Fingerprint_Id[$col_allele]= $FingerprintSnp->Fingerprint_Snp_Id;
									}else
										$FingerprintResult->Fingerprint_Snp_Id = $array_Fingerprint_Id[$col_allele];
										
									/*else
									{
										$FingerprintSnp = FingerprintSnp::findOne([
																	'Snp_lab_Id' => $array_snps[$col_allele],
																	'Fingerprint_Id' => $model->Fingerprint_Id,
															]);
									}*/
									
									
		
									$FingerprintResult->Fingerprint_Material_Id = $FingerprintMaterial->Fingerprint_Material_Id;
									
									//$FingerprintResult->Fingerprint_Snp_Id = $FingerprintSnp->Fingerprint_Snp_Id == '' ? $col_allele : $FingerprintSnp->Fingerprint_Snp_Id ;
									
									switch ($values[$rows][$col_allele]) {
											case "Allele 1":
													$FingerprintResult->Allele_Id = 1;
													break;
											case "Allele 2":
													$FingerprintResult->Allele_Id = 2;
													break;
											case "Allele 1 & 2":
													$FingerprintResult->Allele_Id = 3;
													break;
											default:
													$FingerprintResult->Allele_Id = 4;
													break;
									}
									if(!$FingerprintResult->save())
									{
											$er = $FingerprintResult->getErrors();
											print_r($er);
									}
								}
															
							}//for que recorre las filas a partir de las 3
						}// if que pregunta si la primera veulta
					
				}// for principal
				set_time_limit(60*100);
				print_r ("exito!!!!");	
				
	}
        
    public function actionDownloadTemplate()
    {
       
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename= template_finger.xlsx');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        //header('Content-Length: ' . filesize(Yii::$app->homeUrl."uploads/template_finger.xlsx"));
        ob_clean();
        flush();
        readfile(Yii::$app->homeUrl."uploads/template_finger.xlsx");
        exit;
    }

    public function actionExportErrors()
    {
        $sTitle = "Failed Markers";
        $oExcel = new PHPExcel();
        // Add some data
        $oExcel->setActiveSheetIndex(0);
        $oSheet = $oExcel->getActiveSheet();
        $oExcel->getProperties()->setTitle($sTitle);

        //HEADERS
        $charCol = 65;
        $row = 1;

        $vHeaders = array("MarkerID");
        
        foreach ($vHeaders as $key => $lbl){
                $oSheet->getColumnDimension(chr($charCol))->setAutoSize(true);
                $oSheet->SetCellValue(chr($charCol++).$row, $lbl);
        }
        $oSheet->getStyle(chr(65)."$row:".chr($charCol-1)."$row")->applyFromArray(
                array('fill' 	=> array(	'type'		=> PHPExcel_Style_Fill::FILL_SOLID,
                                                'color'		=> array('rgb' => 'FFFF00')
                                        )
        ));
        //print_r(Yii::$app->request->post()); exit;
        $row++;
        $mfail = Yii::$app->request->post();
        if(is_array($mfail))
        {   
           
            $fails = explode('#-#', $mfail['fails']);
            
            foreach ($fails as $val => $key)
            {
                $charCol = 65;
                $oSheet->SetCellValue(chr($charCol++).$row, $key);
                $row++;
            }
                  
        }else
        {
            echo "Could not perform the requested action.";
            exit;
        }
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=\"$sTitle.xls\"");
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($oExcel, 'Excel5');
        $objWriter->save('php://output'); 
    }
	
   }
