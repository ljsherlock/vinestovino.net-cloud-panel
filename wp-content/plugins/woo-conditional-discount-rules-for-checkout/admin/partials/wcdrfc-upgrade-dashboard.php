<?php
/**
 * Handles free plugin user dashboard
 * 
 * @package Woocommerce_Dynamic_Pricing_And_Discount_Pro
 * @since   2.4.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
require_once( plugin_dir_path( __FILE__ ) . 'header/plugin-header.php' );
global $wcpffc_fs;
?>
	<div class="wcpfc-section-left">
		<div class="dotstore-upgrade-dashboard">
			<div class="premium-benefits-section">
				<h2><?php esc_html_e( 'Go Premium to Increase Profitability', 'woo-conditional-discount-rules-for-checkout' ); ?></h2>
				<p><?php esc_html_e( 'Three Benefits for Upgrading to Premium', 'woo-conditional-discount-rules-for-checkout' ); ?></p>
				<div class="premium-features-boxes">
					<div class="feature-box">
						<span><?php esc_html_e('01', 'woo-conditional-discount-rules-for-checkout'); ?></span>
						<h3><?php esc_html_e('Generate revenue', 'woo-conditional-discount-rules-for-checkout'); ?></h3>
						<p><?php esc_html_e('Optimize revenue by providing attractive discounts to customers.', 'woo-conditional-discount-rules-for-checkout'); ?></p>
					</div>
					<div class="feature-box">
						<span><?php esc_html_e('02', 'woo-conditional-discount-rules-for-checkout'); ?></span>
						<h3><?php esc_html_e('Better Sales', 'woo-conditional-discount-rules-for-checkout'); ?></h3>
						<p><?php esc_html_e('Set any number of discounts rules for your WooCommerce store', 'woo-conditional-discount-rules-for-checkout'); ?></p>
					</div>
					<div class="feature-box">
						<span><?php esc_html_e('03', 'woo-conditional-discount-rules-for-checkout'); ?></span>
						<h3><?php esc_html_e('Increase conversions', 'woo-conditional-discount-rules-for-checkout'); ?></h3>
						<p><?php esc_html_e('Provide seasonal discounts to get more conversions like BOGO, Bulk discounts, etc.', 'woo-conditional-discount-rules-for-checkout'); ?></p>
					</div>
				</div>
			</div>
			<div class="premium-benefits-section unlock-premium-features">
				<p><span><?php esc_html_e( 'Unlock Premium Features', 'woo-conditional-discount-rules-for-checkout' ); ?></span></p>
				<div class="premium-features-boxes">
					<div class="feature-box">
						<h3><?php esc_html_e('Conditional Discount Rule', 'woo-conditional-discount-rules-for-checkout'); ?></h3>
						<span><i class="fa fa-cogs"></i></span>
						<p><?php esc_html_e('Easily apply bulk discounts based on product type, attribute, or category. Boost sales store-wide with customizable offers.', 'woo-conditional-discount-rules-for-checkout'); ?></p>
						<div class="feature-explanation-popup-main">
							<div class="feature-explanation-popup-outer">
								<div class="feature-explanation-popup-inner">
									<div class="feature-explanation-popup">
										<span class="dashicons dashicons-no-alt popup-close-btn" title="<?php esc_attr_e('Close', 'woo-conditional-discount-rules-for-checkout'); ?>"></span>
										<div class="popup-body-content">
											<div class="feature-image">
												<img src="<?php echo esc_url( WDPAD_PLUGIN_URL . 'admin/images/pro-features-img/feature-box-img-1.png' ); ?>" alt="<?php echo esc_attr('Conditional Discount Rule', 'woo-conditional-discount-rules-for-checkout'); ?>">
											</div>
											<div class="feature-content">
												<p><?php esc_html_e('With our plugin, you have the freedom to set up advanced fee rules using category, country, coupon code, and more. Enjoy the flexibility and enhance your customers\' shopping experience!', 'woo-conditional-discount-rules-for-checkout'); ?></p>
												<ul>
													<li><?php esc_html_e('Shop in India and save big on "Product 1" with our exclusive bulk discount offer. Purchase 5 or more units of "Product 1," and the discounted prices will be automatically applied at checkout.', 'woo-conditional-discount-rules-for-checkout'); ?></li>
													<li><?php esc_html_e('Purchase 5 or more shirts, set your location to India, and experience automatic discounts at checkout', 'woo-conditional-discount-rules-for-checkout'); ?></li>
												</ul>
											</div>
										</div>
									</div>		
								</div>
							</div>
						</div>
					</div>
					<div class="feature-box">
						<h3><?php esc_html_e('Location-Based Discount', 'woo-conditional-discount-rules-for-checkout'); ?></h3>
						<span><i class="fa fa-location-arrow"></i></span>
						<p><?php esc_html_e('Targeted discounts: Set custom rules based on location for personalized shopping experiences.', 'woo-conditional-discount-rules-for-checkout'); ?></p>
						<div class="feature-explanation-popup-main">
							<div class="feature-explanation-popup-outer">
								<div class="feature-explanation-popup-inner">
									<div class="feature-explanation-popup">
										<span class="dashicons dashicons-no-alt popup-close-btn" title="<?php esc_attr_e('Close', 'woo-conditional-discount-rules-for-checkout'); ?>"></span>
										<div class="popup-body-content">
                                            <div class="feature-image">
												<img src="<?php echo esc_url( WDPAD_PLUGIN_URL . 'admin/images/pro-features-img/feature-box-img-2.png' ); ?>" alt="<?php echo esc_attr('Location-Based Discount', 'woo-conditional-discount-rules-for-checkout'); ?>">
											</div>
											<div class="feature-content">
												<p><?php esc_html_e('Set customized discounts based on customer location, creating personalized shopping experiences. Maximize conversions and delight customers with targeted pricing strategies.', 'woo-conditional-discount-rules-for-checkout'); ?></p>
												<ul>
													<li><?php esc_html_e('To take advantage of the exclusive discounts for customers in "India", simply choose India as your shipping country during checkout. Our system will automatically apply the special discounted prices to your order.', 'woo-conditional-discount-rules-for-checkout'); ?></li>
													<li><?php esc_html_e('Get an extra discount as customers from "Gujarat" state. Select Gujarat as your shipping state at checkout, and our system will automatically apply exclusive discounted prices.', 'woo-conditional-discount-rules-for-checkout'); ?></li>
												</ul>
											</div>
										</div>
									</div>		
								</div>
							</div>
						</div>
					</div>
					<div class="feature-box">
						<h3><?php esc_html_e('User Role-Based Discount', 'woo-conditional-discount-rules-for-checkout'); ?></h3>
						<span><i class="fa fa-user"></i></span>
						<p><?php esc_html_e('Set conditional product fees based on user roles such as consumer, seller, shop manager, premium customer, and more.', 'woo-conditional-discount-rules-for-checkout'); ?></p>
						<div class="feature-explanation-popup-main">
							<div class="feature-explanation-popup-outer">
								<div class="feature-explanation-popup-inner">
									<div class="feature-explanation-popup">
										<span class="dashicons dashicons-no-alt popup-close-btn" title="<?php esc_attr_e('Close', 'woo-conditional-discount-rules-for-checkout'); ?>"></span>
										<div class="popup-body-content">
											<div class="feature-image">
												<img src="<?php echo esc_url( WDPAD_PLUGIN_URL . 'admin/images/pro-features-img/feature-box-img-3.png' ); ?>" alt="<?php echo esc_attr('User Role-Based Discount', 'woo-conditional-discount-rules-for-checkout'); ?>">
											</div>
											<div class="feature-content">
												<p><?php esc_html_e('It is easy to apply charges based on customer types. Set different delivery charges for consumers, sellers, shop managers, and premium customers.', 'woo-conditional-discount-rules-for-checkout'); ?></p>
												<ul>
													<li><?php esc_html_e('Enjoy an exclusive $20 discount on all your orders. Simply log in as a Vendor, and the discount will be automatically applied at checkout.', 'woo-conditional-discount-rules-for-checkout'); ?></li>
													<li><?php esc_html_e('Enjoy a generous $50 discount on all orders. Simply log in as a customer, and the discount will be automatically applied at checkout. ', 'woo-conditional-discount-rules-for-checkout'); ?></li>
												</ul>
											</div>
										</div>
									</div>		
								</div>
							</div>
						</div>
					</div>
					<div class="feature-box">
						<h3><?php esc_html_e('Percentage Discount On Product Quantity', 'woo-conditional-discount-rules-for-checkout'); ?></h3>
						<span><i class="fa fa-percent"></i></span>
						<p><?php esc_html_e('Unlock personalized discounts with our conditional percentage discount feature', 'woo-conditional-discount-rules-for-checkout'); ?></p>
						<div class="feature-explanation-popup-main">
							<div class="feature-explanation-popup-outer">
								<div class="feature-explanation-popup-inner">
									<div class="feature-explanation-popup">
										<span class="dashicons dashicons-no-alt popup-close-btn" title="<?php esc_attr_e('Close', 'woo-conditional-discount-rules-for-checkout'); ?>"></span>
										<div class="popup-body-content">
											<div class="feature-image">
												<img src="<?php echo esc_url( WDPAD_PLUGIN_URL . 'admin/images/pro-features-img/feature-box-img-4.png' ); ?>" alt="<?php echo esc_attr('Percentage Discount On Product Quantity', 'woo-conditional-discount-rules-for-checkout'); ?>">
											</div>
											<div class="feature-content">
												<p><?php echo sprintf( esc_html__('Unlock percentage discounts with flexible conditional rules. Personalize offers based on product\'s quantity, user details, and more.', 'woo-conditional-discount-rules-for-checkout'), 2 ); ?></p>
												<ul>
													<li><?php esc_html_e('Shop "Product 1" now and enjoy a 10% discount when purchasing 5 or more units. Save big on bulk orders and stock up on your favorite items!', 'woo-conditional-discount-rules-for-checkout'); ?></li>
													<li><?php echo sprintf( esc_html__('Easy to apply user-specific percentage fees: a %d%% fee for shop managers or a %d%% fee for members.', 'woo-conditional-discount-rules-for-checkout'), 3, 2 ); ?></li>
												</ul>
											</div>
										</div>
									</div>		
								</div>
							</div>
						</div>
					</div>
					<div class="feature-box">
						<h3><?php esc_html_e('Buy One, Get One Discount!', 'woo-conditional-discount-rules-for-checkout'); ?></h3>
						<span><i class="fa fa-plus-circle"></i></span>
						<p><?php esc_html_e('Purchase a specified quantity of a product (buy range) and receive a certain quantity for free (get specific quantity)', 'woo-conditional-discount-rules-for-checkout'); ?></p>
						<div class="feature-explanation-popup-main">
							<div class="feature-explanation-popup-outer">
								<div class="feature-explanation-popup-inner">
									<div class="feature-explanation-popup">
										<span class="dashicons dashicons-no-alt popup-close-btn" title="<?php esc_attr_e('Close', 'woo-conditional-discount-rules-for-checkout'); ?>"></span>
										<div class="popup-body-content">
											<div class="feature-image">
												<img src="<?php echo esc_url( WDPAD_PLUGIN_URL . 'admin/images/pro-features-img/feature-box-img-5.png' ); ?>" alt="<?php echo esc_attr('Buy One, Get One Discount!', 'woo-conditional-discount-rules-for-checkout'); ?>">
											</div>
											<div class="feature-content">
												<p><?php esc_html_e('You can offer extra charges to your customer based on the order amount for the free shipping option.', 'woo-conditional-discount-rules-for-checkout'); ?></p>
												<ul>
													<li><?php esc_html_e('Shop now with any combination of "Product 1", "Product 2", or "Product 3" in your cart, totaling 3 to 6 items. If your shipping country is India, you\'ll receive a complimentary "Product 1" added automatically with 1 quantity.', 'woo-conditional-discount-rules-for-checkout'); ?></li>
													<li><?php esc_html_e('Purchase "Product X" and receive another "Product X" absolutely free. Enjoy double the quantity at the price of one.', 'woo-conditional-discount-rules-for-checkout'); ?></li>
												</ul>
											</div>
										</div>
									</div>		
								</div>
							</div>
						</div>
					</div>
					<div class="feature-box">
						<h3><?php esc_html_e('Payment Gateway-Based Discount', 'woo-conditional-discount-rules-for-checkout'); ?></h3>
						<span><i class="fa fa-credit-card"></i></span>
						<p><?php esc_html_e('Charge Discounts from the customers for choosing a specific payment gateway based on the order amount.', 'woo-conditional-discount-rules-for-checkout'); ?></p>
						<div class="feature-explanation-popup-main">
							<div class="feature-explanation-popup-outer">
								<div class="feature-explanation-popup-inner">
									<div class="feature-explanation-popup">
										<span class="dashicons dashicons-no-alt popup-close-btn" title="<?php esc_attr_e('Close', 'woo-conditional-discount-rules-for-checkout'); ?>"></span>
										<div class="popup-body-content">
											<div class="feature-image">
												<img src="<?php echo esc_url( WDPAD_PLUGIN_URL . 'admin/images/pro-features-img/feature-box-img-6.png' ); ?>" alt="<?php echo esc_attr('Payment Gateway-Based Discount', 'woo-conditional-discount-rules-for-checkout'); ?>">
											</div>
											<div class="feature-content">
												<p><?php esc_html_e('Enjoy exclusive discounts based on your preferred payment method! Choose from a variety of payment options, and you\'ll automatically receive special savings at checkout.', 'woo-conditional-discount-rules-for-checkout'); ?></p>
												<ul>
													<li><?php echo sprintf( esc_html__('Customize charges based on payment type: %d%% fee for payments made through credit cards.', 'woo-conditional-discount-rules-for-checkout'), 2 ); ?></li>
													<li><?php esc_html_e('Easily apply a processing fee for cheque payments: a $5 fee per transaction.', 'woo-conditional-discount-rules-for-checkout'); ?></li>
												</ul>
											</div>
										</div>
									</div>		
								</div>
							</div>
						</div>
					</div>

                    <div class="feature-box">
						<h3><?php esc_html_e('Product Adjustment Discount', 'woo-conditional-discount-rules-for-checkout'); ?></h3>
						<span><i class="fa fa-sliders"></i></span>
						<p><?php esc_html_e('Modify product prices based on specific conditions, ensuring you always get the best deal', 'woo-conditional-discount-rules-for-checkout'); ?></p>
						<div class="feature-explanation-popup-main">
							<div class="feature-explanation-popup-outer">
								<div class="feature-explanation-popup-inner">
									<div class="feature-explanation-popup">
										<span class="dashicons dashicons-no-alt popup-close-btn" title="<?php esc_attr_e('Close', 'woo-conditional-discount-rules-for-checkout'); ?>"></span>
										<div class="popup-body-content">
											<div class="feature-image">
												<img src="<?php echo esc_url( WDPAD_PLUGIN_URL . 'admin/images/pro-features-img/feature-box-img-7.png' ); ?>" alt="<?php echo esc_attr('Product Adjustment Discount', 'woo-conditional-discount-rules-for-checkout'); ?>">
											</div>
											<div class="feature-content">
												<p><?php esc_html_e('Experience dynamic shopping with our custom pricing feature! Modify product prices based on specific conditions, ensuring you always get the best deal', 'woo-conditional-discount-rules-for-checkout'); ?></p>
												<ul>
													<li><?php esc_html_e('If "Product 1" is in the cart then "Product 2" will get 60% discount on price.', 'woo-conditional-discount-rules-for-checkout'); ?></li>
													<li><?php esc_html_e('Experience unbeatable savings on our premium product! If you\'re in India, enjoy a special 30% discount on those product applied automatically at checkout.', 'woo-conditional-discount-rules-for-checkout'); ?></li>
												</ul>
											</div>
										</div>
									</div>		
								</div>
							</div>
						</div>
					</div>
                    <div class="feature-box">
						<h3><?php esc_html_e('Time-Based Discounts', 'woo-conditional-discount-rules-for-checkout'); ?></h3>
						<span><i class="fa fa-clock-o"></i></span>
						<p><?php esc_html_e('Enjoy exclusive savings during specified periods. Shop now and take advantage of limited-time offers.', 'woo-conditional-discount-rules-for-checkout'); ?></p>
						<div class="feature-explanation-popup-main">
							<div class="feature-explanation-popup-outer">
								<div class="feature-explanation-popup-inner">
									<div class="feature-explanation-popup">
										<span class="dashicons dashicons-no-alt popup-close-btn" title="<?php esc_attr_e('Close', 'woo-conditional-discount-rules-for-checkout'); ?>"></span>
										<div class="popup-body-content">
											<div class="feature-image">
												<img src="<?php echo esc_url( WDPAD_PLUGIN_URL . 'admin/images/pro-features-img/feature-box-img-8.png' ); ?>" alt="<?php echo esc_attr('Time-Based Discounts', 'woo-conditional-discount-rules-for-checkout'); ?>">
											</div>
											<div class="feature-content">
												<p><?php esc_html_e('Embrace the moment with our time-based discount! For a limited period, enjoy special savings on select products.', 'woo-conditional-discount-rules-for-checkout'); ?></p>
												<ul>
													<li><?php esc_html_e('Celebrate the holidays with us! Enjoy a jolly 25% discount on all products from Christmas to New Year.', 'woo-conditional-discount-rules-for-checkout'); ?></li>
													<li><?php esc_html_e('Enjoy a fantastic 20% discount on all products. This special offer is available for the next 24 hours', 'woo-conditional-discount-rules-for-checkout'); ?></li>
												</ul>
											</div>
										</div>
									</div>		
								</div>
							</div>
						</div>
					</div>
                    <div class="feature-box">
						<h3><?php esc_html_e('Discounts Based on Product Quantity', 'woo-conditional-discount-rules-for-checkout'); ?></h3>
						<span><i class="fa fa-plus-square-o"></i></span>
						<p><?php esc_html_e('Enjoy tailored savings with our range-based product quantities, weight and subtotal discount feature.', 'woo-conditional-discount-rules-for-checkout'); ?></p>
						<div class="feature-explanation-popup-main">
							<div class="feature-explanation-popup-outer">
								<div class="feature-explanation-popup-inner">
									<div class="feature-explanation-popup">
										<span class="dashicons dashicons-no-alt popup-close-btn" title="<?php esc_attr_e('Close', 'woo-conditional-discount-rules-for-checkout'); ?>"></span>
										<div class="popup-body-content">
											<div class="feature-image">
												<img src="<?php echo esc_url( WDPAD_PLUGIN_URL . 'admin/images/pro-features-img/feature-box-img-9.png' ); ?>" alt="<?php echo esc_attr('Discounts Based on Product Quantity', 'woo-conditional-discount-rules-for-checkout'); ?>">
											</div>
											<div class="feature-content">
												<p><?php esc_html_e('Elevate your shopping experience with our range-based quantity discount feature. Discounts that automatically adjust based on product quantities', 'woo-conditional-discount-rules-for-checkout'); ?></p>
												<ul>
													<li><?php echo sprintf( esc_html__('Purchase 3 to 6 units of "Product 1" and automatically receive a $15 discount at checkout. ', 'woo-conditional-discount-rules-for-checkout'), 2 ); ?></li>
													<li><?php esc_html_e('Shop now and when your cart subtotal falls between $125 to $180, you\'ll automatically receive a $25 discount.', 'woo-conditional-discount-rules-for-checkout'); ?></li>
												</ul>
											</div>
										</div>
									</div>		
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="upgrade-to-premium-btn">
				<a href="<?php echo esc_url('https://bit.ly/3Ld7Q73') ?>" target="_blank" class="button button-primary"><?php esc_html_e('Upgrade to Premium', 'woo-conditional-discount-rules-for-checkout'); ?><svg id="Group_52548" data-name="Group 52548" xmlns="http://www.w3.org/2000/svg" width="22" height="20" viewBox="0 0 27.263 24.368"><path id="Path_199491" data-name="Path 199491" d="M333.833,428.628a1.091,1.091,0,0,1-1.092,1.092H316.758a1.092,1.092,0,1,1,0-2.183h15.984a1.091,1.091,0,0,1,1.091,1.092Z" transform="translate(-311.117 -405.352)" fill="#fff"></path><path id="Path_199492" data-name="Path 199492" d="M312.276,284.423h0a1.089,1.089,0,0,0-1.213-.056l-6.684,4.047-4.341-7.668a1.093,1.093,0,0,0-1.9,0l-4.341,7.668-6.684-4.047a1.091,1.091,0,0,0-1.623,1.2l3.366,13.365a1.091,1.091,0,0,0,1.058.825h18.349a1.09,1.09,0,0,0,1.058-.825l3.365-13.365A1.088,1.088,0,0,0,312.276,284.423Zm-4.864,13.151H290.764l-2.509-9.964,5.373,3.253a1.092,1.092,0,0,0,1.515-.4l3.944-6.969,3.945,6.968a1.092,1.092,0,0,0,1.515.4l5.373-3.253Z" transform="translate(-285.455 -280.192)" fill="#fff"></path></svg></a>
			</div>
		</div>
	</div>
	</div>
</div>
</div>
</div>
<?php 
