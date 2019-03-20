<?php
if(!empty($_GET['cronActive']) && isset($_GET['cronActive']) && ($_GET['cronActive']=="9d6e8916c47e033cd080634d90354ba8")) { 
	global $wpdb;
	$tablename = $wpdb->prefix.'order_info';	
	$orderInfo = $wpdb->get_results( "SELECT * FROM $tablename where status=1" );
	foreach($orderInfo as $orderRow) {		
		global $woocommerce;
		
		$orderDetail = wc_get_order( $orderRow->order_id);
			
		$billingAddress = $orderDetail->get_address('billing');
		$shippingAddress = $orderDetail->get_address('shipping');	
		
		$items = $orderDetail->get_items();
		
		$order = wc_create_order(array('customer_id' => $orderRow->user_id,'created_via'=>'update_order'));
		
		foreach( $items as $key => $item){
			
			$order->add_product( get_product($item['product_id']), $item['qty']); 
		}
		
		$order->set_address( $billingAddress, 'billing' );	
		$order->set_address( $shippingAddress, 'shipping' );
		//$payment_gateways = $woocommerce->payment_gateways->payment_gateways();
		$order->set_payment_method('invoice');	
		$order->calculate_totals();
		$order->update_status("processing", 'Recuring order',TRUE);
		
		$childCount = get_post_meta( $orderRow->order_id, 'child_count', true ); 
		update_post_meta( $order->id, 'child_count', $childCount ); 
		
		for($i=1;$i<=$childCount;$i++){	
			
			update_post_meta( $order->id, 'child_toddler_toggle_'.$i, get_post_meta( $orderRow->order_id, 'child_toddler_toggle_'.$i, true ) );
			
			update_post_meta( $order->id, 'child_name_'.$i, get_post_meta( $orderRow->order_id, 'child_name_'.$i, true ) );
			
			update_post_meta( $order->id, 'child_remark_'.$i, get_post_meta( $orderRow->order_id, 'child_remark_'.$i, true ) ); 
			
			update_post_meta( $order->id, 'child_id_number_'.$i, get_post_meta( $orderRow->order_id, 'child_id_number_'.$i, true ) );
			
			update_post_meta( $order->id, 'child_attend_'.$i, get_post_meta( $orderRow->order_id, 'child_attend_'.$i, true ) ); 
			
			update_post_meta( $order->id, 'child_allergy_dairy_'.$i, get_post_meta( $orderRow->order_id, 'child_allergy_dairy_'.$i, true ) ); 
			
			update_post_meta( $order->id, 'child_roomNumber_'.$i, get_post_meta( $orderRow->order_id, 'child_roomNumber_'.$i, true ) ); 
			
			update_post_meta( $order->id, 'child_allergy_vegetarian_'.$i, get_post_meta( $orderRow->order_id, 'child_allergy_vegetarian_'.$i, true ) ); 
			
			update_post_meta( $order->id, 'child_allergy_grains_'.$i, get_post_meta( $orderRow->order_id, 'child_allergy_grains_'.$i, true ) ); 
			
			update_post_meta( $order->id, 'child_allergy_meat_'.$i, get_post_meta( $orderRow->order_id, 'child_allergy_meat_'.$i, true ) ); 
			
			update_post_meta( $order->id, 'child_allergy_seafood_'.$i, get_post_meta( $orderRow->order_id, 'child_allergy_seafood_'.$i, true ) ); 
			
			update_post_meta( $order->id, 'child_allergy_soyproduct_'.$i, get_post_meta( $orderRow->order_id, 'child_allergy_soyproduct_'.$i, true ) ); 
			
			update_post_meta( $order->id, 'child_allergy_legumes_'.$i, get_post_meta( $orderRow->order_id,'child_allergy_legumes_'.$i, true ) ); 
			
			update_post_meta( $order->id, 'child_allergy_fruits_'.$i, get_post_meta( $orderRow->order_id, 'child_allergy_fruits_'.$i, true ) ); 
			
			update_post_meta( $order->id, 'child_allergy_vegetables_'.$i, get_post_meta( $orderRow->order_id, 'child_allergy_vegetables_'.$i, true ) );
						
			update_post_meta( $order->id, 'child_allergy_others_'.$i, get_post_meta( $orderRow->order_id, 'child_allergy_others_'.$i, true ) );
			
			update_post_meta( $order->id, 'child_toddler_toggle_'.$i, get_post_meta( $orderRow->order_id, 'child_toddler_toggle_'.$i, true ) );  
			
		}
		
		global $wpdb;
		$tablename = $wpdb->prefix.'order_info';
		$wpdb->update( $tablename, 
			array( 'order_id' =>  $order->id ), 
			array( 'ID' =>  $orderRow->ID ), 
			array( '%s' ),  
			array( '%d' ) 
		);  
		 
	}
	
	$kitchenEmail = get_option( 'kitchen_staff_mail' );		
	$salesEmail = get_option( 'sales_staff_mail' );			 	
	$subject = "Weekly Order Summary";
	$body = "Weekly Order Summary";				
	$headers[] = 'From: '.get_option( 'blogname' ).' <'.get_option( 'admin_email' ).'>';
	$manageStateTable = $wpdb->prefix.'manage_state';
	$kitchenDetail = $wpdb->get_results( "SELECT * FROM $manageStateTable" );	
	
	foreach($kitchenDetail as $kitchenData) {
		$attachment = createWeeklyPdf($kitchenData->state_code);
		wp_mail($kitchenData->kitchen_email, $subject,$body, $headers ,$attachment);	
	}
		
	$attachment = createWeeklyPdf("all");		
	
	//wp_mail($kitchenEmail, $subject,$body, $headers ,$attachment);	
	wp_mail($salesEmail, $subject,$body, $headers ,$attachment);	
	
}
?>