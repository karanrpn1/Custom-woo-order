<?php 
/*
  Plugin Name: Custom Woo Order
  Plugin URI: http://helpfulinsight.in
  Description: Custom order management for child care	
  Version: 1.0
  Author: Karan Rupani	
  Author URI: http://helpfulinsight.in
*/         
  
defined( 'ABSPATH' ) or die( 'Plugin file cannot be accessed directly.' );

if (!defined('CUS_WOO_ORDER_THEME_DIR'))
    define('CUS_WOO_THEME_DIR', ABSPATH . 'wp-content/themes/' . get_template());

if (!defined('CUS_WOO_ORDER_PLUGIN_NAME'))
    define('CUS_WOO_ORDER_PLUGIN_NAME', trim(dirname(plugin_basename(__FILE__)), '/'));
 
if (!defined('CUS_WOO_ORDER_PLUGIN_DIR'))
    define('CUS_WOO_ORDER_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . CUS_WOO_ORDER_PLUGIN_NAME);	
	
if (!defined('CUS_WOO_ORDER_PLUGIN_URL'))
    define('CUS_WOO_ORDER_PLUGIN_URL', WP_PLUGIN_URL . '/' . CUS_WOO_ORDER_PLUGIN_NAME);	

if ( ! class_exists( 'cuswooorder' ) ) {
	class cuswooorder {
		public function __construct()	{
			// Add styles			
			add_action( 'wp_enqueue_scripts', array($this, 'of_enqueue_styles') );
			add_action( 'admin_enqueue_scripts', array($this, 'of_enqueue_admin_ss') );
		}
		
		public function of_enqueue_styles()	{
			wp_enqueue_style( 'cus_woo_order_css', plugins_url('/css/style.css', __FILE__) );			 
			wp_enqueue_style( 'cus_woo_order_fontawesome_css','//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css', array(), '4.7.0' );			
			wp_enqueue_style( 'cus_woo_order_jquery_datepicker_css',plugins_url('/css/jquery-ui.css', __FILE__) );
			
			wp_enqueue_style( 'cus_woo_order_jquery_datepicker_theme_css',plugins_url('/css/jquery-ui.theme.min.css', __FILE__) );
						  
			wp_enqueue_script( 'cus_woo_order_jquery_datepicker_script',plugins_url('/js/jquery-ui.min.js', __FILE__) ,array( 'jquery' ), '0.5', false);
			
			//wp_enqueue_script( 'cus_woo_order_jquery_datepicker_script',plugins_url('/js/jquery-ui.js', __FILE__) ,array( 'jquery' ), '0.5', false);
						 
			wp_enqueue_script( 'cus_woo_order_script', plugins_url('/js/main.js', __FILE__) ,array( 'jquery' ), '0.5', false);		
			
				
		}   
		
		public function of_enqueue_admin_ss($hook) {
			wp_enqueue_script( 'cus_woo_order_admin_script', plugins_url('/js/admin-main.js', __FILE__) ,array( 'jquery' ), '0.5', false);		
			wp_enqueue_style( 'cus_woo_order_admin_style',  plugins_url('/css/admin-style.css', __FILE__));			
		}		
	}     
}

if ( class_exists( 'cuswooorder' ) ) {
	global $cuswooorder;
	$cuswooorder	= new cuswooorder();
}   
  
/*-------------------------------------------------------
				LOAD TCPDF CLASS
----------------------------------------------------------*/
class TCPDF_Loader {
	/**
	 * Initialise the TCPDF Library plugin
	 */
	public static function init() {
		add_action( 'activated_plugin', array( __CLASS__, 'load_tcpdf_first' ) );


		if ( ! class_exists( 'TCPDF' ) ) {
			define( 'TCPDF_PLUGIN_ACTIVE', true );
			define( 'TCPDF_VERSION', '6.2.11' );
			require( dirname( __FILE__ ) . '/tcpdf/tcpdf.php' );
		}
	}

	/**
	 * When plugins are activated, make sure that TCPDF is first.
	 *
	 * Note: This is just a standard hook. Other plugins can still jump to first if hooking `activated_plugin`
	 */
	public static function load_tcpdf_first() {
		$path            = __FILE__;
		$path            = str_replace( trailingslashit( WP_PLUGIN_DIR ), '', $path );
		$path            = str_replace( WP_CONTENT_DIR . '/plugins/', '', $path );
		$active_plugins  = get_option( 'active_plugins' );
		$this_plugin_key = array_search( $path, $active_plugins );
		if ( $this_plugin_key ) { // if it's 0 it's the first plugin already, no need to continue
			array_splice( $active_plugins, $this_plugin_key, 1 );
			array_unshift( $active_plugins, $path );
			update_option( 'active_plugins', $active_plugins );
		}
	}

}
// Keep it tidy in a class
TCPDF_Loader::init();

/*----------------------------------------------------
		CREATE TABLES ON PLUGIN ACTIVATION
-------------------------------------------------------*/
function custom_woo_create_tables() {
   	global $wpdb;
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
  	$orderTable = $wpdb->prefix.'order_info';
	$optInTable = $wpdb->prefix.'opt_in_alternative';
	$childInfoTable = $wpdb->prefix.'child_info';
	$closeDateTable = $wpdb->prefix.'close_date_info';
	$schoolRedinessTable = $wpdb->prefix.'school_readiness_lunch';

	if($wpdb->get_var("show tables like '$orderTable'") != $orderTable) 
	{
		$sql = "CREATE TABLE " . $orderTable . " (
		 `ID` int(11) NOT NULL AUTO_INCREMENT,
		 `order_id` int(11) NOT NULL,
		 `user_id` int(11) NOT NULL,
		 `allergy_child_ids` varchar(255) NOT NULL,
		 `item_sku` text NOT NULL,
		 `item_quantity` text NOT NULL,
		 `createTime` varchar(255) NOT NULL,
		 `updateTime` varchar(255) NOT NULL,
		 `status` int(11) NOT NULL DEFAULT 0,
		 `update_status` int(11) NOT NULL DEFAULT 1,
		 PRIMARY KEY (`ID`),
		 UNIQUE KEY `order_id` (`order_id`)
		);";		
		dbDelta($sql);
	} 
	
	if($wpdb->get_var("show tables like '$optInTable'") != $optInTable) 
	{
		$sql = "CREATE TABLE " . $optInTable . " (
		 `ID` int(11) NOT NULL AUTO_INCREMENT,
		 `order_info_id` int(11) NOT NULL,
		 `user_id` int(11) NOT NULL,
		 `date` varchar(255) NOT NULL,
		 `choice` int(11) NOT NULL,
		 `total_package` int(11) NOT NULL,
		 PRIMARY KEY (`ID`)
		);";		
		dbDelta($sql);
	} 
	if($wpdb->get_var("show tables like '$childInfoTable'") != $childInfoTable) 
	{
		$sql = "CREATE TABLE " . $childInfoTable . " (
		 `ID` int(11) NOT NULL AUTO_INCREMENT,
		 `user_id` int(11) NOT NULL,
		 `attend` text NOT NULL,
		 `child_name` varchar(255) DEFAULT NULL,
		 `id_number` varchar(255) NOT NULL,
		 `remark` varchar(255) DEFAULT NULL,
		 `dairy` text DEFAULT NULL,
		 `egg` text DEFAULT NULL,
		 `vegetarian` text DEFAULT NULL,
		 `grains` text DEFAULT NULL,
		 `meat` text DEFAULT NULL,
		 `seafood` text DEFAULT NULL,
		 `soyproduct` text DEFAULT NULL,
		 `legumes` text DEFAULT NULL,
		 `fruits` text DEFAULT NULL,
		 `vegetables` text DEFAULT NULL,
		 `nuts` text DEFAULT NULL,
		 `others` text DEFAULT NULL,
		 `toddler_toggle` int(11) NOT NULL DEFAULT 0,
		 `roomNumber` varchar(255) NOT NULL,
		  PRIMARY KEY (`ID`)
		);";		
		dbDelta($sql);
	} 
	if($wpdb->get_var("show tables like '$closeDateTable'") != $closeDateTable) 
	{
		$sql = "CREATE TABLE " . $closeDateTable . " (
		 `ID` int(11) NOT NULL AUTO_INCREMENT,
		 `user_id` int(11) NOT NULL,
		 `startDate` varchar(255) NOT NULL,
		 `endDate` varchar(255) NOT NULL,
		 `reason` text NOT NULL,
		 PRIMARY KEY (`ID`)
		);";		 
		dbDelta($sql);
	} 
	
	if($wpdb->get_var("show tables like '$closeDateTable'") != $schoolRedinessTable) {
		$sql = "CREATE TABLE " . $schoolRedinessTable . " (
		 `ID` int(11) NOT NULL AUTO_INCREMENT,
		 `order_info_id` int(11) NOT NULL,
		 `user_id` int(11) NOT NULL,
		 `date` varchar(255) NOT NULL,
		 `total_package` int(11) NOT NULL,
		 `vegemite` int(11) NOT NULL,
		 `cheese` int(11) NOT NULL,
		 `cheese_vegemite` int(11) NOT NULL,
		 `cheese_lettuce` int(11) NOT NULL,
		 `bottle_large` int(11) NOT NULL,
		 `bottle_small` int(11) NOT NULL,
		 `apples` int(11) NOT NULL,
		 `oranges` int(11) NOT NULL,
		 `pears` int(11) NOT NULL,
		 `bananas` int(11) NOT NULL,
		 PRIMARY KEY (`ID`)
		);";		 
		dbDelta($sql);
	} 
}

// run the install scripts upon plugin activation
register_activation_hook(__FILE__,'custom_woo_create_tables');

