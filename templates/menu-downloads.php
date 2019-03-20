<?php
$args = array(
	'post_type' => 'downloadable_items',
	'meta_key' => 'type_category',
	'meta_value'   => 'menu_download'
);
$the_query = new WP_Query( $args );
if ( $the_query->have_posts() ) {
	echo '<ul class="our-certification">';
	while ( $the_query->have_posts() ) {
		$the_query->the_post();
		$featured_img_url = get_the_post_thumbnail_url(get_the_ID(),'thumbnail');
		$file = get_field('file'); 
		
		if($file) {
			echo '<li><a href='.$file["url"].'" target="_blank">';
			if($featured_img_url) {
				echo '<div class="list-column"><img src="'.$featured_img_url.'" /></div>';
			}
			else {
				echo '<div class="list-column"><img src="'.get_site_url().'/wp-includes/images/media/document.png" /></div>';				
			}
			echo '<div class="list-column"><span class="title">'.get_the_title().'</span><span class="description">'.get_the_content().'</span></div>';		
			
			echo '</a></li>';			
		}       
	}
	echo '</ul>';
	/* Restore original Post Data */
	wp_reset_postdata();
} else {
	echo '<p>'.esc_html_e( "No Data" ).'</p>';
}

?>