<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

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
	<?php if ( $this->has_header_logo() ) {
		echo ($PONo = $this->order->get_meta('PO Number')) ? 'tax ' . $this->get_title() . '/ Reciept' : '' . $this->get_title();
	} ?>
</h1>

<?php do_action( 'wpo_wcpdf_after_document_label', $this->get_type(), $this->order ); ?>

<table class="order-data-addresses">
	<tr>
		<td class="address billing-address">
			<!-- <h3><?php _e( 'Billing Address:', 'woocommerce-pdf-invoices-packing-slips' ); ?></h3> -->
			<?php do_action( 'wpo_wcpdf_before_billing_address', $this->get_type(), $this->order ); ?>
			<?php $this->billing_address(); ?>
			<?php do_action( 'wpo_wcpdf_after_billing_address', $this->get_type(), $this->order ); ?>
			<?php if ( isset( $this->settings['display_email'] ) ) : ?>
				<div class="billing-email"><?php $this->billing_email(); ?></div>
			<?php endif; ?>
			<?php if ( isset( $this->settings['display_phone'] ) ) : ?>
				<div class="billing-phone"><?php $this->billing_phone(); ?></div>
			<?php endif; ?>
			<?php if ( $customerTaxId = get_user_meta($this->order->get_user_id(), 'tax_id') ) : ?>
				<table>
					<tr class="order-tax">
						<th style="padding-right: 7px;"><?php _e( 'TAX ID:', 'woocommerce-pdf-invoices-packing-slips' ); ?></th>
						<td><?= $customerTaxId[0]; ?></td>
					</tr>
				</table>
			<?php endif; ?>
		</td>
		<td class="address shipping-address">
			<?php if ( $this->show_shipping_address() ) : ?>
				<h3><?php _e( 'Ship To:', 'woocommerce-pdf-invoices-packing-slips' ); ?></h3>
				<?php do_action( 'wpo_wcpdf_before_shipping_address', $this->get_type(), $this->order ); ?>
				<?php $this->shipping_address(); ?>
				<?php do_action( 'wpo_wcpdf_after_shipping_address', $this->get_type(), $this->order ); ?>
				<?php if ( isset( $this->settings['display_phone'] ) ) : ?>
					<div class="shipping-phone"><?php $this->shipping_phone(); ?></div>
				<?php endif; ?>
			<?php endif; ?>
		</td>
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

				<?php if ( $PONo = $this->order->get_meta('PO Number') ) : ?>
				<tr class="order-tax no-borders">
					<th class="no-borders"><?php _e( 'PO Number:', 'woocommerce-pdf-invoices-packing-slips' ); ?></th>
					<td class="no-borders"><? echo $PONo; ?></td>
				</tr>
				<?php endif; ?>

				<tr class="no-borders">
					<td class="no-borders">&nbsp;</td>
					<td class="no-borders">&nbsp;</td>
				</tr>
				
				<?php if ( $payment_method = 'Direct Bank Transfer' ) : ?>
					<td class="no-borders">
						<h3><?php _e( 'Payment Details:', 'woocommerce-pdf-invoices-packing-slips' ); ?></h3>
					</td>
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
			<th class="sku"><?php _e( 'SKU', 'woocommerce-pdf-invoices-packing-slips' ); ?></th>
			<th class="product"><?php _e( 'Product', 'woocommerce-pdf-invoices-packing-slips' ); ?></th>
			<th class="quantity"><?php _e( 'Quantity', 'woocommerce-pdf-invoices-packing-slips' ); ?></th>
			<th class="price"><?php _e( 'Price', 'woocommerce-pdf-invoices-packing-slips' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ( $this->get_order_items() as $item_id => $item ) : ?>
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
				<td class="quantity"><?php echo $item['quantity']; ?></td>
				<td class="price"><?php echo $item['order_price']; ?></td>
			</tr>
		<?php endforeach; ?>
	</tbody>
	<tfoot>
		<tr class="no-borders">
			<td class="no-borders">
				<div class="customer-notes">
					<?php do_action( 'wpo_wcpdf_before_customer_notes', $this->get_type(), $this->order ); ?>
					<?php if ( $this->get_shipping_notes() ) : ?>
						<h3><?php _e( 'Customer Notes', 'woocommerce-pdf-invoices-packing-slips' ); ?></h3>
						<?php $this->shipping_notes(); ?>
					<?php endif; ?>
					<?php do_action( 'wpo_wcpdf_after_customer_notes', $this->get_type(), $this->order ); ?>
				</div>				
			</td>
			<td class="no-borders"></td>
			<td class="no-borders" colspan="2">
				<table class="totals">
					<tfoot>
						<tr class="-1">
							<th class="description">Order Quantity</th>
							<td class="price"><span class="totals-price important-total"><?= count($this->get_order_items()) ?></span></td>
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

<?php if ( $PONo = $this->order->get_meta('PO Number') ) : ?>
	<div class="signature-container" style="position: relative;">
		<img class="signature-img" width="112px" height="auto" src="http://dbr.fts.mybluehost.me/wp-content/uploads/2023/11/Signature1.jpg"/>
		<table class="signature">
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