/*--------------------------------------------------
				ADD ADMIN MENU PAGE			
-----------------------------------------------------*/
require_once(CUS_WOO_ORDER_PLUGIN_DIR."/admin/admin-data.php");

  
/*-------------------------------------------------
 				START SESSION
----------------------------------------------------*/
add_action('init','start_session', 1);
function start_session() {
    if(!session_id()) {
        session_start();
    } 
}  
  
/*-------------------------------------------------
 				SHORTOCDE FOR ORDER FORM 
----------------------------------------------------*/
function cus_woo_order_form_shortcode()	{	
	ob_start();
	require CUS_WOO_ORDER_PLUGIN_DIR.'/order-form.php';	
	return ob_get_clean();		
} 
add_shortcode('cus_woo_order_form', 'cus_woo_order_form_shortcode');

 
/*-------------------------------------------------
 				SHORTOCDE FOR ALLERGY FORM 
----------------------------------------------------*/
function cus_woo_child_info_form_shortcode()	{
	return require CUS_WOO_ORDER_PLUGIN_DIR.'/child-info-form.php';		
} 
add_shortcode('cus_woo_child_info_form', 'cus_woo_child_info_form_shortcode'); 

   
/*------------------------------------------------- 
 				HANDLE SAVE CHILD INFO FORM 
----------------------------------------------------*/ 
add_action('admin_post_save-child-form', '_handle_child_form_action'); 
add_action('admin_post_nopriv_save-child-form', '_handle_child_form_action'); 
function _handle_child_form_action(){ 
	$redirectUrl = $_POST['redirect-url'];
	unset($_POST['action']);
	unset($_POST['saveChildInfo']);	
	unset($_POST['redirect-url']);		
	$current_user = wp_get_current_user();
	$postData = $_POST;  
	
	global $wpdb; 
	$tablename = $wpdb->prefix."child_info"; 
	$toggle=0;
	if(isset($_POST['toddler_toggle'])) {
		$toggle=1;
	} else {
		$toggle=0;
	}
	$success =  $wpdb->insert( $tablename, array(
        'user_id' => $current_user->ID, 
		'attend' => json_encode($_POST['attend']),
        'child_name' => $_POST['child_name'],
		'id_number' => $_POST['id_number'],
        'remark' => $_POST['remark'],
		'roomNumber' => $_POST['roomNumber'], 
        'dairy' => json_encode($_POST['dairy']),
        'vegetarian' => json_encode($_POST['vegetarian']), 
        'grains' => json_encode($_POST['grains']),
        'meat' => json_encode($_POST['meat']), 
        'seafood' => json_encode($_POST['seafood']), 
        'soyproduct' => json_encode($_POST['soyproduct']),
        'legumes' => json_encode($_POST['legumes']), 
        'fruits' => json_encode($_POST['fruits']), 
        'vegetables' => json_encode($_POST['vegetables']),
        'others' => json_encode( $_POST['others']), 
		'toddler_toggle' => $toggle
        ),
	 /* added additional %s to ensure others data recorded - comfusion */
     array( '%d', '%s','%s','%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d' ) 
    );
	$_SESSION['success'] = "Child info has been succesfully added." ;
	//$cutomRedirect=get_site_url()."/my-account/recuring_order/";
	wp_redirect( $redirectUrl );
	exit(); 
} 
 

/*-------------------------------------------------
 				HANDLE UPDATE CHILD INFO FORM 
----------------------------------------------------*/
add_action('admin_post_update-child-form', '_handle_update_child_form_action'); 
add_action('admin_post_nopriv_update-child-form', '_handle_update_child_form_action'); 
function _handle_update_child_form_action(){
	$redirectUrl = $_POST['redirect-url'];
	unset($_POST['action']);
	unset($_POST['saveChildInfo']);	
	unset($_POST['redirect-url']);		
	$current_user = wp_get_current_user();
	$postData = $_POST;
	
	$toggle=0;
	if(isset($_POST['toddler_toggle'])) {
		$toggle=1;
	} else {
		$toggle=0;
	}
	
	global $wpdb;
	$tablename = $wpdb->prefix.'child_info';
	$wpdb->update( $tablename, 
		array(        
		'attend' => json_encode($_POST['attend']),
        'child_name' => $_POST['child_name'],
        'remark' => $_POST['remark'], 
		'id_number' => $_POST['id_number'],
		'roomNumber' => $_POST['roomNumber'], 
        'dairy' => json_encode($_POST['dairy']),
        'vegetarian' => json_encode($_POST['vegetarian']), 
        'grains' => json_encode($_POST['grains']),
        'meat' => json_encode($_POST['meat']), 
        'seafood' => json_encode($_POST['seafood']), 
        'soyproduct' => json_encode($_POST['soyproduct']),
        'legumes' => json_encode($_POST['legumes']), 
        'fruits' => json_encode($_POST['fruits']), 
        'vegetables' => json_encode($_POST['vegetables']),
        'others' => json_encode( $_POST['others']),
		'toddler_toggle' => $toggle 
        ),  
		array( 'ID' => $_POST['postId'] ), 
		array( '%s', '%s', '%s','%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' ), 
		array( '%d' ) 
	);
	$_SESSION['success'] = "Child info has been succesfully updated." ;
	//$redirectUrl=get_site_url()."/my-account/recuring_order/";
	wp_redirect( $redirectUrl );
	exit(); 
} 


/*-------------------------------------------------
 				ENQUE AJAX SCRIPT 
----------------------------------------------------*/
wp_register_script('ajax-order-script', plugins_url('js/ajax-order-script.js', __FILE__), array('jquery'), '0.5', false ); 
wp_enqueue_script('ajax-order-script');

wp_localize_script( 'ajax-order-script', 'ajax_order_object', array( 
	'ajaxurl' => admin_url( 'admin-ajax.php' ),
	'redirecturl' => get_site_url().'/cart',
	'myaccountUrl' => get_site_url().'/my-account' ));
 
/*-------------------------------------------------
			HANDLE ORDER FORM AJAX
----------------------------------------------------*/
add_action( 'wp_ajax_nopriv_saveAjaxOrder', '_handle_save_order_form_action' );
add_action( 'wp_ajax_saveAjaxOrder', '_handle_save_order_form_action' );

function _handle_save_order_form_action(){
	ob_start();
	global $woocommerce;
	
	global $wpdb;
	$childTabelName = $wpdb->prefix.'child_info';
	$childData = $wpdb->get_results( "SELECT * FROM $childTabelName where ID IN (".$_POST['childList'].") " );
	
	$mondayAllergyCount = 0;
	$tuesdayAllergyCount = 0;
	$wednesdayAllergyCount = 0;
	$thursdayAllergyCount = 0;
	$fridayAllergyCount = 0;
	foreach($childData as $childRow) {
		$attend = json_decode($childRow->attend,true);
		if(in_array("Monday",$attend)) {
			$mondayAllergyCount++;
		}
		if(in_array("Tuesday",$attend)) {
			$tuesdayAllergyCount++;
		}
		if(in_array("Wednesday",$attend)) {
			$wednesdayAllergyCount++;
		}
		if(in_array("Thursday",$attend)) {
			$thursdayAllergyCount++;
		}
		if(in_array("Friday",$attend)) {
			$fridayAllergyCount++;
		}
	}	
	
	$type = $_POST['type']; 
	$mondayProductId = wc_get_product_id_by_sku($type.'-monday');
	$tuesdayProductId = wc_get_product_id_by_sku($type.'-tuesday');
	$wednesdayProductId = wc_get_product_id_by_sku($type.'-wednesday');
	$thursdayProductId = wc_get_product_id_by_sku($type.'-thursday');
	$fridayProductId = wc_get_product_id_by_sku($type.'-friday');
	
	
	$mondayQuantity = $_POST['monday'];
	$tuesdayQuantity = $_POST['tuesday']; 
	$wednesdayQuantity = $_POST['wednesday']; 
	$thursdayQuantity = $_POST['thursday']; 
	$fridayQuantity = $_POST['friday'];  
	
	$mealProduct = array();
	$mealProduct['type'] = $type;	
	
	WC()->cart->empty_cart();

	if(!empty($mondayQuantity)) {
		$passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $mondayProductId, $mondayQuantity );
	$product_status    = get_post_status( $mondayProductId );
		if ( $passed_validation && WC()->cart->add_to_cart( $mondayProductId, $mondayQuantity ) && 'publish' === $product_status ) {
	
			do_action( 'woocommerce_ajax_added_to_cart', $mondayProductId );
	
			wc_add_to_cart_message( $mondayProductId );
	
		}
		$mealProduct['mondayRegular'] = $mondayQuantity-$mondayAllergyCount;
		$mealProduct['mondayTotal'] = $mondayQuantity;
	}
	if(!empty($tuesdayQuantity)) { 
		$passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $tuesdayProductId, $tuesdayQuantity );
		$product_status    = get_post_status( $tuesdayProductId );
		if ( $passed_validation && WC()->cart->add_to_cart( $tuesdayProductId, $tuesdayQuantity ) && 'publish' === $product_status ) {
	
			do_action( 'woocommerce_ajax_added_to_cart', $tuesdayProductId );
	
			wc_add_to_cart_message( $tuesdayProductId );
	
		} 
		$mealProduct['tuesdayRegular'] = $tuesdayQuantity-$tuesdayAllergyCount;
		$mealProduct['tuesdayTotal'] = $tuesdayQuantity;
	}
	if(!empty($wednesdayQuantity)) {
		$passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $wednesdayProductId, $wednesdayQuantity );
		$product_status    = get_post_status( $wednesdayProductId );
		if ( $passed_validation && WC()->cart->add_to_cart( $wednesdayProductId, $wednesdayQuantity ) && 'publish' === $product_status ) {
	
			do_action( 'woocommerce_ajax_added_to_cart', $wednesdayProductId );
	
			wc_add_to_cart_message( $wednesdayProductId );
	
		} 
		$mealProduct['wednesdayRegular'] = $wednesdayQuantity-$wednesdayAllergyCount;
		$mealProduct['wednesdayTotal'] = $wednesdayQuantity;
	}
	
	if(!empty($thursdayQuantity)) {
		$passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $thursdayProductId, $thursdayQuantity );
	$product_status    = get_post_status( $thursdayProductId );
		if ( $passed_validation && WC()->cart->add_to_cart( $thursdayProductId, $thursdayQuantity ) && 'publish' === $product_status ) {
	
			do_action( 'woocommerce_ajax_added_to_cart', $thursdayProductId );
	
			wc_add_to_cart_message( $thursdayProductId );
	
		} 
		$mealProduct['thursdayRegular'] = $thursdayQuantity-$thursdayAllergyCount;
		$mealProduct['thursdayTotal'] = $thursdayQuantity;
	}
	
	if(!empty($fridayQuantity)) {
		$passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $fridayProductId, $fridayQuantity );
	$product_status    = get_post_status( $fridayProductId );
		if ( $passed_validation && WC()->cart->add_to_cart( $fridayProductId, $fridayQuantity ) && 'publish' === $product_status ) {
	
			do_action( 'woocommerce_ajax_added_to_cart', $fridayProductId );
	
			wc_add_to_cart_message( $fridayProductId );
	
		} 
		$mealProduct['fridayRegular'] = $fridayQuantity-$fridayAllergyCount;
		$mealProduct['fridayTotal'] = $fridayQuantity;
	}	
	$_SESSION['mealProduct'] = $mealProduct;
	$_SESSION['childList'] = $_POST['childList'];
	echo json_encode(array('status' => 'success','redirectUrl'=> 'cartPage'));
	die();
} 

