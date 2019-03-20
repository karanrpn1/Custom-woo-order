<?php
	
	$pdf = new TCPDF('P', PDF_UNIT, 'A3', true, 'UTF-8', false);
 
	// set document information
	$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetAuthor('Karan');
	$pdf->SetTitle('Weekly Order');
	$pdf->SetSubject('Weekly Order');
	$pdf->SetKeywords('Weekly Order');
	
	$pdf->setPrintHeader(false);
	$pdf->setPrintFooter(false);	
	
	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

	$pdf->SetMargins(PDF_MARGIN_LEFT, 15, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
	
	$pdf->SetAutoPageBreak(TRUE, 0);

	// set image scale factor
	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
	
	// set some language-dependent strings (optional)
	if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
		require_once(dirname(__FILE__).'/lang/eng.php');
		$pdf->setLanguageArray($l);
	}	
	
	$pdf->AddPage();
	
	$logo = ( $user_logo = et_get_option( 'divi_logo' ) ) && '' != $user_logo
					? $user_logo
					: $template_directory_uri . '/images/logo.png';
					
	$pdf->Image($logo, '', '', 70, 30, '', '', 'T', false, 300, '', false, false, 1, false, false, false);
	
	$pdf->SetFont('leckerlione', '', 36, '', false);
	
	$pdf->SetTextColor('132','188','61');
	
	$y = $pdf->getY();
	
	$pdf->SetFillColor(255, 255, 255);
	
	$pdf->writeHTMLCell(80, '', '', $y, "", 0, 0, 1, true, 'J', true);
			
	$pdf->writeHTMLCell(150, '', '', '', "Meal Summary", 0, 1, 1, true, 'C', true);	
	
	$pdf->SetFont('leckerlione', '', 24, '', false);
	 
	$pdf->SetTextColor('0','0','0');
	
	$y = $pdf->getY();
	
	$pdf->SetFillColor(255, 255, 255);
	
	$pdf->writeHTMLCell(80, '', '', $y, "", 0, 0, 1, true, 'J', true);
			
	$pdf->writeHTMLCell(150, '', '', '', "All Customers", 0, 1, 1, true, 'C', true);
	
	$pdf->SetFont('helvetica', '', 14);	
	
	$y = $pdf->getY();
	
	$pdf->SetFillColor(255, 255, 255);
	
	$pdf->writeHTMLCell(80, '', '', $y, "", 0, 0, 1, true, 'J', true);
			
	$pdf->writeHTMLCell(150, '', '', '', '<i>From '.date("jS M Y",strtotime("next monday")).' to '.date('jS M Y', strtotime("next friday")).'</i>', 0, 1, 1, true, 'C', true);
	
	$pdf->Ln(10);
	
	$pdf->writeHTML('<u style="text-align:center;"><b>Regular Menu Totals</b></u>', true, true, true, true, '');	
	
	$pdf->Ln(5);	
	
	global $wpdb; 
	$tablename = $wpdb->prefix.'order_info';
	$orderInfo = $wpdb->get_results( "SELECT * FROM $tablename where status=1");
	
	$fullMenuHtml = '<table border="1" cellpadding="10">
			<thead>
				<tr style="background-color:#84bc3d;color:#fff;">
					<td colspan="2"></td>
					<td colspan="4"></td>
					<td>'.date("jS-M", strtotime("next monday")).'</td>
					<td>'.date("jS-M", strtotime("next tuesday")).'</td>
					<td>'.date("jS-M", strtotime("next wednesday")).'</td>
					<td>'.date("jS-M", strtotime("next thursday")).'</td>
					<td>'.date("jS-M", strtotime("next friday")).'</td>
				</tr>
			</thead> 
			<tbody>';
	
	$lunchOnlyHtml = '<table border="1" cellpadding="10">
			<thead>
				<tr style="background-color:#84bc3d;color:#fff;">
					<td colspan="2"></td>
					<td colspan="4"></td>
					<td>'.date("jS-M", strtotime("next monday")).'</td>
					<td>'.date("jS-M", strtotime("next tuesday")).'</td>
					<td>'.date("jS-M", strtotime("next wednesday")).'</td>
					<td>'.date("jS-M", strtotime("next thursday")).'</td>
					<td>'.date("jS-M", strtotime("next friday")).'</td>
				</tr>
			</thead>
			<tbody>';
	
	$toddlerHtml = '<table border="1" cellpadding="10">
			<thead>
				<tr style="background-color:#84bc3d;color:#fff;">
					<td colspan="2"></td>
					<td colspan="4"></td>
					<td>'.date("jS-M", strtotime("next monday")).'</td>
					<td>'.date("jS-M", strtotime("next tuesday")).'</td>
					<td>'.date("jS-M", strtotime("next wednesday")).'</td>
					<td>'.date("jS-M", strtotime("next thursday")).'</td>
					<td>'.date("jS-M", strtotime("next friday")).'</td>
				</tr>
			</thead>
			<tbody>';
			
	$ooshHtml = '<table border="1" cellpadding="10">
			<thead>
				<tr style="background-color:#84bc3d;color:#fff;">
					<td colspan="2"></td>
					<td colspan="4"></td>
					<td>'.date("jS-M", strtotime("next monday")).'</td>
					<td>'.date("jS-M", strtotime("next tuesday")).'</td>
					<td>'.date("jS-M", strtotime("next wednesday")).'</td>
					<td>'.date("jS-M", strtotime("next thursday")).'</td>
					<td>'.date("jS-M", strtotime("next friday")).'</td>
				</tr>
			</thead>
			<tbody>';
	
	$allergyMenuHtml = '<table border="1" cellpadding="10">
		<thead>
			<tr style="background-color:#84bc3d;color:#fff;">
				<td colspan="2">Centre</td>
				<td colspan="4">Allergen Group</td>
				<td>'.date("jS-M", strtotime("next monday")).'</td>
				<td>'.date("jS-M", strtotime("next tuesday")).'</td>
				<td>'.date("jS-M", strtotime("next wednesday")).'</td>
				<td>'.date("jS-M", strtotime("next thursday")).'</td>
				<td>'.date("jS-M", strtotime("next friday")).'</td>
			</tr>
		</thead>
		<tbody>';
		
	$toddlerToggleHtml = '<table border="1" cellpadding="10">
		<thead>
			<tr style="background-color:#84bc3d;color:#fff;" >
				<td colspan="2">Centre</td> 
				<td colspan="4">Allergen Group</td>
				<td>'.date("jS-M", strtotime("next monday")).'</td>
				<td>'.date("jS-M", strtotime("next tuesday")).'</td>
				<td>'.date("jS-M", strtotime("next wednesday")).'</td>
				<td>'.date("jS-M", strtotime("next thursday")).'</td>
				<td>'.date("jS-M", strtotime("next friday")).'</td>
			</tr>
		</thead> 
		<tbody>';
	
	$fullMenuMondayTotal = 0;
	$fullMenuTuesdayTotal = 0;	
	$fullMenuWednesdayTotal = 0;
	$fullMenuThursdayTotal = 0;	
	$fullMenuFridayTotal = 0;			
	
	$lunchMondayTotal = 0;
	$lunchTuesdayTotal = 0;	
	$lunchWednesdayTotal = 0;	
	$lunchThursdayTotal = 0;	
	$lunchFridayTotal = 0;	
	
	$toddlerMondayTotal = 0;
	$toddlerTuesdayTotal = 0;	
	$toddlerWednesdayTotal = 0;	
	$toddlerThursdayTotal = 0;	
	$toddlerFridayTotal = 0;
	
	$ooshMondayTotal = 0;
	$ooshTuesdayTotal = 0;	
	$ooshWednesdayTotal = 0;	
	$ooshThursdayTotal = 0;	
	$ooshFridayTotal = 0;	
	
	$toddlerToggleMondayTotal = 0;
	$toddlerToggleTuesdayTotal = 0;	
	$toddlerToggleWednesdayTotal = 0;	
	$toddlerToggleThursdayTotal = 0;	
	$toddlerToggleFridayTotal = 0;	
	
	$allergyMonday = 0;	
	$allergyTuesday = 0;	
	$allergyWednesday = 0;	
	$allergyThursday = 0;	
	$allergyFriday = 0;				
	
	$toddlerMonday = 0;	
	$toddlerTuesday = 0;	
	$toddlerWednesday = 0;	
	$toddlerThursday = 0;	
	$toddlerFriday = 0;	
	
	
	
	foreach ($orderInfo as $row ) {
		
		$order = wc_get_order( $row->order_id );
		$items = $order->get_items();
		
		$order_data = $order->get_data();	
		if(!empty($order_data['shipping']['state'])) {
			$customerStateCode = $order_data['shipping']['state'];
		}
		else {
			$customerStateCode = $order_data['billing']['state'];
		}	 
		
		if($stateCode<>"all" && $customerStateCode<>$stateCode) { continue; }	
			 
		$order_status = $order_data['status'];		
		if($order_status<>"processing") { continue;}	
		
		$closeDateTable = $wpdb->prefix.'close_date_info';
		$closeDateInfo = $wpdb->get_results( "SELECT * FROM $closeDateTable where user_id=$row->user_id");
		$checkCloseDate = 0;
		 
		$mondayClose = "";
		$tuesdayClose = "";
		$wednesdayClose = "";
		$thursdayClose = "";
		$fridayClose = "";
		foreach($closeDateInfo as $closeDateInfoRow) {
			
			if(!empty($closeDateInfoRow->endDate)) {
				$startDate = $closeDateInfoRow->startDate;
				$endDate = $closeDateInfoRow->endDate;
				$dateArr = array();
				for($i=$startDate;$i<=$endDate;$i=$i+86400) {
					$dateArr[]=$i;
				}				
				
				$loopCurrentDate = strtotime('next monday'); 
				$loopEndDate = $loopCurrentDate+(4*86400);				
				
				for($i=$loopCurrentDate;$i<=$loopEndDate;$i=$i+86400) {
					if(in_array($i,$dateArr)) {					
						
						$dayofweek = date('w', $i);
						if($dayofweek==1) {
							$mondayClose = "Closed";
						}
						elseif($dayofweek==2) {
							$tuesdayClose = "Closed";
						}
						elseif($dayofweek==3) {
							$wednesdayClose = "Closed";
						}
						elseif($dayofweek==4) {
							$thursdayClose = "Closed";
						}
						elseif($dayofweek==5) {
							$fridayClose = "Closed";
						}
						
					}
				}	
											
			}
			else {
			
				$startDate = $closeDateInfoRow->startDate;				
				
				$loopCurrentDate = strtotime('next monday');
				$loopEndDate = $loopCurrentDate+(4*86400);				
				
				for($i=$loopCurrentDate;$i<=$loopEndDate;$i=$i+86400) {
					if($i==$startDate) {
						$dayofweek = date('w', $i);
						if($dayofweek==1) {
							$mondayClose = "Closed";
						}
						elseif($dayofweek==2) {
							$tuesdayClose = "Closed";
						}
						elseif($dayofweek==3) {
							$wednesdayClose = "Closed";
						}
						elseif($dayofweek==4) {
							$thursdayClose = "Closed";
						}
						elseif($dayofweek==5) {
							$fridayClose = "Closed";
						}
					}
				}	
											
			}
		}	
		
		$user = get_user_by( 'id', $row->user_id );	
		
		foreach( $items as $key => $item){
			$item_id = $item['product_id'];
			$product = new WC_Product($item_id);
			$item_sku = $product->get_sku(); 
			$chkSku = explode("-",$item_sku);
			if($chkSku[0]=="fullmenu") {
				$menuName = "Full Menu";
			}
			if($chkSku[0]=="lunchonly") {
				$menuName = "Lunch Only";
			}
			if($chkSku[0]=="toddler") {
				$menuName = "Toddler Menu";
			}
			if($chkSku[0]=="oosh") {
				$menuName = "OOSH (out of school hours)";
			}
			
			if($chkSku[1]=="monday") {
				if(!empty($mondayClose)) {
					$mondayValue = 0 ;		
				}
				else {					
					$mondayValue = $item['qty'];			
				}				
			}
			elseif($chkSku[1]=="tuesday") {
				if(!empty($tuesdayClose)) {
					$tuesdayValue = 0 ;		
				}
				else {					
					$tuesdayValue = $item['qty'];			
				}
			}
			elseif($chkSku[1]=="wednesday") {
				if(!empty($wednesdayClose)) {
					$wednesdayValue = 0 ;		
				}
				else {					
					$wednesdayValue = $item['qty'];			
				}				
			}
			elseif($chkSku[1]=="thursday") {
				if(!empty($thursdayClose)) {
					$thursdayValue = 0 ;		
				}
				else {					
					$thursdayValue = $item['qty'];			
				}
			}
			elseif($chkSku[1]=="friday") {
				if(!empty($fridayClose)) {
					$fridayValue = 0 ;		
				}
				else {					
					$fridayValue = $item['qty'];			
				}				
			}
		}	
		
		if($menuName=="Full Menu") {
			
			$childCount = get_post_meta( $row->order_id, 'child_count', true );
			
			for($i=1;$i<=$childCount;$i++){
				$allergyGroup ="";
				$toddlerGroup ="";
				$attend = json_decode(get_post_meta( $row->order_id, 'child_attend_'.$i, true ));
				
				if(get_post_meta( $row->order_id, 'child_toddler_toggle_'.$i, true )==0) {
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_dairy_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_dairy_'.$i, true )<>"null")) {
						$allergyGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_dairy_'.$i, true )) ).", ";
					}
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_egg_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_egg_'.$i, true )<>"null")) {
						$allergyGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_egg_'.$i, true ))).", ";
					}			
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_vegetarian_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_vegetarian_'.$i, true )<>"null") ) {
						$allergyGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_vegetarian_'.$i, true ))).", ";
					}
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_grains_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_grains_'.$i, true )<>"null")) {
						$allergyGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_grains_'.$i, true ))).", ";
					}
					/* added by comfusion */
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_meat_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_meat_'.$i, true )<>"null")) {
						$allergyGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_meat_'.$i, true ))).", ";
					}
					/* end modification */
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_seafood_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_seafood_'.$i, true )<>"null")) {
						$allergyGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_seafood_'.$i, true ))).", ";
					}
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_soyproduct_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_soyproduct_'.$i, true )<>"null")) {
						$allergyGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_soyproduct_'.$i, true ))).", ";
					}
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_legumes_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_legumes_'.$i, true )<>"null")) {
						$allergyGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_legumes_'.$i, true ))).", ";
					}
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_fruits_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_fruits_'.$i, true )<>"null")) {
						$allergyGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_fruits_'.$i, true ))).", ";
					}
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_vegetables_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_vegetables_'.$i, true )<>"null")) {
						$allergyGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_vegetables_'.$i, true ))).", ";
					}
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_nuts_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_nuts_'.$i, true )<>"null")) {
						$allergyGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_nuts_'.$i, true ))).", ";
					}
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_others_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_others_'.$i, true )<>"null")) {
						$allergyGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_others_'.$i, true ))).", ";
					}
					
					if(!empty(get_post_meta( $row->order_id, 'child_remark_'.$i, true )) && (get_post_meta( $row->order_id, 'child_remark_'.$i, true )<>"null")) {
						$allergyGroup = rtrim($allergyGroup, ', ' ).'<br>';
						$allergyGroup.= get_post_meta( $row->order_id, 'child_remark_'.$i, true );
					}  
					else {
						$allergyGroup = rtrim($allergyGroup, ', ' );
					}
					
					$childName ="";
					if(!empty(get_post_meta( $row->order_id, 'child_id_number_'.$i, true ))) {
						$childName.= get_post_meta( $row->order_id, 'child_id_number_'.$i, true )." - ";
					}
					if(!empty(get_post_meta( $row->order_id, 'child_name_'.$i, true ))) {
						$childName.= get_post_meta( $row->order_id, 'child_name_'.$i, true );
					}
					if(!empty(get_post_meta( $row->order_id, 'child_roomNumber_'.$i, true ))) {
						$childName.= " - ".get_post_meta( $row->order_id, 'child_roomNumber_'.$i, true );
					}	
					
					
					if(in_array("Monday",$attend)) {
						if(empty($mondayClose)) {
							$allergyMonday++;
						}
					}
					if(in_array("Tuesday",$attend)) {
						if(empty($tuesdayClose)) {
							$allergyTuesday++;
						}
					}
					if(in_array("Wednesday",$attend)) {
						if(empty($wednesdayClose)) {
							$allergyWednesday++;
						}
					}
					if(in_array("Thursday",$attend)) {
						if(empty($thursdayClose)) {
							$allergyThursday++;
						}
					}
					if(in_array("Friday",$attend)) {
						if(empty($fridayClose)) {
							$allergyFriday++;
						}
					}
					$allergyMenuHtml.= '<tr>
						<td colspan="2">'.$childName.'</td>
						<td colspan="4">'.$allergyGroup.'</td>
						<td>'.(!empty($mondayClose)?$mondayClose:(in_array("Monday",$attend))?"1":"").'</td>
						<td>'.(!empty($tuesdayClose)?$tuesdayClose:(in_array("Tuesday",$attend))?"1":"").'</td>
						<td>'.(!empty($wednesdayClose)?$wednesdayClose:(in_array("Wednesday",$attend))?"1":"").'</td>
						<td>'.(!empty($thursdayClose)?$thursdayClose:(in_array("Thursday",$attend))?"1":"").'</td>
						<td>'.(!empty($fridayClose)?$fridayClose:(in_array("Friday",$attend))?"1":"").'</td>
					</tr>';	
				}
				else {
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_dairy_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_dairy_'.$i, true )<>"null")) {
						$toddlerGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_dairy_'.$i, true )) ).", ";
					}
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_egg_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_egg_'.$i, true )<>"null")) {
						$toddlerGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_egg_'.$i, true ))).", ";
					}			
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_vegetarian_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_vegetarian_'.$i, true )<>"null") ) {
						$toddlerGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_vegetarian_'.$i, true ))).", ";
					}
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_grains_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_grains_'.$i, true )<>"null")) {
						$toddlerGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_grains_'.$i, true ))).", ";
					}
					/* added by comfusion */
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_meat_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_meat_'.$i, true )<>"null")) {
						$toddlerGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_meat_'.$i, true ))).", ";
					}
					/* end modification */
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_seafood_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_seafood_'.$i, true )<>"null")) {
						$toddlerGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_seafood_'.$i, true ))).", ";
					}
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_soyproduct_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_soyproduct_'.$i, true )<>"null")) {
						$toddlerGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_soyproduct_'.$i, true ))).", ";
					}
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_legumes_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_legumes_'.$i, true )<>"null")) {
						$toddlerGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_legumes_'.$i, true ))).", ";
					}
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_fruits_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_fruits_'.$i, true )<>"null")) {
						$toddlerGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_fruits_'.$i, true ))).", ";
					}
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_vegetables_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_vegetables_'.$i, true )<>"null")) {
						$toddlerGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_vegetables_'.$i, true ))).", ";
					}
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_nuts_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_nuts_'.$i, true )<>"null")) {
						$toddlerGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_nuts_'.$i, true ))).", ";
					}
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_others_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_others_'.$i, true )<>"null")) {
						$toddlerGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_others_'.$i, true ))).", ";
					}
					
					if(!empty(get_post_meta( $row->order_id, 'child_remark_'.$i, true )) && (get_post_meta( $row->order_id, 'child_remark_'.$i, true )<>"null")) {
						$toddlerGroup = rtrim($toddlerGroup, ', ' ).'<br>';
						$toddlerGroup.= get_post_meta( $row->order_id, 'child_remark_'.$i, true );
					}  
					else {
						$toddlerGroup = rtrim($toddlerGroup, ', ' );
					}
					
					$childName = "";
					if(!empty(get_post_meta( $row->order_id, 'child_id_number_'.$i, true ))) {
						$childName.= get_post_meta( $row->order_id, 'child_id_number_'.$i, true )." - ";
					}
					if(!empty(get_post_meta( $row->order_id, 'child_name_'.$i, true ))) {
						$childName.= get_post_meta( $row->order_id, 'child_name_'.$i, true );
					}
					if(!empty(get_post_meta( $row->order_id, 'child_roomNumber_'.$i, true ))) {
						$childName.= " - ".get_post_meta( $row->order_id, 'child_roomNumber_'.$i, true );
					}	
					
					if(in_array("Monday",$attend)) {
						if(empty($mondayClose)) {
							$toddlerMonday++;
						}
					}
					if(in_array("Tuesday",$attend)) {
						if(empty($tuesdayClose)) {
							$toddlerTuesday++;
						}
					}
					if(in_array("Wednesday",$attend)) {
						if(empty($wednesdayClose)) {
							$toddlerWednesday++;
						}
					}
					if(in_array("Thursday",$attend)) {
						if(empty($thursdayClose)) {
							$toddlerThursday++;
						}
					}
					if(in_array("Friday",$attend)) {
						if(empty($fridayClose)) {
							$toddlerFriday++;
						}
					}
					
					$toddlerToggleHtml.= '<tr>
						<td colspan="2">'.$childName.'</td>
						<td colspan="4">'.$toddlerGroup.'</td>
						<td>'.(!empty($mondayClose)?$mondayClose:(in_array("Monday",$attend))?"1":"").'</td>
						<td>'.(!empty($tuesdayClose)?$tuesdayClose:(in_array("Tuesday",$attend))?"1":"").'</td>
						<td>'.(!empty($wednesdayClose)?$wednesdayClose:(in_array("Wednesday",$attend))?"1":"").'</td>
						<td>'.(!empty($thursdayClose)?$thursdayClose:(in_array("Thursday",$attend))?"1":"").'</td>
						<td>'.(!empty($fridayClose)?$fridayClose:(in_array("Friday",$attend))?"1":"").'</td>
					</tr>';	
				}
				
				
			} 			
			
			$fullMenuHtml.= '<tr>
				<td colspan="2">'.$menuName.'</td>
				<td colspan="4">'.$user->billing_company.'</td>
				<td>'.(!empty($mondayClose)?$mondayClose:$mondayValue).'</td>
				<td>'.(!empty($tuesdayClose)?$tuesdayClose:$tuesdayValue).'</td>
				<td>'.(!empty($wednesdayClose)?$wednesdayClose:$wednesdayValue).'</td>
				<td>'.(!empty($thursdayClose)?$thursdayClose:$thursdayValue).'</td>
				<td>'.(!empty($fridayClose)?$fridayClose:$fridayValue).'</td>
			</tr>';	
			
			$fullMenuMondayTotal = ($fullMenuMondayTotal+$mondayValue);
			$fullMenuTuesdayTotal = ($fullMenuTuesdayTotal+$tuesdayValue);
			$fullMenuWednesdayTotal = ($fullMenuTuesdayTotal+$wednesdayValue);
			$fullMenuThursdayTotal = ($fullMenuTuesdayTotal+$thursdayValue);
			$fullMenuFridayTotal = ($fullMenuTuesdayTotal+$fridayValue);		
			
		}
		
		if($menuName=="Lunch Only") {		
			
			$childCount = get_post_meta( $row->order_id, 'child_count', true );
			
			for($i=1;$i<=$childCount;$i++){
				$allergyGroup ="";
				$toddlerGroup ="";
				
				$attend = json_decode(get_post_meta( $row->order_id, 'child_attend_'.$i, true ));				
				if(get_post_meta( $row->order_id, 'child_toddler_toggle_'.$i, true )==0) {
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_dairy_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_dairy_'.$i, true )<>"null")) {
						$allergyGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_dairy_'.$i, true )) ).", ";
					}
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_egg_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_egg_'.$i, true )<>"null")) {
						$allergyGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_egg_'.$i, true ))).", ";
					}			
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_vegetarian_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_vegetarian_'.$i, true )<>"null") ) {
						$allergyGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_vegetarian_'.$i, true ))).", ";
					}
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_grains_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_grains_'.$i, true )<>"null")) {
						$allergyGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_grains_'.$i, true ))).", ";
					}
					/* added by comfusion */
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_meat_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_meat_'.$i, true )<>"null")) {
						$allergyGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_meat_'.$i, true ))).", ";
					}
					/* end modification */
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_seafood_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_seafood_'.$i, true )<>"null")) {
						$allergyGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_seafood_'.$i, true ))).", ";
					}
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_soyproduct_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_soyproduct_'.$i, true )<>"null")) {
						$allergyGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_soyproduct_'.$i, true ))).", ";
					}
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_legumes_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_legumes_'.$i, true )<>"null")) {
						$allergyGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_legumes_'.$i, true ))).", ";
					}
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_fruits_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_fruits_'.$i, true )<>"null")) {
						$allergyGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_fruits_'.$i, true ))).", ";
					}
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_vegetables_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_vegetables_'.$i, true )<>"null")) {
						$allergyGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_vegetables_'.$i, true ))).", ";
					}
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_nuts_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_nuts_'.$i, true )<>"null")) {
						$allergyGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_nuts_'.$i, true ))).", ";
					}
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_others_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_others_'.$i, true )<>"null")) {
						$allergyGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_others_'.$i, true ))).", ";
					}
					
					if(!empty(get_post_meta( $row->order_id, 'child_remark_'.$i, true )) && (get_post_meta( $row->order_id, 'child_remark_'.$i, true )<>"null")) {
						$allergyGroup = rtrim($allergyGroup, ', ' ).'<br>';
						$allergyGroup.= get_post_meta( $row->order_id, 'child_remark_'.$i, true );
					}  
					else {
						$allergyGroup = rtrim($allergyGroup, ', ' );
					}
					
					$childName = "";
					if(!empty(get_post_meta( $row->order_id, 'child_id_number_'.$i, true ))) {
						$childName.= get_post_meta( $row->order_id, 'child_id_number_'.$i, true )." - ";
					}
					if(!empty(get_post_meta( $row->order_id, 'child_name_'.$i, true ))) {
						$childName.= get_post_meta( $row->order_id, 'child_name_'.$i, true );
					}
					if(!empty(get_post_meta( $row->order_id, 'child_roomNumber_'.$i, true ))) {
						$childName.= " - ".get_post_meta( $row->order_id, 'child_roomNumber_'.$i, true );
					}	
					
					if(in_array("Monday",$attend)) {
						if(empty($mondayClose)) {
							$allergyMonday++;
						}
					}
					if(in_array("Tuesday",$attend)) {
						if(empty($tuesdayClose)) {
							$allergyTuesday++;
						}
					}
					if(in_array("Wednesday",$attend)) {
						if(empty($wednesdayClose)) {
							$allergyWednesday++;
						}
					}
					if(in_array("Thursday",$attend)) {
						if(empty($thursdayClose)) {
							$allergyThursday++;
						}
					}
					if(in_array("Friday",$attend)) {
						if(empty($fridayClose)) {
							$allergyFriday++;
						}
					}
					$allergyMenuHtml.= '<tr>
						<td colspan="2">'.$childName.'</td>
						<td colspan="4">'.$allergyGroup.'</td>
						<td>'.(!empty($mondayClose)?$mondayClose:(in_array("Monday",$attend))?"1":"").'</td>
						<td>'.(!empty($tuesdayClose)?$tuesdayClose:(in_array("Tuesday",$attend))?"1":"").'</td>
						<td>'.(!empty($wednesdayClose)?$wednesdayClose:(in_array("Wednesday",$attend))?"1":"").'</td>
						<td>'.(!empty($thursdayClose)?$thursdayClose:(in_array("Thursday",$attend))?"1":"").'</td>
						<td>'.(!empty($fridayClose)?$fridayClose:(in_array("Friday",$attend))?"1":"").'</td>
					</tr>';	
				}
				else {
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_dairy_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_dairy_'.$i, true )<>"null")) {
						$toddlerGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_dairy_'.$i, true )) ).", ";
					}
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_egg_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_egg_'.$i, true )<>"null")) {
						$toddlerGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_egg_'.$i, true ))).", ";
					}			
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_vegetarian_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_vegetarian_'.$i, true )<>"null") ) {
						$toddlerGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_vegetarian_'.$i, true ))).", ";
					}
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_grains_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_grains_'.$i, true )<>"null")) {
						$toddlerGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_grains_'.$i, true ))).", ";
					}
					/* added by comfusion */
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_meat_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_meat_'.$i, true )<>"null")) {
						$toddlerGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_meat_'.$i, true ))).", ";
					}
					/* end modification */
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_seafood_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_seafood_'.$i, true )<>"null")) {
						$toddlerGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_seafood_'.$i, true ))).", ";
					}
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_soyproduct_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_soyproduct_'.$i, true )<>"null")) {
						$toddlerGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_soyproduct_'.$i, true ))).", ";
					}
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_legumes_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_legumes_'.$i, true )<>"null")) {
						$toddlerGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_legumes_'.$i, true ))).", ";
					}
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_fruits_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_fruits_'.$i, true )<>"null")) {
						$toddlerGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_fruits_'.$i, true ))).", ";
					}
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_vegetables_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_vegetables_'.$i, true )<>"null")) {
						$toddlerGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_vegetables_'.$i, true ))).", ";
					}
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_nuts_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_nuts_'.$i, true )<>"null")) {
						$toddlerGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_nuts_'.$i, true ))).", ";
					}
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_others_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_others_'.$i, true )<>"null")) {
						$toddlerGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_others_'.$i, true ))).", ";
					}
					
					if(!empty(get_post_meta( $row->order_id, 'child_remark_'.$i, true )) && (get_post_meta( $row->order_id, 'child_remark_'.$i, true )<>"null")) {
						$toddlerGroup = rtrim($toddlerGroup, ', ' ).'<br>';
						$toddlerGroup.= get_post_meta( $row->order_id, 'child_remark_'.$i, true );
					}  
					else {
						$toddlerGroup = rtrim($toddlerGroup, ', ' );
					}
					
					$childName = "";
					if(!empty(get_post_meta( $row->order_id, 'child_id_number_'.$i, true ))) {
						$childName.= get_post_meta( $row->order_id, 'child_id_number_'.$i, true )." - ";
					}
					if(!empty(get_post_meta( $row->order_id, 'child_name_'.$i, true ))) {
						$childName.= get_post_meta( $row->order_id, 'child_name_'.$i, true );
					}					
					if(!empty(get_post_meta( $row->order_id, 'child_roomNumber_'.$i, true ))) {
						$childName.= " - ".get_post_meta( $row->order_id, 'child_roomNumber_'.$i, true );
					}	
					
					if(in_array("Monday",$attend)) {
						if(empty($mondayClose)) {
							$toddlerMonday++;
						}
						
					}
					if(in_array("Tuesday",$attend)) {
						if(empty($tuesdayClose)) {
							$toddlerTuesday++;
						}
					}
					if(in_array("Wednesday",$attend)) {
						if(empty($wednesdayClose)) {
							$toddlerWednesday++;
						}
					}
					if(in_array("Thursday",$attend)) {
						if(empty($thursdayClose)) {
							$toddlerThursday++;
						}
					}
					if(in_array("Friday",$attend)) {
						if(empty($fridayClose)) {
							$toddlerFriday++;
						}
					}
					
					$toddlerToggleHtml.= '<tr>
						<td colspan="2">'.$user->billing_company.'</td>
						<td colspan="4">'.$toddlerGroup.'</td>
						<td>'.(!empty($mondayClose)?$mondayClose:(in_array("Monday",$attend))?"1":"").'</td>
						<td>'.(!empty($tuesdayClose)?$tuesdayClose:(in_array("Tuesday",$attend))?"1":"").'</td>
						<td>'.(!empty($wednesdayClose)?$wednesdayClose:(in_array("Wednesday",$attend))?"1":"").'</td>
						<td>'.(!empty($thursdayClose)?$thursdayClose:(in_array("Thursday",$attend))?"1":"").'</td>
						<td>'.(!empty($fridayClose)?$fridayClose:(in_array("Friday",$attend))?"1":"").'</td>
					</tr>';	
			
				}
			} 
			
			
			$lunchOnlyHtml.= '<tr>
				<td colspan="2">'.$menuName.'</td>
				<td colspan="4">'.$user->billing_company.'</td>
				<td>'.(!empty($mondayClose)?$mondayClose:$mondayValue).'</td>
				<td>'.(!empty($tuesdayClose)?$tuesdayClose:$tuesdayValue).'</td>
				<td>'.(!empty($wednesdayClose)?$wednesdayClose:$wednesdayValue).'</td>
				<td>'.(!empty($thursdayClose)?$thursdayClose:$thursdayValue).'</td>
				<td>'.(!empty($fridayClose)?$fridayClose:$fridayValue).'</td>
			</tr>';
			
			$lunchMondayTotal = ($lunchMondayTotal+$mondayValue);
			$lunchTuesdayTotal = ($lunchTuesdayTotal+$tuesdayValue);
			$lunchWednesdayTotal = ($lunchWednesdayTotal+$wednesdayValue);
			$lunchThursdayTotal = ($lunchThursdayTotal+$thursdayValue);
			$lunchFridayTotal = ($lunchFridayTotal+$fridayValue);				
					
		}
		
		if($menuName=="Toddler Menu") {				
			
			$childCount = get_post_meta( $row->order_id, 'child_count', true );			
					
			
			for($i=1;$i<=$childCount;$i++){				
				$allergyGroup ="";
				$toddlerGroup ="";
				
				$attend = json_decode(get_post_meta( $row->order_id, 'child_attend_'.$i, true ));
				
				if(get_post_meta( $row->order_id, 'child_toddler_toggle_'.$i, true )==0) {
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_dairy_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_dairy_'.$i, true )<>"null")) {
						$allergyGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_dairy_'.$i, true )) ).", ";
					}
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_egg_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_egg_'.$i, true )<>"null")) {
						$allergyGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_egg_'.$i, true ))).", ";
					}			
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_vegetarian_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_vegetarian_'.$i, true )<>"null") ) {
						$allergyGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_vegetarian_'.$i, true ))).", ";
					}
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_grains_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_grains_'.$i, true )<>"null")) {
						$allergyGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_grains_'.$i, true ))).", ";
					}
					/* added by comfusion */
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_meat_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_meat_'.$i, true )<>"null")) {
						$allergyGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_meat_'.$i, true ))).", ";
					}
					/* end modification */
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_seafood_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_seafood_'.$i, true )<>"null")) {
						$allergyGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_seafood_'.$i, true ))).", ";
					}
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_soyproduct_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_soyproduct_'.$i, true )<>"null")) {
						$allergyGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_soyproduct_'.$i, true ))).", ";
					}
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_legumes_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_legumes_'.$i, true )<>"null")) {
						$allergyGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_legumes_'.$i, true ))).", ";
					}
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_fruits_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_fruits_'.$i, true )<>"null")) {
						$allergyGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_fruits_'.$i, true ))).", ";
					}
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_vegetables_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_vegetables_'.$i, true )<>"null")) {
						$allergyGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_vegetables_'.$i, true ))).", ";
					}
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_nuts_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_nuts_'.$i, true )<>"null")) {
						$allergyGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_nuts_'.$i, true ))).", ";
					}
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_others_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_others_'.$i, true )<>"null")) {
						$allergyGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_others_'.$i, true ))).", ";
					}
					
					if(!empty(get_post_meta( $row->order_id, 'child_remark_'.$i, true )) && (get_post_meta( $row->order_id, 'child_remark_'.$i, true )<>"null")) {
						$allergyGroup = rtrim($allergyGroup, ', ' ).'<br>';
						$allergyGroup.= get_post_meta( $row->order_id, 'child_remark_'.$i, true );
					}  
					else {
						$allergyGroup = rtrim($allergyGroup, ', ' );
					}
					
					$childName = "";
					if(!empty(get_post_meta( $row->order_id, 'child_id_number_'.$i, true ))) {
						$childName.= get_post_meta( $row->order_id, 'child_id_number_'.$i, true )." - ";
					}
					if(!empty(get_post_meta( $row->order_id, 'child_name_'.$i, true ))) {
						$childName.= get_post_meta( $row->order_id, 'child_name_'.$i, true );
					}
					if(!empty(get_post_meta( $row->order_id, 'child_roomNumber_'.$i, true ))) {
						$childName.= " - ".get_post_meta( $row->order_id, 'child_roomNumber_'.$i, true );
					}	
					
					if(in_array("Monday",$attend)) {
						if(empty($mondayClose)) {
							$allergyMonday++;
						}
					}
					if(in_array("Tuesday",$attend)) {
						if(empty($tuesdayClose)) {
							$allergyTuesday++;
						}
					}
					if(in_array("Wednesday",$attend)) {
						if(empty($wednesdayClose)) {
							$allergyWednesday++;
						}
					}
					if(in_array("Thursday",$attend)) {
						if(empty($thursdayClose)) {
							$allergyThursday++;
						}
					}
					if(in_array("Friday",$attend)) {
						if(empty($fridayClose)) {
							$allergyFriday++;
						}
					}
					
					$allergyMenuHtml.= '<tr>
						<td colspan="2">'.$childName.'</td>
						<td colspan="4">'.$allergyGroup.'</td>
						<td>'.(!empty($mondayClose)?$mondayClose:(in_array("Monday",$attend))?"1":"").'</td>
						<td>'.(!empty($tuesdayClose)?$tuesdayClose:(in_array("Tuesday",$attend))?"1":"").'</td>
						<td>'.(!empty($wednesdayClose)?$wednesdayClose:(in_array("Wednesday",$attend))?"1":"").'</td>
						<td>'.(!empty($thursdayClose)?$thursdayClose:(in_array("Thursday",$attend))?"1":"").'</td>
						<td>'.(!empty($fridayClose)?$fridayClose:(in_array("Friday",$attend))?"1":"").'</td>
					</tr>';	
					
				}
				else {
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_dairy_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_dairy_'.$i, true )<>"null")) {
						$toddlerGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_dairy_'.$i, true )) ).", ";
					}
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_egg_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_egg_'.$i, true )<>"null")) {
						$toddlerGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_egg_'.$i, true ))).", ";
					}			
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_vegetarian_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_vegetarian_'.$i, true )<>"null") ) {
						$toddlerGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_vegetarian_'.$i, true ))).", ";
					}
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_grains_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_grains_'.$i, true )<>"null")) {
						$toddlerGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_grains_'.$i, true ))).", ";
					}
					/* added by comfusion */
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_meat_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_meat_'.$i, true )<>"null")) {
						$toddlerGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_meat_'.$i, true ))).", ";
					}
					/* end modification */
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_seafood_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_seafood_'.$i, true )<>"null")) {
						$toddlerGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_seafood_'.$i, true ))).", ";
					}
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_soyproduct_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_soyproduct_'.$i, true )<>"null")) {
						$toddlerGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_soyproduct_'.$i, true ))).", ";
					}
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_legumes_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_legumes_'.$i, true )<>"null")) {
						$toddlerGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_legumes_'.$i, true ))).", ";
					}
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_fruits_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_fruits_'.$i, true )<>"null")) {
						$toddlerGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_fruits_'.$i, true ))).", ";
					}
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_vegetables_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_vegetables_'.$i, true )<>"null")) {
						$toddlerGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_vegetables_'.$i, true ))).", ";
					}
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_nuts_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_nuts_'.$i, true )<>"null")) {
						$toddlerGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_nuts_'.$i, true ))).", ";
					}
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_others_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_others_'.$i, true )<>"null")) {
						$toddlerGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_others_'.$i, true ))).", ";
					}
					
					if(!empty(get_post_meta( $row->order_id, 'child_remark_'.$i, true )) && (get_post_meta( $row->order_id, 'child_remark_'.$i, true )<>"null")) {
						$toddlerGroup = rtrim($toddlerGroup, ', ' ).'<br>';
						$toddlerGroup.= get_post_meta( $row->order_id, 'child_remark_'.$i, true );
					}  
					else {
						$toddlerGroup = rtrim($toddlerGroup, ', ' );
					}
					
					$childName = "";
					if(!empty(get_post_meta( $row->order_id, 'child_id_number_'.$i, true ))) {
						$childName.= get_post_meta( $row->order_id, 'child_id_number_'.$i, true )." - ";
					}
					if(!empty(get_post_meta( $row->order_id, 'child_name_'.$i, true ))) {
						$childName.= get_post_meta( $row->order_id, 'child_name_'.$i, true );
					}
					if(!empty(get_post_meta( $row->order_id, 'child_roomNumber_'.$i, true ))) {
						$childName.= " - ".get_post_meta( $row->order_id, 'child_roomNumber_'.$i, true );
					}	
					
					if(in_array("Monday",$attend)) {
						if(empty($mondayClose)) {
							$toddlerMonday++;
						}
					}
					if(in_array("Tuesday",$attend)) {
						if(empty($tuesdayClose)) {
							$toddlerTuesday++;
						}
					}
					if(in_array("Wednesday",$attend)) {
						if(empty($wednesdayClose)) {
							$toddlerWednesday++;
						}
					}
					if(in_array("Thursday",$attend)) {
						if(empty($thursdayClose)) {
							$toddlerThursday++;
						}
					}
					if(in_array("Friday",$attend)) {
						if(empty($fridayClose)) {
							$toddlerFriday++;
						}
					}
					
					$toddlerToggleHtml.= '<tr>
						<td colspan="2">'.$childName.'</td>
						<td colspan="4">'.$toddlerGroup.'</td>
						<td>'.(!empty($mondayClose)?$mondayClose:(in_array("Monday",$attend))?"1":"").'</td>
						<td>'.(!empty($tuesdayClose)?$tuesdayClose:(in_array("Tuesday",$attend))?"1":"").'</td>
						<td>'.(!empty($wednesdayClose)?$wednesdayClose:(in_array("Wednesday",$attend))?"1":"").'</td>
						<td>'.(!empty($thursdayClose)?$thursdayClose:(in_array("Thursday",$attend))?"1":"").'</td>
						<td>'.(!empty($fridayClose)?$fridayClose:(in_array("Friday",$attend))?"1":"").'</td>
					</tr>';	
				}
				
			} 
			
			$toddlerHtml.= '<tr>
				<td colspan="2">'.$menuName.'</td>
				<td colspan="4">'.$user->billing_company.'</td>
				<td>'.(!empty($mondayClose)?$mondayClose:$mondayValue).'</td>
				<td>'.(!empty($tuesdayClose)?$tuesdayClose:$tuesdayValue).'</td>
				<td>'.(!empty($wednesdayClose)?$wednesdayClose:$wednesdayValue).'</td>
				<td>'.(!empty($thursdayClose)?$thursdayClose:$thursdayValue).'</td>
				<td>'.(!empty($fridayClose)?$fridayClose:$fridayValue).'</td>
			</tr>';	
			
			$toddlerMondayTotal = ($toddlerMondayTotal+$mondayValue);
			$toddlerTuesdayTotal = ($toddlerTuesdayTotal+$tuesdayValue);
			$toddlerWednesdayTotal = ($toddlerWednesdayTotal+$wednesdayValue);
			$toddlerThursdayTotal = ($toddlerThursdayTotal+$thursdayValue);
			$toddlerFridayTotal = ($toddlerFridayTotal+$fridayValue);
						
		}
		
		if($menuName=="OOSH (out of school hours)") {		
			
			$childCount = get_post_meta( $row->order_id, 'child_count', true );
			
			for($i=1;$i<=$childCount;$i++){
				$allergyGroup ="";
				$toddlerGroup ="";
				$attend = json_decode(get_post_meta( $row->order_id, 'child_attend_'.$i, true ));
				
				if(get_post_meta( $row->order_id, 'child_toddler_toggle_'.$i, true )==0) {
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_dairy_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_dairy_'.$i, true )<>"null")) {
						$allergyGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_dairy_'.$i, true )) ).", ";
					}
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_egg_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_egg_'.$i, true )<>"null")) {
						$allergyGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_egg_'.$i, true ))).", ";
					}			
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_vegetarian_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_vegetarian_'.$i, true )<>"null") ) {
						$allergyGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_vegetarian_'.$i, true ))).", ";
					}
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_grains_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_grains_'.$i, true )<>"null")) {
						$allergyGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_grains_'.$i, true ))).", ";
					}
					/* added by comfusion */
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_meat_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_meat_'.$i, true )<>"null")) {
						$allergyGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_meat_'.$i, true ))).", ";
					}
					/* end modification */
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_seafood_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_seafood_'.$i, true )<>"null")) {
						$allergyGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_seafood_'.$i, true ))).", ";
					}
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_soyproduct_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_soyproduct_'.$i, true )<>"null")) {
						$allergyGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_soyproduct_'.$i, true ))).", ";
					}
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_legumes_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_legumes_'.$i, true )<>"null")) {
						$allergyGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_legumes_'.$i, true ))).", ";
					}
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_fruits_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_fruits_'.$i, true )<>"null")) {
						$allergyGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_fruits_'.$i, true ))).", ";
					}
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_vegetables_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_vegetables_'.$i, true )<>"null")) {
						$allergyGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_vegetables_'.$i, true ))).", ";
					}
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_nuts_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_nuts_'.$i, true )<>"null")) {
						$allergyGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_nuts_'.$i, true ))).", ";
					}
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_others_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_others_'.$i, true )<>"null")) {
						$allergyGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_others_'.$i, true ))).", ";
					}
					
					if(!empty(get_post_meta( $row->order_id, 'child_remark_'.$i, true )) && (get_post_meta( $row->order_id, 'child_remark_'.$i, true )<>"null")) {
						$allergyGroup = rtrim($allergyGroup, ', ' ).'<br>';
						$allergyGroup.= get_post_meta( $row->order_id, 'child_remark_'.$i, true );
					}  
					else {
						$allergyGroup = rtrim($allergyGroup, ', ' );
					}
					
					$childName = "";
					if(!empty(get_post_meta( $row->order_id, 'child_id_number_'.$i, true ))) {
						$childName.= get_post_meta( $row->order_id, 'child_id_number_'.$i, true )." - ";
					}
					if(!empty(get_post_meta( $row->order_id, 'child_name_'.$i, true ))) {
						$childName.= get_post_meta( $row->order_id, 'child_name_'.$i, true );
					}
					if(!empty(get_post_meta( $row->order_id, 'child_roomNumber_'.$i, true ))) {
						$childName.= " - ".get_post_meta( $row->order_id, 'child_roomNumber_'.$i, true );
					}	
					
					if(in_array("Monday",$attend)) {
						if(empty($mondayClose)) {
							$allergyMonday++;
						}
					}
					if(in_array("Tuesday",$attend)) {
						if(empty($tuesdayClose)) {
							$allergyTuesday++;
						}
					}
					if(in_array("Wednesday",$attend)) {
						if(empty($wednesdayClose)) {
							$allergyWednesday++;
						}
					}
					if(in_array("Thursday",$attend)) {
						if(empty($thursdayClose)) {
							$allergyThursday++;
						}
					}
					if(in_array("Friday",$attend)) {
						if(empty($fridayClose)) {
							$allergyFriday++;
						}
					}
					
					$allergyMenuHtml.= '<tr>
						<td colspan="2">'.$childName.'</td>
						<td colspan="4">'.$allergyGroup.'</td>
						<td>'.(!empty($mondayClose)?$mondayClose:(in_array("Monday",$attend))?"1":"").'</td>
						<td>'.(!empty($tuesdayClose)?$tuesdayClose:(in_array("Tuesday",$attend))?"1":"").'</td>
						<td>'.(!empty($wednesdayClose)?$wednesdayClose:(in_array("Wednesday",$attend))?"1":"").'</td>
						<td>'.(!empty($thursdayClose)?$thursdayClose:(in_array("Thursday",$attend))?"1":"").'</td>
						<td>'.(!empty($fridayClose)?$fridayClose:(in_array("Friday",$attend))?"1":"").'</td>
					</tr>';	
				}
				else {
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_dairy_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_dairy_'.$i, true )<>"null")) {
						$toddlerGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_dairy_'.$i, true )) ).", ";
					}
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_egg_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_egg_'.$i, true )<>"null")) {
						$toddlerGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_egg_'.$i, true ))).", ";
					}			
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_vegetarian_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_vegetarian_'.$i, true )<>"null") ) {
						$toddlerGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_vegetarian_'.$i, true ))).", ";
					}
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_grains_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_grains_'.$i, true )<>"null")) {
						$toddlerGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_grains_'.$i, true ))).", ";
					}
					/* added by comfusion */
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_meat_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_meat_'.$i, true )<>"null")) {
						$toddlerGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_meat_'.$i, true ))).", ";
					}
					/* end modification */
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_seafood_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_seafood_'.$i, true )<>"null")) {
						$toddlerGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_seafood_'.$i, true ))).", ";
					}
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_soyproduct_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_soyproduct_'.$i, true )<>"null")) {
						$toddlerGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_soyproduct_'.$i, true ))).", ";
					}
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_legumes_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_legumes_'.$i, true )<>"null")) {
						$toddlerGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_legumes_'.$i, true ))).", ";
					}
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_fruits_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_fruits_'.$i, true )<>"null")) {
						$toddlerGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_fruits_'.$i, true ))).", ";
					}
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_vegetables_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_vegetables_'.$i, true )<>"null")) {
						$toddlerGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_vegetables_'.$i, true ))).", ";
					}
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_nuts_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_nuts_'.$i, true )<>"null")) {
						$toddlerGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_nuts_'.$i, true ))).", ";
					}
					if(!empty(get_post_meta( $row->order_id, 'child_allergy_others_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_others_'.$i, true )<>"null")) {
						$toddlerGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_others_'.$i, true ))).", ";
					}
					
					if(!empty(get_post_meta( $row->order_id, 'child_remark_'.$i, true )) && (get_post_meta( $row->order_id, 'child_remark_'.$i, true )<>"null")) {
						$toddlerGroup = rtrim($toddlerGroup, ', ' ).'<br>';
						$toddlerGroup.= get_post_meta( $row->order_id, 'child_remark_'.$i, true );
					}  
					else {
						$toddlerGroup = rtrim($toddlerGroup, ', ' );
					}
					
					$childName = "";
					if(!empty(get_post_meta( $row->order_id, 'child_id_number_'.$i, true ))) {
						$childName.= get_post_meta( $row->order_id, 'child_id_number_'.$i, true )." - ";
					}
					if(!empty(get_post_meta( $row->order_id, 'child_name_'.$i, true ))) {
						$childName.= get_post_meta( $row->order_id, 'child_name_'.$i, true );
					}
					if(!empty(get_post_meta( $row->order_id, 'child_roomNumber_'.$i, true ))) {
						$childName.= " - ".get_post_meta( $row->order_id, 'child_roomNumber_'.$i, true );
					}	
					
					if(in_array("Monday",$attend)) {
						if(empty($mondayClose)) {
							$toddlerMonday++;
						}
					}
					if(in_array("Tuesday",$attend)) {
						if(empty($tuesdayClose)) {
							$toddlerTuesday++;
						}
					}
					if(in_array("Wednesday",$attend)) {
						if(empty($wednesdayClose)) {
							$toddlerWednesday++;
						}
					}
					if(in_array("Thursday",$attend)) {
						if(empty($thursdayClose)) {
							$toddlerThursday++;
						}
					}
					if(in_array("Friday",$attend)) {
						if(empty($fridayClose)) {
							$toddlerFriday++;
						}
					}
					
					$toddlerToggleHtml.= '<tr>
						<td colspan="2">'.$childName.'</td>
						<td colspan="4">'.$toddlerGroup.'</td>
						<td>'.(!empty($mondayClose)?$mondayClose:(in_array("Monday",$attend))?"1":"").'</td>
						<td>'.(!empty($tuesdayClose)?$tuesdayClose:(in_array("Tuesday",$attend))?"1":"").'</td>
						<td>'.(!empty($wednesdayClose)?$wednesdayClose:(in_array("Wednesday",$attend))?"1":"").'</td>
						<td>'.(!empty($thursdayClose)?$thursdayClose:(in_array("Thursday",$attend))?"1":"").'</td>
						<td>'.(!empty($fridayClose)?$fridayClose:(in_array("Friday",$attend))?"1":"").'</td>
					</tr>';	
				}
			} 
			
			$ooshHtml.= '<tr>
				<td colspan="2">'.$menuName.'</td>
				<td colspan="4">'.$user->billing_company.'</td>
				<td>'.(!empty($mondayClose)?$mondayClose:$mondayValue).'</td>
				<td>'.(!empty($tuesdayClose)?$tuesdayClose:$tuesdayValue).'</td>
				<td>'.(!empty($wednesdayClose)?$wednesdayClose:$wednesdayValue).'</td>
				<td>'.(!empty($thursdayClose)?$thursdayClose:$thursdayValue).'</td>
				<td>'.(!empty($fridayClose)?$fridayClose:$fridayValue).'</td>
			</tr>';	
			
			$ooshMondayTotal = ($ooshMondayTotal+$mondayValue);
			$ooshTuesdayTotal = ($ooshTuesdayTotal+$tuesdayValue);
			$ooshWednesdayTotal = ($ooshWednesdayTotal+$wednesdayValue);
			$ooshThursdayTotal = ($ooshThursdayTotal+$thursdayValue);
			$ooshFridayTotal = ($ooshFridayTotal+$fridayValue);	
						
		}
			
		
	}
	
	$fullMenuHtml.= '</tbody>
			<tfoot>
				<tr>
					<td colspan="2">Full Menu</td>
					<td colspan="4">Subtotal</td>
					<td>'.$fullMenuMondayTotal.'</td>
					<td>'.$fullMenuTuesdayTotal.'</td>
					<td>'.$fullMenuWednesdayTotal.'</td>
					<td>'.$fullMenuThursdayTotal.'</td>
					<td>'.$fullMenuFridayTotal.'</td>		
				</tr>
			</tfoot>
			</table>';	
	$lunchOnlyHtml.= '</tbody>
			<tfoot>
				<tr>
					<td colspan="2">Lunch Only</td>
					<td colspan="4">Subtotal</td>
					<td>'.$lunchMondayTotal.'</td>
					<td>'.$lunchTuesdayTotal.'</td>
					<td>'.$lunchWednesdayTotal.'</td>
					<td>'.$lunchThursdayTotal.'</td>
					<td>'.$lunchFridayTotal.'</td>		
				</tr>
			</tfoot>
			</table>';	
			
	$toddlerHtml.= '</tbody>
			<tfoot>
				<tr>
					<td colspan="2">Toddler Menu</td>
					<td colspan="4">Subtotal</td>
					<td>'.$toddlerMondayTotal.'</td>
					<td>'.$toddlerTuesdayTotal.'</td>
					<td>'.$toddlerWednesdayTotal.'</td>
					<td>'.$toddlerThursdayTotal.'</td>
					<td>'.$toddlerFridayTotal.'</td>		
				</tr>
			</tfoot>
			</table>';	
			
	$ooshHtml.= '</tbody>
			<tfoot>
				<tr>
					<td colspan="2">OOSH (out of school hours)</td>
					<td colspan="4">Subtotal</td>
					<td>'.$ooshMondayTotal.'</td>
					<td>'.$ooshTuesdayTotal.'</td>
					<td>'.$ooshWednesdayTotal.'</td>
					<td>'.$ooshThursdayTotal.'</td>
					<td>'.$ooshFridayTotal.'</td>		
				</tr>
			</tfoot>
			</table>';	
	
	$pdf->SetFont('helvetica', '', 10);
		
	$pdf->writeHTML($fullMenuHtml, true, true, true, true, '');			
	
	$pdf->Ln(10);
	
	$pdf->writeHTML($lunchOnlyHtml, true, true, true, true, '');			
	
	$pdf->Ln(10);
	
	//$pdf->writeHTML($toddlerHtml, true, true, true, true, '');			
	
	//$pdf->Ln(10);
	
	$pdf->writeHTML($ooshHtml, true, true, true, true, '');			
	
	//$pdf->AddPage();
	
	$pdf->Ln(20);
	
	$pdf->SetFont('helvetica', '', 14);
	
	$pdf->writeHTML('<u style="text-align:center;"><b>Allergen Menu Totals</b></u>', true, true, true, true, '');	
	
	$pdf->Ln(5);
	
	$allergyMenuHtml.= '<tr><td colspan="2">Allergy Menu</td><td colspan="4">Subtotal</td><td>'.$allergyMonday.'</td><td>'.$allergyTuesday.'</td><td>'.$allergyWednesday.'</td><td>'.$allergyThursday.'</td><td>'.$allergyFriday.'</td></tr>';
	
	$allergyMenuHtml.= '</tbody></table>';
	
	$pdf->SetFont('helvetica', '', 10);
		
	$pdf->writeHTML($allergyMenuHtml, true, true, true, true, '');	
	
	//$pdf->AddPage();
	
	$pdf->Ln(20);
	
	$pdf->SetFont('helvetica', '', 14);
	
	$pdf->writeHTML('<u style="text-align:center;"><b>Toddler Menu Totals</b></u>', true, true, true, true, '');	
	
	$pdf->Ln(5);
	
	$toddlerToggleHtml.= '<tr><td colspan="2">Toddler Menu</td><td colspan="4">Subtotal</td><td>'.$toddlerMonday.'</td><td>'.$toddlerTuesday.'</td><td>'.$toddlerWednesday.'</td><td>'.$toddlerThursday.'</td><td>'.$toddlerFriday.'</td></tr>';
	
	$toddlerToggleHtml.= '</tbody></table>';
	
	$pdf->SetFont('helvetica', '', 10);
		
	$pdf->writeHTML($toddlerToggleHtml, true, true, true, true, '');
		
	 
	// reset pointer to the last page
	$pdf->lastPage();
	
	// ---------------------------------------------------------
	ob_end_clean();	 
	//Close and output PDF document
	$PdfName = CUS_WOO_ORDER_PLUGIN_DIR.'/pdffile/weekly_order_'.$stateCode.'.pdf';
	$pdf->Output($PdfName, 'F');
	return $PdfName;
	
	?>