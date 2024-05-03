<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<?php date_default_timezone_set('Asia/Bangkok'); ?>

<?php if ( ( ! $this->order->get_new_order_email_sent() || isset( $_GET['invoice-packing-slip' ] ) ) && ! isset( $_GET[ 'tax-reciept'] ) ) {
	require_once __DIR__ . '/generate-qr.php';
} 

if( isset( $_GET['statement'] ) ) {
	$customer = 3;
	if(isset($_GET['user_id'])) {
		$customer = $_GET['user_id'];
	}

	// Get all customer orders
	$orders = wc_get_orders( array(
		'numberposts' => -1,
		'customer_id' => $customer, 
		// 'post_status' => array_keys(wc_get_order_statuses()), 'post_status' => array('wc-on-hold'),
		// 'status' => array('wc-processing', 'wc-on-hold'),
		// "completed"
		'orderby' => 'date',
		'order' => 'DESC',
	) );

	$total_due = 0;
	foreach ( $orders as $item_id => $order ) {
		$total_due += $order->get_total();

		$date = date_create();
		date_timestamp_set($date, $order->get_date_created()->getTimeStamp());
		date_modify($date, '+7 days');
		$date_7_days = date_timestamp_get($date);

		$date2 = date_create();
		date_timestamp_set($date2, $order->get_date_created()->getTimeStamp());
		date_modify($date2, '+37 days');
		$date_37_days = date_timestamp_get($date2);

		// $future_date = date_create("2024-01-10");
		// $future_date = date_timestamp_get($future_date);

		// todays date is 7 days more than the created date.
		if(time() <= $date_7_days) {
			$total_not_yet_overdue += $order->get_total();
		
		// todays date is 37 days more than the created date.
		} else if(time() > $date_7_days && time() <= $date_37_days) {
			$total_30_days_overdue += $order->get_total();

		// todays date is over 37 days more than the created date.
		} else if(time() > $date_37_days) {
			$total_90_days_overdue += $order->get_total();
		}
	}

	// not yet due total
	// $total_not_yet_overdue = created date < (created date + 7 days)

	// 1 - 30 days over
	// $total_30_days_overdue = created date > (created date + 37 days)

	// > 90 days over
	// $total_90_days_overdue = created date > (7 created date + 97 days)

} ?>
 
<?php do_action( 'wpo_wcpdf_before_document', $this->get_type(), $this->order ); ?>

<table class="head container">
	<tr>
		<td class="header">
		<?php
		if ( $this->has_header_logo() ) {
			do_action( 'wpo_wcpdf_before_shop_logo', $this->get_type(), $this->order );
			$this->header_logo();
			do_action( 'wpo_wcpdf_after_shop_logo', $this->get_type(), $this->order );
		} else {
			$this->title();
		}
		?>
		</td>
		<td class="shop-info">
			<?php do_action( 'wpo_wcpdf_before_shop_name', $this->get_type(), $this->order ); ?>
			<div class="shop-name"><h3><?php $this->shop_name(); ?></h3></div>
			<?php do_action( 'wpo_wcpdf_after_shop_name', $this->get_type(), $this->order ); ?>
			<?php do_action( 'wpo_wcpdf_before_shop_address', $this->get_type(), $this->order ); ?>
			<div class="shop-address"><?php $this->shop_address(); ?></div>
			<?php do_action( 'wpo_wcpdf_after_shop_address', $this->get_type(), $this->order ); ?>
			<p><strong>Tel:</strong> (+66) 64 62 444 00</p>
			<table>
				<tr class="order-tax">
					<th style="padding-right: 7px;"><?php _e( 'TAX ID:', 'woocommerce-pdf-invoices-packing-slips' ); ?></th>
					<td><?= "0205558021126"; ?></td>
				</tr>
			</table>
		</td>
	</tr>
</table>

<?php do_action( 'wpo_wcpdf_before_document_label', $this->get_type(), $this->order ); ?>

