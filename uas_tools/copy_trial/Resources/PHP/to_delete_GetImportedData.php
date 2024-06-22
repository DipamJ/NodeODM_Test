<?php

	require_once("SetDBConnection.php");
	ini_set('display_errors', 1);
		
	$con = SetDBConnection();
		
	if(mysqli_connect_errno())
	{
		echo "Failed to connect to database server: ".mysqli_connect_error();
	}
	else
	{
		//session_start();
	
		if(isset($_GET['dataset'])) {
	   	 $datasetID = $_GET['dataset'];
		} else {
    		$datasetID = '';
		}				

		//$datasetID = 30;//$_GET["dataset"];
		//$conditions = $_GET["conditions"];
    		//$conditions = $_REQUEST["conditions"];
		if(isset($_REQUEST['conditions'])) {
         	$conditions = $_REQUEST['conditions'];
        	} else {	

        	//conditions = '%';
			$conditions = '%';

      	 	}	

		
		//_log('datasetID ' .$datasetID);


		_log('conditions ' .$conditions);

	

		//$conditions = $_GET["conditions"];
		//var_dump($conditions);//["Value"]);		
		//$sql = "SELECT * FROM row_data where CropDataSet = $datasetID";
		/*
		$sql = 	"SELECT row_data.ID FROM row_data, criteria_data ". 
				"WHERE criteria_data.RowDataSet = row_data.ID and row_data.CropDataSet = $datasetID and ".
				"criteria_data.Name = '".$condition["Name"]."' and criteria_data.Value like '".$condition["Value"]."'";
		$result = mysqli_query($con,$sql);
		*/

		$rowDatasetList = array();
		$firstSet = true;
		foreach ($conditions as $condition){
			//if($condition["Value"] != "%"){
		    $values = isset($condition['Value']) ? $condition['Value'] : '';
			if($condition != "%"){
        		//if($values != "%"){
			//var_dump($values);
			$sql = 	"SELECT row_data.ID FROM row_data, criteria_data ". 
				"WHERE criteria_data.RowDataSet = row_data.ID and row_data.CropDataSet = $datasetID and ".
				"criteria_data.Name = '".$condition["Name"]."' and criteria_data.Value like '".$values."'";// '".$condition["Value"]."'";
				            _log('select row_data 1: '.$sql);
			
	
			$resultArray = array();
			$result = mysqli_query($con,$sql);
			while($row = mysqli_fetch_array($result)){
				$resultArray[] = $row["ID"];
			}
			
			/*
			if (count($rowDatasetList) > 0 ) {
				$rowDatasetList = array_intersect($rowDatasetList, $resultArray); 
			}else {
				$rowDatasetList = $resultArray;
			}
			*/
			if ($firstSet) {
				$rowDatasetList = $resultArray;
				$firstSet = false;
			}else {
				$rowDatasetList = array_intersect($rowDatasetList, $resultArray); 
			}
			}

		}
		
		$dataList = array();
		//while($row = mysqli_fetch_array($result)) 
		foreach ($rowDatasetList as $rowDataset)
		{
		
			$rowData = array();
			$sql = "SELECT Name, Value FROM criteria_data where RowDataSet = ".$rowDataset;
			_log('select row_data 2: '.$sql);
			$criteriaResult = mysqli_query($con,$sql);
			while($criteriaValue = mysqli_fetch_array($criteriaResult)){
				$rowData[] = array("criteria_".$criteriaValue["Name"]=>$criteriaValue["Value"]) ;
			}
			
			$sql = "SELECT Name, Value FROM value_data where RowDataSet = ".$rowDataset;
			_log('select row_data 3: '.$sql);
			$valueResult = mysqli_query($con,$sql);
			while($dataValue = mysqli_fetch_array($valueResult)){
				$rowData[] =  array("data_".$dataValue["Name"]=>$dataValue["Value"]);
			}
			
			$dataList[] = $rowData;
		}
		
		mysqli_close($con);
		echo json_encode($dataList);
	}
?>

