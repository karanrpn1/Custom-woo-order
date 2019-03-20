<?php 
	
	$pdf = new TCPDF('P', PDF_UNIT, 'A3', true, 'UTF-8', false);

	// set document information
	$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetAuthor('Karan Rupani');
	$pdf->SetTitle('Daily School Rediness summary'); 
	$pdf->SetSubject('Daily School Rediness summary');
	$pdf->SetKeywords('Daily School Rediness summary'); 
	
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
			
	$pdf->writeHTMLCell(200, '', '', '', "School Rediness Summary", 0, 1, 1, true, 'C', true);
	
	$pdf->SetFont('leckerlione', '', 20);	
	
	$pdf->SetTextColor('0','0','0');
	
	$y = $pdf->getY();
	
	$pdf->SetFillColor(255, 255, 255);
	
	$pdf->writeHTMLCell(80, '', '', $y, "", 0, 0, 1, true, 'J', true);
			
	$pdf->writeHTMLCell(200, '', '', '', "All Customers", 0, 1, 1, true, 'C', true);	
	
	$pdf->SetFont('helvetica', '', 14);
	
	$y = $pdf->getY();
	
	$pdf->SetFillColor(255, 255, 255);
	
	$pdf->writeHTMLCell(80, '', '', $y, "", 0, 0, 1, true, 'J', true);
			
	$pdf->writeHTMLCell(200, '', '', '', '<i>'.date("jS M Y").'</i>', 0, 1, 1, true, 'C', true);
	
	
	$pdf->Ln(20);
	
	global $wpdb;
	$tablename = $wpdb->prefix.'school_readiness_lunch';
	
	$todayTime = strtotime('today midnight');
	$tomorrowTime = strtotime('tomorrow');
	$optInInfo = $wpdb->get_results( "SELECT * FROM $tablename where date=$todayTime");
	
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
		
		$billingCompany = $user->billing_company;
		if(!empty($row->comment)) {
			$billingCompany.= "<br>".$row->comment;
		}
		
		$redinessLunchHtml = '';
		
		$redinessLunchHtml.= '<table border="1" cellpadding="10">
				<thead>
					<tr style="background-color:#84bc3d;color:#fff;">
						<td>Centre</td>						
						<td colspan="2">Items</td>
						<td>Total</td>					
					</tr>
				</thead>
				<tbody>';
				
		$redinessLunchHtml.= '<tr>		
					<td rowspan="11">'.$billingCompany.'</td>			
					<td colspan="2">Vegemite Sandwich </td>
					<td>'.$row->vegemite.'</td>						
				</tr>';			
		$redinessLunchHtml.= '<tr>					
					<td colspan="2">Cheese Sandwich </td>
					<td>'.$row->cheese.'</td>						
				</tr>';	
		$redinessLunchHtml.= '<tr>					
					<td colspan="2">Cheese and Vegemite Sandwich </td>
					<td>'.$row->cheese_vegemite.'</td>						
				</tr>';	
		$redinessLunchHtml.= '<tr>					
					<td colspan="2">Cheese and Lettuce Sandwich </td>
					<td>'.$row->cheese_lettuce.'</td>						
				</tr>';	
		$redinessLunchHtml.= '<tr>					
					<td colspan="2">2 litre bottle of water </td>
					<td>'.$row->bottle_large.'</td>						
				</tr>';	
		$redinessLunchHtml.= '<tr>					
					<td colspan="2">Small individual bottles of water</td>
					<td>'.$row->bottle_small.'</td>						
				</tr>';	
		$redinessLunchHtml.= '<tr>					
					<td colspan="2">Apples </td>
					<td>'.$row->apples.'</td>						
				</tr>';	
		$redinessLunchHtml.= '<tr>					
					<td colspan="2">Oranges </td>
					<td>'.$row->oranges.'</td>						
				</tr>';	
		$redinessLunchHtml.= '<tr>					
					<td colspan="2">Pears </td>
					<td>'.$row->pears.'</td>						
				</tr>';	
		$redinessLunchHtml.= '<tr>					
					<td colspan="2">Bananas </td>
					<td>'.$row->bananas.'</td>						
				</tr>';
		
		$redinessLunchHtml.= '</tbody><tfoot><tr><td colspan="2">Total</td><td>'.($row->vegemite + $row->cheese + $row->cheese_vegemite + $row->cheese_lettuce + $row->bottle_large + $row->bottle_small + $row->apples + $row->oranges + $row->pears + $row->bananas ).'</td></tr></tfoot></table><br><br><br>';			
		
	
		$pdf->writeHTML($redinessLunchHtml, true, true, true, true, '');
	
	}

	
	// reset pointer to the last page
	$pdf->lastPage();
	
	// ---------------------------------------------------------
	ob_end_clean();	 
	//Close and output PDF document
	$PdfName = CUS_WOO_ORDER_PLUGIN_DIR.'/pdffile/daily_school_rediness_'.$stateCode.'.pdf';
	$pdf->Output($PdfName, 'F');
	return $PdfName;
	
	
?>