/*-------------------------------------------------
 				HANDLE ORDER FORM AJAX
----------------------------------------------------*/
add_action( 'wp_ajax_nopriv_updateAjaxOrder', '_handle_update_order_form_action' );
add_action( 'wp_ajax_updateAjaxOrder', '_handle_update_order_form_action' );

function _handle_update_order_form_action(){
	ob_start();	
	$type = $_POST['type']; 
	$itemSku = array();
	$itemQty = array();
	if(!empty($_POST['monday']) && $_POST['monday']>0) {
		$itemQty[] = $_POST['monday'];
		$itemSku[] = $type.'-monday';
	}
	if(!empty($_POST['tuesday']) && $_POST['tuesday']>0) {
		$itemQty[] = $_POST['tuesday'];
		$itemSku[] = $type.'-tuesday';
	}
	if(!empty($_POST['wednesday']) && $_POST['wednesday']>0) {
		$itemQty[] = $_POST['wednesday'];
		$itemSku[] = $type.'-wednesday';
	}
	if(!empty($_POST['thursday']) && $_POST['thursday']>0) {
		$itemQty[] = $_POST['thursday'];
		$itemSku[] = $type.'-thursday';
	}
	if(!empty($_POST['friday']) && $_POST['friday']>0) {
		$itemQty[] = $_POST['friday'];
		$itemSku[] = $type.'-friday';
	}
	
	$postId = $_POST['postId'];  
	
	global $wpdb;
	$tablename = $wpdb->prefix.'order_info';
	$wpdb->update( $tablename, 
		array(        
		'allergy_child_ids' => $_POST['childList'],
        'item_sku' => json_encode($itemSku),
        'item_quantity' => json_encode($itemQty), 
		'status' => 1, 
        'updateTime' => time()         
        ), 
		array( 'ID' => $_POST['postId'] ), 
		array( '%s', '%s', '%s', '%s','%s' ), 
		array( '%d' ) 
	);
	$_SESSION['success'] = "Your order has been updated it will take 2 days to reflect changes for your order..";
	
	$postId = $_POST['postId'];
	$tablename = $wpdb->prefix.'order_info';
	$orderInfo = $wpdb->get_results( "SELECT * FROM $tablename where ID='".$postId."'" );
	$user = get_user_by( 'id', $orderInfo[0]->user_id );
	
	$kitchenEmail = get_option( 'kitchen_staff_mail' );		
	$salesEmail = get_option( 'sales_staff_mail' );			 	
	$subject = "#".$orderInfo->order_id." Order Update";	
	$headers[] = 'From: '.get_option( 'blogname' ).' <'.get_option( 'admin_email' ).'>';	
	if(get_option('order_update_email_to_admin') ) {
		$search = array("{{USERNAME}}", "{{ORDERID}}");
		$replace   = array( $user->billing_company , $orderInfo[0]->order_id );
		$mailBody = str_replace($search, $replace, get_option('order_update_email_to_admin'));
	}
	else {
		$mailBody = $user->billing_company." has update its order. Order Id #".$orderInfo->order_id;
	}				
	
	
	$order = wc_get_order( $orderInfo[0]->order_id );
	$order_data = $order->get_data();	
	if(!empty($order_data['shipping']['state'])) {
		$customerStateCode = $order_data['shipping']['state'];
	}
	else {
		$customerStateCode = $order_data['billing']['state'];
	}
	
	$manageStateTable = $wpdb->prefix.'manage_state';
	$kitchenDetail = $wpdb->get_results( "SELECT * FROM $manageStateTable where `state_code`='".$customerStateCode."'" );	
	$kitchenStaffEmail = $kitchenDetail[0]->kitchen_email;		
	
	$attachment = createUpdatePdf($postId);
	
	if(!empty($kitchenStaffEmail)) {
		wp_mail($kitchenStaffEmail, $subject,$mailBody, $headers,$attachment);
	}
	//wp_mail($kitchenEmail, $subject,$mailBody, $headers,$attachment);
	wp_mail($salesEmail, $subject,$mailBody, $headers,$attachment);	
	
	if(get_option('order_update_email_to_customer') ) {
		$search = array("{{USERNAME}}", "{{ORDERID}}");
		$replace   = array( $user->billing_company , $orderInfo[0]->order_id );
		$mailBody = str_replace($search, $replace, get_option('order_update_email_to_customer'));
	}
	else {
		$mailBody = $user->billing_company." has update its order. Order Id #".$orderInfo->order_id;
		$mailBody = 'Dear' .$user->billing_company.',';
		$mailBody.= 'Thank you for updating your Hearty Health Menu Summary Order.';
		$mailBody.= 'Please note all changes take 48 hours to apply';

	}		 
	wp_mail($user->user_email, $subject,$mailBody, $headers,$attachment);	
	
	unlink($attachment);
	echo json_encode(array('status' => 'success','redirectUrl'=> 'myaccount'));
	die();
	
}

/*----------------------------------------------------------
 	ADD POST INFO TO CUSTOM TABLE ON ORDER COMPLETE
--------------------------------------------------------------*/
//add_action( 'woocommerce_new_order', 'create_invoice_for_wc_order',  1, 1  ); 
add_action( 'woocommerce_thankyou', 'create_invoice_for_wc_order',  1, 1  );
function create_invoice_for_wc_order( $order_id ) { 
	 
	if(!empty($_SESSION['mealProduct'])) {
		$order = new WC_Order( $order_id );      
		$user_id = $order->user_id;   
		$orderType = $order->created_via;
		
		if(empty($orderType) || ($orderType!="update_order")) {	
			global $wpdb;
			$tablename = $wpdb->prefix.'order_info';
			
			$wpdb->insert( $tablename, array(
				'order_id' => $order_id, 
				'user_id' => $user_id,
				'allergy_child_ids' => $_SESSION['childList'],
				'createTime' => time(), 			
				'status' => '1'            
				),
				array( '%d', '%d', '%s', '%s','%d') 
			);
			$lastid = $wpdb->insert_id;
			
			$childTabelName = $wpdb->prefix.'child_info';
			$childData = $wpdb->get_results( "SELECT * FROM $childTabelName where ID IN (".$_SESSION['childList'].") " );
			update_post_meta( $order_id, 'child_count', $wpdb->num_rows ); 
			$i = 1;
			foreach ($childData as $row){		
				update_post_meta( $order_id, 'child_name_'.$i, $row->child_name );
				update_post_meta( $order_id, 'child_remark_'.$i, $row->remark ); 
				update_post_meta( $order_id, 'child_id_number_'.$i, $row->id_number );
				
				update_post_meta( $order_id, 'child_attend_'.$i, $row->attend ); 
				update_post_meta( $order_id, 'child_allergy_dairy_'.$i, $row->dairy ); 
				update_post_meta( $order_id, 'child_roomNumber_'.$i, $row->roomNumber ); 
				update_post_meta( $order_id, 'child_allergy_vegetarian_'.$i, $row->vegetarian ); 
				update_post_meta( $order_id, 'child_allergy_grains_'.$i, $row->grains ); 
				update_post_meta( $order_id, 'child_allergy_meat_'.$i, $row->meat ); 
				update_post_meta( $order_id, 'child_allergy_seafood_'.$i, $row->seafood ); 
				update_post_meta( $order_id, 'child_allergy_soyproduct_'.$i, $row->soyproduct ); 
				update_post_meta( $order_id, 'child_allergy_legumes_'.$i, $row->legumes ); 
				update_post_meta( $order_id, 'child_allergy_fruits_'.$i, $row->fruits ); 
				update_post_meta( $order_id, 'child_allergy_vegetables_'.$i, $row->vegetables );
				
				update_post_meta( $order_id, 'child_allergy_others_'.$i, $row->others );  
				update_post_meta( $order_id, 'child_toddler_toggle_'.$i, $row->toddler_toggle );  
				$i++;
			}
			
			$user_info = get_userdata($user_id);
			
			$attachment = createNewOrderPdf($lastid);
			$subject = "#".$order_id." new order confirmation";	
			//$headers[] = 'From: '.get_option( 'blogname' ).' <'.get_option( 'admin_email' ).'>';
			$headers = "MIME-Version: 1.0" . "\n";
			$headers .= "Content-type:text/html;charset=iso-8859-1" . "\n";
			$headers .= 'From: '.get_option( 'blogname' ).' <'.get_option( 'admin_email' ).'>'. "\n";
			
			if(get_option('new_order_email_customer') ) {
				$search = array("{{USERNAME}}", "{{ORDERID}}" , "{{TIME}}");
				$replace   = array( $user_info->billing_company , $order_id , date("l m-d-Y h:i:s A") );
				$mailBody = str_replace($search, $replace, get_option('new_order_email_customer'));
			}
			else {
				$mailBody = 'Thank you for your order...Please find the order summary attached.';
			}	
			
			wp_mail($user_info->user_email, $subject,$mailBody, $headers,$attachment);
			
			if(!empty($user_info->operational_email)) {
				wp_mail($user_info->operational_email, $subject,$mailBody, $headers,$attachment);				
			}
			
			$kitchenEmail = get_option( 'kitchen_staff_mail' );		
			$salesEmail = get_option( 'sales_staff_mail' );			 	
			
			
			$subject = "#".$order_id." New Order";	
			$headers1[] = 'From: '.get_option( 'blogname' ).' <'.get_option( 'admin_email' ).'>';	
			if(get_option('new_order_email_admin') ) {
				$search = array("{{USERNAME}}", "{{ORDERID}}");
				$replace   = array( $user_info->billing_company, $order_id );
				$mailBody = str_replace($search, $replace, get_option('new_order_email_admin'));
			}
			else {
				$mailBody = "New Order Received from ".$user_info->billing_company;
			}			
			
			$order_data = $order->get_data();	
			if(!empty($order_data['shipping']['state'])) {
				$customerStateCode = $order_data['shipping']['state'];
			}
			else {
				$customerStateCode = $order_data['billing']['state'];
			}
			
			$manageStateTable = $wpdb->prefix.'manage_state';
			$kitchenDetail = $wpdb->get_results( "SELECT * FROM $manageStateTable where `state_code`='".$customerStateCode."'" );	
			$kitchenStaffEmail = $kitchenDetail[0]->kitchen_email;	
			if(!empty($kitchenStaffEmail)) {
				wp_mail($kitchenStaffEmail, $subject,$mailBody, $headers1,$attachment);
			}	
	
			//wp_mail($kitchenEmail, $subject,$mailBody, $headers1,$attachment);
			wp_mail($salesEmail, $subject,$mailBody, $headers1,$attachment);	
			
			unlink($attachment);	
		
		}
		
		unset($_SESSION['mealProduct']);
		unset($_SESSION['childList']);
	}
}