<h1 class="document-type-label">
	<?php if(isset($_GET['statement'])) { 
		echo 'Statement of Account';
	} else if ( isset($_GET['tax-reciept'])) {
		echo 'tax ' . $this->get_title() . '/ Reciept';
	} else {
		echo $this->get_title();
	} ?>
</h1>

<?php do_action( 'wpo_wcpdf_after_document_label', $this->get_type(), $this->order ); ?>

<table class="order-data-addresses">
	<tr>
		<?php if ( isset($_GET['statement']) ) : ?>
			<td class="address billing-address">
				<!-- <h3><?php _e( 'Billing Address:', 'woocommerce-pdf-invoices-packing-slips' ); ?></h3> -->	
				<!-- address -->
				<?php
				$fname = $this->order->get_billing_first_name();
				$lname = $this->order->get_billing_last_name();
				$billing_company = $this->order->get_billing_company();
				?>
				<?= (isset($billing_company)) ? $billing_company  : $fname . ' ' . $lname; ?><br>
				<?php 
				echo $this->order->get_billing_address_1() . '<br>';
				echo ($address_2 = $this->order->get_billing_address_2()) ? $address_2 . '<br>' : '';
				echo $this->order->get_billing_city() . '<br>';
				$country = $this->order->get_billing_country();
				$state = $this->order->get_billing_state();
				$wc_state_name = WC()->countries->get_states( $country )[$state];
				$state_name = !empty($wc_state_name) ? $wc_state_name : $state;
				echo $state_name . '<br>';
				echo $this->order->get_billing_postcode();
				?>
				<!-- email -->
				<div class="billing-email"><?= get_user_meta($customer, 'billing_email', true); ?>, <?= get_user_meta($customer, 'billing_phone', true); ?></div>
				<!-- phone -->
				<!-- <div class="billing-phone"></div> -->
			</td>
		<?php else : ?>
		
			<td class="address billing-address halloo" style="line-height: 9px;">
				<!-- <h3><?php _e( 'Billing Address:', 'woocommerce-pdf-invoices-packing-slips' ); ?></h3> -->
				<?php do_action( 'wpo_wcpdf_before_billing_address', $this->get_type(), $this->order ); ?>
				

				<?php if( isset($_GET['tax-reciept']) ) { 
					echo $this->order->get_billing_company() . '<br>';
					echo $this->order->get_billing_address_1() . '<br>';
					echo ($address_2 = $this->order->get_billing_address_2()) ? $address_2 . '<br>' : '';
					echo $this->order->get_billing_city() . '<br>';
					$country = $this->order->get_billing_country();
					$state = $this->order->get_billing_state();
					$wc_state_name = WC()->countries->get_states( $country )[$state];
					$state_name = !empty($wc_state_name) ? $wc_state_name : $state;
					echo $state_name . '<br>';
					echo $this->order->get_billing_postcode();
				} else { 
					$this->billing_address();
				} ?>

				<?php do_action( 'wpo_wcpdf_after_billing_address', $this->get_type(), $this->order ); ?>
				<?php if ( !empty(get_user_meta($this->order->get_user_id(), 'tax_id', true)) && !isset($_GET['statement']) ) : ?>
					<div>
						<p>
							<strong style="margin-right: 8px;"><?php _e( 'TAX ID:', 'woocommerce-pdf-invoices-packing-slips' ); ?></strong>
							<?= get_user_meta($this->order->get_user_id(), 'tax_id', true); ?>
						</p>
					</div>			
				<?php endif; ?>
				<?php if ( isset( $this->settings['display_email'] ) ) : ?>
					<div class="billing-email"><?php $this->billing_email(); ?>, <?php if( isset( $this->settings['display_phone'] ) ) { $this->billing_phone(); } ?></div>
				<?php endif; ?>
			</td>
			<td class="address shipping-address">
				<?php if ( $this->show_shipping_address() && !isset($_GET['tax-reciept']) ) : ?>
					<h3><?php _e( 'Ship To:', 'woocommerce-pdf-invoices-packing-slips' ); ?></h3>
					<?php do_action( 'wpo_wcpdf_before_shipping_address', $this->get_type(), $this->order ); ?>
					<?php $this->shipping_address(); ?>
					<?php do_action( 'wpo_wcpdf_after_shipping_address', $this->get_type(), $this->order ); ?>
					<?php if ( isset( $this->settings['display_phone'] ) ) : ?>
						<div class="shipping-phone"><?php $this->shipping_phone(); ?></div>
					<?php endif; ?>
				<?php endif; ?>
			</td>
		<?php endif; ?>
		<?php if (isset($_GET['statement'])) { ?>
			<td class="account-summary" style="width: 35%;">
				<h3><?php _e( 'Account Summary:', 'woocommerce-pdf-invoices-packing-slips' ); ?></h3>
				<p>Generated on <?= date("j F Y") ?></p>
				<table style="width: 100%; margin-top: 5mm; table-layout: fixed;">
					<tr class="not-yet-due">
						<th>Not yet due:</th>
						<td><?= wc_price($total_not_yet_overdue) ?></td>
					</tr>
					<tr class="30-days-due">
						<th>1-30 days overdue:</th>
						<td><?= wc_price($total_30_days_overdue) ?></td>
					</tr>
					<tr class="90-days-due">
						<th>+30 days overdue:</th>
						<td><?= wc_price($total_90_days_overdue) ?></td>
					</tr>
					<tr class="total">
						<th>Total Due:</th>
						<td><?= wc_price($total_due) ?></td>	
					</tr>
				</table>
			</td>
		<?php } else { ?>
		<td class="order-data">
			<table>
				<?php do_action( 'wpo_wcpdf_before_order_data', $this->get_type(), $this->order ); ?>
				<?php if ( isset( $this->settings['display_number'] ) ) : ?>
					<tr class="invoice-number no-borders">
						<th class="no-borders"><?php echo $this->get_number_title(); ?></th>
						<td class="no-borders"><?php $this->invoice_number(); ?></td>
					</tr>
				<?php endif; ?>
				<?php if ( isset( $this->settings['display_date'] ) ) : ?>
					<tr class="invoice-date">
						<th><?php echo $this->get_date_title(); ?></th>
						<td><?php $this->invoice_date(); ?></td>
					</tr>
				<?php endif; ?>
				<tr class="order-number">
					<th><?php _e( 'Order Number:', 'woocommerce-pdf-invoices-packing-slips' ); ?></th>
					<td><?php $this->order_number(); ?></td>
				</tr>
				<tr class="order-date">
					<th><?php _e( 'Order Date:', 'woocommerce-pdf-invoices-packing-slips' ); ?></th>
					<td><?php $this->order_date(); ?></td>
				</tr>
				<?php if ( $payment_status = $this->order->status ) : ?>
				<tr class="payment-method">
					<th><?php _e( 'Payment Status:', 'woocommerce-pdf-invoices-packing-slips' ); ?></th>
					<td><?php echo $payment_status; ?></td>
				</tr>
				<?php endif; ?>
				<!-- Customer Email -->
				<?php if ( isset( $this->settings['display_email'] ) ) : ?>
					<tr class="payment-method">
						<th><?php _e( 'Customer Email:', 'woocommerce-pdf-invoices-packing-slips' ); ?></th>
						<td><?php $this->billing_email(); ?></td>
					</tr>
				<?php endif; ?>
				<?php if ( $trackingNo = $this->order->get_meta('Tracking Number') ) : ?>
				<tr class="order-tax">
					<th><?php _e( 'Tracking Number:', 'woocommerce-pdf-invoices-packing-slips' ); ?></th>
					<td><? echo $trackingNo; ?></td>
				</tr>
				<?php endif; ?>

				<?php if ( $PONo = $this->order->get_meta('_billing_po_number') ) : ?>
				<tr class="order-tax no-borders">
					<th class="no-borders"><?php _e( 'PO Number:', 'woocommerce-pdf-invoices-packing-slips' ); ?></th>
					<td class="no-borders"><? echo $PONo; ?></td>
				</tr>
				<?php endif; ?>

				<tr class="no-borders">
					<td class="no-borders">&nbsp;</td>
					<td class="no-borders">&nbsp;</td>
				</tr>
				
				<?php if ( $payment_method === 'Direct Bank Transfer' && !isset($_GET['statement'])) : ?>
					<tr class="no-borders">
						<td colspan="2">
							<h3><?php _e( 'Payment Details:', 'woocommerce-pdf-invoices-packing-slips' ); ?></h3>
						</td>
					</tr>
					<tr class="order-payment">
						<th>Account Name:</th>
						<td>VENDIS Co., LTD</td>
					</tr>
					<tr class="order-payment">
						<th>Bank Branch:</th>
						<td>SCB Bank</td>
					</tr>
					<tr class="order-payment">
						<th>Account no.:</th>
						<td>863 252140 4</td>
					</tr>
				<?php elseif ($qr_code_img_url = wp_make_link_relative( $this->order->get_meta('qr_code_img_url') ) ) : ?>	
					<tr class="order-payment">
						<td style="vertical-align: bottom;">
							<p style="line-height:8pt; font-size:7pt; margin-bottom: 8px;" class="scantxt">To complete your order, please scan the QR code with your Banking app to transfer your payment.</p>	
						</td>
						<td>
							<img src="<?= ABSPATH . 'wp-content/uploads/2024/01/prompt-pay.jpg'?>" style="width: 50px;display: block; margin: auto; margin-left: 16px;">
							<img style="display: block;width: 55px; margin:auto; margin-left: 16px; margin-bottom: 1em;" src="<?= ABSPATH . $qr_code_img_url; ?>">						
						</td>
					</tr>
				<?php endif; ?>

				<?php do_action( 'wpo_wcpdf_after_order_data', $this->get_type(), $this->order ); ?>
			</table>			
		</td>
		<?php } ?>
	</tr>
