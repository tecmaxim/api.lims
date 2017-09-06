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

/**
 * FingerprintController implements the CRUD actions for Fingerprint model.
 */
class ImportController extends Controller
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
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex()
	{
		set_time_limit(60*10); //5 minutos
        ini_set ( "memory_limit" , - 1 );

        $time_start = microtime(true); 


        $connection = \Yii::$app->db;

        $fingerprint = new Fingerprint();
        $fingerprint->Name = 'test';
        $fingerprint->DateCreated = date('Y-m-d');
        $fingerprint->IsActive = 1;
        $fingerprint->save();

		// $file = 'C:/Advanta/FINGERPRINT_Short.csv';
		// $file = 'C:/Advanta/FINGERPRINT.csv';
		$file = 'C:/Advanta/Fingerprint_1000x1005.csv';
		$row = 1;
		$gestor = fopen($file, "r");

		$alleles = array(
			'Allele 1' => 1,
			'Allele 2' => 2,
			'Allele 1 & 2' => 3,
			'NA' => 4,
		);

	    $snpCsvFile = fopen("C:/Advanta/csvSnp.csv", 'w');
	    $materialCsvFile = fopen("C:/Advanta/csvMaterial.csv", 'w');

		$snpValues = array();
		$csvSnp = '';
		$i = 0;
		$test = '';
		while (($rowData = fgetcsv($gestor, 0, ";")) !== FALSE) 
		{
	        foreach($rowData as $col => $data)
	        {
	        	// SNPS
				if($row == 1)
				{
					$snpValues[$col] = $data;
				}
				else
				{
					if($row == 2)
					{
						if($col > 3)
						{
							$sql = "SELECT sl.Snp_lab_Id
									FROM snp_lab sl
									WHERE sl.LabName = '$snpValues[$col]'";
							$dataQuery = $connection->createCommand($sql)->queryOne();

							$csvSnp = $dataQuery['Snp_lab_Id'] .';' .$fingerprint->Fingerprint_Id .';' .$data .';1';
						    fwrite($snpCsvFile, $csvSnp ."\n");
						}

						// END SNPS
					}
					else
					{
	        			// MATERIALS
						if($col == 1)
							$pedigree = $data;
						if($col == 2)
							$sowingCode = $data;

						if($col >= 4)
						{
							if($pedigree != '')
							{
								if($col == 4)
								{
									$sql = "SELECT m.Material_Id
											FROM material m
											WHERE m.Pedigree = '$pedigree';";
									$dataQuery = $connection->createCommand($sql)->queryOne();

									$csvMaterial = ';' .$fingerprint->Fingerprint_Id .';' .$dataQuery['Material_Id'] .';;' .$sowingCode .';' .$pedigree .';1';
						    		fwrite($materialCsvFile, $csvMaterial ."\n");
								}

								// $sql = "SELECT fm.Fingerprint_Material_Id, fs.Fingerprint_Snp_Id
								// 		FROM fingerprint f
								// 			INNER JOIN fingerprint_material fm ON fm.Fingerprint_Id = f.Fingerprint_Id
								// 			INNER JOIN fingerprint_snp fs ON fs.Fingerprint_Id = f.Fingerprint_Id
								// 			INNER JOIN snp_lab sl ON sl.Snp_lab_Id = fs.Snp_lab_Id
								// 		WHERE 
								// 			f.Fingerprint_Id = 3
								// 			AND fm.Pedigree = '$pedigree'
								// 			AND sl.LabName = '$snpValues[$col]'";
								// $dataQuery = $connection->createCommand($sql)->queryOne();

								// $csvResult = $dataQuery['Fingerprint_Material_Id'] .';' .$dataQuery['Fingerprint_Snp_Id'] .';' .$alleles[$data];
					   //  		fwrite($resultCsvFile, $csvResult ."\n");
							}
						}
					}
	        	}
	        }

	        $row++;
	    }
	    fclose($snpCsvFile);
	    fclose($materialCsvFile);
	    $sql = "LOAD DATA INFILE 'C:/Advanta/csvSnp.csv' INTO TABLE fingerprint_snp  FIELDS TERMINATED BY ';' (Snp_lab_Id, Fingerprint_Id, Quality, IsActive);
	    		LOAD DATA INFILE 'C:/Advanta/csvMaterial.csv' INTO TABLE fingerprint_material  FIELDS TERMINATED BY ';' (Origin_Id, Fingerprint_Id, Material_Id, Observation, TissueOrigin, Pedigree, IsActive);";
		$connection->createCommand($sql)->execute();
	

		fseek($gestor, 0);
	    // $resultCsvFile = fopen("C:/Advanta/csvResult.csv", 'w');
	    $row = 1;
	    while (($rowData = fgetcsv($gestor, 0, ";")) !== FALSE) 
		{
	        foreach($rowData as $col => $data)
	        {
        		if($col == 1)
					$pedigree = $data;
				if($col == 2)
					$sowingCode = $data;
	        	if($row >= 3 && $col >= 4 && $pedigree != '')
				{
		        	$sql = "SELECT fm.Fingerprint_Material_Id, fs.Fingerprint_Snp_Id
							FROM fingerprint f
								INNER JOIN fingerprint_material fm ON fm.Fingerprint_Id = f.Fingerprint_Id
								INNER JOIN fingerprint_snp fs ON fs.Fingerprint_Id = f.Fingerprint_Id
								INNER JOIN snp_lab sl ON sl.Snp_lab_Id = fs.Snp_lab_Id
							WHERE 
								f.Fingerprint_Id = $fingerprint->Fingerprint_Id
								AND fm.Pedigree = '$pedigree'
								AND sl.LabName = '$snpValues[$col]'";
					$dataQuery = $connection->createCommand($sql)->queryOne();

					$csvResult = $dataQuery['Fingerprint_Material_Id'] .';' .$dataQuery['Fingerprint_Snp_Id'] .';' .$alleles[$data];
		    		// fwrite($resultCsvFile, $csvResult ."\n");
		    		echo $csvResult ."\n";
	        	}
	        }
	        $row++;
	    }
	    fclose($gestor);

	    // fclose($resultCsvFile);

	    $sql = "set foreign_key_checks = 0;
			LOAD DATA INFILE 'C:/Advanta/csvResult.csv' INTO TABLE fingerprint_result  FIELDS TERMINATED BY ';' (Fingerprint_Material_Id, Fingerprint_Snp_Id, Allele_Id);
			set foreign_key_checks = 1;";
		$connection->createCommand($sql)->execute();


		$time_end = microtime(true);
		$execution_time = ($time_end - $time_start);
		echo $execution_time;
	    exit();
	}

    public function actionIndex2()
    {
	set_time_limit(60*10); //5 minutos
        ini_set ( "memory_limit" , - 1 );

        $connection = \Yii::$app->db;
        $laQuery = "INSERT INTO fingerprint (Name, Project_Id, DateCreated, IsActive) VALUES ('','', '".date("Y-m-d H:i:s")."',1);
							SET @fingerprint_id = LAST_INSERT_ID();";
		$connection->createCommand($laQuery)->execute();

		// $file = 'C:/Advanta/FINGERPRINT_Short.csv';
		$file = 'C:/Advanta/Fingerprint_1000x1005.csv';
		$row = 1;
		$gestor = fopen($file, "r");

		$alleles = array(
			'Allele 1' => 1,
			'Allele 2' => 2,
			'Allele 1 & 2' => 3,
			'NA' => 4,
		);

		$snpValues = array();
		$sqlInsertSnp = "";
		$sqlInsertMaterial = "";
		$sqlSnp = "";
		$sqlMaterial = "";
		$sqlResult = [];
		$pedigree = '';
		$sowingCode = '';
		$comaResult = '';
		$i = 0;
		while (($rowData = fgetcsv($gestor, 0, ";")) !== FALSE) 
		{
	        foreach($rowData as $col => $data)
	        {
				if($row == 1)
				{
					$snpValues[$col] = $data;
				}
				else
				{
					if($row == 2)
					{
						if($col > 3)
						{
							$sqlSnp .= "INSERT INTO fingerprint_snp (Snp_lab_Id, Fingerprint_Id, Quality, IsActive) 
										SELECT sl.Snp_lab_Id, @fingerprint_id, '$data', 1
										FROM snp_lab sl
										WHERE sl.LabName = '$snpValues[$col]';
										SET @snp_id" .$col."= LAST_INSERT_ID();";

							$sqlInsertSnp .= "INSERT INTO snp(`Name`, `ShortSequence`, `LongSequence`, `PublicLinkageGroup`, `PublicCm`, `AdvLinkageGroup`, `AdvCm`, `PhysicalPosition`, `IsActive`) 
										VALUES ('" .$snpValues[$col] ."', '', NULL, 1, 34, 14, 32, NULL, 1);SET @sid= LAST_INSERT_ID();";

							$sqlInsertSnp .= "INSERT INTO `snp_lab` (`Snp_Id`, `LabName`, `PurchaseSequence`, `AlleleFam`, `AlleleVicHex`, `ValidatedStatus`, `Quality`, `Box`, `PositionInBox`, `PIC`, `IsActive`, `Observation`) 
										VALUES (@sid, '" .$snpValues[$col] ."', '', 'A', 'G', NULL, NULL, 'Box 1', 'H12', NULL, 1, NULL);";
						}
					}
					else
					{
						if($col == 1)
							$pedigree = $data;
						if($col == 2)
							$sowingCode = $data;

						if($col >= 3)
						{
							if($pedigree != '')
							{
								if($col == 3)
								{
										$sqlMaterial .= "INSERT INTO fingerprint_material (Fingerprint_Id, Material_Id, TissueOrigin, IsActive, Observation) 
													SELECT @fingerprint_id, m.Material_Id, '$sowingCode', 1, ''
													FROM material m
													WHERE m.Pedigree = '$pedigree';
												SET  @material_id".$row." = LAST_INSERT_ID();";

										$sqlInsertMaterial .= "INSERT INTO material (`Crop_Id`, `Origin_Id`, `Material_Type_Id`, `seedBreederId`, `Name`, `Pedigree`, `IsActive`, `Observations`, `IsMaterial`) 
																VALUES ('1', '1', '1', '1', '" .$sowingCode ."', '" .$pedigree ."', 1, '', 1);";
								}

								if($i == 0 || $i % 20000 == 0)
								{
									if($i > 0)
										$sqlResult[] = $sql .';';
									$sql = "INSERT INTO fingerprint_result (Fingerprint_Material_Id, Fingerprint_Snp_Id, Allele_Id) VALUES";
									$comaResult = '';
								}

								$sql .= $comaResult ." (@material_id" .$row .", @snp_id" .$col .", " .$alleles[$data] .")";

								$comaResult = ', ';
								$i++;
								// $sqlResult .= 'SET @material_id' .$i++ .' = null;';
							}
						}
					}
				}
	        }

	        $row++;
	    }
	    fclose($gestor);

	    // echo $laQuery;
		// echo $sqlSnp;
		// echo $sqlMaterial;
		// echo $sqlResult[1];
		// echo count($sqlResult);
	    // echo $sqlInsertSnp;
	    // echo $sqlInsertMaterial;
	    exit();

	    $laQuery = "INSERT INTO fingerprint (Name, Project_Id, DateCreated, IsActive) VALUES ('','', '".date("Y-m-d H:i:s")."',1);
							SET @fingerprint_id = LAST_INSERT_ID();";

		// $connection->createCommand($laQuery)->execute();
		// $connection->createCommand($sqlSnp)->execute();
		// $connection->createCommand($sqlMaterial)->execute();
		// foreach ($sqlResult as $sql)
			// $connection->createCommand($sql)->execute();
		exit();
	}
}