/*----------------------------------------------------------
 			SHOW CHILD INFO IN ORDER BACKEND
--------------------------------------------------------------*/
add_action( 'add_meta_boxes', 'tcg_tracking_box' );
function tcg_tracking_box() {
    add_meta_box(
        'tcg-tracking-modal',
        'Child Allergen Info',
        'order_detail_meta_box_callback',
        'shop_order',
        'normal',
        'core'
    );
}

// Callback
function order_detail_meta_box_callback( $post ) {
    $childCount = get_post_meta( $post->ID, 'child_count', true );	
   	if(!empty($childCount)) {
		$text = '<table class="adminAlergyTable"><thead><tr>';
		$text.= '<td>Name and Allergies</td>';
		$text.= '<td>Monday</td>';
		$text.= '<td>Tuesday</td>';
		$text.= '<td>Wednesday</td>';
		$text.= '<td>Thursday</td>';
		$text.= '<td>Friday</td>';
		$text.= '<td>Total</td>';
		$text.= '<td>View</td>';  
		$text.= '</tr></thead><tbody>';
		$yes = "yes";
		$no = "no";
		for($i=1;$i<=$childCount;$i++) {
			$lbl="";
			if(!empty(get_post_meta( $post->ID, "child_toddler_toggle_".$i, true ))) {
				$lbl=" <strong>Toddler</strong>";
			}
			
			$attenData = json_decode(get_post_meta( $post->ID, 'child_attend_'.$i, true ));
			$text.='<tr>';
			$text.='<td>'.get_post_meta( $post->ID, "child_name_".$i, true ).$lbl.'</td>';
			if(in_array('Monday',$attenData)) { $text.= '<td>Yes</td>'; }
			else { $text.= '<td>No</td>'; }
			if(in_array('Tuesday',$attenData)) {
				$text.= '<td>Yes</td>';
			}
			else {
				$text.= '<td>No</td>';
			}
			if(in_array('Wednesday',$attenData)) {
				$text.= '<td>Yes</td>';
			}
			else {
				$text.= '<td>No</td>';
			}
			if(in_array('Thursday',$attenData)) {
				$text.= '<td>Yes</td>';
			}
			else {
				$text.= '<td>No</td>';
			}
			if(in_array('Friday',$attenData)) {
				$text.= '<td>Yes</td>';
			}
			else {  
				$text.= '<td>No</td>';
			}
			$text.='<td>'.count($attenData).'</td>';
			$text.='<td><a href="#" class="alergyViewMore" data-id="alergyView'.$i.'">View</a></td>';			
			$text.='</tr>'; 
			$text.='<tr id="alergyView'.$i.'" style="display:none">';
			$text.='<td colspan="8">';
			$text.='<p><strong>ID Number : </strong>'.get_post_meta( $post->ID, "child_id_number_".$i, true ).'</p>';
			$text.='<p><strong>Room Number : </strong>'.get_post_meta( $post->ID, "child_roomNumber_".$i, true ).'</p>';
			$text.='<p><strong>Dairy : </strong>'.implode(",",json_decode(get_post_meta( $post->ID, "child_allergy_dairy_".$i, true ))).'</p>';
			
			$text.='<p><strong>Vegetarian : </strong>'.implode(",",json_decode(get_post_meta( $post->ID, "child_allergy_vegetarian_".$i, true ))).'</p>';
			$text.='<p><strong>Grains : </strong>'.implode(",",json_decode(get_post_meta( $post->ID, "child_allergy_grains_".$i, true ))).'</p>';
			$text.='<p><strong>Meat : </strong>'.implode(",",json_decode(get_post_meta( $post->ID, "child_allergy_meat_".$i, true ))).'</p>';
			$text.='<p><strong>Seafood : </strong>'.implode(",",json_decode(get_post_meta( $post->ID, "child_allergy_seafood_".$i, true ))).'</p>';
			$text.='<p><strong>Soyproduct : </strong>'.implode(",",json_decode(get_post_meta( $post->ID, "child_allergy_soyproduct_".$i, true ))).'</p>';
			$text.='<p><strong>Legumes : </strong>'.implode(",",json_decode(get_post_meta( $post->ID, "child_allergy_legumes_".$i, true ))).'</p>';
			$text.='<p><strong>fruits : </strong>'.implode(",",json_decode(get_post_meta( $post->ID, "child_allergy_fruits_".$i, true ))).'</p>';
			$text.='<p><strong>Vegetables : </strong>'.implode(",",json_decode(get_post_meta( $post->ID, "child_allergy_vegetables_".$i, true ))).'</p>';
			
			$text.='<p><strong>Others : </strong>'.implode(",",json_decode(get_post_meta( $post->ID, "child_allergy_others_".$i, true ))).'</p>';
			$text.='</td></tr>';
			
		} 
		$text.='</tbody></table>';
	} 
    echo $text; 
}

/*----------------------------------------------------------
 			HANDLE CHILD DELETE AJAX
--------------------------------------------------------------*/
add_action( 'wp_ajax_nopriv_ajaxDeleteChild', '_handle_delete_child_action' );
add_action( 'wp_ajax_ajaxDeleteChild', '_handle_delete_child_action' );

function _handle_delete_child_action(){
	global $wpdb;
	$tablename = $wpdb->prefix.'child_info';
	$childId = $_POST['id'];
	$wpdb->delete( $tablename, array( 'ID' => $childId ) );
} 

/*----------------------------------------------------------
 			ADD PAGE TO MY ACCOUNT MENU 
--------------------------------------------------------------*/ 
add_filter( 'woocommerce_account_menu_items', 'custom_woo_my_account_menu_items', 10, 1 );
function custom_woo_my_account_menu_items( $items ) { 
    $items['recuring_order'] = __( 'Meal Summary', 'iconic' );
	$items['our_certifications'] = __( 'Our Certifications', 'iconic' );
	$items['menu_downloads'] = __( 'Menu Downloads', 'iconic' ); 
	$items['school_rediness'] = __( 'Excursion / School Readiness Lunches', 'iconic' ); 
	$items['payment_options'] = __( 'Payment Options', 'iconic' ); 
    return $items;
}

add_action( 'init', 'custom_woo_add_my_account_endpoint' );
function custom_woo_add_my_account_endpoint() { 
    add_rewrite_endpoint( 'recuring_order', EP_PAGES ); 
	add_rewrite_endpoint( 'our_certifications', EP_PAGES ); 
	add_rewrite_endpoint( 'menu_downloads', EP_PAGES ); 
	add_rewrite_endpoint( 'school_rediness', EP_PAGES ); 
	add_rewrite_endpoint( 'payment_options', EP_PAGES ); 
} 

add_action( 'woocommerce_account_recuring_order_endpoint', 'custom_woo_recuring_order_endpoint_content' );
function custom_woo_recuring_order_endpoint_content() {
     include 'templates/recuring-order.php'; 
} 

