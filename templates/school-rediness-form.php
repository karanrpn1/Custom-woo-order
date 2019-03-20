<?php 
if(!is_user_logged_in()) {
	echo '<script>window.location.href = "'.home_url().'";</script>';
}	
?>

<div class="school-rediness-form">
	<?php	
	if(isset($_SESSION['success'])) {
		echo '<div class="successMsg">'.$_SESSION['success'].'</div>';
		unset($_SESSION['success']);
	}
	elseif(isset($_SESSION['error'])) {
		echo '<div class="errorMsg">'.$_SESSION['error'].'</div>';
		unset($_SESSION['error']);
	}
?>
	<h3>Excursion / School Readiness Lunches</h3>
	<p><strong>Hearty Health</strong> can provide a picnic lunch for children attending an excursion or as preparation for school readiness.</p><p>Morning Tea and Afternoon Tea will be provided as part of your regular delivery unless advised otherwise. Sandwiches will be packed in individual paper bags. Please advise any further requests in ‘Additional Comments’ if necessary.</p><p>There is no additional cost for this request, the number of Excursion / Picnic / School lunches requested will be deducted from regular meals delivered to your Centre for the day.</p><p>48 hours notice is required for this request.</p>
    
    <?php
	
	global $wpdb;
	$current_user = wp_get_current_user();
	
	$tablename = $wpdb->prefix.'order_info';	
	$orderCount = $wpdb->get_var( "SELECT count(*) FROM $tablename where user_id=$current_user->ID and status=1" );
	
	if($orderCount>0) {	
		$orderInfo = $wpdb->get_results( "SELECT * FROM $tablename where user_id=$current_user->ID and status=1" );
		$editOrderId = $orderInfo[0]->ID;
		$order = wc_get_order( $orderInfo[0]->order_id );
		$items = $order->get_items();
		
		foreach( $items as $key => $item){
			$item_id = $item['product_id'];
			$product = new WC_Product($item_id);
			$item_sku = $product->get_sku(); 
			$chkSku = explode("-",$item_sku);
			if($chkSku[1]=="monday") {
				$mondayValue = $item['qty'];
			}
			elseif($chkSku[1]=="tuesday") {
				$tuesdayValue = $item['qty'];
			}
			elseif($chkSku[1]=="wednesday") {
				$wednesdayValue = $item['qty'];
			}
			elseif($chkSku[1]=="thursday") {
				$thursdayValue = $item['qty'];
			}
			elseif($chkSku[1]=="friday") {
				$fridayValue = $item['qty'];
			}
			
		}
		?>
        <form method="post" id="school-rediness" name="school-rediness-form" action="<?php echo get_admin_url(); ?>admin-post.php">
        
        <input type="hidden" id="totalPackageValue" data-monday-value="<?=$mondayValue?>" data-tuesday-value="<?=$tuesdayValue?>" data-wednesday-value="<?=$wednesdayValue?>" data-thursday-value="<?=$thursdayValue?>" data-friday-value="<?=$fridayValue?>" />
        
        <input type="hidden" id="currentDayValue" value=""/>
        
        <input type="hidden" name="redirect-url" value="<?php echo '//'.$_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]; ?>" /> 
        
        <input type="hidden" name="action" value="school-rediness-form" />
        
        <input type="hidden" name="order_id" value="<?=$editOrderId?>" />
        <div id="error"></div>
        
        <div class="grp">             
            <h4> Details </h4> 
            <div class="opt-group">
                <label>Date</label>
                <input type="text" name="date" id="datepicker2" autocomplete="off" placeholder="Select Date" required>
            </div>
            
            <div class="opt-group">
                <label>Total Number of Children</label>
                <input type="number" name="totalPackage" placeholder="Total Number of Children" id="totalPackage" min="1" required readonly>
            </div>
        </div>
        
        <div class="grp chkChildren">         	
        	<h4> Sandwich fillings include:</h4>
            <div class="opt-group">
                <label>Vegemite</label>
                <input type="number" name="vegemite" placeholder="0" min="0" required>
            </div>
           
            <div class="opt-group">
                <label>Cheese</label>
                <input type="number" name="cheese" placeholder="0" min="0" required>
            </div>
           
            <div class="opt-group">
                <label>Cheese and Vegemite</label>
                <input type="number" name="cheese_vegemite" placeholder="0" min="0" required>
            </div>
           
            <div class="opt-group">
                <label>Cheese and Lettuce</label>
                <input type="number" name="cheese_lettuce" placeholder="0" min="0" required>
            </div>
            
            <div class="error"></div>
        </div> 
        
        <div class="grp chkChildren">         	
        	<h4> Water:</h4>
            <div class="opt-group size2">
                <label>2 litre bottle of water</label>
                <input type="number" name="bottle_large" placeholder="0" min="0" required>
            </div>
           
            <div class="opt-group size2">
                <label>Small individual bottles of water</label>
                <input type="number" name="bottle_small" placeholder="0" min="0" required>
            </div>   
            
             <div class="error"></div>            
            
        </div> 
        
        <div class="grp chkChildren">         	
        	<h4> Whole pieces of fruit:</h4>
            <div class="opt-group">
                <label>Apples</label>
                <input type="number" name="apple" placeholder="0" min="0" required>
            </div>
           
            <div class="opt-group">
                <label>Oranges</label>
                <input type="number" name="oranges" placeholder="0" min="0" required>
            </div>   
            
            <div class="opt-group">
                <label>Pears</label>
                <input type="number" name="pears" placeholder="0" min="0"  required>
            </div>   
            
            <div class="opt-group">
                <label>Bananas</label>
                <input type="number" name="bananas" placeholder="0" min="0" required>
            </div> 
            
             <div class="error"></div>  
            
        </div> 
        
        <div class="grp chkChildren">         	
        	<h4> Additional Comment:</h4>
            <div class="opt-group-textarea">               
                <textarea type="number" name="comment" rows="4" placeholder="Additional Comment" required></textarea>
            </div>                        
        </div>
        
        <div class="submit-group">
        	<button type="submit" name="submitRedinessForm">Submit</button>
        </div>
        
    </form>
	<?php	
	}	
	else {
		echo "<div class='errorMsg'>There is no active order..</div>";
		
	}
?>
    
    
    
    
     <?php 
    $tablename = $wpdb->prefix.'school_readiness_lunch';	
	$schoolRedinessData = $wpdb->get_results( "SELECT * FROM $tablename where order_info_id='".$editOrderId."' and date>'".strtotime('tomorrow')."' ");
	$rowcount = $wpdb->num_rows;
	if($rowcount>0) { 
	?>
    <br>
    <table>
        <thead>
        <tr>
            <td>ID</td>
            <td>Date</td>            
            <td>Total Children</td> 
            <td>Comment</td> 
            <td>Action</td>
        </tr>
        </thead>
        <tbody>  
        <?php 		
        $i = 1;
        foreach($schoolRedinessData as $row) { ?>
        <tr>
            <td><?=$i?></td>
            <td class="choiceDateRedinessSchool" data-id="<?=date("Y-m-d",$row->date)?>"><?=date("F d,Y",$row->date)?></td>           
            <td><?=$row->total_package?></td>
            <td><?=$row->comment?></td>
            <td>
                <a href="#" class="deleteSchoolRediness" data-id="<?=$row->ID?>">Delete</a>
            </td>	
        </tr>
        <?php 
        $i++;
        } ?>
        </tbody>
    </table>
    
    <?php } ?>
</div>
