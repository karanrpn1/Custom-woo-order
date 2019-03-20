<?php

	$pdf = new TCPDF('P', PDF_UNIT, 'A3', true, 'UTF-8', false);
	// set document information
	$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetAuthor('Karan Rupani');
	$pdf->SetTitle('School Rediness Lunch Summary');
	$pdf->SetSubject('School Rediness Lunch Summary');
	$pdf->SetKeywords('School Rediness Lunch Summary');
	
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
			
	$pdf->writeHTMLCell(200, '', '', '', "Excursion Meal Summary", 0, 1, 1, true, 'C', true);
	
	$pdf->SetFont('helvetica', 'B', 14);
	 
	$pdf->SetTextColor('0','0','0');
	 
	$pdf->Ln(1);
	
	$y = $pdf->getY();
	
	$pdf->writeHTMLCell(80, '', '', $y, "", 0, 0, 1, true, 'J', true);
			
	$pdf->writeHTMLCell(200, '', '', '', "Your new Meal Summary has been submitted to Hearty Health", 0, 1, 1, true, 'C', true);
	
	$pdf->SetFont('helvetica', '', 14);
	
	$pdf->Ln(2);
	
	$pdf->writeHTMLCell(350, '', '', '','<i>'.date("jS F Y",$date).'</i>', 0, 1, 1, true, 'C', true);	
		
	
	global $wpdb;
	$tablename = $wpdb->prefix.'school_readiness_lunch';
	$choiceInfo = $wpdb->get_results( "SELECT * FROM $tablename where ID=$id");
	
	$redinessLunchHtml = '';
	
	foreach ($choiceInfo as $row ) {
		$user = get_user_by( 'id', $row->user_id );
		
		$pdf->Ln(5);
		
		$billingCompany = $user->billing_company;
		if(!empty($row->comment)) {
			$billingCompany.= "<br>".$row->comment;
		}		
		
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
		
		$pdf->Ln(15);
	
		$pdf->writeHTML($redinessLunchHtml, true, true, true, true, '');	
		
		break;
		
	}
	 
	$pdf->lastPage();
	
	// ---------------------------------------------------------
	ob_end_clean();	 
	//Close and output PDF document
	$PdfName = CUS_WOO_ORDER_PLUGIN_DIR.'/pdffile/user_rediness_school'.rand().'.pdf';
	//$PdfName = CUS_WOO_ORDER_PLUGIN_DIR.'/pdffile/user_rediness_school.pdf';
	$pdf->Output($PdfName, 'F');
	return $PdfName; 

?>