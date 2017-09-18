<?php 
	error_reporting(0);
	if(!isset($_POST)) { 
		echo "You must fill the generate report form.";
		echo "<br /><a href='/export_module'>&laquo; Go to generate report form</a>";
		die; 
	}
	$post_id="";
	$module_id=$_POST['module_id'];
	$startDate=$_POST['startDate'];
	$endDate=$_POST['endDate'];
	
	if($module_id==1) { $module_name="Colonoscopy";} 
	else if($module_id==2) { $module_name="Upper Endoscopy";} 
	else if($module_id==3) { $module_name="Failure to Thrive";} 
	else if($module_id==4) { $module_name="Informed Consent";} 
	else if($module_id==5) { $module_name="Constipation";} 
	else if($module_id==6) { $module_name="Transition";} 
	else if($module_id==7) { $module_name="Enteral Nutrition";} 
	else if($module_id==8) { $module_name="Hepatitis B";}
	
	include "connect.php";
	include "report_inc/mod".$module_id.".php";
	include "classes/PHPExcel.php";
	include "classes/PHPExcel/IOFactory.php";
	
	function getNameFromNumber($num) {
		$numeric = ($num - 1) % 26;
		$letter = chr(65 + $numeric);
		$num2 = intval(($num - 1) / 26);
		if ($num2 > 0) {
			return getNameFromNumber($num2) . $letter;
		} else {
			return $letter;
		}
	}
	
	$objPHPExcel = new PHPExcel();
	
	for($b=0;$b<3;$b++)	{
		$c=$b+1;
		$objPHPExcel->createSheet($b);
		$objPHPExcel->setActiveSheetIndex($b); 
		$objPHPExcel->getActiveSheet()->setTitle($module_name." Data ".$c);
		$i=0;
		$whereQuiz="";
		
		$first_quiz_id=$module[$mod_number]['cs'.$c][1];
		foreach($module[$mod_number]['cs'.$c] as $quiz_id){
			if($i==0){
				$whereQuiz="quiz_id='".$quiz_id."'";
			} else {
				$whereQuiz=$whereQuiz." OR quiz_id='".$quiz_id."'";
			}
			$i++;
		}
		
		$whereUser="";
		if($_POST['select_user']=="specific_user"){
			if(empty($_POST['email'])) { die; }
			
			$i=0;
			foreach($_POST['email'] as $email){
				if($i==0){
					$whereUser="email='".$email."'";
				} else {
					$whereUser=$whereUser." OR email='".$email."'";
				}
				$i++;
			}
		}
		
		$whereDate="";
		if($startDate!="" && $endDate!=""){
			$sd= substr($startDate,6,4)."-".substr($startDate,0,2)."-".substr($startDate,3,2);
			$ed= substr($endDate,6,4)."-".substr($endDate,0,2)."-".substr($endDate,3,2);
			$whereDate=" AND date(time_taken_real) >= '$sd' AND date(time_taken_real) < '$ed'";
		}
		
		if($whereUser!=""){ $whereUser="AND ($whereUser)";}

		$sql_base="SELECT * FROM wp_mlw_results WHERE ($whereQuiz) $whereUser $whereDate";
		$result = mysql_query($sql_base);
		if($result === FALSE) { die(mysql_error()); }
		
		$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Timestamp'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('B1', 'User ID	'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Firstname'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('D1', 'Lastname'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('E1', 'Business (ABP ID)'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('F1', 'Email'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('G1', 'Lesson'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('H1', 'Data');
		$objPHPExcel->getActiveSheet()->SetCellValue('I1', 'Patient');
		
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(18);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(8);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(16);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(35);
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(25);
		$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
		
		$x=1;
		$y=9;
		$question1 = mysql_query("SELECT * FROM wp_mlw_questions WHERE quiz_id='$first_quiz_id'");
		while ($q = mysql_fetch_array($question1)){
			$y++;
			$objPHPExcel->getActiveSheet()->SetCellValue(getNameFromNumber($y).'1', 'Question'.$x); 
			$objPHPExcel->getActiveSheet()->getColumnDimension(getNameFromNumber($y))->setWidth(30);
			$y++;
			$objPHPExcel->getActiveSheet()->SetCellValue(getNameFromNumber($y).'1', 'Question'.$x.' Response'); 
			$objPHPExcel->getActiveSheet()->getColumnDimension(getNameFromNumber($y))->setWidth(20);
			$y++;
			$objPHPExcel->getActiveSheet()->SetCellValue(getNameFromNumber($y).'1', 'Question'.$x.' Point');
			$objPHPExcel->getActiveSheet()->getColumnDimension(getNameFromNumber($y))->setWidth(16);
			$x++;
		}
		
		$styleTitle = array(
			'font' => array(
				'bold' => true,
			),
			'borders' => array(
				'allborders' => array(
				  'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			)
		);
		$objPHPExcel->getActiveSheet()->getStyle('A1:'.getNameFromNumber($y).'1')->applyFromArray($styleTitle);
		
		$rowCount = 2;
		while ($row = mysql_fetch_array($result)){
			$quiz_id=$row['quiz_id'];
			$quiz_result = mysql_query("SELECT quiz_name FROM wp_mlw_quizzes WHERE quiz_id='$quiz_id' ");
			
			if($quiz_result){
				$quiz = mysql_fetch_array($quiz_result);
				$parts = explode(" ", $row['name']);
				$lastname = array_pop($parts);
				$firstname = implode(" ", $parts);
				
				$quiz_name = explode(" - ", $row['quiz_name']);
				$patient = array_pop($quiz_name);
				$survey_data = implode(" - ", $quiz_name);
				
				$objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $row['time_taken_real']); 
				$objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $row['user']); 
				$objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $firstname); 
				$objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $lastname); 
				$objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $row['business']); 
				$objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $row['email']); 
				$objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, $module[$mod_number]['lesson']['cs'.$c]); 
				$objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, $survey_data); 
				$objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount, $patient); 
				
				$y=9;
				$quiz_results=unserialize(utf8_encode($row['quiz_results']));
				foreach($quiz_results[1] as $q_result){
					$y++;
					$objPHPExcel->getActiveSheet()->SetCellValue(getNameFromNumber($y).$rowCount, $q_result[0]); 
					$y++;
					$objPHPExcel->getActiveSheet()->SetCellValue(getNameFromNumber($y).$rowCount, $q_result[1]); 
					$y++;
					$objPHPExcel->getActiveSheet()->SetCellValue(getNameFromNumber($y).$rowCount, $q_result['points']); 
				}
				
				$rowCount++;
				
				if($rowCount>10000){
					echo "The number of reported records will exceed 10,000. Please use a narrower date range.";
					echo "<br /><a href='/export_module'>&laquo; Go to generate report form</a>";
					die;
				}
			}
		}
		
		$allrow=$rowCount-1;
		$styleBody = array(
			'borders' => array(
				'allborders' => array(
				  'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			)
		);
		$objPHPExcel->getActiveSheet()->getStyle('A2:'.getNameFromNumber($y).$allrow)->applyFromArray($styleBody);
		
	}
	
	$objPHPExcel->setActiveSheetIndex(0);
	//die;
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="'.$module_name.'-'.date('YmdHis').'.xls"');
	header('Cache-Control: max-age=0');
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output');
?>