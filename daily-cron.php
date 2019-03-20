<?php
if(!empty($_GET['cronActive']) && isset($_GET['cronActive']) && ($_GET['cronActive']=="9d6e8916c47e033cd080634d90354ba8")) { 
	global $wpdb;
	$tablename = $wpdb->prefix.'order_info';	
	$orderInfo = $wpdb->get_results( "SELECT * FROM $tablename" );
	foreach($orderInfo as $orderRow) {	
		if(!empty($orderRow->updateTime)) {
			$now = time();
			$your_date =  $orderRow->updateTime;
			$datediff = $now - $your_date;	
			$daysNumber = round($datediff / (60 * 60 * 24));

			if($daysNumber>=2) {				
				
				if($orderRow->update_status<>$orderRow->status) {
					if($orderRow->update_status==0) {
						$orderTablename = $wpdb->prefix.'order_info';
						$wpdb->update( $orderTablename, 
							array( 'status' => $orderRow->update_status , 'updateTime' => ''), 
							array( 'ID' =>  $orderRow->ID ), 
							array( '%s','%s' ),  
							array( '%d' ) 
						);
						// CANCEL MAIL SEND
					}
					elseif($orderRow->update_status==1) {
						
						$orderCheck = wc_get_order( $orderRow->order_id );
						$order_data = $orderCheck->get_data();		
						$order_status = $order_data['status'];		
						if($order_status<>"processing") { 	continue; }
						
						$orderTablename = $wpdb->prefix.'order_info';
						$wpdb->update( $orderTablename, 
							array( 'status' => $orderRow->update_status , 'updateTime' => ''), 
							array( 'ID' =>  $orderRow->ID ), 
							array( '%s','%s' ),  
							array( '%d' ) 
						);
						
						global $woocommerce;
						$orderDetail = wc_get_order( $orderRow->order_id);
					
						$billingAddress = $orderDetail->get_address('billing');
						$shippingAddress = $orderDetail->get_address('shipping');
						
						$order = wc_create_order(array('customer_id' => $orderRow->user_id,'created_via'=>'update_order'));
						update_post_meta( $order->id, 'order_type', 'update_order' ); 
						$itemSku = json_decode($orderRow->item_sku);
						$itemQuantity = json_decode($orderRow->item_quantity);
						$i=0;
						foreach($itemSku as $item) {
							$itemId = wc_get_product_id_by_sku( $item );
							$order->add_product( get_product($itemId), $itemQuantity[$i]); 
							$i++; 
						}
						$order->set_address( $billingAddress, 'billing' );	
						$order->set_address( $shippingAddress, 'shipping' );
						//$payment_gateways = $woocommerce->payment_gateways->payment_gateways();
						$order->set_payment_method('invoice');	
						$order->calculate_totals();
						$order->update_status("processing", 'Updated order',TRUE);
						
						$childTabelName = $wpdb->prefix.'child_info';
						$childData = $wpdb->get_results( "SELECT * FROM $childTabelName where ID IN (".$orderRow->allergy_child_ids.") " );
						update_post_meta( $order->id, 'child_count', $wpdb->num_rows ); 
						$i = 1;
						//echo 'Child Data<br><pre>'.$childData.'</pre>';
						
						foreach ($childData as $row){		
							update_post_meta( $order->id, 'child_name_'.$i, $row->child_name );
							update_post_meta( $order->id, 'child_remark_'.$i, $row->remark ); 
							update_post_meta( $order->id, 'child_id_number_'.$i, $row->id_number ); 
							update_post_meta( $order->id, 'child_attend_'.$i, $row->attend ); 
							update_post_meta( $order->id, 'child_allergy_dairy_'.$i, $row->dairy ); 
							update_post_meta( $order->id, 'child_roomNumber_'.$i, $row->roomNumber ); 
							update_post_meta( $order->id, 'child_allergy_vegetarian_'.$i, $row->vegetarian ); 
							update_post_meta( $order->id, 'child_allergy_grains_'.$i, $row->grains ); 
							update_post_meta( $order->id, 'child_allergy_meat_'.$i, $row->meat ); 
							update_post_meta( $order->id, 'child_allergy_seafood_'.$i, $row->seafood ); 
							update_post_meta( $order->id, 'child_allergy_soyproduct_'.$i, $row->soyproduct ); 
							update_post_meta( $order->id, 'child_allergy_legumes_'.$i, $row->legumes ); 
							update_post_meta( $order->id, 'child_allergy_fruits_'.$i, $row->fruits ); 
							update_post_meta( $order->id, 'child_allergy_vegetables_'.$i, $row->vegetables );
							update_post_meta( $order->id, 'child_allergy_others_'.$i, $row->others );  
							update_post_meta( $order_id, 'child_toddler_toggle_'.$i, $row->toddler_toggle ); 
							$i++;
						} 						
						
						$wpdb->update( $orderTablename, 
							array( 'order_id' =>  $order->id , 'updateTime' => ''), 
							array( 'ID' =>  $orderRow->ID ), 
							array( '%s','%s' ),  
							array( '%d' ) 
						);
					}
				}
				else {
					if($orderRow->status==1) {
						$orderCheck = wc_get_order( $orderRow->order_id );
						$order_data = $orderCheck->get_data();		
						$order_status = $order_data['status'];		
						if($order_status<>"processing") { 	continue; }
						
						global $woocommerce;
						$orderDetail = wc_get_order( $orderRow->order_id);
					
						$billingAddress = $orderDetail->get_address('billing');
						$shippingAddress = $orderDetail->get_address('shipping');
						
						$order = wc_create_order(array('customer_id' => $orderRow->user_id,'created_via'=>'update_order'));
						
						$itemSku = json_decode($orderRow->item_sku);
						$itemQuantity = json_decode($orderRow->item_quantity);
						$i=0;
						foreach($itemSku as $item) {
							$itemId = wc_get_product_id_by_sku( $item );
							$order->add_product( get_product($itemId), $itemQuantity[$i]); 
							$i++; 
						}
						$order->set_address( $billingAddress, 'billing' );	
						$order->set_address( $shippingAddress, 'shipping' );
						//$payment_gateways = $woocommerce->payment_gateways->payment_gateways();
						$order->set_payment_method('invoice');	
						$order->calculate_totals();
						$order->update_status("processing", 'Updated order',TRUE);
						
						$childTabelName = $wpdb->prefix.'child_info';
						$childData = $wpdb->get_results( "SELECT * FROM $childTabelName where ID IN (".$orderRow->allergy_child_ids.") " );
						update_post_meta( $order->id, 'child_count', $wpdb->num_rows ); 
						$i = 1;
						foreach ($childData as $row){		
							update_post_meta( $order->id, 'child_name_'.$i, $row->child_name );
							update_post_meta( $order->id, 'child_remark_'.$i, $row->remark ); 
							update_post_meta( $order->id, 'child_id_number_'.$i, $row->id_number ); 
							update_post_meta( $order->id, 'child_attend_'.$i, $row->attend ); 
							update_post_meta( $order->id, 'child_allergy_dairy_'.$i, $row->dairy ); 
							update_post_meta( $order->id, 'child_roomNumber_'.$i, $row->roomNumber ); 
							update_post_meta( $order->id, 'child_allergy_vegetarian_'.$i, $row->vegetarian ); 
							update_post_meta( $order->id, 'child_allergy_grains_'.$i, $row->grains ); 
							update_post_meta( $order->id, 'child_allergy_meat_'.$i, $row->meat ); 
							update_post_meta( $order->id, 'child_allergy_seafood_'.$i, $row->seafood ); 
							update_post_meta( $order->id, 'child_allergy_soyproduct_'.$i, $row->soyproduct ); 
							update_post_meta( $order->id, 'child_allergy_legumes_'.$i, $row->legumes ); 
							update_post_meta( $order->id, 'child_allergy_fruits_'.$i, $row->fruits ); 
							update_post_meta( $order->id, 'child_allergy_vegetables_'.$i, $row->vegetables );
							
							update_post_meta( $order->id, 'child_allergy_others_'.$i, $row->others );  
							update_post_meta( $order_id, 'child_toddler_toggle_'.$i, $row->toddler_toggle ); 
							$i++;
						} 
						
						$orderTablename = $wpdb->prefix.'order_info';
						$wpdb->update( $orderTablename, 
							array( 'order_id' =>  $order->id , 'updateTime' => ''), 
							array( 'ID' =>  $orderRow->ID ), 
							array( '%s','%s' ),  
							array( '%d' ) 
						);
					} 
				} 
				
			}				
		}		
	}
	
	$kitchenEmail = get_option( 'kitchen_staff_mail' );	
	$salesEmail = get_option( 'sales_staff_mail' );			 	
	$subject = "Daily Order Summary";
	$body = "Daily Order Summary";				
	$headers[] = 'From: '.get_option( 'blogname' ).' <'.get_option( 'admin_email' ).'>';
	
	$manageStateTable = $wpdb->prefix.'manage_state';
	$kitchenDetail = $wpdb->get_results( "SELECT * FROM $manageStateTable" );	
	
	foreach($kitchenDetail as $kitchenData) {
		$attachment1 = createDailyPdf($kitchenData->state_code);		
		$attachment2 = createDailyAllCustomerPdf($kitchenData->state_code);	
		$attachment3 = createDailyOptInPdf($kitchenData->state_code);
		$attachment4 = createDailySchoolRedinessPdf($kitchenData->state_code);	
		$attachment5 = createAllCustomerSchoolRedinessPdf($kitchenData->state_code);
		$attachments = array($attachment1,$attachment2,$attachment3,$attachment4,$attachment5);		
		wp_mail($kitchenData->kitchen_email, $subject,$body, $headers ,$attachments);	
	}
	$attachment1 = createDailyPdf("all");		
	$attachment2 = createDailyAllCustomerPdf("all");	
	$attachment3 = createDailyOptInPdf("all");
	$attachment4 = createDailySchoolRedinessPdf("all");	
	$attachment5 = createAllCustomerSchoolRedinessPdf("all");
	$attachments = array($attachment1,$attachment2,$attachment3,$attachment4,$attachment5);	
	
	//wp_mail($kitchenEmail, $subject,$body, $headers ,$attachments);	
	wp_mail($salesEmail, $subject,$body, $headers ,$attachments);	
	
		 	
	$subject = "Prior Opt In Notification";
	$body = "Prior Opt In Notification";				
	$headers[] = 'From: '.get_option( 'blogname' ).' <'.get_option( 'admin_email' ).'>';	
	
	foreach($kitchenDetail as $kitchenData) {
		$attachment = createPriorOptInPdf($kitchenData->state_code);
		wp_mail($kitchenData->kitchen_email, $subject,$body, $headers ,$attachment);	
	}
	
	$attachment = createPriorOptInPdf("all");		
	//wp_mail($kitchenEmail, $subject,$body, $headers ,$attachment);	
	wp_mail($salesEmail, $subject,$body, $headers ,$attachment);
	
	$subject = "Prior School Rediness Lunch Notification";
	$body = "Prior School Rediness Lunch Notification";				
	$headers[] = 'From: '.get_option( 'blogname' ).' <'.get_option( 'admin_email' ).'>';	
	foreach($kitchenDetail as $kitchenData) {
		$attachment = createPriorSchoolRedinessPdf($kitchenData->state_code);
		wp_mail($kitchenData->kitchen_email, $subject,$body, $headers ,$attachment);	
	}
	
	$attachment = createPriorSchoolRedinessPdf("all");	
	 
	//wp_mail($kitchenEmail, $subject,$body, $headers ,$attachment);	
	wp_mail($salesEmail, $subject,$body, $headers ,$attachment);
}
?> 