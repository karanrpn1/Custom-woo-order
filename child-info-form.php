<?php 
if(!is_user_logged_in()) {
	echo '<script>window.location.href = "'.home_url().'";</script>';
}

global $wpdb;
if(isset($_GET['editId'])) {
	$tablename = $wpdb->prefix.'child_info';
	$current_user = wp_get_current_user();
	$childCount = $wpdb->get_var( "SELECT count(*) FROM $tablename where ID='".$_GET['editId']."' and user_id=$current_user->ID" );
	if($childCount==0) {
		$redirectUrl = get_site_url().'/custom-order-form/';
		echo '<script>window.location.href = "'.$redirectUrl.'";</script>';
		exit(); 
	}
	else {
		$childData = $wpdb->get_results( "SELECT * FROM $tablename where ID='".$_GET['editId']."' " );
		$child_name = $childData[0]->child_name;
		$remark = $childData[0]->remark;
		$id_number = $childData[0]->id_number;
		$attend = json_decode($childData[0]->attend); 
		$dairy = json_decode($childData[0]->dairy);
		$roomNumber = $childData[0]->roomNumber; 
		$vegetarian = json_decode($childData[0]->vegetarian);
		$grains = json_decode($childData[0]->grains);
		$meat = json_decode($childData[0]->meat);
		$seafood = json_decode($childData[0]->seafood);
		$soyproduct = json_decode($childData[0]->soyproduct);
		$legumes = json_decode($childData[0]->legumes);
		$fruits = json_decode($childData[0]->fruits);
		$vegetables = json_decode($childData[0]->vegetables);
		$others = json_decode($childData[0]->others);
		$toddler_toggle = $childData[0]->toddler_toggle;
		if($toddler_toggle == 1){
			$toddlerToggle = "checked";
		}
		else {
			$toddlerToggle = "";
		}
	}
}

if(isset($_GET['editId'])) {
	$formAction = '<input type="hidden" name="action" value="update-child-form" /><input type="hidden" name="postId" value="'.$_GET['editId'].'" />';
}
else {
	$formAction = '<input type="hidden" name="action" value="save-child-form" />';
}

if(isset($_SESSION['success']) && !empty($_SESSION['success'])) {
	$successMsg = '<div class="successMsg">'.$_SESSION['success'].'</div>';
	unset($_SESSION['success']);
}
else {
	$successMsg =  "";
}

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
	$redirectUrl = "//".$_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] ;
}