add_action( 'woocommerce_account_our_certifications_endpoint', 'custom_woo_our_certifications_endpoint_content' );
function custom_woo_our_certifications_endpoint_content() {
     include 'templates/our-certifications.php'; 
}  

add_action( 'woocommerce_account_menu_downloads_endpoint', 'custom_woo_menu_downloads_endpoint_content' );
function custom_woo_menu_downloads_endpoint_content() {
     include 'templates/menu-downloads.php'; 
} 

add_action( 'woocommerce_account_school_rediness_endpoint', 'custom_woo_school_rediness_endpoint_content' );
function custom_woo_school_rediness_endpoint_content() {
     include 'templates/school-rediness-form.php'; 
} 
add_action( 'woocommerce_account_payment_options_endpoint', 'custom_woo_payment_options_endpoint_content' );
function custom_woo_payment_options_endpoint_content() {
     include 'templates/payment-option-page.php'; 
} 
 
/*----------------------------------------------------------
					WEEKLY CRON SHORTCODE
-------------------------------------------------------------*/
function weekly_cron_shortcode()	{	
	ob_start();
	require CUS_WOO_ORDER_PLUGIN_DIR.'/weekly-cron.php';	
	return ob_get_clean();		
} 
add_shortcode('weekly_cron', 'weekly_cron_shortcode');

/*----------------------------------------------------------
	DAILY CRON SHORTCODE ( FOR CHANGES & ORDER CANCELATION )
-------------------------------------------------------------*/
function daily_cron_shortcode()	{	
	ob_start();
	require CUS_WOO_ORDER_PLUGIN_DIR.'/daily-cron.php';	
	return ob_get_clean();		
} 
add_shortcode('daily_cron', 'daily_cron_shortcode');


/*-------------------------------------------------
 			HANDLE ORDER UPDATE STATUS
----------------------------------------------------*/
add_action( 'wp_ajax_nopriv_updateOrderStatus', '_handle_update_order_status_action' );
add_action( 'wp_ajax_updateOrderStatus', '_handle_update_order_status_action' );

function _handle_update_order_status_action(){
	ob_start();	
	$postId = $_POST['id'];  
	
	global $wpdb;
	$tablename = $wpdb->prefix.'order_info';
	$wpdb->update( $tablename, 
		array(        
		'update_status' => $_POST['status'],
		'updateTime' => time()
        ), 
		array( 'ID' => $postId ), 
		array( '%d' ), 
		array( '%d' )  
	);	
	
	if($_POST['status']==0) {	
		
		$orderInfo = $wpdb->get_results( "SELECT * FROM $tablename where ID='".$postId."'" );
		$user = get_user_by( 'id', $orderInfo[0]->user_id );
		
		$kitchenEmail = get_option( 'kitchen_staff_mail' );		
		$salesEmail = get_option( 'sales_staff_mail' );			 	
		$subject = "#".$orderInfo->order_id." Order Stopped";
		$headers[] = "Content-type:text/html;charset=UTF-8";
		$headers[] = 'From: '.get_option( 'blogname' ).' <'.get_option( 'admin_email' ).'>';	
		if(get_option('order_off_email') ) {
			$search = array("{{USERNAME}}", "{{ORDERID}}");
			$replace   = array( $user->billing_company , $orderInfo[0]->order_id );
			$mailBody = str_replace($search, $replace, get_option('order_off_email'));
		}
		else {
			$mailBody = $user->billing_company." has stopped it order.<br> Order Id #".$orderInfo->order_id;
		}				
		
		$order = wc_get_order( $orderInfo[0]->order_id );
		$order_data = $order->get_data();	
		if(!empty($order_data['shipping']['state'])) {
			$customerStateCode = $order_data['shipping']['state'];
		}
		else {
			$customerStateCode = $order_data['billing']['state'];
		}
		
		$manageStateTable = $wpdb->prefix.'manage_state';
		$kitchenDetail = $wpdb->get_results( "SELECT * FROM $manageStateTable where `state_code`='".$customerStateCode."'" );	
		$kitchenStaffEmail = $kitchenDetail[0]->kitchen_email;	
		
		if(!empty($kitchenStaffEmail)) {
			wp_mail($kitchenStaffEmail, $subject,$mailBody, $headers);
		}
	
		//wp_mail($kitchenEmail, $subject,$mailBody, $headers);
		wp_mail($salesEmail, $subject,$mailBody, $headers);	
	}
	elseif($_POST['status']==1) {
		$orderInfo = $wpdb->get_results( "SELECT * FROM $tablename where ID='".$postId."'" );
		$user = get_user_by( 'id', $orderInfo[0]->user_id );
		
		$kitchenEmail = get_option( 'kitchen_staff_mail' );		
		$salesEmail = get_option( 'sales_staff_mail' );			 	
		$subject = "#".$orderInfo->order_id." Order Started Again";
		$headers[] = "Content-type:text/html;charset=UTF-8";
		$headers[] = 'From: '.get_option( 'blogname' ).' <'.get_option( 'admin_email' ).'>';	
		
		if(get_option('order_on_email') ) {
			$search = array("{{USERNAME}}", "{{ORDERID}}");
			$replace   = array( $user->billing_company , $orderInfo[0]->order_id );
			$mailBody = str_replace($search, $replace, get_option('order_on_email'));
		}
		else {
			$mailBody = $user->billing_company." has started again its order.<br> Order Id #".$orderInfo->order_id;
		}	
		
		$order = wc_get_order( $orderInfo[0]->order_id );
		$order_data = $order->get_data();	
		if(!empty($order_data['shipping']['state'])) {
			$customerStateCode = $order_data['shipping']['state'];
		}
		else {
			$customerStateCode = $order_data['billing']['state'];
		}
		
		$manageStateTable = $wpdb->prefix.'manage_state';
		$kitchenDetail = $wpdb->get_results( "SELECT * FROM $manageStateTable where `state_code`='".$customerStateCode."'" );	
		$kitchenStaffEmail = $kitchenDetail[0]->kitchen_email;	
		
		if(!empty($kitchenStaffEmail)) {
			wp_mail($kitchenStaffEmail, $subject,$mailBody, $headers);
		}			
		
		//wp_mail($kitchenEmail, $subject,$mailBody, $headers);
		wp_mail($salesEmail, $subject,$mailBody, $headers);	
	}
	
	
	echo json_encode(array('status' => 'success','redirectUrl'=> $postId));
	die();	
}

/*----------------------------------------------------------
			REQUIRED BILLING COMPANY FIELD
-------------------------------------------------------------*/
function sv_require_wc_company_field( $fields ) {
    $fields['company']['required'] = true;
    return $fields;
}
add_filter( 'woocommerce_default_address_fields', 'sv_require_wc_company_field' );


/*----------------------------------------------------------
	TESTING SHORTCODE FOR PDF CREATION
-------------------------------------------------------------*/
function send_pdf_shortcode()	{	
	/*$to = "karanrupani.rpn@gmail.com";			 	
	$subject = "testing pdf";
	$body = "sedn you weekly pdf";				
	$headers[] = "";		
	$attachment = createNewOrderPdf(18);
	wp_mail($to, $subject,$body, $headers ,$attachment);	
	unlink($attachment);*/
	//createDailyPdf(); 
	//createWeeklyPdf();	  
	//createOptInPdf(21); 
	//createNewOrderPdf(32);  
	//createDailyAllCustomerPdf();    
	//createDailyOptInPdf();
	//createUpdatePdf(32);  
	//createPriorOptInPdf();
	//createUserSchoolRedinessPdf(17,'1531922400'); 
	//createDailySchoolRedinessPdf(); 
	//createPriorSchoolRedinessPdf();
	//createAllCustomerSchoolRedinessPdf();	
	
} 
add_shortcode('send_pdf', 'send_pdf_shortcode');

/*----------------------------------------------------------
				DAILY PDF CREATION
-------------------------------------------------------------*/

function createDailyPdf($stateCode) {
	ob_start();
	return include 'templates/pdf/create-daily-order-pdf.php';
	return ob_get_clean();
}

/*----------------------------------------------------------
				WEEKLY PDF CREATION
-------------------------------------------------------------*/

function createWeeklyPdf($stateCode) {
	ob_start();
	return include 'templates/pdf/create-weekly-order-pdf.php';
	return ob_get_clean();
}


/*----------------------------------------------------------
				OORDER UPDATE PDF
-------------------------------------------------------------*/

function createUpdatePdf($order_id) {
	ob_start();
	return include 'templates/pdf/create-update-order-pdf.php';
	return ob_get_clean();		
}
 
/*----------------------------------------------------------
				NEW ORDER CREATE PDF
-------------------------------------------------------------*/

function createNewOrderPdf($id) {
	ob_start();
	return include 'templates/pdf/create-new-order-pdf.php';
	return ob_get_clean();		
}

/*----------------------------------------------------------
					ALTERNATIVE OPT IN PDF
-------------------------------------------------------------*/

function createOptInPdf($id) {
	ob_start();
	return include 'templates/pdf/create-opt-pdf.php'; 
	return ob_get_clean();		
} 


/*----------------------------------------------------------
			DAILY ALL CUSTOMER ORDER PDF
-------------------------------------------------------------*/

function createDailyAllCustomerPdf($stateCode) {
	ob_start();
	return include 'templates/pdf/create-daily-all-customer-pdf.php'; 
	return ob_get_clean();		
} 

/*----------------------------------------------------------
				DAILY OPT IN PDF
-------------------------------------------------------------*/

function createDailyOptInPdf($stateCode) {
	ob_start();
	return include 'templates/pdf/create-daily-opt-in-pdf.php';
	return ob_get_clean();
}

/*----------------------------------------------------------
				PRIOR OPT IN PDF
-------------------------------------------------------------*/

