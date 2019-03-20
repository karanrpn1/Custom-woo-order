<?php

	
	$pdf = new TCPDF(P, PDF_UNIT, A3, true, 'UTF-8', false);
	// set document information
	$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetAuthor('Karan Rupani');
	$pdf->SetTitle('Meal Summary');
	$pdf->SetSubject('New Order Pdf');
	$pdf->SetKeywords('New Order Pdf');
	
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
					
	$pdf->Image($logo, '', '', 50, 30, '', '', 'T', false, 300, '', false, false, 1, false, false, false);
	
	$pdf->Ln(5);	  
		 
	$pdf->SetFont('helvetica', 'B', 26);	
	
	$pdf->SetTextColor('171','13','14');

	$pdf->Write(0, 'Meal Summary Confirmation', '', 0, 'C', true, 0, false, false, 0);
			
	$pdf->SetFont('helvetica', 'B', 14);
	
	$pdf->SetTextColor('0','0','0');
	 
	$pdf->Ln(2);	  
	
	$pdf->Write(0, 'Your new Meal Summary has been submitted to Hearty Health ', '', 0, 'C', true, 0, false, false, 0);	
	
	$pdf->Write(0, 'Please allow up to 48 hours for changes to take effect', '', 0, 'C', true, 0, false, false, 0);		
	
	$pdf->Ln(10);		
	
	global $wpdb;
	$tablename = $wpdb->prefix.'order_info';
	$orderInfo = $wpdb->get_results( "SELECT * FROM $tablename where order_id=$order_id");
	
	foreach ($orderInfo as $row ) {
		
		$user = get_user_by( 'id', $row->user_id );
		
		$order = wc_get_order( $row->order_id );
		$items = $order->get_items();
		
		$order_data = $order->get_data();		
		$order_status = $order_data['status'];		
		
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
		}
		
		$left_column = 'Centre';

		$right_column = $user->billing_company;

		$pdf->SetFillColor(255, 255, 255);
		
		$pdf->SetFont('helvetica', 'B', 14);
		
		$pdf->MultiCell(40, 0, $left_column, 0, 'L', 1, 0, '', '', true, 0, false, true, 0);			
		$pdf->SetFont('helvetica', '', 14);
		
		$pdf->MultiCell(100, 0, $right_column, 0, 'L', 1, 0, '', '', true, 0, false, true, 0);		
		
		$left_column = 'Date';
		
		$right_column = date("D jS F Y");

		$pdf->SetFillColor(255, 255, 255);
		
		$pdf->setCellMargins(40,1,10,1);
		
		$pdf->SetFont('helvetica', 'B', 14);
		
		$pdf->MultiCell(30, 0, $left_column, 0, 'L', 1, 0, '', '', true, 0, false, true, 0);			
		$pdf->setCellMargins(1, 1, 1, 1); 
		
		$pdf->SetFont('helvetica', '', 14);
		
		$pdf->MultiCell(60, 0, $right_column, 0, 'L', 1, 1, '', '', true, 0, false, true, 0);		
		
		$left_column = 'Closure Date';

		$right_column = 'None';

		$pdf->SetFillColor(255, 255, 255);
		
		$pdf->setCellMargins(0,0,0,0);
		
		$pdf->SetFont('helvetica', 'B', 14);
		
		$pdf->MultiCell(40, 0, $left_column, 0, 'L', 1, 0, '', '', true, 0, false, true, 0);		
		$pdf->SetFont('helvetica', '', 14);
			
		$pdf->MultiCell(80, 0, $right_column, 0, 'L', 1, 0, '', '', true, 0, false, true, 0);		
		
		$left_column = 'Time';

		$right_column = date("g:i a");

		$pdf->SetFillColor(255, 255, 255);
		
		$pdf->setCellMargins(60,1,10,1);
		
		$pdf->SetFont('helvetica', 'B', 14);
		
		$pdf->MultiCell(30, 0, $left_column, 0, 'L', 1, 0, '', '', true, 0, false, true, 0);			
		$pdf->setCellMargins(1,1,1,1);
		
		$pdf->SetFont('helvetica', '', 14);
		
		$pdf->MultiCell(60, 0, $right_column, 0, 'L', 1, 1, '', '', true, 0, false, true, 0);		
		
		$left_column = 'Menu Selection';

		$right_column = $menuName;

		$pdf->SetFillColor(255, 255, 255);
		
		$pdf->setCellMargins(0,0,0,0);
		
		$pdf->SetFont('helvetica', 'B', 14);
		
		$pdf->MultiCell(40, 0, $left_column, 0, 'L', 1, 0, '', '', true, 0, false, true, 0);			
		$pdf->SetFont('helvetica', '', 14);
		
		$pdf->MultiCell(80, 0, $right_column, 0, 'L', 1, 1, '', '', true, 0, false, true, 0);		 
		
		$pdf->setCellMargins(0, 0, 0, 0); 
		
		$pdf->Ln(10);
		
		$pdf->writeHTML('<u style="text-align:center;"><b>Regular Menu</b></u>', true, true, true, true, '');	
		
		$pdf->Ln(5);
		
		$allergyHtml = '<table border="1" cellpadding="10">
		<thead>
			<tr>
				<td>Name</td>
				<td>Allergen Group</td>
				<td>Monday</td>
				<td>Tuesday</td>
				<td>Wednesday</td>
				<td>Thursday</td>
				<td>Friday</td>
			</tr>
		</thead>';
		$allergyMonday = 0;	
		$allergyTuesday = 0;	
		$allergyWednesday = 0;	
		$allergyThursday = 0;	
		$allergyFriday = 0;	
		
		$toddlerHtml = '<table border="1" cellpadding="10">
		<thead>
			<tr>
				<td>Name</td>
				<td>Allergen Group</td>
				<td>Monday</td>
				<td>Tuesday</td>
				<td>Wednesday</td>
				<td>Thursday</td>
				<td>Friday</td>
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
				$allergyGroup.= implode(",",json_decode(get_post_meta( $row->order_id, 'child_allergy_dairy_'.$i, true )) ).",";
			}
			if(!empty(get_post_meta( $row->order_id, 'child_allergy_egg_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_egg_'.$i, true )<>"null")) {
				$allergyGroup.= implode(",",json_decode(get_post_meta( $row->order_id, 'child_allergy_egg_'.$i, true ))).",";
			}			
			if(!empty(get_post_meta( $row->order_id, 'child_allergy_vegetarian_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_vegetarian_'.$i, true )<>"null") ) {
				$allergyGroup.= implode(",",json_decode(get_post_meta( $row->order_id, 'child_allergy_vegetarian_'.$i, true ))).",";
			}
			if(!empty(get_post_meta( $row->order_id, 'child_allergy_grains_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_grains_'.$i, true )<>"null")) {
				$allergyGroup.= implode(",",json_decode(get_post_meta( $row->order_id, 'child_allergy_grains_'.$i, true ))).",";
			}
			if(!empty(get_post_meta( $row->order_id, 'child_allergy_seafood_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_seafood_'.$i, true )<>"null")) {
				$allergyGroup.= implode(",",json_decode(get_post_meta( $row->order_id, 'child_allergy_seafood_'.$i, true ))).",";
			}
			if(!empty(get_post_meta( $row->order_id, 'child_allergy_soyproduct_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_soyproduct_'.$i, true )<>"null")) {
				$allergyGroup.= implode(",",json_decode(get_post_meta( $row->order_id, 'child_allergy_soyproduct_'.$i, true ))).",";
			}
			if(!empty(get_post_meta( $row->order_id, 'child_allergy_legumes_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_legumes_'.$i, true )<>"null")) {
				$allergyGroup.= implode(",",json_decode(get_post_meta( $row->order_id, 'child_allergy_legumes_'.$i, true ))).",";
			}
			if(!empty(get_post_meta( $row->order_id, 'child_allergy_fruits_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_fruits_'.$i, true )<>"null")) {
				$allergyGroup.= implode(",",json_decode(get_post_meta( $row->order_id, 'child_allergy_fruits_'.$i, true ))).",";
			}
			if(!empty(get_post_meta( $row->order_id, 'child_allergy_vegetables_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_vegetables_'.$i, true )<>"null")) {
				$allergyGroup.= implode(",",json_decode(get_post_meta( $row->order_id, 'child_allergy_vegetables_'.$i, true ))).",";
			}
			if(!empty(get_post_meta( $row->order_id, 'child_allergy_nuts_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_nuts_'.$i, true )<>"null")) {
				$allergyGroup.= implode(",",json_decode(get_post_meta( $row->order_id, 'child_allergy_nuts_'.$i, true ))).",";
			}
			if(!empty(get_post_meta( $row->order_id, 'child_allergy_others_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_others_'.$i, true )<>"null")) {
				$allergyGroup.= implode(",",json_decode(get_post_meta( $row->order_id, 'child_allergy_others_'.$i, true ))).",";
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
					<td>'.get_post_meta( $row->order_id, 'child_name_'.$i, true ).'</td>
					<td>'.rtrim($allergyGroup, ',').'</td> 
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
					<td>'.get_post_meta( $row->order_id, 'child_name_'.$i, true ).'</td>
					<td>'.rtrim($allergyGroup, ',').'</td> 
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
				<td colspan="2">Allergy Sub Totals</td>
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
				<td colspan="2">Toddler Sub Totals</td>
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
			<tr>
				<td colspan="2"></td>
				<td>Monday</td>
				<td>Tuesday</td>
				<td>Wednesday</td>
				<td>Thursday</td>
				<td>Friday</td>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td colspan="2">'.$menuName.'</td>
				<td>'.($mondayValue-($allergyMonday+$toddlerMonday)).'</td>
				<td>'.($tuesdayValue-($allergyTuesday+$toddlerTuesday)).'</td>
				<td>'.($wednesdayValue-($allergyWednesday+$toddlerWednesday)).'</td>
				<td>'.($thursdayValue-($allergyThursday+$toddlerThursday)).'</td>
				<td>'.($fridayValue-($allergyFriday+$toddlerFriday)).'</td>
			</tr>
		</tbody>
		</table>';
		
		$pdf->writeHTML($html, true, true, true, true, '');	
		
		$pdf->Ln(10);
		
		$pdf->writeHTML('<u style="text-align:center;"><b>Allergen Menu</b></u>', true, true, true, true, '');
		
		$pdf->Ln(5);	
	
		$pdf->writeHTML($allergyHtml, true, true, true, true, '');	
		
		$pdf->Ln(10);
		
		$pdf->writeHTML('<u style="text-align:center;"><b>Toddler Menu</b></u>', true, true, true, true, '');
		
		$pdf->Ln(5);	
	
		$pdf->writeHTML($toddlerHtml, true, true, true, true, '');	
		
		$pdf->Ln(10);
		
		$pdf->writeHTML('<u style="text-align:center;"><b>Totals</b></u>', true, true, true, true, '');
		
		$pdf->Ln(5);
	
		$html = '<table border="1" cellpadding="10">
		<thead>
			<tr>
				<td colspan="2">Totals</td>
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
	$PdfName = CUS_WOO_ORDER_PLUGIN_DIR.'/pdffile/order_create'.rand().'.'.pdf;
	$pdf->Output($PdfName, 'F');
	return $PdfName; 
?>