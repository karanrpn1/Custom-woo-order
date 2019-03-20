 <?php
$pdf = new TCPDF('P', PDF_UNIT, 'A3', true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Karan');
$pdf->SetTitle('Daily Order');
$pdf->SetSubject('Daily Order');
$pdf->SetKeywords('Daily Order');

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
	
$pdf->SetFont('helvetica', '', 26);	

$pdf->SetFont('leckerlione', '', 36, '', false);
	
$pdf->SetTextColor('132','188','61');

$y = $pdf->getY();
	
$pdf->SetFillColor(255, 255, 255);

$pdf->writeHTMLCell(80, '', '', $y, "", 0, 0, 1, true, 'J', true);
		
$pdf->writeHTMLCell(150, '', '', '', "Meal Summary", 0, 1, 1, true, 'C', true);

$pdf->SetFont('leckerlione', '', 20, '', false);

$pdf->SetTextColor('0','0','0');

$y = $pdf->getY();
	
$pdf->writeHTMLCell(80, '', '', $y, "", 0, 0, 1, true, 'J', true);
		
$pdf->writeHTMLCell(150, '', '', '', "All Customers", 0, 1, 1, true, 'C', true);

$pdf->SetFont('helvetica', '', 14);

$y = $pdf->getY();
	
$pdf->writeHTMLCell(80, '', '', $y, "", 0, 0, 1, true, 'J', true);
		
$pdf->writeHTMLCell(150, '', '', '', '<i>Dated '.date("jS M Y").'</i>', 0, 1, 1, true, 'C', true);

$pdf->Ln(10);

$pdf->writeHTML('<u style="text-align:center;"><b>Regular Menu Totals</b></u>', true, true, true, true, '');	

$pdf->Ln(5);

global $wpdb;
$tablename = $wpdb->prefix.'order_info';
$orderInfo = $wpdb->get_results( "SELECT * FROM $tablename where status=1");
$day = date("l");

$fullMenuHtml = '<table border="1" cellpadding="10">
		<thead>
			<tr style="background-color:#84bc3d;color:#fff;">
				<td>Menu</td>
				<td colspan="3">Centre</td>
				<td>'.date("l").'</td>
			</tr>
		</thead>
		<tbody>';

$lunchOnlyHtml = '<table border="1" cellpadding="10">
		<thead>
			<tr style="background-color:#84bc3d;color:#fff;">
				<td>Menu</td>
				<td colspan="3">Centre</td>
				<td>'.date("l").'</td>
			</tr>
		</thead>
		<tbody>';

$toddlerHtml = '<table border="1" cellpadding="10">
		<thead>
			<tr style="background-color:#84bc3d;color:#fff;">
				<td>Menu</td>
				<td colspan="3">Centre</td>
				<td>'.date("l").'</td>
			</tr>
		</thead>
		<tbody>';
		
$ooshHtml = '<table border="1" cellpadding="10">
		<thead>
			<tr style="background-color:#84bc3d;color:#fff;">
				<td>Menu</td>
				<td colspan="3">Centre</td>
				<td>'.date("l").'</td>				
			</tr>
		</thead>
		<tbody>';

$allergyMenuHtml = '<table border="1" cellpadding="10">
	<thead>
		<tr style="background-color:#84bc3d;color:#fff;">
			<td>Centre</td>
			<td colspan="3">Allergen Group</td>
			<td>'.date("l").'</td>
		</tr>
	</thead>
	<tbody>';
	
$toddlerToggleHtml = '<table border="1" cellpadding="10">
	<thead>
		<tr style="background-color:#84bc3d;color:#fff;">
			<td>Centre</td> 
			<td colspan="3">Allergen Group</td>
			<td>'.date("l").'</td>			
		</tr>
	</thead> 
	<tbody>';

$fullMenuTotal = 0;
$lunchMenuTotal = 0;
$toddlerMenuTotal = 0;
$ooshMenuTotal = 0;
$toddlerToggleTotal = 0;

$allergyTotal = 0;		
$toddlerTotal = 0;

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
	
	$timestamp = strtotime('today midnight');
	$optInTable = $wpdb->prefix.'opt_in_alternative';
	$optInTableInfo = $wpdb->get_var( "SELECT count(*) FROM $optInTable where user_id=$row->user_id and order_info_id=$row->ID and date>=$timestamp and date<=$timestamp");
	if($optInTableInfo>0) {	continue;	} 
	
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
	
	if($checkCloseDate==1) { continue; }
	
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
		if($chkSku[1]==strtolower($day)) {
			$itemValue = $item['qty'];
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
				
				if(in_array($day,$attend)) {
					$allergyTotal++;					
					$allergyMenuHtml.= '<tr>
						<td>'.$childName.'</td>
						<td colspan="3">'.$allergyGroup.'</td>
						<td>1</td>			
					</tr>';						
				}				
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
				
				if(in_array($day,$attend)) {
					$toddlerTotal++;					
					$toddlerToggleHtml.= '<tr>
						<td>'.$childName.'</td>
						<td colspan="3">'.$toddlerGroup.'</td>
						<td>1</td>
						
					</tr>';	
					
				}	
							
			}	
			
		} 
		
		if($itemValue>0) {
			$fullMenuHtml.= '<tr>
				<td>'.$menuName.'</td>
				<td colspan="3">'.$user->billing_company.'</td>
				<td>'.$itemValue.'</td>			
			</tr>';	
		}
		
		$fullMenuTotal = ($fullMenuTotal+$itemValue);		
		
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
				if(!empty(get_post_meta( $row->order_id, 'child_allergy_meat_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_meat_'.$i, true )<>"null")) {
					$allergyGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_meat_'.$i, true ))).", ";
				}
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
				
				if(in_array($day,$attend)) {
					$allergyTotal++;					
					$allergyMenuHtml.= '<tr>
					<td>'.$childName.'</td>
					<td colspan="3">'.$allergyGroup.'</td>
					<td>1</td>			
					</tr>';				
				}				
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
				if(!empty(get_post_meta( $row->order_id, 'child_allergy_meat_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_meat_'.$i, true )<>"null")) {
					$toddlerGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_meat_'.$i, true ))).", ";
				}
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
				
				if(in_array($day,$attend)) {
					$toddlerTotal++;
					$toddlerToggleHtml.= '<tr>
						<td>'.$childName.'</td>
						<td colspan="3">'.$toddlerGroup.'</td>
						<td>1</td>			
					</tr>';						
				}				
			}
			
			
			
		} 		
		
		
		if($itemValue>0) {	
			$lunchOnlyHtml.= '<tr>
				<td>'.$menuName.'</td>
				<td colspan="3">'.$user->billing_company.'</td>
				<td>'.$itemValue.'</td>			
			</tr>';
		}
		
		$lunchMenuTotal = ($lunchMenuTotal+$itemValue);		
				
	}
	
	if($menuName=="Toddler Menu") {				
		
		$childCount = get_post_meta( $row->order_id, 'child_count', true );			
		
		for($i=1;$i<=$childCount;$i++){
			$toddlerGroup ="";
			$allergyGroup ="";
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
				if(!empty(get_post_meta( $row->order_id, 'child_allergy_meat_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_meat_'.$i, true )<>"null")) {
					$allergyGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_meat_'.$i, true ))).", ";
				}
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
				
				if(in_array($day,$attend)) {
					$allergyTotal++;
					
					$allergyMenuHtml.= '<tr>
						<td>'.$childName.'</td>
						<td colspan="3">'.$allergyGroup.'</td>
						<td>1</td>			
					</tr>';	
					
				}				
			}
			else {
				if(!empty(get_post_meta( $row->order_id, 'child_allergy_dairy_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_dairy_'.$i, true )<>"null")) {
					$toddlerGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_dairy_'.$i, true )) ).",";
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
				if(!empty(get_post_meta( $row->order_id, 'child_allergy_meat_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_meat_'.$i, true )<>"null")) {
					$toddlerGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_meat_'.$i, true ))).", ";
				}
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
				
				if(in_array($day,$attend)) {
					$toddlerTotal++;
					
					$toddlerToggleHtml.= '<tr>
					<td>'.$childName.'</td>
					<td colspan="3">'.$toddlerGroup.'</td>
					<td>1</td>		
					</tr>';	
				}
					
			}
			
			
		} 
		
		
		
		if($itemValue>0) {
			$toddlerHtml.= '<tr>
				<td>'.$menuName.'</td>
				<td colspan="3">'.$user->billing_company.'</td>
				<td>'.$itemValue.'</td>			
			</tr>';	
		}
		
		$toddlerMenuTotal = ($toddlerMenuTotal+$itemValue);	
					
	}
	
	if($menuName=="OOSH (out of school hours)") {		
		
		$childCount = get_post_meta( $row->order_id, 'child_count', true );	
		
		for($i=1;$i<=$childCount;$i++){
			$allergyGroup ="";
			$toddlerGroup ="";
			
			$attend = json_decode(get_post_meta( $row->order_id, 'child_attend_'.$i, true ));			
			if(get_post_meta( $row->order_id, 'child_toddler_toggle_'.$i, true )==0) {
				if(!empty(get_post_meta( $row->order_id, 'child_allergy_dairy_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_dairy_'.$i, true )<>"null")) {
					$allergyGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_dairy_'.$i, true )) ).",";
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
				if(!empty(get_post_meta( $row->order_id, 'child_allergy_meat_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_meat_'.$i, true )<>"null")) {
					$allergyGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_meat_'.$i, true ))).", ";
				}
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
				
				if(in_array($day,$attend)) {
					$allergyTotal++;					
					
					$allergyMenuHtml.= '<tr>
						<td>'.$childName.'</td>
						<td colspan="3">'.$allergyGroup.'</td>
						<td>1</td>			
					</tr>';			
				}				
			}
			else {
				if(!empty(get_post_meta( $row->order_id, 'child_allergy_dairy_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_dairy_'.$i, true )<>"null")) {
					$toddlerGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_dairy_'.$i, true )) ).",";
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
				if(!empty(get_post_meta( $row->order_id, 'child_allergy_meat_'.$i, true )) && (get_post_meta( $row->order_id, 'child_allergy_meat_'.$i, true )<>"null")) {
					$toddlerGroup.= implode(", ",json_decode(get_post_meta( $row->order_id, 'child_allergy_meat_'.$i, true ))).", ";
				}
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
				
				if(in_array($day,$attend)) {
					$toddlerTotal++;
					
					$toddlerToggleHtml.= '<tr>
						<td>'.$childName.'</td>
						<td colspan="3">'.$toddlerGroup.'</td>
						<td>1</td>			
					</tr>';	
					
				}				
			}
			
			
		} 	
		
		
		
		if($itemValue>0) {	
			$ooshHtml.= '<tr>
				<td>'.$menuName.'</td>
				<td colspan="3">'.$user->billing_company.'</td>
				<td>'.$itemValue.'</td>			
			</tr>';	
		}
		
		$ooshMenuTotal = ($ooshMenuTotal+$itemValue);	
					
	}
		
	
}

$fullMenuHtml.= '</tbody>
		<tfoot>
			<tr>
				<td>Full Menu</td>
				<td colspan="3">Subtotal</td>
				<td>'.$fullMenuTotal.'</td>				
			</tr>
		</tfoot>
		</table>';	
$lunchOnlyHtml.= '</tbody>
		<tfoot>
			<tr>
				<td>Lunch Only</td>
				<td colspan="3">Subtotal</td>
				<td>'.$lunchMenuTotal.'</td>				
			</tr>
		</tfoot>
		</table>';	
		
$toddlerHtml.= '</tbody>
		<tfoot>
			<tr>
				<td>Toddler Menu</td>
				<td colspan="3">Subtotal</td>
				<td>'.$toddlerMenuTotal.'</td>				
			</tr>
		</tfoot>
		</table>';	
		
$ooshHtml.= '</tbody>
		<tfoot>
			<tr>
				<td>OOSH (out of school hours)</td>
				<td colspan="3">Subtotal</td>
				<td>'.$ooshMenuTotal.'</td>
				
			</tr>
		</tfoot>
		</table>';	

	
$pdf->writeHTML($fullMenuHtml, true, true, true, true, '');			

$pdf->Ln(10);

$pdf->writeHTML($lunchOnlyHtml, true, true, true, true, '');			

//$pdf->Ln(10);
//
//$pdf->writeHTML($toddlerHtml, true, true, true, true, '');			

$pdf->Ln(10);

$pdf->writeHTML($ooshHtml, true, true, true, true, '');			

$pdf->Ln(10);

$pdf->writeHTML('<u style="text-align:center;"><b>Allergen Menu Totals</b></u>', true, true, true, true, '');	

$pdf->Ln(5);

$allergyMenuHtml.= '<tr><td>Allergen Menu</td><td colspan="3">Subtotal</td><td>'.$allergyTotal.'</td></tr>';

$allergyMenuHtml.= '</tbody></table>';
	
$pdf->writeHTML($allergyMenuHtml, true, true, true, true, '');	

$pdf->Ln(10);

$pdf->writeHTML('<u style="text-align:center;"><b>Toddler Menu Totals</b></u>', true, true, true, true, '');	

$pdf->Ln(5);

$toddlerToggleHtml.= '<tr><td>Toddler Menu</td><td colspan="3">Subtotal</td><td>'.$toddlerTotal.'</td></tr>';


$toddlerToggleHtml.= '</tbody></table>';
	
$pdf->writeHTML($toddlerToggleHtml, true, true, true, true, '');
	

// reset pointer to the last page
$pdf->lastPage();

// ---------------------------------------------------------
ob_end_clean();	 
//Close and output PDF document
$PdfName = CUS_WOO_ORDER_PLUGIN_DIR.'/pdffile/daily_all_customer_order_'.$stateCode.'.pdf';
$pdf->Output($PdfName, 'F');
return $PdfName;

?>