function createPriorOptInPdf($stateCode) {
	ob_start();
	return include 'templates/pdf/create-prior-opt-in-pdf.php';
	return ob_get_clean();
}

/*----------------------------------------------------------
			SCHOOL REDINESS PDF TO USER
-------------------------------------------------------------*/

function createUserSchoolRedinessPdf($id,$date) {
	ob_start();
	return include 'templates/pdf/create-school-rediness-pdf.php';
	return ob_get_clean();
}

/*----------------------------------------------------------
			SCHOOL REDINESS DAILY PDF
-------------------------------------------------------------*/

function createDailySchoolRedinessPdf($stateCode) { 
	ob_start();
	return include 'templates/pdf/create-daily-school-rediness-pdf.php';
	return ob_get_clean();
}

/*----------------------------------------------------------
		SCHOOL REDINESS DAILY ALL CUSTOMER PDF
-------------------------------------------------------------*/

function createAllCustomerSchoolRedinessPdf($stateCode) { 
	ob_start();
	return include 'templates/pdf/create-all-customer-school-rediness-pdf.php';
	return ob_get_clean();
}


/*----------------------------------------------------------
			SCHOOL REDINESS PRIOR PDF
-------------------------------------------------------------*/

function createPriorSchoolRedinessPdf($stateCode) { 
	ob_start();
	return include 'templates/pdf/create-prior-school-rediness-pdf.php';
	return ob_get_clean();
}


/*-------------------------------------------------
 		SHORTOCDE FOR LUNCH OPT IN
----------------------------------------------------*/
function cus_lunch_opt_in_form_shortcode()	{	
	ob_start();
	require CUS_WOO_ORDER_PLUGIN_DIR.'/lunch-opt-form.php';	
	return ob_get_clean();		
}  
add_shortcode('cus_lunch_opt_in_form', 'cus_lunch_opt_in_form_shortcode');


/*------------------------------------------------- 
 			HANDLE LUNCH OPT IN FORM 
----------------------------------------------------*/ 
add_action('admin_post_save-opt-in-form', '_handle_opt_in_form_action'); 
add_action('admin_post_nopriv_save-opt-in-form', '_handle_opt_in_form_action'); 
function _handle_opt_in_form_action(){
	$redirectUrl = $_POST['redirect-url'];
	unset($_POST['action']);
	unset($_POST['submitOptInOrder']);	
	unset($_POST['redirect-url']);		
	$current_user = wp_get_current_user();
	
	global $wpdb; 
	
	if(isset($_POST['order_id'])) {
	
		$tablename = $wpdb->prefix.'order_info';	
		$orderCount = $wpdb->get_var( "SELECT count(*) FROM $tablename where ID='".$_POST['order_id']."' and user_id=$current_user->ID and status=1" );
		
		if($orderCount==0) {
			$_SESSION['error'] = "Sorry something went wrong." ;	
			wp_redirect( $redirectUrl );
			exit();
		}  
		else {
			$tablename = $wpdb->prefix."opt_in_alternative"; 
			$user = wp_get_current_user();	
			$success =  $wpdb->insert( $tablename, array(
				'order_info_id' => $_POST['order_id'], 
				'user_id' => $user->ID,
				'date' => strtotime($_POST['date']),
				'choice' => $_POST['mealChoice'],
				'total_package' => $_POST['totalPackage']
				),
				array( '%d', '%s', '%s', '%s','%s') 
			);
			$lastid = $wpdb->insert_id;	
						
			$_SESSION['success'] = "Lunch opt in has been succesfully added." ;	
			
			$kitchenEmail = get_option( 'kitchen_staff_mail' );	
			$salesEmail = get_option( 'sales_staff_mail' );	
			$customerEmail = $user->user_email ;
			$customerOperationalEmail = $user->operational_email;
						 	
			$subject = "Alernative Meal Summary";
			$body = "Alernative Meal Summary";				
			$headers[] = 'From: '.get_option( 'blogname' ).' <'.get_option( 'admin_email' ).'>';
			$attachment = createOptInPdf($lastid);	
			
			$tablename = $wpdb->prefix.'order_info';	
			$orderInfo = $wpdb->get_results( "SELECT * FROM $tablename where ID='".$_POST['order_id']."' " );
		
			$order = wc_get_order( $orderInfo[0]->order_id );
			$order_data = $order->get_data();	
			if(!empty($order_data['shipping']['state'])) {
				$customerStateCode = $order_data['shipping']['state'];
			}
			else {
				$customerStateCode = $order_data['billing']['state'];
			}
			
			$manageStateTable = $wpdb->prefix.'manage_state';
			$kitchenDetail = $wpdb->get_results( "SELECT * FROM $manageStateTable where `state_code`='".$customerStateCode."'" );	
			$kitchenStaffEmail = $kitchenDetail[0]->kitchen_email;				
			if(!empty($kitchenStaffEmail)) {
				wp_mail($kitchenStaffEmail, $subject,$body, $headers ,$attachment);
			}	
			
			//wp_mail($kitchenEmail, $subject,$body, $headers ,$attachment);
			wp_mail($customerEmail, $subject,$body, $headers ,$attachment);	
			wp_mail($salesEmail, $subject,$body, $headers ,$attachment);
			if(!empty($customerOperationalEmail)) {
				wp_mail($customerOperationalEmail, $subject,$body, $headers ,$attachment);
			}
			unlink($attachment);	
			wp_redirect( $redirectUrl );
			exit(); 
		}
	}
	else {
		//$_SESSION['success'] = "Lunch opt in has been succesfully added." ;	
		$_SESSION['error'] = "Sorry something went wrong." ;	
		wp_redirect( $redirectUrl );
		exit(); 
	}
	
} 

/*----------------------------------------------------------
 			HANDLE OPT IN DELETE AJAX
--------------------------------------------------------------*/
add_action( 'wp_ajax_nopriv_ajaxDeleteOptIn', '_handle_delete_opt_in_action' );
add_action( 'wp_ajax_ajaxDeleteOptIn', '_handle_delete_opt_in_action' );

function _handle_delete_opt_in_action(){
	global $wpdb;
	
	if(isset($_POST['id'])) {
		$current_user = wp_get_current_user();
		$tablename = $wpdb->prefix.'opt_in_alternative';
				
		$childId = $_POST['id'];
		$closeDetail = $wpdb->get_var( "SELECT count(*) FROM $tablename where user_id=$current_user->ID and ID=$childId" );
		
		if($closeDetail>=1) {		
			$wpdb->delete( $tablename, array( 'ID' => $childId ) );
		}
	}
}

/*----------------------------------------------------------
			ADD MEAL SUMMARY INFO TO CART PAGE
-------------------------------------------------------------*/
add_action( 'woocommerce_after_cart_table', 'custom_woo_after_cart_contents', 10, 0 ); 
function custom_woo_after_cart_contents(  ) { 
    include 'templates/cart-contents.php'; 
}


/*----------------------------------------------------------
			HIDE ORDER TOTAL ON CART PAGE
-------------------------------------------------------------*/

add_action( 'woocommerce_cart_collaterals', 'remove_cart_totals', 9 );
function remove_cart_totals(){
    // Remove cart totals block
	if(isset($_SESSION['mealProduct'])) {
		remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cart_totals', 10 );
	
		// Add back "Proceed to checkout" button (and hooks)
		echo '<div class="cart_totals">';
		do_action( 'woocommerce_before_cart_totals' );
	
		echo '<div class="wc-proceed-to-checkout">';
		do_action( 'woocommerce_proceed_to_checkout' );
		echo '</div>';
	
		do_action( 'woocommerce_after_cart_totals' );
		echo '</div><br clear="all">';
	}
}

/*----------------------------------------------------------
			HIDE ORDER DETAIL ON CHECKOUT PAGE
-------------------------------------------------------------*/

add_action( 'woocommerce_checkout_order_review', 'remove_checkout_totals', 1 );
function remove_checkout_totals(){  
	if(isset($_SESSION['mealProduct'])) {
    	remove_action( 'woocommerce_checkout_order_review', 'woocommerce_order_review', 10 );
	}
}

/*----------------------------------------------------------
			HIDE ORDER DETAIL ON THANK YOU PAGE
-------------------------------------------------------------*/ 
add_action( 'woocommerce_thankyou', 'custom_woo_remove_order_review_thankyou' ); 
function custom_woo_remove_order_review_thankyou() {
	
	echo '<style>section.woocommerce-order-details,	.woocommerce-thankyou-order-details.order_details li.woocommerce-order-overview__total.total { display: none; }</style>';
		
	echo '<a href="'.get_permalink( get_option("woocommerce_myaccount_page_id") ).'" class="return-account">Return to My Account</a>';
}
 

/*----------------------------------------------------------
		REMOVE ORDER TOTAL FROM MY ACCOUNT ORDER PAGE
-------------------------------------------------------------*/ 
add_filter('woocommerce_my_account_my_orders_columns', 'my_custom_function_name', 10);

function my_custom_function_name($order){
  unset($order['order-total']);
  return $order;
}

/*-------------------------------------------------
 		SHORTOCDE FOR CLOSE DATE FORM 
----------------------------------------------------*/
function cus_woo_order_close_date_form_shortcode()	{	
	ob_start();
	require CUS_WOO_ORDER_PLUGIN_DIR.'/templates/close-date-form.php';	
	return ob_get_clean();		
} 
add_shortcode('cus_woo_close_date_form', 'cus_woo_order_close_date_form_shortcode');


