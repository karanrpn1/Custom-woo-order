<?php if(isset($_SESSION['mealProduct'])) : ?>
<?php
if(isset($_GET['redUrl'])) {
	$redirectUrl = $_GET['redUrl'];
	if (strpos($redirectUrl, '?editId') !== false) {
		if(isset($_GET['mondayVal'])) 
			$redirectUrl.="&mondayVal=".$_GET['mondayVal'];
	}
	else {
		if(isset($_GET['mondayVal'])) 
			$redirectUrl.="?mondayVal=".$_GET['mondayVal'];
	}	
	if(isset($_GET['tuesdayVal'])) 
		$redirectUrl.="&tuesdayVal=".$_GET['tuesdayVal'];
	if(isset($_GET['wednesdayVal'])) 
		$redirectUrl.="&wednesdayVal=".$_GET['wednesdayVal'];
	if(isset($_GET['thursdayVal'])) 
		$redirectUrl.="&thursdayVal=".$_GET['thursdayVal'];
	if(isset($_GET['fridayVal'])) 
		$redirectUrl.="&fridayVal=".$_GET['fridayVal'];
}
else {
	$redirectUrl = get_site_url().'/custom-order-form/';
}
?>
<style>
	table.shop_table.shop_table_responsive.cart.woocommerce-cart-form__contents,
	.cart_totals.calculated_shipping table.shop_table,
	.cart_totals.calculated_shipping h2 {
		display:none;
	}
</style>

<div class="rightBtnChild"><a <?php if(isset($_GET['redUrl'])){ echo "href=$redirectUrl";} else { echo "onclick='history.go( -1 );return true;'"; }?>>Go back</a></div>

<h2>Meal Summary</h2>

<?php
if($_SESSION['mealProduct']['type']=="fullmenu") {
	$menuName = "Hearty Health Full Menu";
}
elseif($_SESSION['mealProduct']['type']=="lunchonly"){
	$menuName = "Lunch Only";
}
elseif($_SESSION['mealProduct']['type']=="toddler"){
	$menuName = "Toddler";
}
elseif($_SESSION['mealProduct']['type']=="oosh"){
	$menuName = "OOSH (out of school hours)";
}
else {
	$menuName = "";
}

?>

<h3>Total Meal</h3>
<table>
	<thead>
    	<tr>
        	<td></td>
            <td>Monday</td>
            <td>Tuesday</td>
            <td>Wednesday</td>
            <td>Thursday</td>
            <td>Friday</td>
        </tr>
    </thead>
    <tbody>
    	<tr>
        	<td><?=$menuName?></td>
            <td><?=$_SESSION['mealProduct']['mondayTotal']?></td>
            <td><?=$_SESSION['mealProduct']['tuesdayTotal']?></td>
            <td><?=$_SESSION['mealProduct']['wednesdayTotal']?></td>
            <td><?=$_SESSION['mealProduct']['thursdayTotal']?></td>
            <td><?=$_SESSION['mealProduct']['fridayTotal']?></td>
        </tr>
    </tbody>
</table>


<h3>Regular Meal</h3>
<table>
	<thead>
    	<tr>
        	<td></td>
            <td>Monday</td>
            <td>Tuesday</td>
            <td>Wednesday</td>
            <td>Thursday</td>
            <td>Friday</td>
        </tr>
    </thead>
    <tbody>
    	<tr>
        	<td><?=$menuName?></td>
            <td><?=$_SESSION['mealProduct']['mondayRegular']?></td>
            <td><?=$_SESSION['mealProduct']['tuesdayRegular']?></td>
            <td><?=$_SESSION['mealProduct']['wednesdayRegular']?></td>
            <td><?=$_SESSION['mealProduct']['thursdayRegular']?></td>
            <td><?=$_SESSION['mealProduct']['fridayRegular']?></td>
        </tr>
    </tbody>
</table>


