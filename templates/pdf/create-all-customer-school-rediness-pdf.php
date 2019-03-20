<?php 
	
	$pdf = new TCPDF('P', PDF_UNIT, 'A3', true, 'UTF-8', false);

	// set document information
	$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetAuthor('Karan Rupani');
	$pdf->SetTitle('All Customer School Rediness summary'); 
	$pdf->SetSubject('All Customer Rediness summary');
	$pdf->SetKeywords('All Customer Rediness summary'); 
	
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
	$optInInfo = $wpdb->get_results( "SELECT * FROM $tablename where date=$todayTime");
	
	$vegemiteSandwichHtml = '<table border="1" cellpadding="10">
		<thead>
			<tr style="background-color:#84bc3d;color:#fff;">
				<td colspan="2">Centre</td>
				<td>Total</td>				
			</tr>
		</thead>
		<tbody>';
	$cheeseSandwichHtml = '<table border="1" cellpadding="10">
		<thead>
			<tr style="background-color:#84bc3d;color:#fff;">
				<td colspan="2">Centre</td>
				<td>Total</td>				
			</tr>
		</thead>
		<tbody>';
	$cheeseVegemiteSandwichHtml = '<table border="1" cellpadding="10">
		<thead>
			<tr style="background-color:#84bc3d;color:#fff;">
				<td colspan="2">Centre</td>
				<td>Total</td>				
			</tr>
		</thead>
		<tbody>';
	$cheeseLettuceSandwichHtml = '<table border="1" cellpadding="10">
		<thead>
			<tr style="background-color:#84bc3d;color:#fff;">
				<td colspan="2">Centre</td>
				<td>Total</td>				
			</tr>
		</thead>
		<tbody>';
	$bottleLargeHtml = '<table border="1" cellpadding="10">
		<thead>
			<tr style="background-color:#84bc3d;color:#fff;">
				<td colspan="2">Centre</td>
				<td>Total</td>				
			</tr>
		</thead>
		<tbody>';
	$bottleSmallHtml = '<table border="1" cellpadding="10">
		<thead>
			<tr style="background-color:#84bc3d;color:#fff;">
				<td colspan="2">Centre</td>
				<td>Total</td>				
			</tr>
		</thead>
		<tbody>';
	$appleHtml = '<table border="1" cellpadding="10">
		<thead>
			<tr style="background-color:#84bc3d;color:#fff;">
				<td colspan="2">Centre</td>
				<td>Total</td>				
			</tr>
		</thead>
		<tbody>';
	$orangesHtml = '<table border="1" cellpadding="10">
		<thead>
			<tr style="background-color:#84bc3d;color:#fff;">
				<td colspan="2">Centre</td>
				<td>Total</td>				
			</tr>
		</thead>
		<tbody>';
	$pearsHtml = '<table border="1" cellpadding="10">
		<thead>
			<tr style="background-color:#84bc3d;color:#fff;">
				<td colspan="2">Centre</td>
				<td>Total</td>				
			</tr>
		</thead>
		<tbody>';
	$bananasHtml = '<table border="1" cellpadding="10">
		<thead>
			<tr style="background-color:#84bc3d;color:#fff;">
				<td colspan="2">Centre</td>
				<td>Total</td>				
			</tr>
		</thead>
		<tbody>';
	
	$vegemiteSandwichTotal = 0;
	$cheeseSandwichTotal = 0;
	$cheeseVegemiteSandwichTotal = 0; 	
	$cheeseLettuceSandwichTotal = 0; 
	$bottleLargeTotal = 0; 
	$bottleSmallTotal = 0; 
	$appleTotal = 0; 
	$orangesTotal = 0; 
	$pearsTotal = 0; 
	$bananasTotal = 0; 
	
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
		
		$vegemiteSandwichHtml.= '<tr>
				<td colspan="2">'.$billingCompany.'</td>
				<td>'.$row->vegemite.'</td>					
			</tr>';
		
		$cheeseSandwichHtml.= '<tr>
				<td colspan="2">'.$billingCompany.'</td>
				<td>'.$row->cheese.'</td>					
			</tr>';
		
		$cheeseVegemiteSandwichHtml.= '<tr>
				<td colspan="2">'.$billingCompany.'</td>
				<td>'.$row->cheese_vegemite.'</td>					
			</tr>';
			
		$cheeseLettuceSandwichHtml.= '<tr>
				<td colspan="2">'.$billingCompany.'</td>
				<td>'.$row->cheese_lettuce.'</td>					
			</tr>';	
		
		$bottleLargeHtml.= '<tr>
				<td colspan="2">'.$billingCompany.'</td>
				<td>'.$row->bottle_large.'</td>					
			</tr>';			
		
		$bottleSmallHtml.= '<tr>
				<td colspan="2">'.$billingCompany.'</td>
				<td>'.$row->bottle_small.'</td>					
			</tr>';
		
		$appleHtml.= '<tr>
				<td colspan="2">'.$billingCompany.'</td>
				<td>'.$row->apples.'</td>					
			</tr>';
		
		$orangesHtml.= '<tr>
				<td colspan="2">'.$billingCompany.'</td>
				<td>'.$row->oranges.'</td>					
			</tr>';
		
		$pearsHtml.= '<tr>
				<td colspan="2">'.$billingCompany.'</td>
				<td>'.$row->pears.'</td>					
			</tr>';
		
		$bananasHtml.= '<tr>
				<td colspan="2">'.$billingCompany.'</td>
				<td>'.$row->bananas.'</td>					
			</tr>';					
		
		$vegemiteSandwichTotal+=$row->vegemite;
		$cheeseSandwichTotal+=$row->cheese;
		$cheeseVegemiteSandwichTotal+=$row->cheese_vegemite;
		$cheeseLettuceSandwichTotal+=$row->cheese_lettuce;
		$bottleLargeTotal+=$row->bottle_large;
		$bottleSmallTotal+=$row->bottle_small;
		$appleTotal+=$row->apples;
		$orangesTotal+=$row->oranges;
		$pearsTotal+=$row->pears;
		$bananasTotal+=$row->bananas;
	}

	$vegemiteSandwichHtml.= '</tbody>
		<tfoot>
			<tr>
				<td colspan="2">Total</td>				
				<td>'.$vegemiteSandwichTotal.'</td>				
			</tr>
		</tfoot>
		</table>';		
	$cheeseSandwichHtml.= '</tbody>
		<tfoot>
			<tr>
				<td colspan="2">Total</td>				
				<td>'.$cheeseSandwichTotal.'</td>				
			</tr>
		</tfoot>
		</table>';	
	$cheeseVegemiteSandwichHtml.= '</tbody>
		<tfoot>
			<tr>
				<td colspan="2">Total</td>				
				<td>'.$cheeseVegemiteSandwichTotal.'</td>				
			</tr>
		</tfoot>
		</table>';		
	$cheeseLettuceSandwichHtml.= '</tbody>
		<tfoot>
			<tr>
				<td colspan="2">Total</td>				
				<td>'.$cheeseLettuceSandwichTotal.'</td>				
			</tr>
		</tfoot>
		</table>';	
	$bottleLargeHtml.= '</tbody>
		<tfoot>
			<tr>
				<td colspan="2">Total</td>				
				<td>'.$bottleLargeTotal.'</td>				
			</tr>
		</tfoot>
		</table>';	
	$bottleSmallHtml.= '</tbody>
		<tfoot>
			<tr>
				<td colspan="2">Total</td>				
				<td>'.$bottleSmallTotal.'</td>				
			</tr>
		</tfoot>
		</table>';	
	$appleHtml.= '</tbody>
		<tfoot>
			<tr>
				<td colspan="2">Total</td>				
				<td>'.$appleTotal.'</td>				
			</tr>
		</tfoot>
		</table>';	
	$orangesHtml.= '</tbody>
		<tfoot>
			<tr>
				<td colspan="2">Total</td>				
				<td>'.$appleTotal.'</td>				
			</tr>
		</tfoot>
		</table>';	
	$pearsHtml.= '</tbody>
		<tfoot>
			<tr>
				<td colspan="2">Total</td>				
				<td>'.$pearsTotal.'</td>				
			</tr>
		</tfoot>
		</table>';	
	$bananasHtml.= '</tbody>
		<tfoot>
			<tr>
				<td colspan="2">Total</td>				
				<td>'.$bananasTotal.'</td>				
			</tr>
		</tfoot>
		</table>';	
	
	$pdf->writeHTML('<u style="text-align:center;"><b>Vegemite Sandwich Total</b></u>', true, true, true, true, '');	
	
	$pdf->Ln(5);	
	
	$pdf->writeHTML($vegemiteSandwichHtml, true, true, true, true, '');			

	$pdf->Ln(10);	
	
	$pdf->writeHTML('<u style="text-align:center;"><b>Cheese Sandwich Total</b></u>', true, true, true, true, '');	
	
	$pdf->Ln(5);
	
	$pdf->writeHTML($cheeseSandwichHtml, true, true, true, true, '');	
	
	$pdf->Ln(10);	
	
	$pdf->writeHTML('<u style="text-align:center;"><b>Cheese Vegemite Total</b></u>', true, true, true, true, '');	
	
	$pdf->Ln(5);
	
	$pdf->writeHTML($cheeseVegemiteSandwichHtml, true, true, true, true, '');
	
	$pdf->Ln(10);	
	
	$pdf->writeHTML('<u style="text-align:center;"><b>Cheese Lettuce Total</b></u>', true, true, true, true, '');	
	
	$pdf->Ln(5);
	
	$pdf->writeHTML($cheeseLettuceSandwichHtml, true, true, true, true, '');
	
	$pdf->Ln(10);	
	
	$pdf->writeHTML('<u style="text-align:center;"><b>2 litre bottle of water</b></u>', true, true, true, true, '');	
	
	$pdf->Ln(5);
	
	$pdf->writeHTML($bottleLargeHtml, true, true, true, true, '');
	
	$pdf->Ln(10);	
	
	$pdf->writeHTML('<u style="text-align:center;"><b>Small individual bottles of water</b></u>', true, true, true, true, '');	
	
	$pdf->Ln(5);
	
	$pdf->writeHTML($bottleSmallHtml, true, true, true, true, '');
	
	$pdf->Ln(10);	
	
	$pdf->writeHTML('<u style="text-align:center;"><b>Apples Total</b></u>', true, true, true, true, '');	
	
	$pdf->Ln(5);
	
	$pdf->writeHTML($appleHtml, true, true, true, true, '');
	
	$pdf->Ln(10);	
	
	$pdf->writeHTML('<u style="text-align:center;"><b>Oranges Total</b></u>', true, true, true, true, '');	
	
	$pdf->Ln(5);
	
	$pdf->writeHTML($orangesHtml, true, true, true, true, '');
	
	$pdf->Ln(10);	
	
	$pdf->writeHTML('<u style="text-align:center;"><b>Pears Total</b></u>', true, true, true, true, '');	
	
	$pdf->Ln(5);
	
	$pdf->writeHTML($pearsHtml, true, true, true, true, '');
	
	$pdf->Ln(10);	
	
	$pdf->writeHTML('<u style="text-align:center;"><b>Bananas Total</b></u>', true, true, true, true, '');	
	
	$pdf->Ln(5);
	
	$pdf->writeHTML($bananasHtml, true, true, true, true, '');
	
	
	
	
	// reset pointer to the last page
	$pdf->lastPage();
	
	// ---------------------------------------------------------
	ob_end_clean();	 
	//Close and output PDF document
	$PdfName = CUS_WOO_ORDER_PLUGIN_DIR.'/pdffile/all_customer_school_rediness_'.$stateCode.'.pdf';
	$pdf->Output($PdfName, 'F');
	return $PdfName;
	
	
?>