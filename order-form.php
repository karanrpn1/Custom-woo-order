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
		
		if(!empty($orderInfo[0]->item_sku) && !empty($orderInfo[0]->item_quantity)) {		
			$updatedItemSku = json_decode($orderInfo[0]->item_sku);
			$updatedOrderQuantity = json_decode($orderInfo[0]->item_quantity); 
			$mondayValue = 0;
			$tuesdayValue = 0;
			$wednesdayValue = 0;
			$thursdayValue = 0;
			$fridayValue = 0;
			
			$i=0;
			foreach($updatedItemSku as $itemSku) {
				$chkSku = explode("-",$itemSku);
				if($chkSku[1]=="monday") {
					$mondayValue = $updatedOrderQuantity[$i];
				}
				elseif($chkSku[1]=="tuesday") {
					$tuesdayValue = $updatedOrderQuantity[$i];
				}
				elseif($chkSku[1]=="wednesday") {
					$wednesdayValue = $updatedOrderQuantity[$i];
				}
				elseif($chkSku[1]=="thursday") {
					$thursdayValue = $updatedOrderQuantity[$i];
				}
				elseif($chkSku[1]=="friday") {
					$fridayValue = $updatedOrderQuantity[$i];
				}
				
				$i++; 
			}
		}
		
	}
	
}
else {
	$tablename = $wpdb->prefix.'order_info';	
	$orderCount = $wpdb->get_var( "SELECT count(*) FROM $tablename where user_id=$current_user->ID" );	
	if($orderCount>0) { 
		$redirectUrl = get_site_url().'/my-account/recuring_order/';
		echo '<script>window.location.href = "'.$redirectUrl.'";</script>';
		exit(); 
	}
}

$childInfoTable = $wpdb->prefix.'child_info';
$childData = $wpdb->get_results( "SELECT * FROM $childInfoTable where user_id=$current_user->ID" );
$childIds = array(); 
foreach ($childData as $row){
	$childIds[] = $row->ID;
}


if(isset($_GET['mondayVal'])) 
	$mondayValue = $_GET['mondayVal'];
if(isset($_GET['tuesdayVal'])) 
	$tuesdayValue = $_GET['tuesdayVal'];
if(isset($_GET['wednesdayVal'])) 
	$wednesdayValue = $_GET['wednesdayVal'];
if(isset($_GET['thursdayVal'])) 
	$thursdayValue = $_GET['thursdayVal'];
if(isset($_GET['fridayVal'])) 
	$fridayValue = $_GET['fridayVal'];