/*------------------------------------------------- 
 			HANDLE CLOSE DATE FORM FORM 
----------------------------------------------------*/ 
add_action('admin_post_save-close-date-form', '_handle_save_close_date_form_action'); 
add_action('admin_post_nopriv_save-close-date-form', '_handle_save_close_date_form_action'); 
function _handle_save_close_date_form_action(){
	$redirectUrl = $_POST['redirect-url'];
	
	$current_user = wp_get_current_user();
	
	global $wpdb; 
	
	if(isset($_POST['date'])) {
	
		$tablename = $wpdb->prefix.'order_info';	
		$orderCount = $wpdb->get_var( "SELECT count(*) FROM $tablename where user_id=$current_user->ID and status=1" );
		
		if($orderCount==0) {
			$_SESSION['error'] = "Sorry you don't have any active order." ;	
			wp_redirect( $redirectUrl );
			exit();
		}  
		else {			
			$dates = explode(" - ",$_POST['date']);
			
			$startDate = "";
			$endDate  = "";
			if(!empty($dates[0])) {
				$startDate = $dates[0];
			}
			
			if(!empty($dates[0])) {
				$endDate = $dates[1];
			}
			
			$tablename = $wpdb->prefix."close_date_info"; 
			$success =  $wpdb->insert( $tablename, array(			
				'user_id' => $current_user->ID,
				'startDate' => strtotime($startDate),
				'endDate' => strtotime($endDate),
				'reason' => $_POST['reason']
				),
				array( '%d', '%s', '%s', '%s') 
			);	
			
			$kitchenEmail = get_option( 'kitchen_staff_mail' );		
			$salesEmail = get_option( 'sales_staff_mail' );			 	
			$subject = $current_user->billing_company." will be closed on ".$startDate;	
			$headers[] = 'From: '.get_option( 'blogname' ).' <'.get_option( 'admin_email' ).'>';	 
			
			if(empty($endDate)) {
				$mailBody = $current_user->billing_company." will be closed on ".$startDate;	
			}
			else {
				$mailBody = $current_user->billing_company." will be closed from ".$startDate.' to '.$endDate;	
			}
			
			$tablename = $wpdb->prefix.'order_info';
			$orderInfo = $wpdb->get_results( "SELECT * FROM $tablename where user_id=$current_user->ID and status=1" );
			
			$order = wc_get_order( $orderInfo[0]->order_id );
			$order_data = $order->get_data();	
			if(!empty($order_data['shipping']['state'])) {
				$customerStateCode = $order_data['shipping']['state'];
			}
			else {
				$customerStateCode = $order_data['billing']['state'];
			}
			
			$manageStateTable = $wpdb->prefix.'manage_state';
			$kitchenDetail = $wpdb->get_results( "SELECT * FROM $manageStateTable where `state_code`='".$customerStateCode."'" );	
			$kitchenStaffEmail = $kitchenDetail[0]->kitchen_email;
			
			if(!empty($kitchenStaffEmail)) {
				wp_mail($kitchenStaffEmail, $subject,$mailBody, $headers);		
			}
					
			
			//wp_mail($kitchenEmail, $subject,$mailBody, $headers);
			wp_mail($salesEmail, $subject,$mailBody, $headers);			
						
			$_SESSION['success'] = "Close date has been succesfully added." ;	
						
			wp_redirect( $redirectUrl );
			exit(); 
		}
	}
	else {
		//$_SESSION['success'] = "Lunch opt in has been succesfully added." ;	
		$_SESSION['error'] = "Sorry something went wrong." ;	
		wp_redirect( $redirectUrl );
		exit(); 
	}
	
} 



/*----------------------------------------------------------
 			HANDLE DELETE CLOSE FORM AJAX
--------------------------------------------------------------*/
add_action( 'wp_ajax_nopriv_ajaxDeleteCloseForm', '_handle_delete_close_form_action' );
add_action( 'wp_ajax_ajaxDeleteCloseForm', '_handle_delete_close_form_action' );

function _handle_delete_close_form_action(){
	global $wpdb;
	
	if(isset($_POST['id'])) {
		$current_user = wp_get_current_user();
		$tablename = $wpdb->prefix.'close_date_info';
		$childId = $_POST['id'];	
		
		$closeDetail = $wpdb->get_var( "SELECT count(*) FROM $tablename where user_id=$current_user->ID and ID=$childId" );
		
		if($closeDetail>=1) {
			
			$orderTable = $wpdb->prefix."order_info";
			$checkUserOrderCount = $wpdb->get_row("SELECT * FROM $orderTable where user_id=$current_user->ID" );
			$checkUserOrderCount = $wpdb->num_rows;
			if($checkUserOrderCount>0) {
			
				$closeInfo = $wpdb->get_results( "SELECT * FROM $tablename where ID=$childId" );
				
				$kitchenEmail = get_option( 'kitchen_staff_mail' );		
				$salesEmail = get_option( 'sales_staff_mail' );			 	
				$subject = $current_user->billing_company." has canceled his close date ".date("m-d-Y",$closeInfo[0]->startDate);	
				$headers[] = 'From: '.get_option( 'blogname' ).' <'.get_option( 'admin_email' ).'>';	 
				 
				if(empty($closeInfo[0]->endDate)) {
					$mailBody = $current_user->billing_company." has canceled his closed date as of ".date("m-d-Y",$closeInfo[0]->startDate).' and center will remain open on this day.';	
				}
				else {
					$mailBody = $current_user->billing_company." has canceled his closed date as of ".date("m-d-Y",$closeInfo[0]->startDate).' to '.date("m-d-Y",$closeInfo[0]->endDate). ' and center will remain open on these days.';	
				}	
				
				
				$tablename = $wpdb->prefix.'order_info';
				$orderInfo = $wpdb->get_results( "SELECT * FROM $tablename where user_id=$current_user->ID and status=1" );
				
				$order = wc_get_order( $orderInfo[0]->order_id );
				$order_data = $order->get_data();	
				if(!empty($order_data['shipping']['state'])) {
					$customerStateCode = $order_data['shipping']['state'];
				}
				else {
					$customerStateCode = $order_data['billing']['state'];
				}
				
				$manageStateTable = $wpdb->prefix.'manage_state';
				$kitchenDetail = $wpdb->get_results( "SELECT * FROM $manageStateTable where `state_code`='".$customerStateCode."'" );	
				$kitchenStaffEmail = $kitchenDetail[0]->kitchen_email;
				
				if(!empty($kitchenStaffEmail)) {
					wp_mail($kitchenStaffEmail, $subject,$mailBody, $headers);		
				}   
				
				//wp_mail($kitchenEmail, $subject,$mailBody, $headers); 
				wp_mail($salesEmail, $subject,$mailBody, $headers);		
			}
			
			$tablename = $wpdb->prefix.'close_date_info';
			$wpdb->delete( $tablename, array( 'ID' => $childId ) );	
			
		}	
	}
}


/*-----------------------------------------------------------------
	REMOVE MEAL PRODUCT FROM CART IF MEAL SESSION IS NOT SET
--------------------------------------------------------------------*/

add_action( 'woocommerce_check_cart_items', 'custom_woo_remove_item_from_cart' );
function custom_woo_remove_item_from_cart() {    
	global $woocommerce;
	$items = $woocommerce->cart->get_cart();
	foreach($items as $key => $item) {  
		$item_id = $item['product_id']; 
		$product = new WC_Product($item_id);
		$item_sku = $product->get_sku(); 
		$chkSku = explode("-",$item_sku);
		if($chkSku[0]=="fullmenu" || $chkSku[0]=="lunchonly" || $chkSku[0]=="toddler" || $chkSku[0]=="oosh") {
			if(!isset($_SESSION['mealProduct']) || empty($_SESSION['mealProduct'])){
				$woocommerce->cart->empty_cart();
			}			
		}		
	}
}

/*--------------------------------------------------------------
			GO BACK BUTTON ON CART PAGE
-----------------------------------------------------------------*/
/*
add_action('woocommerce_before_cart','add_back_button_on_cart',10);
function add_back_button_on_cart() {
	echo '<div class="rightBtnChild"><a onClick="history.go( -1 );return true;">Go back</a>	</div>';
}
*/


/*------------------------------------------------------------
		ADD OPERATION MANAGER EMAIL ON MYACCOUNT FRONTEND
---------------------------------------------------------------*/

add_action( 'woocommerce_edit_account_form', 'add_operation_email_edit_account_form' );
function add_operation_email_edit_account_form() {
    $user = wp_get_current_user();    
    echo '<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">';
	echo '<label for="operation-email">Operationl Manager Email</label>';
	echo '<input type="email" class="woocommerce-Input woocommerce-Input--text input-text" name="operational_email" value="'.esc_attr( $user->operational_email ).' " /></p>';
	   
}
 
add_action( 'woocommerce_save_account_details', 'save_operation_email_account_details', 12, 1 );
function save_operation_email_account_details( $user_id ) {
   
    if( isset( $_POST['operational_email'] ) )
        update_user_meta( $user_id, 'operational_email', sanitize_text_field( $_POST['operational_email'] ) );  
}


/*------------------------------------------------------------
		ADD OPERATION MANAGER EMAIL ON MYACCOUNT BACKEND
---------------------------------------------------------------*/

add_filter( 'user_contactmethods', 'extra_contact_info' );

function extra_contact_info( $fields ) {
  $fields['operational_email'] = __( 'Operational Manager Email' );
  return $fields; 
} 


/*-------------------------------------------------
 			HANDLE ADMIN ORDER UPDATE STATUS
----------------------------------------------------*/
add_action( 'wp_ajax_nopriv_adminOrderUpdateStatus', '_handle_admin_order_status_action' );
add_action( 'wp_ajax_adminOrderUpdateStatus', '_handle_admin_order_status_action' );

