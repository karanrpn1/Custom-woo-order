<?php

	$pdf = new TCPDF('P', PDF_UNIT, 'A3', true, 'UTF-8', false);
	// set document information
	$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetAuthor('Karan Rupani');
	$pdf->SetTitle('Alternative Choice Summary');
	$pdf->SetSubject('Alternative Choice Summary');
	$pdf->SetKeywords('Alternative Choice Summary');
	
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
			
	$pdf->writeHTMLCell(200, '', '', '', "Alternative Meal Summary", 0, 1, 1, true, 'C', true);
	
	$pdf->SetFont('helvetica', 'B', 14);
	 
	$pdf->SetTextColor('0','0','0');
	 
	$pdf->Ln(2);
	
	$y = $pdf->getY();
	
	$pdf->writeHTMLCell(80, '', '', $y, "", 0, 0, 1, true, 'J', true);
			
	$pdf->writeHTMLCell(200, '', '', '', "Your new Meal Summary has been submitted to Hearty Health", 0, 1, 1, true, 'C', true);	
	
	$pdf->Ln(20);		
	
	global $wpdb;
	$tablename = $wpdb->prefix.'opt_in_alternative';
	$choiceInfo = $wpdb->get_results( "SELECT * FROM $tablename where ID=$id");
	foreach ($choiceInfo as $row ) {
		$user = get_user_by( 'id', $row->user_id );
		
		$left_column = 'Centre';

		$right_column = $user->billing_company;

		$pdf->SetFillColor(255, 255, 255);
		
		$pdf->SetFont('helvetica', 'B', 14);
		
		$pdf->MultiCell(80, 0, $left_column, 0, 'L', 1, 0, '', '', true, 0, false, true, 0);			
		$pdf->SetFont('helvetica', '', 14);
		
		$pdf->MultiCell(150, 0, $right_column, 0, 'L', 1, 1, '', '', true, 0, false, true, 0);		
		
		$pdf->Ln(5);
		
		$left_column = 'Date';
		
		$right_column = date("jS F Y");

		$pdf->SetFillColor(255, 255, 255);
		
		$pdf->SetFont('helvetica', 'B', 14);
		
		$pdf->MultiCell(80, 0, $left_column, 0, 'L', 1, 0, '', '', true, 0, false, true, 0);			
		$pdf->SetFont('helvetica', '', 14);
		
		$pdf->MultiCell(150, 0, $right_column, 0, 'L', 1, 1, '', '', true, 0, false, true, 0);		
		
		$pdf->Ln(5);
		
		$left_column = 'Day';

		$right_column = date("l");

		$pdf->SetFillColor(255, 255, 255);
		
		$pdf->SetFont('helvetica', 'B', 14);
		
		$pdf->MultiCell(80, 0, $left_column, 0, 'L', 1, 0, '', '', true, 0, false, true, 0);		
		
		$pdf->SetFont('helvetica', '', 14);
		
		$pdf->MultiCell(150, 0, $right_column, 0, 'L', 1, 1, '', '', true, 0, false, true, 0);		
		
		$pdf->Ln(5);
		
		$left_column = 'Menu Selection';
		
		if($row->choice==1) {
			$menuName = "Spaghetti Bolognaise";
		} 
		elseif($row->choice==2) {
			$menuName = "Spaghetti Bolognaise";
		}
		elseif($row->choice==3) { 
			$menuName = "Pumpkin Soup with bread";
		}
		
		$right_column = $menuName;

		$pdf->SetFillColor(255, 255, 255);
		
		$pdf->SetFont('helvetica', 'B', 14);
		
		$pdf->MultiCell(80, 0, $left_column, 0, 'L', 1, 0, '', '', true, 0, false, true, 0);			
		$pdf->SetFont('helvetica', '', 14);
		
		$pdf->MultiCell(150, 0, $right_column, 0, 'L', 1, 1, '', '', true, 0, false, true, 0);		
		
		$pdf->Ln(5);
		
		$left_column = 'Total Package';

		$right_column = $row->total_package;

		$pdf->SetFillColor(255, 255, 255);		
		
		$pdf->SetFont('helvetica', 'B', 14);
		
		$pdf->MultiCell(80, 0, $left_column, 0, 'L', 1, 0, '', '', true, 0, false, true, 0);		
		$pdf->SetFont('helvetica', '', 14);
			
		$pdf->MultiCell(150, 0, $right_column, 0, 'L', 1, 1, '', '', true, 0, false, true, 0);		
		 
		
	}
	
	$pdf->lastPage();
	
	// ---------------------------------------------------------
	ob_end_clean();	 
	//Close and output PDF document
	$PdfName = CUS_WOO_ORDER_PLUGIN_DIR.'/pdffile/alternative_order_create'.rand().'.pdf';
	$pdf->Output($PdfName, 'F');
	return $PdfName; 

?>