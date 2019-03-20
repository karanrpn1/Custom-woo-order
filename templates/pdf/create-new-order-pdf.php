<?php	
	$pdf = new TCPDF('P', PDF_UNIT, 'A3', true, 'UTF-8', false);
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
					
	$pdf->Image($logo, '', '', 70, 30, '', '', 'T', false, 300, '', false, false, 1, false, false, false);
		 
	$pdf->SetFont('leckerlione', '', 36, '', false);
	
	$pdf->SetTextColor('132','188','61');

	//$pdf->writeHTMLCell(0, 'Meal Summary Confirmation', '', 0, 'C', true, 0, false, false, 0);
	
	$y = $pdf->getY();
	
	$pdf->SetFillColor(255, 255, 255);
	
	$pdf->writeHTMLCell(80, '', '', $y, "", 0, 0, 1, true, 'J', true);
			
	$pdf->writeHTMLCell(200, '', '', '', "Meal Summary Confirmation", 0, 1, 1, true, 'C', true);
	
	$pdf->SetFont('helvetica', 'B', 14);
	 
	$pdf->SetTextColor('0','0','0');
	 
	$pdf->Ln(2);
	
	$y = $pdf->getY();
	
	$pdf->writeHTMLCell(80, '', '', $y, "", 0, 0, 1, true, 'J', true);
			
	$pdf->writeHTMLCell(200, '', '', '', "Your new Meal Summary has been submitted to Hearty Health", 0, 1, 1, true, 'C', true);	
	
	$y = $pdf->getY();
	
	$pdf->writeHTMLCell(80, '', '', $y, "", 0, 0, 1, true, 'J', true);
			
	$pdf->writeHTMLCell(200, '', '', '', "Please allow up to 48 hours for changes to take effect", 0, 1, 1, true, 'C', true);
	
	$pdf->Ln(10);		
	
	global $wpdb;	
	$tablename = $wpdb->prefix.'order_info';
	$orderInfo = $wpdb->get_results( "SELECT * FROM $tablename where ID=$id");
	
	$userIdCloseDate = 0;
	foreach ($orderInfo as $row ) {
		
		$userIdCloseDate = $row->user_id;
		
		$user = get_user_by( 'id', $row->user_id );
		
		$order = wc_get_order( $row->order_id );		
		$items = $order->get_items();
		
		$mondayValue = 0;
		$tuesdayValue = 0; 
		$wednesdayValue = 0;
		$thursdayValue = 0;
		$fridayValue = 0;
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
		
		$pdf->Ln(15);
		
		$nextMonday = strtotime('next monday');
		
		$pdf->writeHTML('<u style="text-align:center;">This new Meal Summary applies for deliveries as of '.date("l jS F Y",$nextMonday).'</u>', true, true, true, true, '');
		
		$pdf->Ln(10);
		 
		
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
				<td>Total</td>
			</tr>
		</thead>';
		
		$allergyMonday = 0;	
		$allergyTuesday = 0;	
		$allergyWednesday = 0;	
		$allergyThursday = 0;	
		$allergyFriday = 0;	
		
		$childCount = get_post_meta( $row->order_id, 'child_count', true ); 
		
		for($i=1;$i<=$childCount;$i++){
			
			$attend = json_decode(get_post_meta( $row->order_id, 'child_attend_'.$i, true ));
			$allergyGroup = "";
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
			
			if(get_post_meta( $row->order_id, 'child_toddler_toggle_'.$i, true )==1) {
				$toddlerLabel = "<strong> Toddler</strong>";
			}
			else {
				$toddlerLabel = "";
			}
			
			
					
			$allergyHtml.='<tbody>
				<tr>
					<td colspan="2">'.$childName.' '.$toddlerLabel.'</td>
					<td colspan="3">'.$allergyGroup.'</td> 
					<td>'.(in_array("Monday",$attend)?"1":"").'</td>
					<td>'.(in_array("Tuesday",$attend)?"1":"").'</td>
					<td>'.(in_array("Wednesday",$attend)?"1":"").'</td>
					<td>'.(in_array("Thursday",$attend)?"1":"").'</td>
					<td>'.(in_array("Friday",$attend)?"1":"").'</td>
					<td>'.count($attend).'</td>
				</tr>';		
					
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
		}	
		  
		$pdf->writeHTML('<u style="text-align:center;"><b>Regular Menu</b></u>', true, true, true, true, '');	
		
		$pdf->Ln(5); 
		 
		$html = '<table border="1" cellpadding="10">
		<thead >
			<tr style="background-color:#84bc3d;color:#fff;">
				<td colspan="5"></td>
				<td>Mon</td>
				<td>Tue</td>
				<td>Wed</td>
				<td>Thu</td>
				<td>Fri</td>
				<td>Total</td>
			</tr>
		</thead>  
		<tbody>
			<tr>
				<td colspan="5">Number of '.$menuName.'</td>
				<td>'.($mondayValue-$allergyMonday).'</td>
				<td>'.($tuesdayValue-$allergyTuesday).'</td>
				<td>'.($wednesdayValue-$allergyWednesday).'</td>
				<td>'.($thursdayValue-$allergyThursday).'</td>
				<td>'.($fridayValue-$allergyFriday).'</td>
				<td>'.(($mondayValue-$allergyMonday)+($tuesdayValue-$allergyTuesday)+($wednesdayValue-$allergyWednesday)+($thursdayValue-$allergyThursday)+($fridayValue-$allergyFriday)).'</td>
			</tr>
		</tbody>
		</table>';
		
		$pdf->SetFont('helvetica', '', 12);
		
		$pdf->writeHTML($html, true, true, true, true, '');	
		
		$pdf->Ln(10);
		
		$allergyHtml.='</tbody>
		<tfoot>
			<tr>
				<td colspan="5">Allergy Sub Totals</td>
				<td>'.$allergyMonday.'</td>
				<td>'.$allergyTuesday.'</td>
				<td>'.$allergyWednesday.'</td>
				<td>'.$allergyThursday.'</td>
				<td>'.$allergyFriday.'</td>	
				<td>'.($allergyMonday+$allergyTuesday+$allergyWednesday+$allergyThursday+$allergyFriday).'</td>	
			</tr>
		</tfoot>
		</table>';
		
		$pdf->SetFont('helvetica', '', 14);
		
		$pdf->writeHTML('<u style="text-align:center;"><b>Allergen & Toddler Menu</b></u>', true, true, true, true, '');
		
		$pdf->Ln(5);	
		
		$pdf->SetFont('helvetica', '', 12);
	
		$pdf->writeHTML($allergyHtml, true, true, true, true, '');			
		
		$pdf->Ln(10);
		
		$pdf->SetFont('helvetica', '', 14);
		
		$pdf->writeHTML('<u style="text-align:center;"><b>Totals</b></u>', true, true, true, true, '');
		
		$pdf->Ln(5);
		
		$pdf->SetFont('helvetica', '', 12);
	
		$html = '<table border="1" cellpadding="10">
		<thead>
			<tr>
				<td colspan="5" style="background-color:#84bc3d;color:#fff;">Meals per day</td>
				<td>'.($mondayValue).'</td>
				<td>'.($tuesdayValue).'</td>
				<td>'.($wednesdayValue).'</td>
				<td>'.($thursdayValue).'</td>
				<td>'.($fridayValue).'</td>
				<td>'.($mondayValue+$tuesdayValue+$wednesdayValue+$thursdayValue+$fridayValue).'</td>            
			</tr>
		</thead>    
		</table>';
		
		$pdf->writeHTML($html, true, true, true, true, '');	
		
		$pdf->Ln(15);
		
		$pdf->SetFont('helvetica', '', 14);
		
		$pdf->writeHTML('<u style="text-align:center;"><b>Close Dates</b></u>', true, true, true, true, '');
		
		$pdf->Ln(5);
		
		$pdf->SetFont('helvetica', '', 12);
		
		$closeDateHtml = '<table border="1" cellpadding="10">
		<thead>
			<tr>
				<td colspan="5" style="background-color:#84bc3d;color:#fff;">Date</td>
				<td colspan="6" style="background-color:#84bc3d;color:#fff;">Remark</td>
			</tr>
		</thead>
		<tbody>';
		
		$tablename = $wpdb->prefix.'close_date_info';	
        $closeDateFormInfo = $wpdb->get_results( "SELECT * FROM $tablename where user_id='".$userIdCloseDate."'  and startDate>'".strtotime('tomorrow')."' ");
		
		foreach($closeDateFormInfo as $row) {
			$date = "";
			if(!empty($row->startDate)) { 
				$date.= date("F d,Y",$row->startDate); 
			} 
			if(!empty($row->endDate)) {	
				$date.= ' - '.date("F d,Y",$row->endDate);
			}						
			$closeDateHtml.= '<tr><td colspan="5">'.$date.'</td><td colspan="6">'.$row->reason.'</td></tr>';
		}
		 
		$closeDateHtml.= '</tbody></table>';
		
		$pdf->writeHTML($closeDateHtml, true, true, true, true, '');	
	}
	// reset pointer to the last page
	$pdf->lastPage(); 
	
	// ---------------------------------------------------------
	ob_end_clean();	 
	//Close and output PDF document
	$PdfName = CUS_WOO_ORDER_PLUGIN_DIR.'/pdffile/order_create'.rand().'.pdf';
	//$PdfName = CUS_WOO_ORDER_PLUGIN_DIR.'/pdffile/order_create.pdf';
	$pdf->Output($PdfName, 'F');
	return $PdfName; 
?>