<?php
global $wpdb;
$tablename = $wpdb->prefix.'order_info';
$current_user = wp_get_current_user();
$orderInfo = $wpdb->get_results( "SELECT * FROM $tablename where user_id=$current_user->ID" );
$rowcount = $wpdb->num_rows;
?>
<h2>My Meal Order Summary</h2>
<?php if($rowcount<=0) { ?>
<a href="<?=get_site_url();?>/custom-order-form" class="button">Add New Meal Order</a> 
<?php } ?>
    
<a href="<?=get_site_url();?>/close-date-form/" class="button" style="float: right;">Add Centre Close Dates</a>
<br>
<div id="order_info_text">
<p>Your Meal Summary must be submitted by 6.00pm Wednesdays to take effect the following Monday.</p>
<p>Any updates require 48 hours notice to take effect.</p>
</div>

<?php



if(isset($_SESSION['success'])) {
	echo '<div class="successMsg">'.$_SESSION['success'].'</div>';
	unset($_SESSION['success']);
}
?>


<div id="msg"></div>
<table class="recuringOrder">
	<thead>
        <tr>
        	<td>Order</td>
            <td>Date</td>
            <td>Status</td>
            <td><!--Order Active?--></td>
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
            <td><?=date("F d,Y",$row->createTime)?></td>
            <td><?php if($order_status=="on-hold") { echo "Waiting for Approval";} else { if($row->status==1){ echo "Active";}else { echo "Not Active";} } ?></td>
            <td class="actionRow">
            	<?php /*?><label class="switch"><input type="checkbox" value="<?=$row->ID?>" id="check<?=$i?>" <?php if($row->status==1){ echo "checked"; } ?> onchange="cTrig('check<?=$i?>')">  <span class="slider round"></span></label><?php */?>
                <a class="<?php if($row->status==1) { echo 'tooltipHidden'; } else { echo 'tooltip';} ?>" href="<?php if($row->status==1){ echo get_site_url().'/custom-order-form/?editId='.$row->ID;} else { echo "#";} ?>">Edit<span class="tooltiptext">Please activate order to edit !!</span></a> 
                
                <?php if($row->status==1) { ?>
                	<a href="<?=get_site_url();?>/lunch-opt/?editId=<?=$row->ID;?>">Lunch Opt In Alternative</a>
                <?php } ?>
           	</td>	
        </tr>
       	<?php 
		$i++;
		} ?>
    </tbody>
</table>
