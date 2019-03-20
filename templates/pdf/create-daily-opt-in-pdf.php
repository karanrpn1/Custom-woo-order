<?php 
	
	$pdf = new TCPDF('P', PDF_UNIT, 'A3', true, 'UTF-8', false);

	// set document information
	$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetAuthor('Karan Rupani');
	$pdf->SetTitle('Daily Opt In summary'); 
	$pdf->SetSubject('Daily Opt In summary');
	$pdf->SetKeywords('Daily Opt In summary'); 
	
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
			
	$pdf->writeHTMLCell(150, '', '', '', "Opt In Order Summary", 0, 1, 1, true, 'C', true);
	
	$pdf->SetFont('leckerlione', '', 20);	
	
	$pdf->SetTextColor('0','0','0');
	
	$y = $pdf->getY();
	
	$pdf->SetFillColor(255, 255, 255);
	
	$pdf->writeHTMLCell(80, '', '', $y, "", 0, 0, 1, true, 'J', true);
			
	$pdf->writeHTMLCell(150, '', '', '', "All Customers", 0, 1, 1, true, 'C', true);	
	
	$pdf->SetFont('helvetica', '', 14);
	
	$y = $pdf->getY();
	
	$pdf->SetFillColor(255, 255, 255);
	
	$pdf->writeHTMLCell(80, '', '', $y, "", 0, 0, 1, true, 'J', true);
			
	$pdf->writeHTMLCell(150, '', '', '', '<i>'.date("jS M Y").'</i>', 0, 1, 1, true, 'C', true);
	
	$pdf->Ln(10);
	
	global $wpdb;
	$tablename = $wpdb->prefix.'opt_in_alternative';
	
	$todayTime = strtotime('today midnight');
	$tomorrowTime = strtotime('tomorrow');
	$optInInfo = $wpdb->get_results( "SELECT * FROM $tablename where date=$todayTime");
	
	$spaghettiHtml = '';
	
	$sandwichesHtml = '';
	
	$pumpkinHtml = '';
	
	$spaghettiTotal = 0;
	$sandwichesTotal = 0;
	$pumpkinTotal = 0;
	
	foreach ($optInInfo as $row ) {
		$user = get_user_by( 'id', $row->user_id );
		
		$orderTableName = $wpdb->prefix.'order_info';		
		$orderInfo = $wpdb->get_results( "SELECT * FROM $orderTableName where ID=$row->order_info_id");
		
		$order = wc_get_order( $orderInfo[0]->order_id );
	
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
		
		if($checkCloseDate==1) { continue; }	
		
		
		$childCount = get_post_meta( $orderInfo[0]->order_id, 'child_count', true ); 
		
		if($row->choice==1) {
			$spaghettiHtml.= '<table border="1" cellpadding="10">
				<thead>
					<tr style="background-color:#84bc3d;color:#fff;">
						<td>Centre</td>
						<td colspan="2">Allergens</td>
						<td>Total</td>					
					</tr>
				</thead>
				<tbody>';
			$spaghettiHtml.= '<tr>
					<td rowspan="'.($childCount+2).'">'.$user->billing_company.'</td>
					<td colspan="2">Regular</td>
					<td>'.($row->total_package-$childCount).'</td>						
				</tr>';
		}
		elseif($row->choice==2) { 
			$sandwichesHtml.= '<table border="1" cellpadding="10">
				<thead>
					<tr style="background-color:#84bc3d;color:#fff;">
						<td>Centre</td>
						<td colspan="2">Allergens</td>
						<td>Total</td>								
					</tr>
				</thead>
				<tbody>';
			$sandwichesHtml.= '<tr>
					<td rowspan="'.($childCount+2).'">'.$user->billing_company.'</td>
					<td colspan="2">Regular</td>
					<td>'.($row->total_package-$childCount).'</td>						
				</tr>';
		}
		elseif($row->choice==3) { 
			$pumpkinHtml.= '<table border="1" cellpadding="10">
				<thead>
					<tr style="background-color:#84bc3d;color:#fff;">
						<td>Centre</td>
						<td colspan="2">Allergens</td>
						<td>Total</td>							
					</tr>
				</thead>
				<tbody>';
			
			$pumpkinHtml.= '<tr>
					<td rowspan="'.($childCount+2).'">'.$user->billing_company.'</td>
					<td colspan="2">Regular</td>
					<td>'.($row->total_package-$childCount).'</td>						
				</tr>';
		}
		
		
		for($i=1;$i<=$childCount;$i++){
			$attend = json_decode(get_post_meta( $orderInfo[0]->order_id, 'child_attend_'.$i, true ));
			$allergyGroup ="";
			if(!empty(get_post_meta( $orderInfo[0]->order_id, 'child_allergy_dairy_'.$i, true )) && (get_post_meta( $orderInfo[0]->order_id, 'child_allergy_dairy_'.$i, true )<>"null")) {
				$allergyGroup.= implode(", ",json_decode(get_post_meta( $orderInfo[0]->order_id, 'child_allergy_dairy_'.$i, true )) ).", ";
			}
			if(!empty(get_post_meta( $orderInfo[0]->order_id, 'child_allergy_egg_'.$i, true )) && (get_post_meta( $orderInfo[0]->order_id, 'child_allergy_egg_'.$i, true )<>"null")) {
				$allergyGroup.= implode(", ",json_decode(get_post_meta( $orderInfo[0]->order_id, 'child_allergy_egg_'.$i, true ))).", ";
			}			
			if(!empty(get_post_meta( $orderInfo[0]->order_id, 'child_allergy_vegetarian_'.$i, true )) && (get_post_meta( $orderInfo[0]->order_id, 'child_allergy_vegetarian_'.$i, true )<>"null") ) {
				$allergyGroup.= implode(", ",json_decode(get_post_meta( $orderInfo[0]->order_id, 'child_allergy_vegetarian_'.$i, true ))).", ";
			}
			if(!empty(get_post_meta( $orderInfo[0]->order_id, 'child_allergy_grains_'.$i, true )) && (get_post_meta( $orderInfo[0]->order_id, 'child_allergy_grains_'.$i, true )<>"null")) {
				$allergyGroup.= implode(", ",json_decode(get_post_meta( $orderInfo[0]->order_id, 'child_allergy_grains_'.$i, true ))).", ";
			}
			/* added by comfusion */
			if(!empty(get_post_meta( $orderInfo[0]->order_id, 'child_allergy_meat_'.$i, true )) && (get_post_meta( $orderInfo[0]->order_id, 'child_allergy_meat_'.$i, true )<>"null")) {
				$allergyGroup.= implode(", ",json_decode(get_post_meta( $orderInfo[0]->order_id, 'child_allergy_meat_'.$i, true ))).", ";
			}
			/* end modification */
			if(!empty(get_post_meta( $orderInfo[0]->order_id, 'child_allergy_seafood_'.$i, true )) && (get_post_meta( $orderInfo[0]->order_id, 'child_allergy_seafood_'.$i, true )<>"null")) {
				$allergyGroup.= implode(", ",json_decode(get_post_meta( $orderInfo[0]->order_id, 'child_allergy_seafood_'.$i, true ))).", ";
			}
			if(!empty(get_post_meta( $orderInfo[0]->order_id, 'child_allergy_soyproduct_'.$i, true )) && (get_post_meta($orderInfo[0]->order_id, 'child_allergy_soyproduct_'.$i, true )<>"null")) {
				$allergyGroup.= implode(", ",json_decode(get_post_meta( $orderInfo[0]->order_id, 'child_allergy_soyproduct_'.$i, true ))).", ";
			}
			if(!empty(get_post_meta( $orderInfo[0]->order_id, 'child_allergy_legumes_'.$i, true )) && (get_post_meta( $orderInfo[0]->order_id, 'child_allergy_legumes_'.$i, true )<>"null")) {
				$allergyGroup.= implode(", ",json_decode(get_post_meta($orderInfo[0]->order_id, 'child_allergy_legumes_'.$i, true ))).", ";
			}
			if(!empty(get_post_meta( $orderInfo[0]->order_id, 'child_allergy_fruits_'.$i, true )) && (get_post_meta( $orderInfo[0]->order_id, 'child_allergy_fruits_'.$i, true )<>"null")) {
				$allergyGroup.= implode(",",json_decode(get_post_meta( $orderInfo[0]->order_id, 'child_allergy_fruits_'.$i, true ))).", ";
			}
			if(!empty(get_post_meta( $orderInfo[0]->order_id, 'child_allergy_vegetables_'.$i, true )) && (get_post_meta( $orderInfo[0]->order_id, 'child_allergy_vegetables_'.$i, true )<>"null")) {
				$allergyGroup.= implode(", ",json_decode(get_post_meta( $orderInfo[0]->order_id, 'child_allergy_vegetables_'.$i, true ))).", ";
			}
			if(!empty(get_post_meta( $orderInfo[0]->order_id, 'child_allergy_nuts_'.$i, true )) && (get_post_meta( $orderInfo[0]->order_id, 'child_allergy_nuts_'.$i, true )<>"null")) {
				$allergyGroup.= implode(", ",json_decode(get_post_meta($orderInfo[0]->order_id, 'child_allergy_nuts_'.$i, true ))).", ";
			}
			if(!empty(get_post_meta( $orderInfo[0]->order_id, 'child_allergy_others_'.$i, true )) && (get_post_meta( $orderInfo[0]->order_id, 'child_allergy_others_'.$i, true )<>"null")) {
				$allergyGroup.= implode(", ",json_decode(get_post_meta( $orderInfo[0]->order_id, 'child_allergy_others_'.$i, true ))).", ";
			}
			
			$childName = get_post_meta( $orderInfo[0]->order_id, 'child_name_'.$i, true );
			if(!empty($allergyGroup)) {
				$childName.= " - ".rtrim($allergyGroup,', ');
			}	
			
			if($row->choice==1) {	
				$spaghettiHtml.= '<tr>					
					<td colspan="2">'.$childName.'</td>
					<td>1</td>						
				</tr>';	
			}
			elseif($row->choice==2) {	
				$sandwichesHtml.= '<tr>				
					<td colspan="2">'.$childName.'</td>
					<td>1</td>						
				</tr>';	
			}
			elseif($row->choice==3) {	
				$pumpkinHtml.= '<tr>				
					<td colspan="2">'.$childName.'</td>
					<td>1</td>						
				</tr>';	
			}
			
		}
		
		if($row->choice==1) {
			$spaghettiHtml.= '</tbody><tfoot><tr><td colspan="2">Total</td><td>'.$row->total_package.'</td></tr></tfoot></table><br><br><br>';			
		
		}
		elseif($row->choice==2) {
			$sandwichesHtml.= '</tbody><tfoot><tr><td colspan="2">Total</td><td>'.$row->total_package.'</td></tr></tfoot></table><br><br><br>';				
		}
		elseif($row->choice==3) {
			$pumpkinHtml.= '</tbody><tfoot><tr><td colspan="2">Total</td><td>'.$row->total_package.'</td></tr></tfoot></table><br><br><br>';			
		}
		
		
	}
	
	if(!empty($spaghettiHtml)) { 
		
		$pdf->writeHTML('<u style="text-align:center;"><b>Spaghetti Bolognaise</b></u>', true, true, true, true, '');	
	
		$pdf->Ln(5);
	
		$pdf->writeHTML($spaghettiHtml, true, true, true, true, '');	
		
		$pdf->Ln(10);
	}
	
	
	if(!empty($sandwichesHtml)) { 
	
		$pdf->writeHTML('<u style="text-align:center;"><b>Sandwiches</b></u>', true, true, true, true, '');	
		
		$pdf->Ln(5);	
			
		$pdf->writeHTML($sandwichesHtml, true, true, true, true, '');
		
		$pdf->Ln(10);
	}
	
	if(!empty($pumpkinHtml)) { 
	
		$pdf->writeHTML('<u style="text-align:center;"><b>Pumpkin Soup with bread</b></u>', true, true, true, true, '');	
		
		$pdf->Ln(5);	
			
		$pdf->writeHTML($pumpkinHtml, true, true, true, true, '');
	}
	
	if(empty($spaghettiHtml) && empty($sandwichesHtml) && empty($pumpkinHtml)) { 
		
		$pdf->Ln(5);	
			
		$pdf->writeHTML('<b style="text-align:center;">No Data</b>', true, true, true, true, '');	
	}	
	
	// reset pointer to the last page
	$pdf->lastPage();
	
	// ---------------------------------------------------------
	ob_end_clean();	 
	//Close and output PDF document
	$PdfName = CUS_WOO_ORDER_PLUGIN_DIR.'/pdffile/daily_opt_in_'.$stateCode.'.pdf';
	$pdf->Output($PdfName, 'F');
	return $PdfName;
	
	
?>