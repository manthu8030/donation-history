<?php
/*
Plugin Name: Donatation History
Description: This plugin use for a list of donation history on my account page.
Version: 1.0
Author: Manthan Desai
*/


// Add new endpoint to WooCommerce
function custom_add_my_account_endpoint() {
    add_rewrite_endpoint( 'donation-endpoint', EP_PAGES ); // Change 'my-custom-endpoint' to your desired endpoint slug
}
add_action( 'init', 'custom_add_my_account_endpoint' );

// Add content to the new endpoint
function custom_donation() {
    $current_user_id = get_current_user_id();
	if ( $current_user_id ) {
		// User is logged in

		global $wpdb;

		$user_id = $current_user_id; // Your user ID

		// Custom query
		$query = $wpdb->prepare( "SELECT payment_ids FROM {$wpdb->prefix}give_donors WHERE user_id = %d", $user_id );

		// Execute query
		$payment_ids = $wpdb->get_var( $query );
		if ( $payment_ids !== null ) {
		// Output the payment_ids value
			$payment_id = explode(',',$payment_ids);
			
			?>
			<h3>Your Donation</h3>
			<table class="woocommerce-orders-table woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_orders account-orders-table">
				<thead>
					<tr>
						<th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-number"><span class="nobr">Donation ID</span></th>
						<th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-date"><span class="nobr">Name</span></th>
						<th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-status"><span class="nobr">Email</span></th>
						<th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-total"><span class="nobr">Amount</span></th>
					</tr>
				</thead>
				<tbody>
				<?php  
					foreach($payment_id as $key => $donation_id){
						
						?>
						<tr class="woocommerce-orders-table__row woocommerce-orders-table__row--status-processing order">
							<td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-number" data-title="Order"><?php echo $donation_id; ?></td>
							<td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-date" data-title="Date"><?php 
							
							$fname_query = $wpdb->prepare( "SELECT meta_value FROM {$wpdb->prefix}give_donationmeta WHERE donation_id = $donation_id AND meta_key = '_give_donor_billing_first_name'"); 
							$lname_query = $wpdb->prepare( "SELECT meta_value FROM {$wpdb->prefix}give_donationmeta WHERE donation_id = $donation_id AND meta_key = '_give_donor_billing_last_name'");
							
							$fname = $wpdb->get_var( $fname_query ); 
							$lname = $wpdb->get_var( $lname_query ); 
							echo $fname.' '.$lname;
							?></td>
							<td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-status" data-title="Status"><?php 
							$donor_email_query = $wpdb->prepare( "SELECT meta_value FROM {$wpdb->prefix}give_donationmeta WHERE donation_id = $donation_id AND meta_key = '_give_payment_donor_email'"); 
							$email = $wpdb->get_var( $donor_email_query ); 
							echo $email;
							?></td>
							<td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-total" data-title="Total">
											<span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">$</span><?php 
							$payment_total_query = $wpdb->prepare( "SELECT meta_value FROM {$wpdb->prefix}give_donationmeta WHERE donation_id = $donation_id AND meta_key = '_give_payment_total'"); 
							$payment_total = $wpdb->get_var( $payment_total_query ); 
							echo number_format($payment_total, 2);
							?></span>
							</td>
						</tr>
						<?php
					}
				?>
				</tbody>
			</table>
			<?php
		}else{
			?>
				<p class="shopengine-woocommerce-info">No donation has been made yet.
				</p>
			
			<?php
		}

	}
}
add_action( 'woocommerce_account_donation-endpoint_endpoint', 'custom_donation' );

// Add menu item for the new endpoint
function custom_add_my_account_menu_item( $items ) {
    $items['donation-endpoint'] = 'Donation'; // Change 'My Custom Endpoint' to your desired menu item label
    return $items;
}
add_filter( 'woocommerce_account_menu_items', 'custom_add_my_account_menu_item', 10);