$rtForm = '<div class="custom-order-form"> 
	<div class="rightBtnChild">
			<a href="'.$redirectUrl.'">Go back</a>
	</div>
	<form name="save-child-info" method="post" action="'.get_admin_url().'admin-post.php">
	'.$successMsg.$formAction.'
	<input type="hidden" name="redirect-url" value="'.$redirectUrl.'" /> 
	<div class="allergens-info">
        <div class="allergy-header"> 
			<h2>Toddlers & Allergens</h2>
			<label class="checkContainer">Is this child a toddler?
                <input type="checkbox" name="toddler_toggle" value="1" '.$toddlerToggle.'><span class="checkmark"></span>
            </label>
            <span class="heading">Allergy Information</span>
			<p>Please note Hearty Health does not use egg in meal preparation. Hearty Health are egg free zone.</p>
			<input type="text" name="id_number" placeholder="ID number" value="'.(!empty($id_number)?$id_number:"").'" id="childID"/>
            <input type="text" name="child_name" placeholder="Child Name" value="'.(!empty($child_name)?$child_name:"").'" required/>			 
            <input type="text" name="roomNumber" placeholder="Room Name or Number" value="'.(!empty($roomNumber)?$roomNumber:"").'"/> 
			<small id="childIdError"></small>
        </div>
		<div class="allergen-group"> 
        	<span class="subHeading">This child attends on</span>
            <label class="checkContainer">Monday
                <input type="checkbox" name="attend[]" value="Monday" '.(!empty($attend)?(in_array("Monday",$attend)?"checked":""):"").'><span class="checkmark"></span>
            </label>   
            <label class="checkContainer">Tuesday
                <input type="checkbox" name="attend[]" value="Tuesday" '.(!empty($attend)?(in_array("Tuesday",$attend)?"checked":""):"").'><span class="checkmark"></span>
            </label>
			<label class="checkContainer">Wednesday
                <input type="checkbox" name="attend[]" value="Wednesday" '.(!empty($attend)?(in_array("Wednesday",$attend)?"checked":""):"").'><span class="checkmark"></span>
            </label>
			<label class="checkContainer">Thursday
                <input type="checkbox" name="attend[]" value="Thursday" '.(!empty($attend)?(in_array("Thursday",$attend)?"checked":""):"").'><span class="checkmark"></span>
            </label>
			<label class="checkContainer">Friday
                <input type="checkbox" name="attend[]" value="Friday" '.(!empty($attend)?(in_array("Friday",$attend)?"checked":""):"").'><span class="checkmark"></span>
            </label>
        </div>
        <div class="allergen-group"> 
        	<span class="subHeading">Dairy</span>
            <label class="checkContainer">Dairy
                <input type="checkbox" name="dairy[]" value="Dairy" '.(!empty($dairy)?(in_array("Dairy",$dairy)?"checked":""):"").'><span class="checkmark"></span>
            </label>
            <label class="checkContainer">Lactose
                <input type="checkbox" name="dairy[]" value="Lactose" '.(!empty($dairy)?(in_array("Lactose",$dairy)?"checked":""):"").'><span class="checkmark"></span>
            </label>
        </div>
        
        <div class="allergen-group"> 
        	<span class="subHeading">Vegetarian</span>
            <label class="checkContainer">Vegetarian
                <input type="checkbox" name="vegetarian[]" value="Vegetarian" '.(!empty($vegetarian)?(in_array("Vegetarian",$vegetarian)?"checked":""):"").'><span class="checkmark"></span>
            </label>
        </div>
        <div class="allergen-group"> 
        	<span class="subHeading">Grains</span>
            <label class="checkContainer">Gluten and or wheat
                <input type="checkbox" name="grains[]" value="Gluten and or wheat" '.(!empty($grains)?(in_array("Gluten and or wheat",$grains)?"checked":""):"").'><span class="checkmark"></span>
           	</label>
        </div>
        <div class="allergen-group"> 
        	<span class="subHeading">Meat Products <small>(HeartyHealth does not use pork or any of its byproducts in meal preperation )</small> 
          	</span>
            <label class="checkContainer">Beef
                <input type="checkbox" name="meat[]" value="Beef" '.(!empty($meat)?(in_array("Beef",$meat)?"checked":""):"").'><span class="checkmark"></span>
            </label>
            <label class="checkContainer">Chicken
                <input type="checkbox" name="meat[]" value="Chicken" '.(!empty($meat)?(in_array("Chicken",$meat)?"checked":""):"").'><span class="checkmark"></span>
            </label>
            <label class="checkContainer">Lamb
                <input type="checkbox" name="meat[]" value="Lamb" '.(!empty($meat)?(in_array("Lamb",$meat)?"checked":""):"").'><span class="checkmark"></span>
            </label>
            <label class="checkContainer">Preserved Meats & Sausages
                <input type="checkbox" name="meat[]" value="Preserved Meats & Sausages" '.(!empty($meat)?(in_array("Preserved Meats & Sausages",$meat)?"checked":""):"").'><span class="checkmark" ></span>                
            </label>
            <label class="checkContainer">Halal <small> ( By choosing "Halal" all meat provided will be certified Halal )</small>
                <input type="checkbox" name="meat[]" value="Halal" '.(!empty($meat)?(in_array("Halal",$meat)?"checked":""):"").'><span class="checkmark"></span>
           	</label>
        </div>
        <div class="allergen-group"> 
        	<span class="subHeading">Seafood</span>
            <label class="checkContainer">Fish
                <input type="checkbox" name="seafood[]" value="Fish" '.(!empty($seafood)?(in_array("Fish",$seafood)?"checked":""):"").'><span class="checkmark"></span>
            </label>
        </div>
        <div class="allergen-group"> 
        	<span class="subHeading">Soy Products</span>
            <label class="checkContainer">Soy / Soya Products
                <input type="checkbox" name="soyproduct[]" value="Soy / Soya Products" '.(!empty($soyproduct)?(in_array("Soy / Soya Products",$soyproduct)?"checked":""):"").'><span class="checkmark" ></span>
            </label>
            <label class="checkContainer">Tofu
                <input type="checkbox" name="soyproduct[]" value="Tofu" '.(!empty($soyproduct)?(in_array("Tofu",$soyproduct)?"checked":""):"").'><span class="checkmark" ></span>
            </label>
        </div>
        <div class="allergen-group"> 
        	<span class="subHeading">Legumes</span>
            <label class="checkContainer">Broad Beans
                <input type="checkbox" name="legumes[]" value="Broad Beans" '.(!empty($legumes)?(in_array("Broad Beans",$legumes)?"checked":""):"").'><span class="checkmark"></span>
            </label>
            <label class="checkContainer">Chick Peas
                <input type="checkbox" name="legumes[]" value="Chick Peas" '.(!empty($legumes)?(in_array("Chick Peas",$legumes)?"checked":""):"").'><span class="checkmark"></span>
            </label>
            <label class="checkContainer">Fava Beans
                <input type="checkbox" name="legumes[]" value="Fava Beans" '.(!empty($legumes)?(in_array("Fava Beans",$legumes)?"checked":""):"").'><span class="checkmark"></span>
            </label>
            <label class="checkContainer"> Haricot Beans
                <input type="checkbox" name="legumes[]" value="Haricot Beans" '.(!empty($legumes)?(in_array("Haricot Beans",$legumes)?"checked":""):"").'><span class="checkmark"></span>
            </label>
            <label class="checkContainer">Kidney Beans
                <input type="checkbox" name="legumes[]" value="Kidney Beans" '.(!empty($legumes)?(in_array("Kidney Beans",$legumes)?"checked":""):"").'><span class="checkmark"></span>
            </label>
            <label class="checkContainer">Lentils
                <input type="checkbox" name="legumes[]" value="Lentils" '.(!empty($legumes)?(in_array("Lentils",$legumes)?"checked":""):"").'><span class="checkmark"></span>
            </label>
            <label class="checkContainer">Red Lentils
                <input type="checkbox" name="legumes[]" value="Red Lentils" '.(!empty($legumes)?(in_array("Red Lentils",$legumes)?"checked":""):"").'><span class="checkmark"></span>
            </label>
            <label class="checkContainer">Split Beans
                <input type="checkbox" name="legumes[]" value="Split Beans" '.(!empty($legumes)?(in_array("Split Beans",$legumes)?"checked":""):"").'><span class="checkmark"></span>
            </label>
            <label class="checkContainer">Sunflower Seeds
                <input type="checkbox" name="legumes[]" value="Sunflower Seeds" '.(!empty($legumes)?(in_array("Sunflower Seeds",$legumes)?"checked":""):"").'><span class="checkmark"></span>
            </label>			
			 <label class="checkContainer">Peas
                <input type="checkbox" name="legumes[]" value="Peas" '.(!empty($legumes)?(in_array("Peas",$legumes)?"checked":""):"").'><span class="checkmark"></span>
            </label>
			
        </div>
        <div class="allergen-group"> 
			<span class="subHeading">Fruits</span>
            <label class="checkContainer">Apple
                <input type="checkbox" name="fruits[]" value="Apple" '.(!empty($fruits)?(in_array("Apple",$fruits)?"checked":""):"").'><span class="checkmark"></span>
            </label>
            <label class="checkContainer">Apricot
                <input type="checkbox" name="fruits[]" value="Apricot" '.(!empty($fruits)?(in_array("Apricot",$fruits)?"checked":""):"").'><span class="checkmark"></span>
            </label>
            <label class="checkContainer">Banana
                <input type="checkbox" name="fruits[]" value="Banana" '.(!empty($fruits)?(in_array("Banana",$fruits)?"checked":""):"").'><span class="checkmark"></span>
            </label>
            <label class="checkContainer">Blueberry
                <input type="checkbox" name="fruits[]" value="Blueberry Seeds" '.(!empty($fruits)?(in_array("Blueberry Seeds",$fruits)?"checked":""):"").'><span class="checkmark"></span>
            </label>
            <label class="checkContainer">Grapes
                <input type="checkbox" name="fruits[]" value="Grapes Seeds" '.(!empty($fruits)?(in_array("Grapes Seeds",$fruits)?"checked":""):"").'><span class="checkmark"></span>
            </label>
            <label class="checkContainer">Honey Dew Melon
                <input type="checkbox" name="fruits[]" value="Honey Dew Melon" '.(!empty($fruits)?(in_array("Honey Dew Melon",$fruits)?"checked":""):"").'><span class="checkmark"></span>
                </label>
            <label class="checkContainer">Kiwi Fruit
                <input type="checkbox" name="fruits[]" value="Kiwi Fruit" '.(!empty($fruits)?(in_array("Kiwi Fruit",$fruits)?"checked":""):"").'><span class="checkmark"></span>
                </label>
            <label class="checkContainer">Lemon
                <input type="checkbox" name="fruits[]" value="Lemon" '.(!empty($fruits)?(in_array("Lemon",$fruits)?"checked":""):"").'><span class="checkmark"></span>
            </label>
            <label class="checkContainer">Lime
                <input type="checkbox" name="fruits[]" value="Lime" '.(!empty($fruits)?(in_array("Lime",$fruits)?"checked":""):"").'><span class="checkmark"></span>
            </label>
            <label class="checkContainer">Mango
                <input type="checkbox" name="fruits[]" value="Mango" '.(!empty($fruits)?(in_array("Mango",$fruits)?"checked":""):"").'><span class="checkmark"></span>
            </label>
            <label class="checkContainer">Orange
                <input type="checkbox" name="fruits[]" value="Orange" '.(!empty($fruits)?(in_array("Orange",$fruits)?"checked":""):"").'><span class="checkmark"></span>
            </label>
            <label class="checkContainer">Pears
                <input type="checkbox" name="fruits[]" value="Pears" '.(!empty($fruits)?(in_array("Pears",$fruits)?"checked":""):"").'><span class="checkmark"></span>
            </label>
            <label class="checkContainer">Pineapple
                <input type="checkbox" name="fruits[]" value="Pineapple" '.(!empty($fruits)?(in_array("Pineapple",$fruits)?"checked":""):"").'><span class="checkmark"></span>
            </label>
            <label class="checkContainer">Plum
                <input type="checkbox" name="fruits[]" value="Plum" '.(!empty($fruits)?(in_array("Plum",$fruits)?"checked":""):"").'><span class="checkmark"></span>
            </label>
            <label class="checkContainer">Rockmelon
                <input type="checkbox" name="fruits[]" value="Rockmelon" '.(!empty($fruits)?(in_array("Rockmelon",$fruits)?"checked":""):"").'><span class="checkmark"></span>
            </label>
            <label class="checkContainer">Strawberries
                <input type="checkbox" name="fruits[]" value="Strawberries" '.(!empty($fruits)?(in_array("Strawberries",$fruits)?"checked":""):"").'><span class="checkmark"></span>
            </label>
        </div>
        <div class="allergen-group"> 
        	<span class="subHeading">Vegetables</span>
            <label class="checkContainer">Asparagus
                <input type="checkbox" name="vegetables[]" value="Asparagus" '.(!empty($vegetables)?(in_array("Asparagus",$vegetables)?"checked":""):"").'><span class="checkmark"></span>
            </label>
            <label class="checkContainer">Avocado
                <input type="checkbox" name="vegetables[]" value="Avocado" '.(!empty($vegetables)?(in_array("Avocado",$vegetables)?"checked":""):"").'><span class="checkmark"></span>
            </label>
            <label class="checkContainer">Beans (Green)
                <input type="checkbox" name="vegetables[]" value="Beans (Green)" '.(!empty($vegetables)?(in_array("Beans (Green)",$vegetables)?"checked":""):"").'><span class="checkmark"></span>
            </label>
            <label class="checkContainer">Beetroot
                <input type="checkbox" name="vegetables[]" value="Beetroot" '.(!empty($vegetables)?(in_array("Beetroot",$vegetables)?"checked":""):"").'><span class="checkmark"></span>
            </label>
            <label class="checkContainer">Broccoli
                <input type="checkbox" name="vegetables[]" value="Broccoli" '.(!empty($vegetables)?(in_array("Broccoli",$vegetables)?"checked":""):"").'><span class="checkmark"></span>
            </label>
            <label class="checkContainer">Capsicum
                <input type="checkbox" name="vegetables[]" value="Capsicum" '.(!empty($vegetables)?(in_array("Capsicum",$vegetables)?"checked":""):"").'><span class="checkmark"></span>
            </label>
            <label class="checkContainer">Carrot
                <input type="checkbox" name="vegetables[]" value="Carrot" '.(!empty($vegetables)?(in_array("Carrot",$vegetables)?"checked":""):"").'><span class="checkmark"></span>
            </label>
            <label class="checkContainer">Cauliflower
                <input type="checkbox" name="vegetables[]" value="Cauliflower" '.(!empty($vegetables)?(in_array("Cauliflower",$vegetables)?"checked":""):"").'><span class="checkmark"></span>
            </label>
            <label class="checkContainer">Celery
                <input type="checkbox" name="vegetables[]" value="Celery" '.(!empty($vegetables)?(in_array("Celery",$vegetables)?"checked":""):"").'><span class="checkmark"></span>
            </label>
            <label class="checkContainer">Chilli
                <input type="checkbox" name="vegetables[]" value="Chilli" '.(!empty($vegetables)?(in_array("Chilli",$vegetables)?"checked":""):"").'><span class="checkmark"></span>
            </label>
            <label class="checkContainer">Corn
                <input type="checkbox" name="vegetables[]" value="Corn" '.(!empty($vegetables)?(in_array("Corn",$vegetables)?"checked":""):"").'><span class="checkmark"></span>
            </label>
            <label class="checkContainer">Cucumber
                <input type="checkbox" name="vegetables[]" value="Cucumber" '.(!empty($vegetables)?(in_array("Cucumber",$vegetables)?"checked":""):"").'><span class="checkmark"></span>
            </label>
            <label class="checkContainer">Eggplant
                <input type="checkbox" name="vegetables[]" value="Eggplant" '.(!empty($vegetables)?(in_array("Eggplant",$vegetables)?"checked":""):"").'><span class="checkmark"></span>
            </label>
            <label class="checkContainer">Galangal
                <input type="checkbox" name="vegetables[]" value="Galangal" '.(!empty($vegetables)?(in_array("Galangal",$vegetables)?"checked":""):"").'><span class="checkmark"></span>
            </label>
            <label class="checkContainer">Garlic
                <input type="checkbox" name="vegetables[]" value="Garlic" '.(!empty($vegetables)?(in_array("Garlic",$vegetables)?"checked":""):"").'><span class="checkmark"></span>
            </label>
            <label class="checkContainer">Herbs Mixed Variesties
                <input type="checkbox" name="vegetables[]" value="Herbs Mixed Variesties" '.(!empty($vegetables)?(in_array("Herbs Mixed Variesties",$vegetables)?"checked":""):"").'><span class="checkmark"></span>
            </label>
            <label class="checkContainer">Lemongrass
                <input type="checkbox" name="vegetables[]" value="Lemongrass" '.(!empty($vegetables)?(in_array("Lemongrass",$vegetables)?"checked":""):"").'><span class="checkmark"></span>
            </label>
            <label class="checkContainer">Lettuce
                <input type="checkbox" name="vegetables[]" value="Lettuce" '.(!empty($vegetables)?(in_array("Lettuce",$vegetables)?"checked":""):"").'><span class="checkmark"></span>
            </label>
            <label class="checkContainer">Galangal
                <input type="checkbox" name="vegetables[]" value="Galangal" '.(!empty($vegetables)?(in_array("Galangal",$vegetables)?"checked":""):"").'><span class="checkmark"></span>
            </label>
            <label class="checkContainer">Mushroom
                <input type="checkbox" name="vegetables[]" value="Mushroom" '.(!empty($vegetables)?(in_array("Mushroom",$vegetables)?"checked":""):"").'><span class="checkmark"></span>
            </label>
            <label class="checkContainer">Onion
                <input type="checkbox" name="vegetables[]" value="Onion" '.(!empty($vegetables)?(in_array("Onion",$vegetables)?"checked":""):"").'><span class="checkmark"></span>
            </label>           
            <label class="checkContainer">Potato
                <input type="checkbox" name="vegetables[]" value="Potato" '.(!empty($vegetables)?(in_array("Potato",$vegetables)?"checked":""):"").'><span class="checkmark"></span>
            </label>
            <label class="checkContainer">Pumpkin
                <input type="checkbox" name="vegetables[]" value="Pumpkin" '.(!empty($vegetables)?(in_array("Pumpkin",$vegetables)?"checked":""):"").'> <span class="checkmark"></span>
            </label>
            <label class="checkContainer">Shallots
                <input type="checkbox" name="vegetables[]" value="Shallots" '.(!empty($vegetables)?(in_array("Shallots",$vegetables)?"checked":""):"").'><span class="checkmark"></span>
            </label>
            <label class="checkContainer">Spinach
                <input type="checkbox" name="vegetables[]" value="Spinach" '.(!empty($vegetables)?(in_array("Spinach",$vegetables)?"checked":""):"").'><span class="checkmark"></span>
            </label>
            <label class="checkContainer">Sweet Potato
                <input type="checkbox" name="vegetables[]" value="Sweet Potato" '.(!empty($vegetables)?(in_array("Sweet Potato",$vegetables)?"checked":""):"").'><span class="checkmark"></span>
            </label>
            <label class="checkContainer">Tomato (Fresh)
                <input type="checkbox" name="vegetables[]" value="Tomato (Fresh)" '.(!empty($vegetables)?(in_array("Tomato (Fresh)",$vegetables)?"checked":""):"").'><span class="checkmark"></span>
            </label>
            <label class="checkContainer">Zucchini
                <input type="checkbox" name="vegetables[]" value="Zucchini" '.(!empty($vegetables)?(in_array("Zucchini",$vegetables)?"checked":""):"").'><span class="checkmark"></span>
            </label>
        </div>
       
        <div class="allergen-group"> 
        	<span class="subHeading">Others</span>
            <label class="checkContainer">Canola Oil
                <input type="checkbox" name="others[]" value="canolaoil" '.(!empty($others)?(in_array("canolaoil",$others)?"checked":""):"").'><span class="checkmark"></span>
            </label>
            <label class="checkContainer">Chia
                <input type="checkbox" name="others[]" value="chia" '.(!empty($others)?(in_array("chia",$others)?"checked":""):"").'><span class="checkmark"></span>
            </label>
            <label class="checkContainer">Cocao/Chocolate
                <input type="checkbox" name="others[]" value="Cocao/Chocolate" '.(!empty($others)?(in_array("Cocao/Chocolate",$others)?"checked":""):"").'><span class="checkmark"></span>
            </label>
            <label class="checkContainer">Coconut
                <input type="checkbox" name="others[]" value="Coconut" '.(!empty($others)?(in_array("Coconut",$others)?"checked":""):"").'><span class="checkmark"></span>
            </label>
            <label class="checkContainer">Currents
                <input type="checkbox" name="others[]" value="Currents" '.(!empty($others)?(in_array("Currents",$others)?"checked":""):"").'><span class="checkmark"></span>
            </label>
            <label class="checkContainer">Curry Powder
                <input type="checkbox" name="others[]" value="Curry Powder" '.(!empty($others)?(in_array("Curry Powder",$others)?"checked":""):"").'><span class="checkmark"></span>
            </label>
            <label class="checkContainer">Honey
                <input type="checkbox" name="others[]" value="Honey" '.(!empty($others)?(in_array("Honey",$others)?"checked":""):"").'><span class="checkmark"></span>
            </label>
            <label class="checkContainer">Mustard
                <input type="checkbox" name="others[]" value="Mustard" '.(!empty($others)?(in_array("Mustard",$others)?"checked":""):"").'><span class="checkmark"></span>
            </label>
            <label class="checkContainer"> Olive Oil
                <input type="checkbox" name="others[]" value="Olive Oil" '.(!empty($others)?(in_array("Olive Oil",$others)?"checked":""):"").'><span class="checkmark"></span>
            </label>
            <label class="checkContainer">Paprika
                <input type="checkbox" name="others[]" value="Paprika" '.(!empty($others)?(in_array("Paprika",$others)?"checked":""):"").'><span class="checkmark"></span>
            </label>
            <label class="checkContainer">Rice
                <input type="checkbox" name="others[]" value="Rice" '.(!empty($others)?(in_array("Rice",$others)?"checked":""):"").'><span class="checkmark"></span>
            </label>
            <label class="checkContainer">Sesame
                <input type="checkbox" name="others[]" value="Sesame" '.(!empty($others)?(in_array("Sesame",$others)?"checked":""):"").'><span class="checkmark"></span>
            </label>
            <label class="checkContainer">Sesame Oil
                <input type="checkbox" name="others[]" value="Sesame Oil" '.(!empty($others)?(in_array("Sesame Oil",$others)?"checked":""):"").'><span class="checkmark"></span>
            </label>
            <label class="checkContainer">Spices
                <input type="checkbox" name="others[]" value="Spices" '.(!empty($others)?(in_array("Spices",$others)?"checked":""):"").'><span class="checkmark"></span>
            </label>
            <label class="checkContainer">Sugar
                <input type="checkbox" name="others[]" value="Sugar" '.(!empty($others)?(in_array("Sugar",$others)?"checked":""):"").'><span class="checkmark"></span>
            </label>
            <label class="checkContainer">Tapioca
                <input type="checkbox" name="others[]" value="Tapioca" '.(!empty($others)?(in_array("Tapioca",$others)?"checked":""):"").'><span class="checkmark"></span>
            </label>
            <label class="checkContainer">Tomato (Tinned)
                <input type="checkbox" name="others[]" value="Tomato (Tinned)" '.(!empty($others)?(in_array("Tomato (Tinned)",$others)?"checked":""):"").'><span class="checkmark"></span> 
            </label>
            <label class="checkContainer">Vegetable Oil
                <input type="checkbox" name="others[]" value="Vegetable Oil" '.(!empty($others)?(in_array("Vegetable Oil",$others)?"checked":""):"").'><span class="checkmark"></span>
            </label>
            <label class="checkContainer">Vegemite
                <input type="checkbox" name="others[]" value="Vegemite" '.(!empty($others)?(in_array("Vegemite",$others)?"checked":""):"").'><span class="checkmark"></span>
            </label>
            <label class="checkContainer">Vinegar
                <input type="checkbox" name="others[]" value="Vinegar" '.(!empty($others)?(in_array("Vinegar",$others)?"checked":""):"").'><span class="checkmark"></span>
            </label>
            <label class="checkContainer">Yeast
                <input type="checkbox" name="others[]" value="Yeast" '.(!empty($others)?(in_array("Yeast",$others)?"checked":""):"").'><span class="checkmark"></span>
            </label>
        </div>
		
		<div class="allergen-group">
			<textarea name="remark" placeholder="Remark">'.(!empty($remark)?$remark:"").'</textarea> 			
		</div>
        <div class="submit-group">
        	<button type="submit" name="saveChildInfo">Save</button>
        </div>
	</div>
</div>';
return $rtForm;
?>