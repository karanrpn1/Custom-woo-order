<?php
	
	$pdf = new TCPDF('P', PDF_UNIT, 'A3', true, 'UTF-8', false);

	// set document information
	$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetAuthor('Karan Rupani');
	$pdf->SetTitle('Meal Summary');
	$pdf->SetSubject('Daily Pdf');
	$pdf->SetKeywords('Meal Summary');
	
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
	
	$pdf->SetFont('helvetica', '', 20);	
	
	global $wpdb;
	$tablename = $wpdb->prefix.'order_info';
	$orderInfo = $wpdb->get_results( "SELECT * FROM $tablename where status=1");
		
	foreach ($orderInfo as $row ) {
		
		$user = get_user_by( 'id', $row->user_id );
		
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
		if($order_status<>"processing") { 	continue; }
		
		$timestamp = strtotime('today midnight');
		$optInTable = $wpdb->prefix.'opt_in_alternative';
		$optInTableInfo = $wpdb->get_var( "SELECT count(*) FROM $optInTable where user_id=$row->user_id and order_info_id=$row->ID and date>=$timestamp and date<=$timestamp");
		if($optInTableInfo>0) {
			continue;
		} 
		
		$schoolRedinessTable = $wpdb->prefix.'school_readiness_lunch';
		$schoolRedinessTableInfo = $wpdb->get_var( "SELECT count(*) FROM $schoolRedinessTable where user_id=$row->user_id and order_info_id=$row->ID and date>=$timestamp and date<=$timestamp");
		if($schoolRedinessTableInfo>0) {
			continue;
		} 
		
		$closeDateTable = $wpdb->prefix.'close_date_info';
		$closeDateInfo = $wpdb->get_results( "SELECT * FROM $closeDateTable where user_id=$row->user_id");
		
		$checkCloseDate = 0;
		foreach($closeDateInfo as $closeDateInfoRow) {
			if(!empty($closeDateInfoRow->endDate)) {
				$startDate = $closeDateInfoRow->startDate;
				$endDate = ($closeDateInfoRow->endDate)+86400;
				$todayDate = strtotime('today midnight');
				if($todayDate>=$startDate && $todayDate<=$endDate) {
					$checkCloseDate = 1 ; 	//SET TO TRUE FOR SKIPPING ORDER
					break;
				}
			}
			else {			
				$startDate = $closeDateInfoRow->startDate;
				$endDate = ($closeDateInfoRow->startDate)+86400;
				$todayDate = strtotime('today midnight');
				if($todayDate>=$startDate && $todayDate<=$endDate) {
					$checkCloseDate = 1 ; 	//SET TO TRUE FOR SKIPPING ORDER
					break;					
				}
			}
		}
		
		if($checkCloseDate==1) {
			continue;
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
				
		$pdf->writeHTMLCell(200, '', '', '', "Meal Summary", 0, 1, 1, true, 'C', true);
	
		$pdf->SetFont('helvetica', '', 14);	
		
		$pdf->SetTextColor('0','0','0');
		
		$y = $pdf->getY();
	
		$pdf->SetFillColor(255, 255, 255);
		
		$pdf->writeHTMLCell(80, '', '', $y, "", 0, 0, 1, true, 'J', true);
				
		$pdf->writeHTMLCell(200, '', '', '', $user->billing_company, 0, 1, 1, true, 'C', true);	
		
		$y = $pdf->getY();
	
		$pdf->SetFillColor(255, 255, 255);
		
		$pdf->writeHTMLCell(80, '', '', $y, "", 0, 0, 1, true, 'J', true);
				
		$pdf->writeHTMLCell(200, '', '', '','<i>This meal summary replaces any previous meal summary as of '.date("jS M Y").'</i>', 0, 1, 1, true, 'C', true);	
	
		$pdf->Ln(10);
		
		$pdf->writeHTML('<u style="text-align:center;"><b>Regular Menu</b></u>', true, true, true, true, '');	
		
		$pdf->Ln(5);	
		
		$allergyHtml = '<table border="1" cellpadding="10">
		<thead>
			<tr style="background-color:#84bc3d;color:#fff;">
				<td colspan="2">Name</td>
				<td colspan="3">Allergen Group</td>
				<td>Mon</td>
				<td>Tue</td>
				<td>Wed</td>
				<td>Thu</td>
				<td>Fri</td>
			</tr>
		</thead>';
		$allergyMonday = 0;	
		$allergyTuesday = 0;	
		$allergyWednesday = 0;	
		$allergyThursday = 0;	
		$allergyFriday = 0;	
		
		$toddlerHtml = '<table border="1" cellpadding="10">
		<thead>
			<tr style="background-color:#84bc3d;color:#fff;">
				<td colspan="2">Name</td>
				<td colspan="3">Allergen Group</td>
				<td>Mon</td>
				<td>Tue</td>
				<td>Wed</td>
				<td>Thu</td>
				<td>Fri</td>
			</tr>
		</thead>';
		$toddlerMonday = 0;	
		$toddlerTuesday = 0;	
		$toddlerWednesday = 0;	
		$toddlerThursday = 0;	
		$toddlerFriday = 0;	
		
		
		$childCount = get_post_meta( $row->order_id, 'child_count', true ); 
		
		for($i=1;$i<=$childCount;$i++){
			$attend = json_decode(get_post_meta( $row->order_id, 'child_attend_'.$i, true ));
			$allergyGroup ="";
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
				$allergyGroup.= implode(",",json_decode(get_post_meta( $row->order_id, 'child_allergy_fruits_'.$i, true ))).", ";
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
			
			if(get_post_meta( $row->order_id, 'child_toddler_toggle_'.$i, true )==0) {
				if(in_array("Monday",$attend)) {
				$allergyMonday++;
				}
				if(in_array("Tuesday",$attend)) {
					$allergyTuesday++;
				}
				if(in_array("Wednesday",$attend)) {
					$allergyWednesday++;
				}
				if(in_array("Thursday",$attend)) {
					$allergyThursday++;
				}
				if(in_array("Friday",$attend)) {
					$allergyFriday++;
				}
				
				$allergyHtml.='<tbody>
				<tr>
					<td colspan="2">'.$childName.'</td>
					<td colspan="3">'.$allergyGroup.'</td> 
					<td>'.(in_array("Monday",$attend)?"1":"").'</td>
					<td>'.(in_array("Tuesday",$attend)?"1":"").'</td>
					<td>'.(in_array("Wednesday",$attend)?"1":"").'</td>
					<td>'.(in_array("Thursday",$attend)?"1":"").'</td>
					<td>'.(in_array("Friday",$attend)?"1":"").'</td>
				</tr>';
			}
			else {
				if(in_array("Monday",$attend)) {
					$toddlerMonday++;
				}
				if(in_array("Tuesday",$attend)) {
					$toddlerTuesday++;
				}
				if(in_array("Wednesday",$attend)) {
					$toddlerWednesday++;
				}
				if(in_array("Thursday",$attend)) {
					$toddlerThursday++;
				}
				if(in_array("Friday",$attend)) {
					$toddlerFriday++;
				}
				
				$toddlerHtml.='<tbody>
				<tr>
					<td colspan="2">'.$childName.'</td>
					<td colspan="3">'.$allergyGroup.'</td> 
					<td>'.(in_array("Monday",$attend)?"1":"").'</td>
					<td>'.(in_array("Tuesday",$attend)?"1":"").'</td>
					<td>'.(in_array("Wednesday",$attend)?"1":"").'</td>
					<td>'.(in_array("Thursday",$attend)?"1":"").'</td>
					<td>'.(in_array("Friday",$attend)?"1":"").'</td>
				</tr>';
			}
			
		}	 
		  
		$allergyHtml.='</tbody>
		<tfoot>
			<tr>
				<td colspan="5">Allergy Sub Totals</td>
				<td>'.$allergyMonday.'</td>
				<td>'.$allergyTuesday.'</td>
				<td>'.$allergyWednesday.'</td>
				<td>'.$allergyThursday.'</td>
				<td>'.$allergyFriday.'</td>			
			</tr>
		</tfoot>
		</table>';	
		
		$toddlerHtml.='</tbody>
		<tfoot>
			<tr>
				<td colspan="5">Toddler Sub Totals</td>
				<td>'.$toddlerMonday.'</td>
				<td>'.$toddlerTuesday.'</td>
				<td>'.$toddlerWednesday.'</td>
				<td>'.$toddlerThursday.'</td>
				<td>'.$toddlerFriday.'</td>			
			</tr>
		</tfoot>
		</table>';	
		
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
				$mondayValue = $item['qty'];
			}
			elseif($chkSku[1]=="tuesday") {
				$tuesdayValue = $item['qty'];
			}
			elseif($chkSku[1]=="wednesday") {
				$wednesdayValue = $item['qty'];
			}
			elseif($chkSku[1]=="thursday") {
				$thursdayValue = $item['qty'];
			}
			elseif($chkSku[1]=="friday") {
				$fridayValue = $item['qty'];
			}
			
		}
		  
		$html = '<table border="1" cellpadding="10">
		<thead>
			<tr style="background-color:#84bc3d;color:#fff;">
				<td colspan="5"></td>
				<td>Mon</td>
				<td>Tue</td>
				<td>Wed</td>
				<td>Thu</td>
				<td>Fri</td>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td colspan="5">'.$menuName.'</td>
				<td>'.($mondayValue-($allergyMonday+$toddlerMonday)).'</td>
				<td>'.($tuesdayValue-($allergyTuesday+$toddlerTuesday)).'</td>
				<td>'.($wednesdayValue-($allergyWednesday+$toddlerWednesday)).'</td>
				<td>'.($thursdayValue-($allergyThursday+$toddlerThursday)).'</td>
				<td>'.($fridayValue-($allergyFriday+$toddlerFriday)).'</td>
			</tr>
		</tbody>
		</table>';
		
		$pdf->SetFont('helvetica', '', 12);	
		
		$pdf->writeHTML($html, true, true, true, true, '');	
		
		$pdf->Ln(10);
		
		$pdf->SetFont('helvetica', '', 14);	
		
		$pdf->writeHTML('<u style="text-align:center;"><b>Allergen Menu</b></u>', true, true, true, true, '');
		
		$pdf->Ln(5);	
		
		$pdf->SetFont('helvetica', '', 12);	
	
		$pdf->writeHTML($allergyHtml, true, true, true, true, '');	
		
		$pdf->Ln(15);
		
		$pdf->Ln(10);
		
		$pdf->SetFont('helvetica', '', 14);	
		
		$pdf->writeHTML('<u style="text-align:center;"><b>Toddler Menu</b></u>', true, true, true, true, '');
		
		$pdf->Ln(5);	
		
		$pdf->SetFont('helvetica', '', 12);	
	
		$pdf->writeHTML($toddlerHtml, true, true, true, true, '');	

		$pdf->Ln(15);
		
		$pdf->SetFont('helvetica', '', 14);	
		
		$pdf->writeHTML('<u style="text-align:center;"><b>Totals</b></u>', true, true, true, true, '');
		
		$pdf->Ln(5);
		
		$pdf->SetFont('helvetica', '', 12);	
		
	
		$html = '<table border="1" cellpadding="10">
		<thead>
			<tr>
				<td colspan="5" style="background-color:#84bc3d;color:#fff;">Totals</td>
				<td>'.($mondayValue).'</td>
				<td>'.($tuesdayValue).'</td>
				<td>'.($wednesdayValue).'</td>
				<td>'.($thursdayValue).'</td>
				<td>'.($fridayValue).'</td>            
			</tr>
		</thead>    
		</table>';
		
		$pdf->writeHTML($html, true, true, true, true, '');	
	}
	// reset pointer to the last page
	$pdf->lastPage();
	
	// ---------------------------------------------------------
	ob_end_clean();	 
	//Close and output PDF document
	$PdfName = CUS_WOO_ORDER_PLUGIN_DIR.'/pdffile/daily_order_'.$stateCode.'.pdf';
	$pdf->Output($PdfName, 'F');
	return $PdfName;
	?>