function _handle_admin_order_status_action(){	
	$postId = $_POST['id'];  
	
	global $wpdb;
	$tablename = $wpdb->prefix.'order_info';
	$wpdb->update( $tablename, 
		array(        
		'status' => $_POST['status']
        ), 
		array( 'ID' => $postId ), 
		array( '%d' )
	);
	
	echo json_encode(array('status' => 'success','redirectUrl'=> $postId));
	die();	
}


/*------------------------------------------------- 
 			HANDLE SCHOOL REDINESS FORM
----------------------------------------------------*/ 
add_action('admin_post_school-rediness-form', '_handle_school_rediness_form_action'); 
add_action('admin_post_nopriv_school-rediness-form', '_handle_school_rediness_form_action'); 
function _handle_school_rediness_form_action(){
	
	$redirectUrl = $_POST['redirect-url'];
	
	$current_user = wp_get_current_user();
	
	global $wpdb; 
	
	if(isset($_POST['order_id'])) {
		
		$tablename = $wpdb->prefix.'order_info';	
		$orderCount = $wpdb->get_var( "SELECT count(*) FROM $tablename where ID='".$_POST['order_id']."' and user_id=$current_user->ID and status=1" );
		
		if($orderCount==0) {			
			$_SESSION['error'] = "Sorry something went wrong." ;	
			wp_redirect( $redirectUrl );
			exit();
		}  
		else {			
			$tablename = $wpdb->prefix."school_readiness_lunch"; 
		
			$success =  $wpdb->insert( $tablename, array(
				'order_info_id' => $_POST['order_id'], 
				'user_id' => $current_user->ID,
				'date' => strtotime($_POST['date']),
				'total_package' => $_POST['totalPackage'],				
				'vegemite' => $_POST['vegemite'],
				'cheese' => $_POST['cheese'],
				'cheese_vegemite' => $_POST['cheese_vegemite'],
				'cheese_lettuce' => $_POST['cheese_lettuce'],
				'bottle_large' => $_POST['bottle_large'],
				'bottle_small' => $_POST['bottle_small'],
				'apples' => $_POST['apple'],
				'oranges' => $_POST['oranges'],
				'pears' => $_POST['pears'],
				'bananas' => $_POST['bananas'],
				'comment' => $_POST['comment']
				),
				array('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s') 
			); 
			$lastid = $wpdb->insert_id;	
						
			$_SESSION['success'] = "School Rediness has been succesfully added." ;	
			
			$kitchenEmail = get_option( 'kitchen_staff_mail' );	
			$salesEmail = get_option( 'sales_staff_mail' );		
			$customerEmail = $current_user->user_email ;
			$customerOperationalEmail = $current_user->operational_email;
						 	
			$subject = "Excursion/School Rediness Meal Summary";
			$body = "Excursion/School Rediness Meal Summary";				
			$headers[] = 'From: '.get_option( 'blogname' ).' <'.get_option( 'admin_email' ).'>';
			$attachment = createUserSchoolRedinessPdf($lastid,strtotime($_POST['date']));		
			
			$tablename = $wpdb->prefix.'order_info';	
			$orderInfo = $wpdb->get_results( "SELECT * FROM $tablename where ID='".$_POST['order_id']."' " );
		
			$order = wc_get_order( $orderInfo[0]->order_id );
			
			$order_data = $order->get_data();	
			if(!empty($order_data['shipping']['state'])) {
				$customerStateCode = $order_data['shipping']['state'];
			}
			else {
				$customerStateCode = $order_data['billing']['state'];
			}
			
			$manageStateTable = $wpdb->prefix.'manage_state';
			$kitchenDetail = $wpdb->get_results( "SELECT * FROM $manageStateTable where `state_code`='".$customerStateCode."'" );	
			$kitchenStaffEmail = $kitchenDetail[0]->kitchen_email;
			
			if(!empty($kitchenStaffEmail)) {
				wp_mail($kitchenStaffEmail, $subject,$body, $headers ,$attachment);
			}
	
			//wp_mail($kitchenEmail, $subject,$body, $headers ,$attachment);	
			wp_mail($salesEmail, $subject,$body, $headers ,$attachment);
			wp_mail($customerEmail, $subject,$body, $headers ,$attachment);
			if(!empty($customerOperationalEmail)) {
				wp_mail($customerOperationalEmail, $subject,$body, $headers ,$attachment);
			}
			unlink($attachment);	
			wp_redirect( $redirectUrl );
			exit(); 
		} 
	}
	else {
		$_SESSION['error'] = "Sorry something went wrong." ;	
		wp_redirect( $redirectUrl );
		exit(); 
	}
} 



/*----------------------------------------------------------
 			HANDLE DELETE SCHOOL REDINESS AJAX
--------------------------------------------------------------*/
add_action( 'wp_ajax_nopriv_ajaxDeleteSchoolRediness', '_handle_delete_school_rediness_action' );
add_action( 'wp_ajax_ajaxDeleteSchoolRediness', '_handle_delete_school_rediness_action' );

function _handle_delete_school_rediness_action(){
	global $wpdb;
	
	if(isset($_POST['id'])) {
		$current_user = wp_get_current_user();
		$tablename = $wpdb->prefix.'school_readiness_lunch';
				
		$childId = $_POST['id'];
		$closeDetail = $wpdb->get_var( "SELECT count(*) FROM $tablename where user_id=$current_user->ID and ID=$childId" );
		
		if($closeDetail>=1) {		
			$wpdb->delete( $tablename, array( 'ID' => $childId ) );
		}
	}
}


/*----------------------------------------------------------
 			HANDLE CLOSE DATE ORDER FORM AJAX
--------------------------------------------------------------*/
add_action( 'wp_ajax_nopriv_ajaxSubmitCloseDateForm', '_handle_close_date_order_form_ajax_action' );
add_action( 'wp_ajax_ajaxSubmitCloseDateForm', '_handle_close_date_order_form_ajax_action' );

function _handle_close_date_order_form_ajax_action(){
	global $wpdb;
	$current_user = wp_get_current_user();
	
	if(isset($_POST['date'])) {	
			
		$dates = explode(" - ",$_POST['date']);
			
		$startDate = "";
		$endDate  = "";
		
		$returnDate = "";
		$returnStartDate = "";
		$returnEndDate = "";
		if(!empty($dates[0])) {
			$startDate = strtotime($dates[0]);
			$returnStartDate = date("m-d-Y",$startDate);
			$returnDate = date("F d,Y",$startDate);
		}
		
		if(!empty($dates[1])) {
			$endDate = strtotime($dates[1]);
			$returnEndDate = date("m-d-Y",$endDate);
			$returnDate.= ' - '.date("F d,Y",$endDate);
		}
			
		$tablename = $wpdb->prefix."close_date_info"; 
		$success =  $wpdb->insert( $tablename, array(			
			'user_id' => $current_user->ID,
			'startDate' => $startDate,
			'endDate' => $endDate,
			'reason' => $_POST['reason']
			),
			array( '%d', '%s', '%s', '%s') 
		);	
						
		
		echo json_encode(array('status' => 'success','postId'=>$wpdb->insert_id,'date'=>$returnDate,'reason'=>$_POST['reason'],'startDate'=>$returnStartDate,'endDate'=>$returnEndDate));
		die();	
		
	}
	else {
		echo json_encode(array('error' => 'success','message'=> "Something went wrong"));
		die();	
	}
}


/*----------------------------------------------------------
 			HANDLE CHILD ID CHECK AJAX
--------------------------------------------------------------*/
add_action( 'wp_ajax_nopriv_ajaxChildIdCheck', '_handle_child_id_check_ajax_action' );
add_action( 'wp_ajax_ajaxChildIdCheck', '_handle_child_id_check_ajax_action' );

function _handle_child_id_check_ajax_action(){	
	$current_user = wp_get_current_user();
	
	if(isset($_POST['val'])) {
		global $wpdb;
		$tablename = $wpdb->prefix.'child_info';	
		$idCount = $wpdb->get_var( "SELECT count(*) FROM $tablename where id_number='".$_POST['val']."' and user_id=$current_user->ID" );
		
		if($idCount>0) {	
			echo json_encode(array('status' => 'error','message' => 'Child ID Already exist'));
			die();			
		}
		else {
			echo json_encode(array('status' => 'success','message' => ''));
			die();
		}
	}	
	else {
		echo json_encode(array('status' => 'error','message' => 'Something Went Wrong'));
		die();	
	}
}


/*-------------------------------------------------
 				HANDLE ADMIN ADD STATE FORM 
----------------------------------------------------*/
add_action('admin_post_admin-add-state', '_handle_admin_add_state_action'); 
add_action('admin_post_admin-add-state', '_handle_admin_add_state_action'); 
function _handle_admin_add_state_action(){		
		
	global $wpdb; 
	$tablename = $wpdb->prefix."manage_state"; 
	
	$success =  $wpdb->insert( $tablename, array(        
		'state_code' => $_POST['state_code'],
        'kitchen_email' => $_POST['kitchen_email'],		
        ),
     array( '%s','%s') 
    );	
	
	wp_redirect( $_POST['redirect-url'] ); 
	exit(); 
} 


/*----------------------------------------------------------
 			HANDLE ADMIN DELETE STATE
--------------------------------------------------------------*/
add_action( 'wp_ajax_nopriv_adminDeleteState', '_handle_admin_delete_state_action' );
add_action( 'wp_ajax_adminDeleteState', '_handle_admin_delete_state_action' );

function _handle_admin_delete_state_action(){
	global $wpdb;
	
	if(isset($_POST['id'])) {
		$current_user = wp_get_current_user();
		$tablename = $wpdb->prefix.'manage_state';
				
		$postID = $_POST['id'];
		$postExist = $wpdb->get_var( "SELECT count(*) FROM $tablename where ID=$postID" );
		
		if($postExist>=1) {		
			$wpdb->delete( $tablename, array( 'ID' => $postID ) );
		}
	}
}