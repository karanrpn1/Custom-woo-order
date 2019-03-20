<?php if(!is_user_logged_in()) { echo '<script>window.location.href = "'.home_url().'";</script>'; } ?>

<?php
	$current_user = wp_get_current_user();
?>
<div class="close-date-form">
	
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

	<p>Please add any dates that your centre will be closed and not require deliveries from Hearty Health</p>
    
    <a href="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ); ?>" class="return-account">Return to My Account</a>
    
    <form method="post" id="close-date-form" name="save-close-date-form" action="<?php echo get_admin_url(); ?>admin-post.php">
    	
         <input type="hidden" name="redirect-url" value="<?php echo '//'.$_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]; ?>" />
         
    	<input type="hidden" name="action" value="save-close-date-form" />
     
        <div class="opt-group">
        	<div id="jrange" class="dates">
            	<label>Select Date</label>
            	<input name="date" type="text" placeholder="Select Date" autocomplete="off" required/>
            	<div></div>
           	</div>
        </div>
        
        <div class="opt-group">
        	<label>Reason for closure</label>
        	<input type="text" name="reason" placeholder="Reason for closure" id="reason"required>
       	</div>
        
        <div class="submit-group">
        	<button type="submit" name="submitCloseDateForm">Add</button>
        </div>
        
    </form>   
    <?php
	global $wpdb;
    $tablename = $wpdb->prefix.'close_date_info';	
	$closeDateFormInfo = $wpdb->get_results( "SELECT * FROM $tablename where user_id='".$current_user->ID."'  and startDate>'".strtotime('tomorrow')."' ");
	$rowcount = $wpdb->num_rows;
	if($rowcount>0) { 
	?>
    <table>
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
    
    <?php } ?>
    
    
</div>
