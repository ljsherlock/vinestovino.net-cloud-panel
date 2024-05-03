/**
 * User type added by vtv-users.php VTVUsers
 * function add_role_to_body_class ()
 */

const is_company = jQuery('.user-type-company').length;

if(is_company === 1) {
    jQuery('li.wc_payment_method.payment_method_rapyd_card').remove();
} 
