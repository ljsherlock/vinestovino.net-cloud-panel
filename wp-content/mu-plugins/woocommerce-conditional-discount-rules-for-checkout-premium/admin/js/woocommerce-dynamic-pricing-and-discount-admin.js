(function( $ ) {
    $( window ).load( function() {
        // Integrate select2
        jQuery( '.multiselect2' ).select2();

        // Add placeholder for conditional fields
        jQuery('#tbl-product-fee tr').each(function() {
            var val = jQuery(this).find('.th_product_dpad_conditions_condition select').val();
            var get_placehoder = coditional_vars['select_'+val];
        
            if( jQuery(this).find('.condition-value textarea').length ){
                //Textarea fields
                jQuery(this).find('.condition-value textarea').attr('placeholder', get_placehoder);
            } else if( jQuery(this).find('.condition-value select').length ){
                //Select 2 fields
                jQuery(this).find('.condition-value select').select2({
                    placeholder: get_placehoder
                });
            } else {
                //Input fields
                if ( val === 'product_qty' || val === 'quantity' || val === 'product_count' ) {
                    get_placehoder = coditional_vars.select_integer_number;
                } else {
                    get_placehoder = coditional_vars.select_float_number;
                }                
                jQuery(this).find('.product_dpad_conditions_values').attr('placeholder', get_placehoder);
            }
        });

        // Integrate select for products condition
        jQuery('.product_filter_select2').select2(select2object('wdpad_product_dpad_conditions_values_product'));

        $( '#dpad_settings_start_date' ).datepicker( {
            dateFormat: 'dd-mm-yy',
            minDate: '0',
            onSelect: function() {
                var dt = $( this ).datepicker( 'getDate' );
                dt.setDate( dt.getDate() + 1 );
                $( '#dpad_settings_end_date' ).datepicker( 'option', 'minDate', dt );
            }
        } ).keyup(function(e) {
            if(e.keyCode === 8 || e.keyCode === 46) {
                $.datepicker._clearDate(this);
            }
        });
        $( '#dpad_settings_end_date' ).datepicker( {
            dateFormat: 'dd-mm-yy',
            minDate: '0',
            onSelect: function() {
                var dt = $( this ).datepicker( 'getDate' );
                dt.setDate( dt.getDate() - 1 );
                $( '#dpad_settings_start_date' ).datepicker( 'option', 'maxDate', dt );
            }
        } ).keyup(function(e) {
            if(e.keyCode === 8 || e.keyCode === 46) {
                $.datepicker._clearDate(this);
            }
        });

        /*
         * Timepicker
         * */
        var dpad_time_from = $('#dpad_time_from').val();
        var dpad_time_to = $('#dpad_time_to').val();
        
        $('#dpad_time_from').timepicker({
            timeFormat: 'h:mm p',
            interval: 60,
            minTime: '00:00AM',
            maxTime: '11:59PM',
            startTime: dpad_time_from,
            dynamic: true,
            dropdown: true,
            scrollbar: true
        });
        
        $('#dpad_time_to').timepicker({
            timeFormat: 'h:mm p',
            interval: 60,
            minTime: '00:00AM',
            maxTime: '11:59PM',
            startTime: dpad_time_to,
            dynamic: true,
            dropdown: true,
            scrollbar: true
        });
        
        var ele = $('#total_row').val();
        var count;
        if (ele > 2) {
            count = ele;
        } else {
            count = 2;
        }
        $('body').on('click', '#fee-add-field', function () {
            var fee_add_field=$('#tbl-product-fee tbody').get(0);
            
            var tr = document.createElement('tr');
            tr=setAllAttributes(tr,{'id':'row_'+count});
            fee_add_field.appendChild(tr);
            
            // generate th of condition
            var th = document.createElement('td');
            th=setAllAttributes(th,{
                'class':'titledesc th_product_dpad_conditions_condition',
            });
            tr.appendChild(th);
            var conditions = document.createElement('select');
            conditions=setAllAttributes(conditions,{
                'rel-id':count,
                'id':'product_dpad_conditions_condition_'+count,
                'name':'dpad[product_dpad_conditions_condition][]',
                'class':'product_dpad_conditions_condition'
            });
            conditions=insertOptions(conditions,get_all_condition());
            th.appendChild(conditions);
            // th ends
            
            // generate td for equal or no equal to
            td = document.createElement('td');
            td = setAllAttributes(td,{});
            tr.appendChild(td);
            var conditions_is = document.createElement('select');
            conditions_is=setAllAttributes(conditions_is,{
                'name':'dpad[product_dpad_conditions_is][]',
                'class':'product_dpad_conditions_is product_dpad_conditions_is_'+count
            });
            conditions_is=insertOptions(conditions_is,condition_types());
            td.appendChild(conditions_is);
            // td ends
            
            // td for condition values
            td = document.createElement('td');
            td = setAllAttributes(td,{'id': 'column_'+count, 'class': 'condition-value'});
            tr.appendChild(td);
            condition_values(jQuery('#product_dpad_conditions_condition_'+count));
            
            var condition_key = document.createElement('input');
            condition_key=setAllAttributes(condition_key,{
                'type':'hidden',
                'name':'condition_key[value_'+count+'][]',
                'value':'',
            });
            td.appendChild(condition_key);
            conditions_values_index=jQuery('.product_dpad_conditions_values_' + count).get(0);
            jQuery('.product_dpad_conditions_values_' + count).trigger('chosen:updated');
            // td ends
            
            // td for delete button
            td = document.createElement('td');
            tr.appendChild(td);
            delete_button = document.createElement('a');
            delete_button=setAllAttributes(delete_button,{
                'id': 'fee-delete-field',
                'rel-id': count,
                'title': 'Delete',
                'class': 'delete-row',
                'href': 'javascript:void(0);'
            });
            deleteicon=document.createElement('i');
            deleteicon=setAllAttributes(deleteicon,{
                'class': 'dashicons dashicons-trash'
            });
            delete_button.appendChild(deleteicon);
            td.appendChild(delete_button);
            // td ends
            numberValidateForAdvanceRules();
            count++;
        });
        
        function insertOptions(parentElement,options){
            for(var i=0;i<options.length;i++){
                if(options[i].type === 'optgroup'){
                    optgroup=document.createElement('optgroup');
                    optgroup=setAllAttributes(optgroup,options[i].attributes);
                    for(var j=0;j<options[i].options.length;j++){
                        option=document.createElement('option');
                        option=setAllAttributes(option,options[i].options[j].attributes);
                        option.textContent=options[i].options[j].name;
                        optgroup.appendChild(option);
                    }
                    parentElement.appendChild(optgroup);
                } else {
                    option=document.createElement('option');
                    option=setAllAttributes(option,options[i].attributes);
                    option.textContent=allowSpeicalCharacter(options[i].name);
                    parentElement.appendChild(option);
                }
                
            }
            return parentElement;
            
        }
        function allowSpeicalCharacter(str){
            return str.replace('&#8211;','–').replace('&gt;','>').replace('&lt;','<').replace('&#197;','Å');
        }
        
        
        
        function setAllAttributes(element,attributes){
            Object.keys(attributes).forEach(function (key) {
                element.setAttribute(key, attributes[key]);
                // use val
            });
            return element;
        }
        
        function get_all_condition(){
            return [
                {
                    'type': 'optgroup',
                    'attributes' : {'label' :'Location Specific'},
                    'options' :[
                        {'name': 'Country','attributes' : {'value':'country'} },
                        {'name': 'City (Available in PRO)','attributes' : {'value':'', 'disabled':'disabled'} },
                        {'name': 'State (Available in PRO)','attributes' : {'value':'', 'disabled':'disabled'} },
                        {'name': 'Postcode (Available in PRO)','attributes' : {'value':'', 'disabled':'disabled'} },
                        {'name': 'Zone (Available in PRO)','attributes' : {'value':'', 'disabled':'disabled'} },
                    ]
                },
                {
                    'type': 'optgroup',
                    'attributes' : {'label' :'Product Specific'},
                    'options' :[
                        {'name': 'Product','attributes' : {'value':'product'} },
                        {'name': 'Variable Product (Available in PRO)','attributes' : {'value':'', 'disabled':'disabled'} },
                        {'name': 'Category','attributes' : {'value':'category'} },
                        {'name': 'Tag (Available in PRO)','attributes' : {'value':'', 'disabled':'disabled'} },
                        {'name': 'Product\'s quantity (Available in PRO)', 'attributes': {'value' : '', 'disabled':'disabled'} },
                        {'name': 'Product\'s count', 'attributes': {'value' : 'product_count'} },
                    ]
                },
                {
                    'type': 'optgroup',
                    'attributes' : {'label' : 'User Specific'},
                    'options': [
                        {'name' : 'User', 'attributes': {'value' : 'user'}},
                        {'name' : 'User Role (Available in PRO)', 'attributes': {'value' : '', 'disabled':'disabled'}},
                        {'name' : 'User Email (Available in PRO)', 'attributes': {'value' : '', 'disabled':'disabled'}},
                    ]
                },
                {
                    'type': 'optgroup',
                    'attributes' : {'label' : 'Purchase History'},
                    'options': [
                        {'name' : 'Last order spent  (Available in PRO)', 'attributes': {'value' : '', 'disabled':'disabled'}},
                        {'name' : 'Total order spent (all time) (Available in PRO)', 'attributes': {'value' : '', 'disabled':'disabled'}},
                        {'name' : 'Number of orders (all time) (Available in PRO)', 'attributes': {'value' : '', 'disabled':'disabled'}},
                    ]
                },
                {
                    'type': 'optgroup',
                    'attributes' : {'label' : 'Cart Specific'},
                    'options': [
                        {'name' : 'Cart Subtotal', 'attributes': {'value' : 'cart_total'}},
                        {'name' : 'Cart Subtotal (After Discount) (Available in PRO)', 'attributes': {'value' : '', 'disabled':'disabled'}},
                        {'name' : 'Quantity', 'attributes': {'value' : 'quantity'}},
                        {'name' : 'Weight (kg) (Available in PRO)', 'attributes': {'value' : '', 'disabled':'disabled'}},
                        {'name' : 'Coupon (Available in PRO)', 'attributes': {'value' : '', 'disabled':'disabled'}},
                        {'name' : 'Shipping Class (Available in PRO)', 'attributes': {'value' : '', 'disabled':'disabled'}}
                    ]
                },
                {
                    'type': 'optgroup',
                    'attributes' : {'label' : 'Payment Specific'},
                    'options': [
                        {'name' : 'Payment Gateway (Available in PRO)', 'attributes': {'value' : '', 'disabled':'disabled'}},
                    ]
                },
                {
                    'type': 'optgroup',
                    'attributes' : {'label' : 'Shipping Specific'},
                    'options': [
                        {'name' : 'Shipping Method (Available in PRO)', 'attributes' : {'value' : '', 'disabled':'disabled'}},
                        {'name' : 'Shipping Total (Available in PRO)', 'attributes' : {'value' : '', 'disabled':'disabled'}},
                    ]
                }
            ];
        }
        
        $( 'body' ).on( 'click', '#fee-delete-field', function() {
            var deleId = $( this ).attr( 'rel-id' );
            $( '#row_' + deleId ).remove();
        } );
        $( 'body' ).on( 'change', '.product_dpad_conditions_condition', function() {
            condition_values(this);
        } );
        
        function condition_values(element) {
            var condition = $(element).val();
            var count = $(element).attr('rel-id');
            var column=jQuery('#column_' + count).get(0);
            jQuery(column).empty();
            var loader=document.createElement('img');
            loader=setAllAttributes(loader,{'src':coditional_vars.plugin_url+'images/ajax-loader.gif'});
            column.appendChild(loader);
            var data = {
                'action': 'wdpad_product_dpad_conditions_values_ajax',
                'security': coditional_vars.wcdrfc_ajax_verification_nonce,
                'condition': condition,
                'count': count
            };
            jQuery.ajaxSetup({
                headers: {
                    'Accept': 'application/json; charset=utf-8'
                }
            });
            // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
            jQuery.post(ajaxurl, data, function (response) {
                jQuery('.product_dpad_conditions_is_' + count).empty();
                var column=jQuery('#column_' + count).get(0);
                var condition_is=jQuery('.product_dpad_conditions_is_' + count).get(0);
                
                if (condition === 'cart_total'
                    || condition === 'quantity'
                    || condition === 'product_count'
                ) {
                    condition_is=insertOptions(condition_is,condition_types(true));
                } else {
                    condition_is=insertOptions(condition_is,condition_types(false));
                }
                jQuery('.product_dpad_conditions_is_' + count).trigger('chosen:updated');
                jQuery(column).empty();
                
                var condition_values_id='';
                if(condition === 'product'){
                    condition_values_id = 'product-filter';
                }

                var product_condition_class;
                if ( condition === 'product' ) {
                    product_condition_class = 'product_filter_select2';
                } else {
                    product_condition_class = '';
                }

                var condition_values;
                if(isJson(response)){
                    condition_values = document.createElement('select');
                    condition_values=setAllAttributes(condition_values,{
                        'name':  'dpad[product_dpad_conditions_values][value_'+count+'][]',
                        'class': 'product_dpad_conditions_values product_discount_select product_dpad_conditions_values_'+count+' multiselect2 '+product_condition_class+' multiselect2_' + count + '_' + condition,
                        'multiple': 'multiple',
                        'id':condition_values_id+'-'+count,
                        'placeholder': 'please enter 3 characters'
                    });
                    column.appendChild(condition_values);
                    data=JSON.parse(response);
                    condition_values=insertOptions(condition_values,data);
                } else{
                    var input_extra_class;
                    if (condition === 'quantity' || condition === 'product_count') {
                        input_extra_class = ' qty-class';
                    }
                    if (condition === 'cart_total' ) {
                        input_extra_class = ' price-class';
                    }
                    
                    let fieldPlaceholder;
                    if ( condition === 'quantity' || condition === 'product_count' ) {
                        fieldPlaceholder = coditional_vars.select_integer_number;
                    } else {
                        fieldPlaceholder = coditional_vars.select_float_number;
                    }

                    condition_values = document.createElement(response.trim());
                    condition_values=setAllAttributes(condition_values,{
                        'name':  'dpad[product_dpad_conditions_values][value_'+count+']',
                        'class': 'product_dpad_conditions_values' + input_extra_class,
                        'type': 'text',
                        'min':0,
                        'placeholder': fieldPlaceholder
                    });
                    column.appendChild(condition_values);
                }
                
                column = $('#column_' + count).get(0);
                var input_node = document.createElement('input');
                input_node = setAllAttributes(input_node,{
                    'type':'hidden',
                    'name':'condition_key[value_'+count+'][]',
                    'value':''
                });
                column.appendChild(input_node);

                var p_node = document.createElement( 'p' );
                var b_node = document.createElement( 'b' );
                var b_text_node = document.createTextNode( coditional_vars.note );
                var text_node;
                if ( condition === 'product_count' ) {
                    b_node = setAllAttributes( b_node, {
                        'style': 'color: red;',
                    } );
                    b_node.appendChild( b_text_node );
                    text_node = document.createTextNode( coditional_vars.product_count_msg );
                    p_node.appendChild( b_node );
                    p_node.appendChild( text_node );
                    column.appendChild( p_node );
                }

                // Add placeholder for all the conditions
                var selectCoundition = coditional_vars['select_' + condition];
                if ( condition === 'product' ) {
                    $( '.multiselect2_' + count + '_' + condition ).select2(select2object('wdpad_product_dpad_conditions_values_product'));
                } else {
                    $( '.multiselect2_' + count + '_' + condition ).select2({
                        placeholder: selectCoundition
                    });
                }

                numberValidateForAdvanceRules();
            });
        }
        
        
        function condition_types(text){
            if( text === true ){
                return [
                    {'name': 'Equal to ( = )','attributes' : {'value':'is_equal_to'} },
                    {'name': 'Less or Equal to ( <= )','attributes' : {'value':'less_equal_to'} },
                    {'name': 'Less then ( < )','attributes' : {'value':'less_then'} },
                    {'name': 'greater or Equal to ( >= )','attributes' : {'value':'greater_equal_to'} },
                    {'name': 'greater then ( > )','attributes' : {'value':'greater_then'} },
                    {'name': 'Not Equal to ( != )','attributes' : {'value':'not_in'} },
                ];
            } else {
                return  [
                    {'name': 'Equal to ( = )','attributes' : {'value':'is_equal_to'} },
                    {'name': 'Not Equal to ( != )','attributes' : {'value':'not_in'} },
                ];
                
            }
            
        }
        $('#extra_product_cost, .price-field').keypress(function (e) {
            var regex = new RegExp('^[0-9.]+$');
            var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
            if (regex.test(str)) {
                return true;
            }
            e.preventDefault();
            return false;
        });
        numberValidateForAdvanceRules();
        function numberValidateForAdvanceRules() {
            $('.number-field').keypress(function (e) {
                var regex = new RegExp('^[0-9-%.]+$');
                var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
                if (regex.test(str)) {
                    return true;
                }
                e.preventDefault();
                return false;
            });
            $('.qty-class').keypress(function (e) {
                var regex = new RegExp('^[0-9]+$');
                var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
                if (regex.test(str)) {
                    return true;
                }
                e.preventDefault();
                return false;
            });
            $('.weight-class, .price-class').keypress(function (e) {
                var regex = new RegExp('^[0-9.]+$');
                var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
                if (regex.test(str)) {
                    return true;
                }
                e.preventDefault();
                return false;
            });
        }
        
        function isJson(str) {
            try {
                JSON.parse(str);
            } catch (err) {
                return false;
            }
            return true;
        }
        function select2object(ajaxtype){
            return {
                minimumInputLength: 3,
                placeholder: coditional_vars.select_product,
                ajax: {
                    url: ajaxurl,
                    dataType: 'json',
                    data: function (params) {
                        // Query parameters will be ?search=[term]&page=[page]
                        return {
                            action: ajaxtype+'_ajax',
                            security: coditional_vars.wcdrfc_ajax_verification_nonce,
                            search: params.term,
                            posts_per_page: coditional_vars.select2_per_product_ajax,
                            offset: params.page || 1,
                        };
                    },
                    processResults: function( data ) {
                        var options = [];
                        if ( data ) {
                            $.each( data, function( index, text ) {
                                options.push( { id: text.id, text: allowSpeicalCharacter( text.text ) } );
                            } );
                        }
                        bogo_product_more = data.length > 0 ? true : false;
                        return {
                            results: options,
                            pagination: {
                                more : bogo_product_more
                            } 
                        };
                    },
                    cache: true,
                }
            };
        }
        
        $('.submitDiscount').click(function(e) {
            
            var discount_type = $('#dpad_settings_select_dpad_type').val();
            var discount_cost = $('#dpad_settings_product_cost').val();
            if( ( 'fixed' === discount_type || 'percentage' === discount_type ) && '' === discount_cost ){
                if ( $( '#warning_msg_6' ).length < 1 ) {
                    var div_6 = document.createElement( 'div' );
                    div_6 = setAllAttributes( div_6, {
                        'class': 'warning_msg',
                        'id': 'warning_msg_6'
                    } );
                    div_6.textContent = coditional_vars.discount_cost_msg;
                    $( '.wdpad-main-table' ).prepend( div_6 );
                }
                if ( $( '#warning_msg_6' ).length ) {
                    $( 'html, body' ).animate( { scrollTop: 0 }, 'slow' );
                    setTimeout( function() {
                        $( '#warning_msg_6' ).remove();
                    }, 7000 );
                }
                e.preventDefault();
                return false;
            }

            var price_cartqty_based = $('#price_cartqty_based').val();
            if (price_cartqty_based === 'qty_product_based') {
                var f = 0;
                $('.product_dpad_conditions_condition').each(function () {
        
                    if ($(this).val() === 'product' || $(this).val() === 'variableproduct') {
                        f = 1;
                    }
        
                });
                if ($('#dpad_chk_qty_price').is(':checked') && f === 0) {
                    e.preventDefault();
                    if ( $( '#warning_msg_6' ).length < 1 ) {
                        var div = document.createElement( 'div' );
                        div = setAllAttributes( div, {
                            'class': 'warning_msg',
                            'id': 'warning_msg_6'
                        } );
                        div.textContent = coditional_vars.warning_msg_per_qty;
                        $( '.wdpad-main-table' ).prepend( div );
                    }
                    if ( $( '#warning_msg_6' ).length ) {
                        $( 'html, body' ).animate( { scrollTop: 0 }, 'slow' );
                        setTimeout( function() {
                            $( '#warning_msg_6' ).remove();
                        }, 7000 );
                    }
                    return;
        
                }
            }
        });
        
        /* description toggle */
        $( 'span.woocommerce_conditional_product_dpad_checkout_tab_descirtion' ).click( function( event ) {
            event.preventDefault();
            // var data = $( this );
            $( this ).next( 'p.description' ).toggle();
            //$('span.advance_extra_flate_rate_disctiption_tab').next('p.description').toggle();
        } );

        $( '.product_dpad_conditions_values_country' ).select2({
			placeholder: coditional_vars.select_country
		});

        function get_all_products_and_variations_select_init(){
            var bogo_product_more = false;
            $( '.all-products-variations' ).select2({
                ajax: {
                    url: coditional_vars.ajaxurl,
                    dataType: 'json',
                    delay: 250,
                    allowSpeicalCharacter: false,
                    // dropdownAutoWidth : true,
                    data: function( params ) {
                        return {
                            value: params.term,
                            action: 'wdpad_simple_and_variation_product_list_ajax',
                            security: coditional_vars.wcdrfc_ajax_verification_nonce,
                            posts_per_page: coditional_vars.select2_per_product_ajax,
                            offset: params.page || 1,
                        };
                    },
                    processResults: function( data ) {
                        var options = [];
                        if ( data ) {
                            $.each( data, function( index, text ) {
                                options.push( { id: text[ 0 ], text: allowSpeicalCharacter( text[ 1 ] ) } );
                            } );
                        }
                        bogo_product_more = data.length > 0 ? true : false;
                        return {
                            results: options,
                            pagination: {
                                more : bogo_product_more
                            } 
                        };
                    },
                    cache: true
                },
                minimumInputLength: 3,
                allowClear: true,
                placeholder: coditional_vars.select2_product_placeholder
            });
        }
        get_all_products_and_variations_select_init();
    } );
    
    
    jQuery( document ).ready( function( $ ) {
        
        /** tiptip js implementation */
		$( '.woocommerce-help-tip' ).tipTip( {
			'attribute': 'data-tip',
			'fadeIn': 50,
			'fadeOut': 50,
			'delay': 200,
			'keepAlive': true
		} );

        $( '.tablesorter' ).tablesorter( {
            headers: {
                0: {
                    sorter: false
                },
                4: {
                    sorter: false
                }
            }
        } );
        var fixHelperModified = function( e, tr ) {
            var $originals = tr.children();
            var $helper = tr.clone();
            $helper.children().each( function( index ) {
                $( this ).width( $originals.eq( index ).width() );
            } );
            return $helper;
        };
        //Make diagnosis table sortable
        if( jQuery('#the-list tr').length > 1 ) {
            $('.wdpad-main-table .wp-list-table tbody').sortable({
                helper: fixHelperModified,
                stop: function() {
                    var listing = [];
                    var paged = $('.current-page').val();
                    jQuery('.ui-sortable-handle').each(function(){
                        listing.push(jQuery(this).find('input').val());
                    });
                    var data = {
                        'action': 'wdpad_product_discount_conditions_sorting',
                        'sorting_conditional_fee': jQuery('#sorting_conditional_fee').val(),
                        'listing': listing,
                        'paged': paged
                    };
                    jQuery.ajaxSetup({
                        headers: {
                            'Accept': 'application/json; charset=utf-8'
                        }
                    });
                    // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
                    jQuery.post(ajaxurl, data, function (response){
                        var div_wrap = $('<div></div>').addClass('notice notice-success');
                        var p_text = $('<p></p>').text(response.data.message);
                        div_wrap.append(p_text);
                        $(div_wrap).insertAfter($('.search-box'));
                        setTimeout( function(){
                            div_wrap.remove();
                        }, 2000 );
                    });
                }
            });
            $( 'table#conditional-fee-listing tbody' ).disableSelection();
        }
        
        /* Apply per quantity conditions start */
        if ( $( '#dpad_chk_qty_price' ).is( ':checked' ) ) {
            $( '.wdpad-main-table .product_cost_right_div .applyperqty-boxtwo' ).show();
            $( '.wdpad-main-table .product_cost_right_div .applyperqty-boxthree' ).show();
            $( '#extra_product_cost' ).prop( 'required', true );
        } else {
            $( '.wdpad-main-table .product_cost_right_div .applyperqty-boxtwo' ).hide();
            $( '.wdpad-main-table .product_cost_right_div .applyperqty-boxthree' ).hide();
            $( '#extra_product_cost' ).prop( 'required', false );
        }
        $( document ).on( 'change', '#dpad_chk_qty_price', function() {
            if ( this.checked ) {
                $( '.wdpad-main-table .product_cost_right_div .applyperqty-boxtwo' ).show();
                $( '.wdpad-main-table .product_cost_right_div .applyperqty-boxthree' ).show();
                $( '#extra_product_cost' ).prop( 'required', true );
            } else {
                $( '.wdpad-main-table .product_cost_right_div .applyperqty-boxtwo' ).hide();
                $( '.wdpad-main-table .product_cost_right_div .applyperqty-boxthree' ).hide();
                $( '#extra_product_cost' ).prop( 'required', false );
            }
        } );
        /* Apply per quantity conditions end */
        /* Check price only digits allow */
        $( '#dpad_settings_product_cost' ).keypress( function( e ) {
            //if the letter is not digit then display error and don't type anything
            if ( e.which !== 46 && e.which !== 8 && e.which !== 0 && (e.which < 48 || e.which > 57) ) {
                //display error message
                
                return false;
            }
        } );
        
        function show_hide_discount_textbox(elem, shelem){
            if( $(elem).prop('checked') ){
                $(shelem).show();
            } else {
                $(shelem).hide();
            }
        }

        //Discount text message show/hide
        $('#dpad_chk_discount_msg').change(function(){
            show_hide_discount_textbox('#'+$(this).attr('id'), '.display_discount_message_text');
        });
        show_hide_discount_textbox('#dpad_chk_discount_msg', '.display_discount_message_text');

        //Discount selected product show/hide
        $( document ).on( 'click', '#dpad_chk_discount_msg_selected_product', function() {
            show_hide_discount_textbox('#'+$(this).attr('id'), '.wdpad-selected-product-list');
        });
        show_hide_discount_textbox('#dpad_chk_discount_msg_selected_product', '.wdpad-selected-product-list');

        // script for plugin rating
        jQuery(document).on('click', '.dotstore-sidebar-section .content_box .wcdrc-star-rating label', function(e){
            e.stopImmediatePropagation();
            var rurl = jQuery('#wcdrc-review-url').val();
            window.open( rurl, '_blank' );
        });

        $('.dpad_chk_advanced_settings').click(function(){
            $('.dpad_advanced_setting_section').toggle();
        });

        // Toggle dynamic rules visibility script start
	    var show_dynamic_rules = localStorage.getItem('dpad-dynamic-rules-display');
	    if( ( null !== show_dynamic_rules || undefined !== show_dynamic_rules ) && ( 'hide' === show_dynamic_rules ) ) {
	        $('.dpad_dynamic_rules_tooltips p').addClass('dpad-dynamic-rules-hide');
	        $('.dpad_dynamic_rules_tooltips p + .dpad_dynamic_rules_content').css('display', 'none');
	    } else {
	        $('.dpad_dynamic_rules_tooltips p').removeClass('dpad-dynamic-rules-hide');
	        $('.dpad_dynamic_rules_tooltips p + .dpad_dynamic_rules_content').css('display', 'block');
	    }

	    $(document).on( 'click', '.dpad_dynamic_rules_tooltips p', function(){
	        $(this).toggleClass('dpad-dynamic-rules-hide');
	        $(this).next('.dpad_dynamic_rules_content').slideToggle(300);
	        if( $(this).hasClass('dpad-dynamic-rules-hide') ){
	            localStorage.setItem('dpad-dynamic-rules-display', 'hide');
	        } else {
	            localStorage.setItem('dpad-dynamic-rules-display', 'show');
	        }
	    });
	    // Toggle dynamic rules visibility script end

        $( document ).on( 'click', '#dpad_status_id', function() {
            var current_fees_id = $( this ).attr( 'data-smid' );
            var current_value = $( this ).prop( 'checked' );
            $('.wdpad-main-table').block({
                message: null,
                overlayCSS: {
                    background: 'rgb(255, 255, 255)',
                    opacity: 0.6,
                },
            });
            $.ajax( {
				type: 'POST',
				url: coditional_vars.ajaxurl,
				data: {
					'action': 'wdpad_change_status_from_list_section',
                    'security': coditional_vars.wcdrfc_ajax_verification_nonce,
					'current_dpad_id': current_fees_id,
					'current_value': current_value
				}, complete: function() {
                    jQuery('.wdpad-main-table').unblock();
				}, success: function( response ) {
                    var div_wrap = $('<div></div>').addClass('notice notice-success');
                    var p_text = $('<p></p>').text(jQuery.trim( response.data ));
                    div_wrap.append(p_text);
                    $('.wdpad-main-table').prepend(div_wrap);
                    setTimeout( function(){
                        div_wrap.remove();
                    }, 3000 );
                    jQuery('.wdpad-main-table').unblock();
				}
			} );
        });

        //Discount message background and text color
        jQuery('#dpad_discount_msg_bg_color, #dpad_discount_msg_text_color').wpColorPicker();
        
        /** Dynamic Promotional Bar START */
		/** Hide free guide notification popup */
        $(document).on('click', '.dpbpop-close', function () {
            var popupName 		= $(this).attr('data-popup-name');
            setCookie( 'banner_' + popupName, 'yes', 60 * 24 * 7);
            $('.' + popupName).hide();
        });

		$(document).on('click', '.dpb-popup .dpb-popup-meta', function () {
            var promotional_id         = $(this).parent().find('.dpbpop-close').attr('data-bar-id');
            var popupName                 = $(this).parent().find('.dpbpop-close').attr('data-popup-name');
            setCookie( 'banner_' + popupName, 'yes', 60 * 24 * 7);
            $('.' + popupName).hide();

			//Create a new Student object using the values from the textfields
			var apiData = {
				'bar_id' : promotional_id
			};

			$.ajax({
				type: 'POST',
				url: coditional_vars.dpb_api_url + 'wp-content/plugins/dots-dynamic-promotional-banner/bar-response.php',
				data: JSON.stringify(apiData),// now data come in this function
		        dataType: 'json',
		        cors: true,
		        contentType:'application/json',
				success: function (data) {
					console.log(data);
				},
				error: function () {
				}
			 });
        });
        /** Dynamic Promotional Bar END */

        /** Upgrade Dashboard Script START */
	    // Dashboard features popup script
	    $(document).on('click', '.dotstore-upgrade-dashboard .unlock-premium-features .feature-box', function (event) {
	    	let $trigger = $('.feature-explanation-popup, .feature-explanation-popup *');
			if(!$trigger.is(event.target) && $trigger.has(event.target).length === 0){
	    		$('.feature-explanation-popup-main').not($(this).find('.feature-explanation-popup-main')).hide();
	        	$(this).find('.feature-explanation-popup-main').show();
	        	$('body').addClass('feature-explanation-popup-visible');
	    	}
	    });
	    $(document).on('click', '.dotstore-upgrade-dashboard .popup-close-btn', function () {
	    	$(this).parents('.feature-explanation-popup-main').hide();
	    	$('body').removeClass('feature-explanation-popup-visible');
	    });
	    /** Upgrade Dashboard Script End */

        /** Plugin Setup Wizard Script START */
		// Hide & show wizard steps based on the url params 
	  	var urlParams = new URLSearchParams(window.location.search);
	  	if (urlParams.has('require_license')) {
	    	$('.ds-plugin-setup-wizard-main .tab-panel').hide();
	    	$( '.ds-plugin-setup-wizard-main #step5' ).show();
	  	} else {
	  		$( '.ds-plugin-setup-wizard-main #step1' ).show();
	  	}
	  	
        // Plugin setup wizard steps script
        $(document).on('click', '.ds-plugin-setup-wizard-main .tab-panel .btn-primary:not(.ds-wizard-complete)', function () {
	        var curruntStep = jQuery(this).closest('.tab-panel').attr('id');
	        var nextStep = 'step' + ( parseInt( curruntStep.slice(4,5) ) + 1 ); // Masteringjs.io

	        if( 'step5' !== curruntStep ) {

                //Youtube videos stop on next step
                $('iframe[src*="https://www.youtube.com/embed/"]').each(function(){
                    $(this).attr('src', $(this).attr('src'));
                    return false;
                });

	         	$( '#' + curruntStep ).hide();
	            $( '#' + nextStep ).show();   
	        }
	    });

	    // Get allow for marketing or not
	    if ( $( '.ds-plugin-setup-wizard-main .ds_count_me_in' ).is( ':checked' ) ) {
	    	$('#fs_marketing_optin input[name="allow-marketing"][value="true"]').prop('checked', true);
	    } else {
	    	$('#fs_marketing_optin input[name="allow-marketing"][value="false"]').prop('checked', true);
	    }

		// Get allow for marketing or not on change	    
	    $(document).on( 'change', '.ds-plugin-setup-wizard-main .ds_count_me_in', function() {
			if ( this.checked ) {
				$('#fs_marketing_optin input[name="allow-marketing"][value="true"]').prop('checked', true);
			} else {
		    	$('#fs_marketing_optin input[name="allow-marketing"][value="false"]').prop('checked', true);
		    }
		});

	    // Complete setup wizard
	    $(document).on( 'click', '.ds-plugin-setup-wizard-main .tab-panel .ds-wizard-complete', function() {
			if ( $( '.ds-plugin-setup-wizard-main .ds_count_me_in' ).is( ':checked' ) ) {
				$( '.fs-actions button'  ).trigger('click');
			} else {
		    	$('.fs-actions #skip_activation')[0].click();
		    }
		});

	    // Send setup wizard data on Ajax callback
		$(document).on( 'click', '.ds-plugin-setup-wizard-main .fs-actions button', function() {
			var wizardData = {
                'action': 'wcpfc_plugin_setup_wizard_submit',
                'survey_list': $('.ds-plugin-setup-wizard-main .ds-wizard-where-hear-select').val(),
                'nonce': coditional_vars.setup_wizard_ajax_nonce
            };

            $.ajax({
                url: coditional_vars.ajaxurl,
                data: wizardData,
                success: function ( success ) {
                    console.log(success);
                }
            });
		});
		/** Plugin Setup Wizard Script End */

        $( '.dpad_reset_time' ).click(function(){
			$( '#dpad_time_from' ).val('');
			$( '#dpad_time_to' ).val('');
		});
    } );

    //set cookies
	function setCookie(name, value, minutes) {
		var expires = '';
		if (minutes) {
			var date = new Date();
			date.setTime(date.getTime() + (minutes * 60 * 1000));
			expires = '; expires=' + date.toUTCString();
		}
		document.cookie = name + '=' + (value || '') + expires + '; path=/';
	}
})( jQuery );