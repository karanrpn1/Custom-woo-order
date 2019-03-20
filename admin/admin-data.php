<?php
function custom_woo_order_menu(){
  	add_menu_page('Custom Woo Order', 'Custom Woo Order', 'manage_options', 'custom-woo-order', 'custom_woo_order_field_settings_page');
  	
	add_submenu_page( 'custom-woo-order', 'General Setting', 'General Setting', 'manage_options', 'custom-woo-order', 'custom_woo_order_field_settings_page');
  	
	add_submenu_page( 'custom-woo-order', 'Order PDF', 'Order PDF', 'edit_posts', 'custom-wo-pdf-download', 'custom_woo_order_pdf_download');
	
	add_submenu_page( 'custom-woo-order', 'Recuring Orders', 'Recuring Orders', 'manage_options', 'custom-woo-recuring-orders', 'custom_woo_recuring_orders');
	
	add_submenu_page( 'custom-woo-order', 'Manage State', 'Manage State', 'manage_options', 'custom-woo-manage-state', 'custom_woo_manage_state');
	
	add_action( 'admin_init', 'custom_woo_order_field_plugin_settings' );
	
}
add_action('admin_menu', 'custom_woo_order_menu');



function custom_woo_order_pdf_download(){
	echo '<div class="wrap"><div id="icon-options-general" class="icon32"><br></div>	<h2>Order Pdf</h2></div>'; ?> 
	<table class="widefat">
	<thead> 
        <tr>
        	<td>Sr No.</td>
            <td>Name</td>           
            <td>Action</td>
        </tr>
    </thead>
    <tbody>  
		<?php 
        $i = 1;
        $dir = CUS_WOO_ORDER_PLUGIN_DIR.'/pdffile/';
        $directoryUrl = CUS_WOO_ORDER_PLUGIN_URL.'/pdffile/';		
        
        if ($handle = opendir($dir)) {
    
            while (false !== ($entry = readdir($handle))) {
        
                if ($entry != "." && $entry != "..") {
                    $fileName = preg_replace('/\\.[^.\\s]{2,4}$/', '', $entry);
                    $fileName = str_replace('_',' ', $fileName);
                    echo '<tr>
                    <td>#'.$i.'</td>
                    <td>'.ucwords($fileName).'</td>
                    <td><a href="'.$directoryUrl.$entry.'" target="_blank">View</a></td>
                    </tr>';
                    $i++; 
                }				
            }		
            closedir($handle);
        }		
        ?>
    </tbody>
</table> 

<?php
}

function custom_woo_recuring_orders(){
	echo '<div class="wrap"><div id="icon-options-general" class="icon32"><br></div>	<h2>Recurring Order</h2></div>'; 
	
	global $wpdb;
	$tablename = $wpdb->prefix.'order_info';
	$current_user = wp_get_current_user();
	$orderInfo = $wpdb->get_results( "SELECT * FROM $tablename" );
	?> 
    
	<table class="widefat">
	<thead>
        <tr>
        	<td>Order</td>
            <td>Centre</td>
            <td>Date</td>
            <td>Status</td>
            <td>Order Active?</td>
        </tr>
    </thead>
    <tbody>  
    	<?php 
		$i = 1;
		foreach($orderInfo as $row) {		
		
		$order = wc_get_order( $row->order_id );	
		
		$order_data = $order->get_data();		
		$order_status = $order_data['status'];			
		
	 ?>
    	<tr>
        	<td>#<?=$row->order_id?></td>
            
            <td><?=$order_data['billing']['company']?></td>
            
            <td><?=date("F d,Y",$row->createTime)?></td>
            
            <td><?php if($order_status=="on-hold") { echo "Waiting for Approval";} else { if($row->status==1){ echo "Active";}else { echo "Not Active";} } ?></td>
            
            <td><label class="switch"><input type="checkbox" value="<?=$row->ID?>" id="check<?=$i?>" <?php if($row->status==1){ echo "checked"; } ?> onchange="recuringTrigger('check<?=$i?>')">  <span class="slider round"></span></label>
           	</td>	
        </tr> 
       	<?php 
		$i++;
		} ?>
    </tbody>
</table> 

<?php
}

function custom_woo_manage_state(){
	echo '<div class="wrap"><div id="icon-options-general" class="icon32"><br></div>	<h2>Manage State</h2></div>'; 
	$redirectUrl = "//".$_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] ;
	?>	
    <form name="save-child-info" method="post" action="<?php echo get_admin_url().'admin-post.php'; ?>">  
    	<input type="hidden" name="action" value="admin-add-state" />
    	<input type="hidden" name="redirect-url" value="<?php echo $redirectUrl; ?>" /> 
    	<div class="add_state">
        	<input type="text" name="state_code" placeholder="State Code" required>
            <input type="email" name="kitchen_email" placeholder="Kitchen Email" required>
            <button type="submit" name="save_state">Add State</button>
        </div>          
    </form>
    <table class="widefat">
	<thead>
        <tr>
        	<td>Sr No.</td>
            <td>State Code</td>
            <td>Kitchen Email</td>
            <td>Action</td>
        </tr>
    </thead>
    <tbody> 
    <?php
	global $wpdb;
	$tabelName = $wpdb->prefix.'manage_state';
	$stateData = $wpdb->get_results( "SELECT * FROM $tabelName " );
	$i=1;
	foreach($stateData as $stateInfo) {
		echo '<tr><td>'.$i.'</td><td>'.$stateInfo->state_code.'</td><td>'.$stateInfo->kitchen_email.'</td><td><a href="#" id="delState_'.$stateInfo->ID.'" onclick="deleteState('.$stateInfo->ID.')">Delete</a></td></tr>';
		$i++; 
	}  
	?>            	 
    </tbody>
