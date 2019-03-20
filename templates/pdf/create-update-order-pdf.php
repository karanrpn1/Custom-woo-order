<?php

	
	$pdf = new TCPDF('P', PDF_UNIT, 'A3', true, 'UTF-8', false);

	// set document information
	$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetAuthor('Karan');
	$pdf->SetTitle('Update Order');
	$pdf->SetSubject('Update Order');
	$pdf->SetKeywords('Update Order');
	
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
			
	$pdf->writeHTMLCell(200, '', '', '', "Order Update Summary", 0, 1, 1, true, 'C', true);
	
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
	$orderInfo = $wpdb->get_results( "SELECT * FROM $tablename where ID=$order_id");
	
	$userIdCloseDate = 0;
	foreach ($orderInfo as $row ) {
		
		$userIdCloseDate = $row->user_id;
		
		$user = get_user_by( 'id', $row->user_id );	
		
		$order = wc_get_order( $row->order_id );		
		$items = $order->get_items();
		
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
		
		$left_column = 'Menu Selection';  
 
		$right_column = $menuName;

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
		
		$pdf->setCellMargins(0, 0, 0, 0); 
		
		$pdf->Ln(15);
		
		$nextMonday = strtotime('+2 days');
		
		$pdf->writeHTML('<u style="text-align:center;">This new Meal Summary applies for deliveries as of '.date("l jS F Y",$nextMonday).'</u>', true, true, true, true, '');
		
		$pdf->Ln(10);	
		
		$pdf->writeHTML('<u style="text-align:center;"><b>Regular Menu</b></u>', true, true, true, true, '');	 
		 
		$pdf->Ln(5);		
		 
		$items = json_decode($row->item_sku,true);
		$itemQty = json_decode($row->item_quantity,true);
		
		$i = 0;
		foreach( $items as $item){			
			$item_sku = $item; 
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
				$mondayValue = $itemQty[$i];
			}
			elseif($chkSku[1]=="tuesday") {
				$tuesdayValue = $itemQty[$i];
			}
			elseif($chkSku[1]=="wednesday") {
				$wednesdayValue = $itemQty[$i];
			}
			elseif($chkSku[1]=="thursday") {
				$thursdayValue = $itemQty[$i];
			}
			elseif($chkSku[1]=="friday") {
				$fridayValue = $itemQty[$i];
			}
			$i++;
		}
	
		
		$html = '<table border="1" cellpadding="10">
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
		
		$childTabelName = $wpdb->prefix.'child_info';
		$childData = $wpdb->get_results( "SELECT * FROM $childTabelName where ID IN (".$row->allergy_child_ids.") " );
		 
		$allergyMonday = 0;	 
		$allergyTuesday = 0;	
		$allergyWednesday = 0;	
		$allergyThursday = 0;	
		$allergyFriday = 0;
		
		foreach($childData as $childRow ) {
			$attend = json_decode($childRow->attend,true);
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
			
			$allergyGroup ="";
			if(!empty($childRow->dairy) && ($childRow->dairy<>"null")) {
				$allergyGroup.= implode(", ",json_decode($childRow->dairy,true) ).", ";
			}
			if(!empty($childRow->egg) && ($childRow->egg<>"null")) {
				$allergyGroup.= implode(", ",json_decode($childRow->egg,true) ).", ";
			}
			if(!empty($childRow->vegetarian) && ($childRow->vegetarian<>"null")) {
				$allergyGroup.= implode(", ",json_decode($childRow->vegetarian,true) ).", ";
			}
			if(!empty($childRow->grains) && ($childRow->grains<>"null")) {
				$allergyGroup.= implode(", ",json_decode($childRow->grains,true) ).", ";
			}
			if(!empty($childRow->meat) && ($childRow->meat<>"null")) {
				$allergyGroup.= implode(", ",json_decode($childRow->meat,true) ).", ";
			}
			if(!empty($childRow->seafood) && ($childRow->seafood<>"null")) {
				$allergyGroup.= implode(", ",json_decode($childRow->seafood,true) ).", ";
			}
			if(!empty($childRow->soyproduct) && ($childRow->soyproduct<>"null")) {
				$allergyGroup.= implode(",",json_decode($childRow->soyproduct,true) ).",";
			}
			if(!empty($childRow->legumes) && ($childRow->legumes<>"null")) {
				$allergyGroup.= implode(",",json_decode($childRow->legumes,true) ).", ";
			}
			if(!empty($childRow->fruits) && ($childRow->fruits<>"null")) {
				$allergyGroup.= implode(", ",json_decode($childRow->fruits,true) ).", ";
			}
			if(!empty($childRow->vegetables) && ($childRow->vegetables<>"null")) {
				$allergyGroup.= implode(", ",json_decode($childRow->vegetables,true) ).", ";
			}
			if(!empty($childRow->nuts) && ($childRow->nuts<>"null")) {
				$allergyGroup.= implode(", ",json_decode($childRow->nuts,true) ).", ";
			}
			if(!empty($childRow->others) && ($childRow->others<>"null")) {
				$allergyGroup.= implode(", ",json_decode($childRow->others,true) ).", ";
			}
			
			if(!empty($childRow->remark) && ($childRow->remark<>"null")) {
				$allergyGroup = rtrim($allergyGroup, ', ' ).'<br>';
				$allergyGroup.= $childRow->remark;
			}  
			else {
				$allergyGroup = rtrim($allergyGroup, ', ' );
			}
			
			$childName = "";
			if(!empty($childRow->id_number) && ($childRow->id_number<>"null")) {
				$childName.= $childRow->id_number." - ";
			}
			if(!empty($childRow->child_name) && ($childRow->child_name<>"null")) {
				$childName.= $childRow->child_name;
			}
			
			if(!empty($childRow->roomNumber) && ($childRow->roomNumber<>"null")) {
				$childName.= " - ".$childRow->roomNumber;
			}
			
			if(!empty($childRow->toddler_toggle) && ($childRow->toddler_toggle<>"null")) {
				$toddlerLabel = "<strong> Toddler</strong>";
			}
			else {
				$toddlerLabel = "";
			}
			 
			
			$html.='<tbody>
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
		}
		$html.='</tbody>
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
		
		$regularHtml = '<table border="1" cellpadding="10">
		<thead>
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
				<td colspan="5">'.$menuName.'</td>
				<td>'.($mondayValue-$allergyMonday).'</td>
				<td>'.($tuesdayValue-$allergyTuesday).'</td>
				<td>'.($wednesdayValue-$allergyWednesday).'</td>
				<td>'.($thursdayValue-$allergyThursday).'</td>
				<td>'.($fridayValue-$allergyFriday).'</td>
				<td>'.(($mondayValue-$allergyMonday)+($tuesdayValue-$allergyTuesday)+($wednesdayValue-$allergyWednesday)+($thursdayValue-$allergyThursday)+($fridayValue-$allergyFriday)).'</td>
			</tr>
		</tbody>
		</table>';
		
		
		$pdf->writeHTML($regularHtml, true, true, true, true, '');	
		
		$pdf->Ln(15);
		
		$pdf->writeHTML('<u style="text-align:center;"><b>Allergen Menu</b></u>', true, true, true, true, '');
		
		$pdf->Ln(5);	
		
		$pdf->writeHTML($html, true, true, true, true, '');	
		
		$pdf->Ln(15);
		
		$pdf->writeHTML('<u style="text-align:center;"><b>Totals</b></u>', true, true, true, true, '');
		
		$pdf->Ln(5);
	
		$html = '<table border="1" cellpadding="10">
		<thead>
			<tr>
				<td colspan="5" style="background-color:#84bc3d;color:#fff;">Totals</td>
				<td>'.$mondayValue.'</td>
				<td>'.$tuesdayValue.'</td>
				<td>'.$wednesdayValue.'</td>
				<td>'.$thursdayValue.'</td>
				<td>'.$fridayValue.'</td>     
				<td>'.($mondayValue+$tuesdayValue+$wednesdayValue+$thursdayValue+$fridayValue).'</td>       
			</tr>
		</thead>    
		</table>';
		$pdf->writeHTML($html, true, true, true, true, '');	
		
		$pdf->Ln(15);
		
		$pdf->writeHTML('<u style="text-align:center;"><b>Close Dates</b></u>', true, true, true, true, '');
		
		$pdf->Ln(5);
		
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
	
	$PdfName = CUS_WOO_ORDER_PLUGIN_DIR.'/pdffile/update_order'.rand().'.pdf';	
	//$PdfName = CUS_WOO_ORDER_PLUGIN_DIR.'/pdffile/update_order.'.pdf;	
	$pdf->Output($PdfName, 'F');
	return $PdfName;
	?>