<h3>Allergen & Toddler Meal</h3>
<table>
	<thead>
    	<tr>
        	<td>Name</td>
            <td>Allergen</td>
            <td>Monday</td>
            <td>Tuesday</td>
            <td>Wednesday</td>
            <td>Thursday</td>
            <td>Friday</td>
        </tr>
    </thead>
    <tbody>
	<?php
    global $wpdb;
    $childTabelName = $wpdb->prefix.'child_info';
    $childData = $wpdb->get_results( "SELECT * FROM $childTabelName where ID IN (".$_SESSION['childList'].") " );
	
	$mondayTotal = 0;
	$tuesdayTotal = 0;
	$wednesdayTotal = 0;
	$thursdayTotal = 0;
	$fridayTotal = 0;
	
    foreach ($childData as $row){
        $childIds[] = $row->ID;
        $attenData = json_decode($row->attend,true);
		$allergyGroup ="";
		$lbl="";
		if($row->toddler_toggle==1) {
			$lbl=" <strong>Toddler</strong>";
		}
		if(!empty($row->dairy) && $row->dairy<>"null") {
			$allergyGroup.= implode(",",json_decode($row->dairy) ).",";
		}
		
		if(!empty($row->vegetarian) && $row->vegetarian<>"null") {
			$allergyGroup.= implode(",",json_decode($row->vegetarian) ).",";
		}
		if(!empty($row->grains) && $row->grains<>"null") {
			$allergyGroup.= implode(",",json_decode($row->grains) ).",";
		}
		if(!empty($row->meat) && $row->meat<>"null") {
			$allergyGroup.= implode(",",json_decode($row->meat) ).",";
		}
		if(!empty($row->seafood) && $row->seafood<>"null") {
			$allergyGroup.= implode(",",json_decode($row->seafood) ).",";
		}
		if(!empty($row->soyproduct) && $row->soyproduct<>"null") {
			$allergyGroup.= implode(",",json_decode($row->soyproduct) ).",";
		}
		if(!empty($row->legumes) && $row->legumes<>"null") {
			$allergyGroup.= implode(",",json_decode($row->legumes) ).",";
		}
		if(!empty($row->fruits) && $row->fruits<>"null") {
			$allergyGroup.= implode(",",json_decode($row->fruits) ).",";
		}
		if(!empty($row->vegetables) && $row->vegetables<>"null") {
			$allergyGroup.= implode(",",json_decode($row->vegetables) ).",";
		}					
							
		if(!empty($row->others) && $row->others<>"null") {
			$allergyGroup.= implode(",",json_decode($row->others) ).",";
		}
    ?>
    	<tr>
        	<td><?=$row->child_name.$lbl;?></td>
            <td><?=rtrim($allergyGroup,",")?></td>
            <td><?php if(in_array('Monday',$attenData)) { echo "1"; $mondayTotal++; } ?></td>
            <td><?php if(in_array('Tuesday',$attenData)) { echo "1"; $tuesdayTotal++;  }  ?></td>
            <td><?php if(in_array('Wednesday',$attenData)) { echo "1"; $wednesdayTotal++;  } ?></td>
            <td><?php if(in_array('Thursday',$attenData)) { echo "1"; $thursdayTotal++;  } ?></td>
            <td><?php if(in_array('Friday',$attenData)) { echo "1"; $fridayTotal++;  } ?></td>
        </tr>
        <?php } ?>
    </tbody>
    
    <tfoot>
    	<tr>
        	<td colspan="2">Allergy Total</td>            
            <td><?=$mondayTotal?></td>
            <td><?=$tuesdayTotal?></td>
            <td><?=$wednesdayTotal?></td>
            <td><?=$thursdayTotal?></td>
            <td><?=$fridayTotal?></td>
        </tr>
    </tfoot>
</table>

<?php
	global $wpdb;
	$current_user = wp_get_current_user();
    $tablename = $wpdb->prefix.'close_date_info';	
	$closeDateFormInfo = $wpdb->get_results( "SELECT * FROM $tablename where user_id='".$current_user->ID."'  and startDate>'".strtotime('tomorrow')."' ");
	$rowcount = $wpdb->num_rows;
	if($rowcount>0) { 
	?>
<h3>Close Dates</h3>
<table>
    <thead>
        <tr>
            <td>Sr No.</td>
            <td>Date</td>
            <td>Reason for closure</td> 
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
    </tr>
    <?php 
    $i++;
    } ?>
    </tbody>
</table>
<?php } ?>

<?php endif; ?>