$uri_parts = explode('?', $_SERVER['REQUEST_URI'], 2);
if(isset($_REQUEST['editId'])) {
	$redUrl = "//".$_SERVER["HTTP_HOST"] . $uri_parts[0]."?editId=".$_REQUEST['editId'];
}
else {
	$redUrl = "//".$_SERVER["HTTP_HOST"] . $uri_parts[0];
}
?>
<div class="custom-order-form">
	<?php 
	if(isset($_SESSION['success']) && !empty($_SESSION['success'])) {
		echo '<div class="successMsg">'.$_SESSION['success'].'</div>';
		unset($_SESSION['success']);
	}
	?>
	<form name="order-form" method="post" id="new-order-create">
    	<?php /*?><a href="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ); ?>" class="return-account">Return to My Account</a><?php */?>
		<?php 
		if(isset($_GET['editId'])) {
			echo '<input type="hidden" name="action" value="updateAjaxOrder" /><input type="hidden" name="postId" value="'.$_GET['editId'].'" />';
		}
		else {
			echo '<input type="hidden" name="action" value="saveAjaxOrder" />';
		}
		?>
		<input type="hidden" name="redirect-url" value="<?php echo $redUrl; ?>" /> 
		<input type="hidden" name="childIds" value="<?php echo implode(",",$childIds); ?>" id="childIds"/>
        
        <?php
		$monday=0;
		$tuesday=0;
		$wednesday=0;
		$thursday=0;
		$friday=0;
		foreach ($childData as $row){
			$childIds[] = $row->ID;
			$attenData = json_decode($row->attend,true);
			if(in_array('Monday',$attenData)) { $monday++; } 
			if(in_array('Tuesday',$attenData)) { $tuesday++; } 
			if(in_array('Wednesday',$attenData)) { $wednesday++; }
			if(in_array('Thursday',$attenData)) { $thursday++; }
			if(in_array('Friday',$attenData)) { $friday++; }
		}  
		?>	 
        <input type="hidden" name="monday_allergie" id="monday_allergie" value="<?=$monday;?>"/>
        <input type="hidden" name="tuesday_allergie" id="tuesday_allergie" value="<?=$tuesday;?>"/>
        <input type="hidden" name="wednesday_allergie" id="wednesday_allergie" value="<?=$wednesday;?>"/>
        <input type="hidden" name="thursday_allergie" id="thursday_allergie" value="<?=$thursday;?>"/>
        <input type="hidden" name="friday_allergie" id="friday_allergie" value="<?=$friday;?>"/>  
		
    	<div class="menuType">
        	<h3>Select Menu Type</h3>
        	<div class="menu-group">
            	<label class="checkContainer">Hearty Health Full Menu<input type="radio" <?php if(!empty($chkSku[0])){ if($chkSku[0]=='fullmenu'){ echo "checked";} } else { echo "checked"; } ?> name="productType" value="fullmenu"><span class="checkmark"></span></label>            	
           	</div>
           	<div class="menu-group">
            <label class="checkContainer">Lunch Only <input type="radio" name="productType" <?php if(!empty($chkSku[0])){ if($chkSku[0]=='lunchonly'){ echo "checked";} } ?> value="lunchonly"><span class="checkmark"></span></label>             		
          	</div>
            
            <div class="menu-group">
            	<label class="checkContainer">OOSH (out of school hours)<input type="radio" name="productType" <?php if(!empty($chkSku[0])){ if($chkSku[0]=='oosh'){ echo "checked";} } ?> value="oosh"><span class="checkmark"></span></label>
           	</div>
        </div>
        
        <div class="menuOrder totalMeal">
        	<h3 style="padding-bottom:0;">Regular Meals Required</h3>
            <p style="padding-bottom:15px;">Enter total number of regular meals, excluding allergen and toddler meals</p>
        	<div class="weekday-group">
				<label>Monday</label>            	
                <input type="number" placeholder="Number of Meals" min="30" name="mondayPackage" value="<?php if(isset($_GET['mondayVal'])) { echo $mondayValue; } else { if(!empty($mondayValue)){ echo $mondayValue-$monday; } } ?>" data-change="monday_regular" data-substract="monday_allergie" required/>	
                <span class="error">Error message</span>			
            </div>
            <div class="weekday-group">
				<label>Tuesday</label>            	
                <input type="number" placeholder="Number of Meals" min="30" name="tuesdayPackage" value="<?php if(isset($_GET['tuesdayVal'])) { echo $tuesdayValue; } else { if(!empty($tuesdayValue)){ echo $tuesdayValue-$tuesday; } } ?>" data-change="tuesday_regular" data-substract="tuesday_allergie" required/>	
                <span class="error">Error message</span>
            </div>
            <div class="weekday-group">
				<label>Wednesday</label>            	
                <input type="number" placeholder="Number of Meals" min="30" name="wednesdayPackage" value="<?php if(isset($_GET['wednesdayVal'])) { echo $wednesdayValue; } else { if(!empty($wednesdayValue)){ echo $wednesdayValue-$wednesday; } } ?>" data-change="wednesday_regular" data-substract="wednesday_allergie" required/>	
                <span class="error">Error message</span>
            </div>
            <div class="weekday-group">
				<label>Thursday</label>            	
                <input type="number" placeholder="Number of Meals" min="30" name="thursdayPackage" value="<?php if(isset($_GET['thursdayVal'])) { echo $thursdayValue; } else { if(!empty($thursdayValue)){ echo $thursdayValue-$thursday; } } ?>" data-change="thursday_regular" data-substract="thursday_allergie" required/>
                <span class="error">Error message</span>	
            </div>
            <div class="weekday-group">
				<label>Friday</label>            	
                <input type="number" placeholder="Number of Meals" min="30" name="fridayPackage" value="<?php if(isset($_GET['fridayVal'])) { echo $fridayValue; } else { if(!empty($fridayValue)){ echo $fridayValue-$friday; } }?>" data-change="friday_regular" data-substract="friday_allergie" required/>	
                <span class="error">Error message</span>
            </div>
            
        </div>
        
        <div class="menuOrder">
                
        	<h3>Total Meals</h3>
        	
            <div class="weekday-group">
				<label>Monday</label>            	
               	<input type="text" class="noborder" id="monday_regular" value="<?php if(!empty($_GET['editId'])) { if(isset($_GET['mondayVal'])) { echo ($_GET['mondayVal']+$monday); } else { echo $mondayValue; } } else { if(isset($_GET['mondayVal'])) { echo ($_GET['mondayVal']+$monday); } else { echo '0'; } }?>" disabled>		
            </div>
            
            <div class="weekday-group">
				<label>Tuesday</label>            	
                <input type="text" class="noborder" id="tuesday_regular" value="<?php if(!empty($_GET['editId'])) { if(isset($_GET['tuesdayVal'])) { echo ($_GET['tuesdayVal']+$tuesday); } else { echo $tuesdayValue; } } else { if(isset($_GET['tuesdayVal'])) { echo ($_GET['tuesdayVal']+$tuesday); } else { echo '0'; } }?>" disabled> 
            </div>
            
            <div class="weekday-group">
				<label>Wednesday</label>            	
                <input type="text" class="noborder" id="wednesday_regular" value="<?php if(!empty($_GET['editId'])) { if(isset($_GET['wednesdayVal'])) { echo ($_GET['wednesdayVal']+$wednesday); } else { echo $wednesdayValue; } } else { if(isset($_GET['wednesdayVal'])) { echo ($_GET['wednesdayVal']+$wednesday); } else { echo '0'; } }?>" disabled>
            </div>
            
            <div class="weekday-group">
				<label>Thursday</label>            	
                <input type="text" class="noborder" id="thursday_regular" value="<?php if(!empty($_GET['editId'])) { if(isset($_GET['thursdayVal'])) { echo ($_GET['thursdayVal']+$thursday); } else { echo $thursdayValue; } } else { if(isset($_GET['thursdayVal'])) { echo ($_GET['thursdayVal']+$thursday); } else { echo '0'; } }?>" disabled>
            </div>
            
            <div class="weekday-group">
				<label>Friday</label>            	
                <input type="text" class="noborder" id="friday_regular" value="<?php if(!empty($_GET['editId'])) { if(isset($_GET['fridayVal'])) { echo ($_GET['fridayVal']+$friday); } else { echo $fridayValue; } } else { if(isset($_GET['fridayVal'])) { echo ($_GET['fridayVal']+$friday); } else { echo '0'; } }?>" disabled>
            </div>
             
        </div>
        
		<div class="allegenMeals">
			<h3>Allergen & Toddler Meals</h3>
            <div class="rightButton">           
                <a class="addNewChild" href="<?php echo get_site_url().'/custom-add-info/?redUrl='.$redUrl; ?>"> Add Child Info</a>
            </div>
            <table>
                <thead>
                    <tr>
                        <td>Name and Allergies</td>
                        <td>Monday</td>
                        <td>Tuesday</td>
                        <td>Wednesday</td>
                        <td>Thursday</td>
                        <td>Friday</td>
                        <td>Total</td>
                        <td>Edit</td>
                        <td>Delete</td>
                    </tr>
                </thead>
                <tbody>
                <?php
				$monday=0;
				$tuesday=0;
				$wednesday=0;
				$thursday=0;
				$friday=0;
				
				$mondayTotal = 0;
				$tuesdayTotal = 0;
				$wednesdayTotal = 0;
				$thursdayTotal = 0;
				$fridayTotal = 0;
				
				foreach ($childData as $row){
					$childIds[] = $row->ID;
					$lbl="";
					if($row->toddler_toggle==1) {
					$lbl=" <strong>Toddler</strong>";
					}
					$attenData = json_decode($row->attend,true); 
					
					$allergyGroup ="";
					if(!empty($row->dairy) && $row->dairy<>"null") {
						$allergyGroup.= implode(", ",json_decode($row->dairy) ).", ";
					}
					
					if(!empty($row->vegetarian) && $row->vegetarian<>"null") {
						$allergyGroup.= implode(", ",json_decode($row->vegetarian) ).", ";
					}
					if(!empty($row->grains) && $row->grains<>"null") {
						$allergyGroup.= implode(", ",json_decode($row->grains) ).", ";
					}
					if(!empty($row->meat) && $row->meat<>"null") {
						$allergyGroup.= implode(", ",json_decode($row->meat) ).", ";
					}
					if(!empty($row->seafood) && $row->seafood<>"null") {
						$allergyGroup.= implode(", ",json_decode($row->seafood) ).", ";
					}
					if(!empty($row->soyproduct) && $row->soyproduct<>"null") {
						$allergyGroup.= implode(", ",json_decode($row->soyproduct) ).", ";
					}
					if(!empty($row->legumes) && $row->legumes<>"null") {
						$allergyGroup.= implode(", ",json_decode($row->legumes) ).", ";
					}
					if(!empty($row->fruits) && $row->fruits<>"null") {
						$allergyGroup.= implode(", ",json_decode($row->fruits) ).", ";
					}
					if(!empty($row->vegetables) && $row->vegetables<>"null") {
						$allergyGroup.= implode(", ",json_decode($row->vegetables) ).", ";
					}					
										
					if(!empty($row->others) && $row->others<>"null") {
						$allergyGroup.= implode(", ",json_decode($row->others) ).", ";
					}
					
				?>
					<tr>
                    	<td><?=$row->child_name.$lbl.'<br> ('.rtrim($allergyGroup,", ").')'?></td>
						<td><?php if(in_array('Monday',$attenData)) { echo "Yes"; $mondayTotal++;  } else { echo "No";} ?></td>
                        <td><?php if(in_array('Tuesday',$attenData)) { echo "Yes"; $tuesdayTotal++; } else { echo "No";} ?></td>
                        <td><?php if(in_array('Wednesday',$attenData)) { echo "Yes"; $wednesdayTotal++;  } else { echo "No";} ?></td>
                        <td><?php if(in_array('Thursday',$attenData)) { echo "Yes"; $thursdayTotal++; } else { echo "No";} ?></td>
                        <td><?php if(in_array('Friday',$attenData)) { echo "Yes"; $fridayTotal++;  } else { echo "No";} ?></td>
                        <td><?=count($attenData)?></td>
                        <td><a href="<?php echo get_site_url().'/custom-add-info/?editId='.$row->ID.'&redUrl='.$redUrl; ?>" class="editChild">Edit</a></td>
                        <td><a href="#" class="deleteChild" data-id="<?=$row->ID?>">Delete</a></td>
                    </tr>
                    
					<?php }  ?>	
                    
                    <tr>
                    	<td>Total</td>
						<td><?=$mondayTotal?></td>
                        <td><?=$tuesdayTotal?></td>
                        <td><?=$wednesdayTotal?></td>
                        <td><?=$thursdayTotal?></td>
                        <td><?=$fridayTotal?></td>
                        <td><?php echo ($mondayTotal+$tuesdayTotal+$wednesdayTotal+$thursdayTotal+$fridayTotal); ?></td>
                        <td></td>
                        <td></td>
                    </tr> 
                    
                </tbody>
            </table>
        </div>        
        
        
         <!-- CLOSE DATE FORM --> 
        <div class="closeDateOrderPage">
            <h3>Centre Close Date</h3>           
            <div class="opt-group">
                <div id="jrange" class="dates">
                    <label>Select Date</label>
                    <input id="closeDate" type="text" placeholder="Select Date" autocomplete="off" required /> 
                    <div></div> 
                </div>
            </div>
            
            <div class="opt-group">
                <label>Reason for closure</label>
                <input type="text" id="closeReason" placeholder="Reason for closure">
            </div>
            
            <div class="opt-group submit-group">
                <button type="submit" id="submitCloseDateOrderForm">Add</button>
            </div> 
            <?php
            global $wpdb;
            $tablename = $wpdb->prefix.'close_date_info';	
            $closeDateFormInfo = $wpdb->get_results( "SELECT * FROM $tablename where user_id='".$current_user->ID."'  and startDate>'".strtotime('tomorrow')."' ");
            $rowcount = $wpdb->num_rows;           
            ?>
            <table id="closeDateTable">
                <thead>
                <tr>
                    <td>Sr No.</td>
                    <td>Date</td>
                    <td>Reason for closure</td>            
                    <td>Cancel</td>
                </tr>
                </thead>
                <tbody>  
                <?php 		
                $i = 1;
                foreach($closeDateFormInfo as $row) { ?>
                <tr>
                    <td><?=$i?></td>
                    <td class="choiceDateCloseForm" data-startdate="<?php if(!empty($row->startDate)) { echo date("m-d-Y",$row->startDate); } ?>" data-enddate="<?php if(!empty($row->endDate)) { echo date("m-d-Y",$row->endDate); } ?>">
                    
                    <?php 
                        if(!empty($row->startDate)) { 
                            echo date("F d,Y",$row->startDate); 
                        } 
                        if(!empty($row->endDate)) {	
                            echo ' - '.date("F d,Y",$row->endDate);
                        }
                    ?> 
                    
                    </td>
                    
                    <td><?=$row->reason;?></td>           
                    <td>
                        <a href="#" class="deleteCloseDateForm" data-id="<?=$row->ID;?>">Cancel</a>
                    </td>	
                </tr>
                <?php 
                $i++;
                } ?>
                </tbody>
            </table>  
           
        </div>
    
        <div class="submit-group">
        	<button type="submit" name="submitOrder" id="submitOrder">Submit</button>
            <a href="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ); ?>/recuring_order/">Cancel</a>
        </div>
    </form>  
   
</div>


<script type="text/javascript" language="javascript">
   	jQuery(window).on("beforeunload", function() {
		return "Are you sure? You didn't finish the form!";
	});
	
	jQuery(document).ready(function($){
		$("a").click(function(e){
			$(window).off("beforeunload");
		});
		$(".addNewChild,.editChild").click(function(e){
			$(window).off("beforeunload");
			e.preventDefault();
			var mondayValue = $('form#new-order-create input[name="mondayPackage"]').val();
			var tuesdayValue = $('form#new-order-create input[name="tuesdayPackage"]').val();
			var wednesdayValue = $('form#new-order-create input[name="wednesdayPackage"]').val();
			
			var thursdayValue = $('form#new-order-create input[name="thursdayPackage"]').val();
			var fridayValue = $('form#new-order-create input[name="fridayPackage"]').val();
			var appendedLink = "&mondayVal="+mondayValue+"&tuesdayVal="+tuesdayValue+"&wednesdayVal="+wednesdayValue+"&thursdayVal="+thursdayValue+"&fridayVal="+fridayValue;
			var link1 = $(this).attr("href");
			window.location.href=link1+appendedLink;
			
		});
	});
	
</script>