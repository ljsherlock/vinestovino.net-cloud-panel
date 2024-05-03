<?php 

require_once __DIR__ . '/vendor/autoload.php';

use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

function generate_qr ( $url, $filepath ) {
	$renderer = new ImageRenderer(
		new RendererStyle(300),
		new ImagickImageBackEnd()
	);
	$writer = new Writer($renderer);
	$writer->writeFile($url, $filepath);
}

function saveQRShippingLocatino ( $file_url, $filename ) {
	// Now you can use it!
	$filepath = ABSPATH . 'wp-content/uploads/qr-shipping-locations/' . $filename .'.png';

	generate_qr( $file_url, $filepath );

	// //return FILE PATH for use.
	return $filepath;
}

/** 
 * $path = 'https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=http%3A%2F%2Fmaps.google.com%3Fq%3D' . $latitude .','. $longitude .'&choe=UTF-8';
 */
function get_qr_attachment ($longitude, $latitude, $invoice_id = '9090') {
	$qr_image = 'wp-content/uploads/qr-shipping-locations/'. $invoice_id .'.png';

	// if there is already a saved QR code, serve it.
	if ( file_exists( ABSPATH . $qr_image ) ) {
		return ABSPATH . $qr_image;
	// otherwise create and save a new one.
	} else {
		return saveQRShippingLocatino('https://maps.google.com?q=' . $latitude .','. $longitude, $invoice_id);
	}
}

$billing_longitude = $this->order->get_meta('_billing_long');
$billing_latitude = $this->order->get_meta('_billing_lat');
$shipping_longitude = $this->order->get_meta('_shipping_long');
$shipping_latitude = $this->order->get_meta('_shipping_lat');

if($billing_longitude !== '') {
	$location_qr_code = get_qr_attachment($billing_longitude, $billing_latitude, $this->get_invoice_number());
} else if ($shipping_longitude !== '') {
	$location_qr_code = get_qr_attachment($shipping_longitude, $shipping_latitude, $this->get_invoice_number());
}