</table>

<?php do_action( 'wpo_wcpdf_before_order_details', $this->get_type(), $this->order ); ?>

<?php if(isset($_GET['statement'])) { ?>

	<table class="order-details">
		<thead>
			<tr>
				<th colspan="6" style="background-color:white; font-size: 7pt; color: #222; border: 0 none; text-align: center;">
					Showing all outstanding invoices to date.
				</th>
			</tr>
			<tr>
				<th class=""><?php _e( 'Date', 'woocommerce-pdf-invoices-packing-slips' ); ?></th>
				<th class=""><?php _e( 'Due Date', 'woocommerce-pdf-invoices-packing-slips' ); ?></th>
				<th class=""><?php _e( 'Details', 'woocommerce-pdf-invoices-packing-slips' ); ?></th>
				<th class="price"><?php _e( 'Amount', 'woocommerce-pdf-invoices-packing-slips' ); ?></th>
				<th class="price"><?php _e( 'Total Paid', 'woocommerce-pdf-invoices-packing-slips' ); ?></th>
				<th class="price"><?php _e( 'Amount Due', 'woocommerce-pdf-invoices-packing-slips' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ( $orders as $item_id => $order ) : 
				
				$exclude_status =  array('completed');
				if( in_array($order->status, $exclude_status) ) {
					continue;
				}

				?>
				<tr class="wpo_wcpdf_item_row_class">
					<td class="">
						<?= $order->get_date_created()->format('j F Y') ?>
					</td>
					<td class="">
						<?= $order->get_date_created()->modify('+7 days')->format('j F Y') ?>
					</td>
					<td class="">
						<?= '#' . $order->get_meta('_wcpdf_invoice_number') ?> <?=' | ' . $order->status ?>
					</td>
					<td class="price">
						<?= $order->get_formatted_order_total() ?>
					</td>
					<td class="price">
						<?= '0' ?>
					</td>
					<td class="price">
						<?= $order->get_formatted_order_total() ?>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
		<tfoot>
			<tr class="no-borders">
				<td class="no-borders">		
				</td>
				<td class="no-borders"></td>
				<td class="no-borders"></td>
				<td class="no-borders"></td>
				<td class="no-borders" colspan="2">
					<table class="totals">
						<tfoot>
							<tr class="-1">
								<th class="description">Total Amount Due</th>
								<td class="price">
									<span class="totals-price important-total">
										<?= wc_price($total_due) ?>
									</span>
								</td>
							</tr>
						</tfoot>
					</table>
				</td>
			</tr>
		</tfoot>
	</table>

<?php } else { ?>

	<table class="order-details">
		<thead>
			<tr>
				<th class="sku"><?php _e( 'SKU', 'woocommerce-pdf-invoices-packing-slips' ); ?></th>
				<th class="product"><?php _e( 'Product', 'woocommerce-pdf-invoices-packing-slips' ); ?></th>
				<th class="quantity"><?php _e( 'Cost', 'woocommerce-pdf-invoices-packing-slips' ); ?></th>
				<th class="quantity" style="width: 7%;"><?php _e( 'Qty', 'woocommerce-pdf-invoices-packing-slips' ); ?></th>
				<th class="price"><?php _e( 'Total', 'woocommerce-pdf-invoices-packing-slips' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php $total_quantity = 0; ?>
			<?php foreach ( $this->get_order_items() as $item_id => $item ) : 
			$total_quantity += (int)$item['quantity'];
			?>
				<tr class="<?php echo apply_filters( 'wpo_wcpdf_item_row_class', 'item-'.$item_id, esc_attr( $this->get_type() ), $this->order, $item_id ); ?>">
					<td class="sku">
						<?php if ( ! empty( $item['sku'] ) ) :
							echo esc_attr( $item['sku'] );
						endif; ?>
					</td>
					<td class="product">
						<?php $description_label = __( 'Description', 'woocommerce-pdf-invoices-packing-slips' ); // registering alternate label translation ?>
						<span class="item-name"><?php echo $item['name']; ?></span>
					</td>
					<td class="quantity" style="width: 13.5%;"><?= $item['ex_single_price']  ?></td>
					<td class="quantity" style="width: 7%;"><?php echo $item['quantity']; ?></td>
					<td class="price" style="width: 12%;"><?php echo $item['order_price']; ?></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
		<tfoot>
			<tr class="no-borders">
				<td class="no-borders">
					<!-- customer notes -->
				</td>
				<td class="no-borders"></td>
				<td class="no-borders" colspan="3">
					<table class="totals">
						<tfoot>
							<tr class="-1">
								<th class="description">Order Quantity</th>
								<td class="price"><span class="totals-price important-total"><?= $total_quantity ?></span></td>
							</tr>
							<?php foreach ( $this->get_woocommerce_totals() as $key => $total ) : ?>
								<tr class="<?php echo esc_attr( $key ); ?>">
									<th class="description <?= ($key === 'order_total') ? 'important-total' : '' ?>"><?php echo $total['label']; ?></th>
									<td class="price"><span class="totals-price <?= ($key === 'order_total') ? 'important-total' : '' ?>"><?php echo $total['value']; ?></span></td>
								</tr>
							<?php endforeach; ?>
						</tfoot>
					</table>
				</td>
			</tr>
		</tfoot>
	</table>
<?php } ?>

<?php if ( isset($_GET['tax-reciept']) && ! isset($_GET['statement']) ) : ?>
	<div class="signature-container" style="position: relative;">
		<img class="signature-img" width="125" height="auto" src="http://dbr.fts.mybluehost.me/wp-content/uploads/2023/11/Signature1.jpg"/>
		<table class="signature">
			<tr>
				<td style="border-bottom: 1px dotted;">Authorized Signature</td>
			</tr>
			<tr class="date">
				<td><?php $this->invoice_date(); ?></td>
			</tr>
		</table>
	</div>
<?php endif; ?>

<div class="bottom-spacer"></div>

<?php do_action( 'wpo_wcpdf_after_order_details', $this->get_type(), $this->order ); ?>

<?php if ( $this->get_footer() ) : ?>
	<div id="footer">
		<!-- hook available: wpo_wcpdf_before_footer -->
		<?php $this->footer(); ?>
		<!-- hook available: wpo_wcpdf_after_footer -->
	</div><!-- #letter-footer -->
<?php endif; ?>

<?php do_action( 'wpo_wcpdf_after_document', $this->get_type(), $this->order ); ?>

<?php 

if ( ( ! $this->order->get_new_order_email_sent() || isset( $_GET['invoice-packing-slip' ] ) ) && ! isset( $_GET[ 'tax-reciept'] ) ) : ?>

<div style="break-before: page; page-break-before: always;">

	<?php do_action( 'wpo_wcpdf_before_document', $this->get_type(), $this->order ); ?>

	<table class="head container">
		<tr>
			<td class="header">
			<?php
			if ( $this->has_header_logo() ) {
				do_action( 'wpo_wcpdf_before_shop_logo', $this->get_type(), $this->order );
				$this->header_logo();
				do_action( 'wpo_wcpdf_after_shop_logo', $this->get_type(), $this->order );
			} else {
				$this->title();
			}
			?>
			</td>
			<td class="shop-info">
				<?php do_action( 'wpo_wcpdf_before_shop_name', $this->get_type(), $this->order ); ?>
				<div class="shop-name"><h3><?php $this->shop_name(); ?></h3></div>
				<?php do_action( 'wpo_wcpdf_after_shop_name', $this->get_type(), $this->order ); ?>
				<?php do_action( 'wpo_wcpdf_before_shop_address', $this->get_type(), $this->order ); ?>
				<div class="shop-address"><?php $this->shop_address(); ?></div>
				<?php do_action( 'wpo_wcpdf_after_shop_address', $this->get_type(), $this->order ); ?>
				<p><strong>Tel:</strong> (+66) 64 62 444 00</p>
			</td>
		</tr>
	</table>

	<?php do_action( 'wpo_wcpdf_before_document_label', $this->get_type(), $this->order ); ?>

	<h1 class="document-type-label" style="margin-top: 2em;">
		<?= "packing slip" ?>
	</h1>

	<?php do_action( 'wpo_wcpdf_after_document_label', $this->get_type(), $this->order ); ?>

	<table class="order-data-addresses">
		<tr>
			<?php if($location_qr_code) { ?>
				<td class="address billing-address" style="text-align:center; width: 80px;">
					<img src="<?= $location_qr_code ?>" width="80" height="80" style="border: 1px solid;border-radius: 5px;padding: 0px; margin-right:22.5px;"/>
					<h3 style="font-size: 10px;max-width: 80px;text-align: center;margin: auto; margin-top:1em;line-height: 130%; margin-right: 22.5px;">Scan to open in Google Maps</h3>
					<!-- สแกนเพื่อเปิดใน Google Maps -->
				</td>
			<?php } ?>
			<td class="address shipping-address">
				<!-- <h3><?php _e( 'Shipping Address:', 'woocommerce-pdf-invoices-packing-slips' ); ?></h3> -->
				<?php do_action( 'wpo_wcpdf_before_shipping_address', $this->get_type(), $this->order ); ?>
				<?php $this->shipping_address(); ?>
				<?php do_action( 'wpo_wcpdf_after_shipping_address', $this->get_type(), $this->order ); ?>
				</br>
				<?php if( isset( $this->settings['display_phone'] ) ) { $this->billing_phone(); } ?>
			</td>
			<td class="order-data">
				<table>
					<?php do_action( 'wpo_wcpdf_before_order_data', $this->get_type(), $this->order ); ?>
					<tr class="order-number">
						<th><?php _e( 'Order Number:', 'woocommerce-pdf-invoices-packing-slips' ); ?></th>
						<td><?php $this->order_number(); ?></td>
					</tr>
					<tr class="order-date">
						<th><?php _e( 'Order Date:', 'woocommerce-pdf-invoices-packing-slips' ); ?></th>
						<td><?php $this->order_date(); ?></td>
					</tr>
					<?php if ( $shipping_method = $this->get_shipping_method() ) : ?>
					<tr class="shipping-method">
						<th><?php _e( 'Shipping Method:', 'woocommerce-pdf-invoices-packing-slips' ); ?></th>
						<td><?php echo $shipping_method; ?></td>
					</tr>
					<?php endif; ?>
					<?php do_action( 'wpo_wcpdf_after_order_data', $this->get_type(), $this->order ); ?>
				</table>			
			</td>
		</tr>
	</table>

	<?php do_action( 'wpo_wcpdf_before_order_details', $this->get_type(), $this->order ); ?>

	<table class="order-details">
		<thead>
			<tr>
				<th class="product"><?php _e( 'Product', 'woocommerce-pdf-invoices-packing-slips' ); ?></th>
				<th class="quantity"><?php _e( 'Quantity', 'woocommerce-pdf-invoices-packing-slips' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ( $this->get_order_items() as $item_id => $item ) : ?>
				<tr class="<?php echo apply_filters( 'wpo_wcpdf_item_row_class', 'item-'.$item_id, esc_attr( $this->get_type() ), $this->order, $item_id ); ?>">
					<td class="product">
						<?php $description_label = __( 'Description', 'woocommerce-pdf-invoices-packing-slips' ); // registering alternate label translation ?>
						<span class="item-name"><?php echo $item['name']; ?></span>
						<?php do_action( 'wpo_wcpdf_before_item_meta', $this->get_type(), $item, $this->order  ); ?>
						<span class="item-meta"><?php echo $item['meta']; ?></span>
						<dl class="meta">
							<?php $description_label = __( 'SKU', 'woocommerce-pdf-invoices-packing-slips' ); // registering alternate label translation ?>
							<?php if ( ! empty( $item['sku'] ) ) : ?><dt class="sku"><?php _e( 'SKU:', 'woocommerce-pdf-invoices-packing-slips' ); ?></dt><dd class="sku"><?php echo esc_attr( $item['sku'] ); ?></dd><?php endif; ?>
							<?php if ( ! empty( $item['weight'] ) ) : ?><dt class="weight"><?php _e( 'Weight:', 'woocommerce-pdf-invoices-packing-slips' ); ?></dt><dd class="weight"><?php echo esc_attr( $item['weight'] ); ?><?php echo esc_attr( get_option( 'woocommerce_weight_unit' ) ); ?></dd><?php endif; ?>
						</dl>
						<?php do_action( 'wpo_wcpdf_after_item_meta', $this->get_type(), $item, $this->order  ); ?>
					</td>
					<td class="quantity"><?php echo $item['quantity']; ?></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<div class="bottom-spacer"></div>

	<?php do_action( 'wpo_wcpdf_after_order_details', $this->get_type(), $this->order ); ?>

	<?php do_action( 'wpo_wcpdf_before_customer_notes', $this->get_type(), $this->order ); ?>

	<div class="customer-notes">
		<?php if ( $this->get_shipping_notes() ) : ?>
			<h3><?php _e( 'Customer Notes', 'woocommerce-pdf-invoices-packing-slips' ); ?></h3>
			<?php $this->shipping_notes(); ?>
		<?php endif; ?>
	</div>

	<?php do_action( 'wpo_wcpdf_after_customer_notes', $this->get_type(), $this->order ); ?>

	<?php if ( $this->get_footer() ) : ?>
		<div id="footer">
			<!-- hook available: wpo_wcpdf_before_footer -->
			<?php $this->footer(); ?>
			<!-- hook available: wpo_wcpdf_after_footer -->
		</div><!-- #letter-footer -->
	<?php endif; ?>

</div>

<?php endif; // get_new_order_email_sent ?>