</table> 
<?php	
}


function custom_woo_order_field_plugin_settings() {		
	//register_setting( 'custom-woo-order-plugin-settings-group',"kitchen_staff_mail");	
	register_setting( 'custom-woo-order-plugin-settings-group',"sales_staff_mail");	
	register_setting( 'custom-woo-order-plugin-settings-group',"order_off_email");
	register_setting( 'custom-woo-order-plugin-settings-group',"order_on_email");	
	register_setting( 'custom-woo-order-plugin-settings-group',"order_update_email_to_admin");
	register_setting( 'custom-woo-order-plugin-settings-group',"order_update_email_to_customer");	
	register_setting( 'custom-woo-order-plugin-settings-group',"new_order_email_customer");	
	register_setting( 'custom-woo-order-plugin-settings-group',"new_order_email_admin");	
	
}

function custom_woo_order_field_settings_page() {  ?>
	<h1>Custom Plugin Setting </h1>
   
    <form method="post" action="options.php" >
    	
    <?php settings_fields( 'custom-woo-order-plugin-settings-group' ); ?>
 	<?php do_settings_sections( 'custom-woo-order-plugin-settings-group' ); ?>
    <table class="form-table">
    	
       <?php /*?> <tr valign="top">
        <th scope="row">Kitchen Staff Email</th>
        <td><input name="kitchen_staff_mail" type="text" value="<?php echo esc_attr( get_option('kitchen_staff_mail') ); ?>">  </td>
        </tr><?php */?>
        
        <tr valign="top">
        <th scope="row">Sales Staff Email</th>
        <td><input name="sales_staff_mail" type="text" value="<?php echo esc_attr( get_option('sales_staff_mail') ); ?>">  </td>
        </tr>
         
       <tr valign="top">
        	<th scope="row">Order Off Email</th>
        	<td><?php $content = get_option('order_off_email');
          wp_editor( $content, 'order_off_email', $settings = array('textarea_rows'=> '10','editor_class'=>'order_off_email') ); ?><small><strong>Note</strong> : for username use {{USERNAME}} and for Order Id use {{ORDERID}}</small></td>
        </tr> 
        
      	<tr valign="top">
        	<th scope="row">Order On Email</th>
        	<td><?php $content = get_option('order_on_email');
          wp_editor( $content, 'order_on_email', $settings = array('textarea_rows'=> '10','editor_class'=>'order_on_email') ); ?><small><strong>Note</strong> : for username use {{USERNAME}} and for Order Id use {{ORDERID}}</small></td>
        </tr> 
        
        <tr valign="top">
        	<th scope="row">Order Update Email To Admin</th>
        	<td><?php $content = get_option('order_update_email_to_admin');
          wp_editor( $content, 'order_update_email_to_admin', $settings = array('textarea_rows'=> '10','editor_class'=>'order_update_email_to_admin') ); ?><small><strong>Note</strong> : for username use {{USERNAME}} and for Order Id use {{ORDERID}}</small></td>
        </tr> 
        
         <tr valign="top">
        	<th scope="row">Order Update Email To Customer</th>
        	<td><?php $content = get_option('order_update_email_to_customer');
          wp_editor( $content, 'order_update_email_to_customer', $settings = array('textarea_rows'=> '10','editor_class'=>'order_update_email_to_customer') ); ?><small><strong>Note</strong> : for username use {{USERNAME}} and for Order Id use {{ORDERID}}</small></td>
        </tr> 
        
        <tr valign="top">
        	<th scope="row">New Order Email To Customer</th>
        	<td><?php $content = get_option('new_order_email_customer');
          wp_editor( $content, 'new_order_email_customer', $settings = array('textarea_rows'=> '10','editor_class'=>'new_order_email_customer') ); ?><small><strong>Note</strong> : for username use {{USERNAME}} and for Order Id use {{ORDERID}}</small></td>
        </tr>  
        
        <tr valign="top">
        	<th scope="row">New Order Email To Admin</th>
        	<td><?php $content = get_option('new_order_email_admin');
          wp_editor( $content, 'new_order_email_admin', $settings = array('textarea_rows'=> '10','editor_class'=>'new_order_email_admin') ); ?><small><strong>Note</strong> : for username use {{USERNAME}} and for Order Id use {{ORDERID}}</small></td>
        </tr>
                
    </table>
	
    <?php submit_button(); ?>
    </form>
<?php } 
