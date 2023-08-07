<?php
function mytheme_woo_add_custom_fields() {

	echo '<div class="options_group" style = "clear: both;">';

 	// Text Field
	woocommerce_wp_text_input(
		array(
			'id'          => '_ean_code_field',
			'label'       => __( 'EAN Code', 'woocommerce' ),
			'placeholder' => '',
			'desc_tip'    => true,
			'description' => __( "Add EAN Code Here.", "woocommerce" )
		)
 	);
	
	// Text Field
	woocommerce_wp_text_input(
		array(
			'id'          => '_mpn_code_field',
			'label'       => __( 'MPN (part number)', 'woocommerce' ),
			'placeholder' => '',
			'desc_tip'    => true,
			'description' => __( "Add MPN Code Here.", "woocommerce" )
		)
 	);

	// Dropdown Menu
	woocommerce_wp_select(
		array(
			'id'      => '_availability',
			'label'   => __( 'Delivery in', 'woocommerce' ),
			'options' => array(
				'1-2'   => __( '1-2 business days', 'woocommerce' ),
				'5-10'   => __( '5-10 business days', 'woocommerce' ),
				'10-15' => __( '10-15 business days', 'woocommerce' )
			)
		)
	);

 	echo '</div>';
}
add_action( 'woocommerce_product_options_general_product_data', 'mytheme_woo_add_custom_fields' );
add_action('woocommerce_product_quick_edit_end', 'mytheme_woo_add_custom_fields');

//SAVE
function mytheme_woo_add_custom_fields_save( $post_id ){

	// Text Field
	$woocommerce_text_field = $_POST['_ean_code_field'];
   update_post_meta( $post_id, '_ean_code_field', esc_attr( $woocommerce_text_field ) );
	
		// Text Field
	$woocommerce_text_field = $_POST['_mpn_code_field'];
   update_post_meta( $post_id, '_mpn_code_field', esc_attr( $woocommerce_text_field ) );

    // Dropdown Menu
    $woocommerce_select = $_POST['_availability'];
	update_post_meta( $post_id, '_availability', esc_attr( $woocommerce_select ) );
}
add_action( 'woocommerce_process_product_meta', 'mytheme_woo_add_custom_fields_save' );

// QUICK EDIT BLOCK CUSTOM FIELDS SAVE
function quick_edit_dropdown_field_save( $product ) {
    $post_id = $product->get_id();
    if ( isset( $_REQUEST['_availability'] ) ) {
        $custom_field = $_REQUEST['_availability'];
        update_post_meta( $post_id, '_availability', wc_clean( $custom_field ) );
    }
}
add_action( 'woocommerce_product_quick_edit_save', 'quick_edit_dropdown_field_save' );
 
function quick_edit_text_field_save( $product ) {
    $post_id = $product->get_id();
    if ( isset( $_REQUEST['_ean_code_field'] ) ) {
        $custom_field = $_REQUEST['_ean_code_field'];
        update_post_meta( $post_id, '_ean_code_field', wc_clean( $custom_field ) );
    }
	
	if ( isset( $_REQUEST['_mpn_code_field'] ) ) {
        $custom_field_mpn = $_REQUEST['_mpn_code_field'];
        update_post_meta( $post_id, '_mpn_code_field', wc_clean( $custom_field_mpn ) );
    }
	
}
add_action( 'woocommerce_product_quick_edit_save', 'quick_edit_text_field_save' );

/* -- UPDATE INPUT FIELD ON QUICK EDIT -- */
function bbloomer_show_custom_input_quick_edit_data( $column, $post_id ){
    if ( 'name' !== $column ) return;
    echo '<div style = "display:none;">Custom field: <span id="cf_' . $post_id . '">' . esc_html( get_post_meta( $post_id, '_ean_code_field', true ) ) . '</span></div>';
	echo '<div style = "display:none;">Custom field: <span id="cf2_' . $post_id . '">' . esc_html( get_post_meta( $post_id, '_mpn_code_field', true ) ) . '</span></div>';
   wc_enqueue_js( "
      $('#the-list').on('click', '.editinline', function() {
         var post_id = $(this).closest('tr').attr('id');
         post_id = post_id.replace('post-', '');
         var custom_field = $('#cf_' + post_id).text();
		 var custom_field_two = $('#cf2_' + post_id).text();
         $('input[name=\'_ean_code_field\']', '.inline-edit-row').val(custom_field);
		 $('input[name=\'_mpn_code_field\']', '.inline-edit-row').val(custom_field_two);
        });
   " );
}
add_action( 'manage_product_posts_custom_column', 'bbloomer_show_custom_input_quick_edit_data', 9998, 2 );
/* -- END UPDATE INPUT FIELD ON QUICK EDIT -- */

// DISPLAY EAN ON SINGLE PRODUCT PAGE
function mytheme_display_woo_custom_fields() {
	global $post;
	global $product;

	// Text Field
	$ourTextField = get_post_meta( $post->ID, '_ean_code_field', true );

	if ( !empty( $ourTextField ) ) {

		?>
			<div class = 'ean-code'>
				<p>EAN Code: <?php echo $ourTextField; ?></p>
			</div>
		<?php	
	}
}
add_action( 'woocommerce_single_product_summary', 'mytheme_display_woo_custom_fields', 16 );

function display_availability_text(){
	global $post;
	global $product;

	// Dropdown Menu
	$ourDropdownValue = get_post_meta( $post->ID, '_availability', true );

	//if($product->backorders_allowed()) { 
        if(!empty( $ourDropdownValue )){
			?>
				<div class = 'dropdown-value'>
					<p><strong><?php echo __('Delivery in','woocommerce') ?></strong> <?php echo $ourDropdownValue.' '. __('business days', 'woocommerce')?></p>
				</div>
			<?php	
		}
    //}
}
add_action( 'woocommerce_single_product_summary', 'display_availability_text', 17 );

//PISPLAY ON BACKEND PRODUCTS EDIT PAGE
function show_custom_field_quick_edit_data( $column, $post_id ){
    if ( 'name' !== $column ) return;
    echo '<div>Available in: <span id="cf_' . $post_id . '">' . esc_html( get_post_meta( $post_id, '_availability', true ) ) . '</span> days</div>';
}
add_action( 'manage_product_posts_custom_column', 'show_custom_field_quick_edit_data', 9999, 2 );