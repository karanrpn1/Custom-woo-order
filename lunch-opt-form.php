<?php 
if(!is_user_logged_in()) {
	echo '<script>window.location.href = "'.home_url().'";</script>';
}

global $wpdb;
$current_user = wp_get_current_user();
if(isset($_GET['editId'])) {
	
	$tablename = $wpdb->prefix.'order_info';	
	$orderCount = $wpdb->get_var( "SELECT count(*) FROM $tablename where ID='".$_GET['editId']."' and user_id=$current_user->ID and status=1" );
	
	if($orderCount==0) {
		$redirectUrl = get_site_url().'/my-account/recuring_order/';
		echo '<script>window.location.href = "'.$redirectUrl.'";</script>';
		exit(); 
	}  
	else {
		$orderInfo = $wpdb->get_results( "SELECT * FROM $tablename where ID='".$_GET['editId']."' " );
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
	}
}
?>

<div class="lunch-opt-form">
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

	<p>If you would like to substitute your lunches on a particular day for one of Opt in Alternatives,please select date below  and choose from the option.</p>
    
    <form method="post" id="lunch-opt" name="save-opt-in-form" action="<?php echo get_admin_url(); ?>admin-post.php">
    	 
        <input type="hidden" id="totalPackageValue" data-monday-value="<?=$mondayValue?>" data-tuesday-value="<?=$tuesdayValue?>" data-wednesday-value="<?=$wednesdayValue?>" data-thursday-value="<?=$thursdayValue?>" data-friday-value="<?=$fridayValue?>" />
        
        <input type="hidden" id="currentDayValue" value=""/>
        
        <input type="hidden" name="redirect-url" value="<?php echo '//'.$_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]; ?>" /> 
        
        <input type="hidden" name="action" value="save-opt-in-form" />
        
        <input type="hidden" name="order_id" value="<?=$_GET['editId']?>" />
        <div id="error"></div>
         
        <div class="opt-group">
        	<label>Select Date</label>
        	<input type="text" name="date" id="datepicker1" autocomplete="off" placeholder="Select Date" required>
       	</div>
        
        <div class="opt-group">
        	<label>Number of Lunches</label>
        	<input type="number" name="totalPackage" placeholder="Number of Lunches" id="totalPackage" min="1" required readonly>
       	</div>
        
       <!-- <div class="opt-group">
        	<label>Number of Lunch Meals.</label>
        	<input type="number" name="mealPackage" placeholder="Meals Package" id="mealPackage" min="1" required>
       	</div>-->
        
        <div class="form-group"> 
        	<label class="checkContainer">Spaghetti Bolognaise<small>Ingredients: Bolognaise Suace-beef, carrot, onion, celery, tomato, garlic, bay leaf, basil, Pasta-wheat, flour,water,salt</small><input type="radio" name="mealChoice" value="1" required checked><span class="checkmark"></span></label> 
        </div>
        
        <div class="form-group" id="disabledChoice">  
        	<label class="checkContainer">Sandwiches<small>Ingredients: Baked-bakers flour, yeast, water, sugar, salt, Vegemite, cheese, lettuce</small><input type="radio" name="mealChoice" value="2" required><span class="checkmark"></span></label>        	
        </div>
        
        <div class="form-group">      
        	<label class="checkContainer">Pumpkin Soup with bread<small>Ingredients: Pumpkin, potato, onion, water, cumin, garlic, bread-bakers flour, yeast, water, sugar , salt</small><input type="radio" name="mealChoice" value="3" required><span class="checkmark"></span></label>
        </div>
        
        <div class="submit-group">
        	<button type="submit" name="submitOptInOrder">Submit</button>
        </div>
        
    </form>
    
    
     <?php 
    $tablename = $wpdb->prefix.'opt_in_alternative';	
	$lunchOptInfo = $wpdb->get_results( "SELECT * FROM $tablename where order_info_id='".$_GET['editId']."' and date>'".strtotime('tomorrow')."' ");
	$rowcount = $wpdb->num_rows;
	if($rowcount>0) { 
	?>
    <table>
        <thead>
        <tr>
            <td>ID</td>
            <td>Date</td>
            <td>Choice</td>
            <td>Total Package</td>
            <td>Action</td>
        </tr>
        </thead>
        <tbody>  
        <?php 		
        $i = 1;
        foreach($lunchOptInfo as $row) { ?>
        <tr>
            <td><?=$i?></td>
            <td class="choiceDate" data-id="<?=date("Y-m-d",$row->date)?>"><?=date("F d,Y",$row->date)?></td>
            <td><?php if($row->choice==1){ echo "Spaghetti Bolognaise";}elseif($row->choice==2) { echo "Sandwiches";} elseif($row->choice==3){ echo "Pumpkin Soup with bread"; } ?></td>
            <td><?=$row->total_package?></td>
            <td>
                <a href="#" class="deleteOptIn" data-id="<?=$row->ID?>">Delete</a>
            </td>	
        </tr>
        <?php 
        $i++;
        } ?>
        </tbody>
    </table>
    
    <?php } ?>
</div>
