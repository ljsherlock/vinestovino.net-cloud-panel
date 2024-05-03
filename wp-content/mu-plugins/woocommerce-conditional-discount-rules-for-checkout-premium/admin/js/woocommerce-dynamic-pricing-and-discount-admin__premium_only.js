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
        jQuery('.product_var_filter_select2').select2(select2object('wdpad_product_dpad_conditions_varible_values_product'));

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
            
            // generate td of condition
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
            td = setAllAttributes(td,{
                'class':'select_condition_for_in_notin'
            });
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
                'title':'Delete',
                'class':'delete-row',
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
        
        var bogo_ele = $('#bogo_total_row').val();
        var bogo_count;
        if (bogo_ele > 1) {
            bogo_count = bogo_ele;
        } else {
            bogo_count = 1;
        }
        $('body').on('click', '#bogo-add-field', function () {
            var bogo_add_field = $('#tbl-bogo-discount tbody').get(0);
            
            var tr = document.createElement('tr');
            tr = setAllAttributes( tr, {'id':'bogo_row_'+bogo_count, 'valign':'top', 'class':'bogo_row_tr'} );
            bogo_add_field.appendChild(tr);

            // Generate buy product for BOGO ruleset
            var bbp_td = document.createElement('td');
            tr.appendChild(bbp_td);
            var buy_select = document.createElement('select');
            buy_select=setAllAttributes(buy_select,{
                'name':'dpad[bogo_ruleset]['+bogo_count+'][bogo_buy_products][]',
                'class':'bogo-products bogo-buy-products',
                'multiple':'multiple'
            });
            bbp_td.appendChild(buy_select);

            // Generate buy product minimum quantity for BOGO ruleset
            var bbmin_td = document.createElement('td');
            tr.appendChild(bbmin_td);
            var bogo_buy_min_qty = document.createElement('input');
            bogo_buy_min_qty=setAllAttributes(bogo_buy_min_qty,{
                'type':'number',
                'name':'dpad[bogo_ruleset]['+bogo_count+'][bogo_buy_products_min_qty]',
                'min' : 1
            });
            bbmin_td.appendChild(bogo_buy_min_qty);

            // Generate buy product maximum quantity for BOGO ruleset
            var bbmax_td = document.createElement('td');
            tr.appendChild(bbmax_td);
            var bogo_buy_max_qty = document.createElement('input');
            bogo_buy_max_qty=setAllAttributes(bogo_buy_max_qty,{
                'type':'number',
                'name':'dpad[bogo_ruleset]['+bogo_count+'][bogo_buy_products_max_qty]',
                'min' : 1
            });
            bbmax_td.appendChild(bogo_buy_max_qty);

            // Generate get product for BOGO ruleset
            var bgp_td = document.createElement('td');
            tr.appendChild(bgp_td);
            var get_select = document.createElement('select');
            get_select=setAllAttributes(get_select,{
                'name':'dpad[bogo_ruleset]['+bogo_count+'][bogo_get_products][]',
                'class':'bogo-products bogo-get-products',
                'multiple':'multiple'
            });
            bgp_td.appendChild(get_select);
            var copy_buy_link = document.createElement( 'a' );
			copy_buy_link = setAllAttributes( copy_buy_link, {
				'class': 'copy_buy_product',
				'href': 'javascript:void(0);',
			} );
            copy_buy_link.innerHTML = coditional_vars.bogo_copy_buy_product_text;
            bgp_td.appendChild(copy_buy_link);

            // Generate get product free quantity for BOGO ruleset
            var bgpfq_td = document.createElement('td');
            tr.appendChild(bgpfq_td);
            var bogo_get_free_qty = document.createElement('input');
            bogo_get_free_qty=setAllAttributes(bogo_get_free_qty,{
                'type':'number',
                'name':'dpad[bogo_ruleset]['+bogo_count+'][bogo_get_products_free_qty]',
                'min' : 1
            });
            bgpfq_td.appendChild(bogo_get_free_qty);

            //Generate delete and clone link for BOGO ruleset
            var dc_td = document.createElement('td');
            tr.appendChild(dc_td);
            //Remove link
            var tr_remove = document.createElement( 'a' );
			tr_remove = setAllAttributes( tr_remove, {
				'class': 'delete-row',
				'href': 'javascript:void(0);',
                'data-id': bogo_count
			} );
            deleteicon=document.createElement('i');
            deleteicon=setAllAttributes(deleteicon,{
                'class': 'dashicons dashicons-trash'
            });
            tr_remove.appendChild(deleteicon);
            dc_td.appendChild(tr_remove);
            
            //Clone link
            var tr_clone = document.createElement( 'a' );
			tr_clone = setAllAttributes( tr_clone, {
                'data-id': bogo_count,
				'class': 'duplicate-row',
				'href': 'javascript:void(0);',
			} );
            cloneicon=document.createElement('i');
            cloneicon=setAllAttributes(cloneicon,{
                'class': 'fa fa-clone'
            });
            tr_clone.appendChild(cloneicon);
            dc_td.appendChild(tr_clone);

            getBOGOSelectDropdown();
            bogo_count++;
        });

        $('body').on('click', '.duplicate-row', function(){

            var main_id = $(this).data('id');

            // var bogo_add_field = $('#tbl-bogo-discount tbody').get(0);

            //First destroy select2 for not conflict with exist select2 dropdown
            $('.bogo-products').select2('destroy');
            
            var buy_product_clone = $('#bogo_row_'+main_id).clone();
            buy_product_clone.find('td').each(function(){
                var clone_el = $(this);
                if( typeof clone_el.find('select').attr('name') !== 'undefined' ){
                    var select_name = clone_el.find('select').attr('name');
                    clone_el.find('select').attr('name', select_name.replace(/\d+/, bogo_count) );
                    clone_el.find('select option').attr('selected','selected');
                } else if( typeof clone_el.find('input').attr('name') !== 'undefined' ){
                    var input_name = clone_el.find('input').attr('name');
                    clone_el.find('input').attr('name', input_name.replace(/\d+/, bogo_count) );
                } else if( typeof clone_el.find('a').attr('data-id') !== 'undefined' ) {
                    clone_el.find('a').attr('data-id', bogo_count );
                }
            });
            buy_product_clone.attr('id', 'bogo_row_'+bogo_count);
            // buy_product_clone.appendTo(bogo_add_field);
            buy_product_clone.insertAfter($(this).parent().parent());

            //After clone apply select2 dropdown
            getBOGOSelectDropdown();
            
            bogo_count++;
        });

        function insertOptions(parentElement,options){
            for(var i=0;i<options.length;i++){
                if(options[i].type==='optgroup'){
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
                        {'name': 'City','attributes' : {'value':'city'} },
                        {'name': 'State','attributes' : {'value':'state'} },
                        {'name': 'Postcode','attributes' : {'value':'postcode'} },
                        {'name': 'Zone','attributes' : {'value':'zone'} },
                    ]
                },
                {
                    'type': 'optgroup',
                    'attributes' : {'label' :'Product Specific'},
                    'options' :[
                        {'name': 'Product','attributes' : {'value':'product'} },
                        {'name': 'Variable Product','attributes' : {'value':'variableproduct'} },
                        {'name': 'Category','attributes' : {'value':'category'} },
                        {'name': 'Tag','attributes' : {'value':'tag'} },
                        {'name': 'Product\'s quantity', 'attributes': {'value' : 'product_qty'} },
                        {'name': 'Product\'s count', 'attributes': {'value' : 'product_count'} },
                    ]
                },
                {
                    'type': 'optgroup',
                    'attributes' : {'label' : 'User Specific'},
                    'options': [
                        {'name' : 'User', 'attributes': {'value' : 'user'}},
                        {'name' : 'User Role', 'attributes': {'value' : 'user_role'}},
                        {'name' : 'User Email', 'attributes': {'value' : 'user_mail'}},
                    ]
                },
                {
                    'type': 'optgroup',
                    'attributes' : {'label' : 'Purchase History'},
                    'options': [
                        {'name' : 'Last order spent', 'attributes': {'value' : 'last_spent_order'}},
                        {'name' : 'Total order spent (all time)', 'attributes': {'value' : 'total_spent_order'}},
                        {'name' : 'Number of orders (all time)', 'attributes': {'value' : 'spent_order_count'}},
                        {'name' : 'User repeat product', 'attributes': {'value' : 'user_repeat_product'}},
                    ]
                },
                {
                    'type': 'optgroup',
                    'attributes' : {'label' : 'Cart Specific'},
                    'options': [
                        {'name' : 'Cart Subtotal (Before Discount)', 'attributes': {'value' : 'cart_total'}},
                        {'name' : 'Cart Subtotal (After Discount)', 'attributes': {'value' : 'cart_totalafter'}},
                        {'name' : 'Quantity', 'attributes': {'value' : 'quantity'}},
                        {'name' : 'Weight (kg)', 'attributes': {'value' : 'weight'}},
                        {'name' : 'Coupon', 'attributes': {'value' : 'coupon'}},
                        {'name' : 'Shipping Class', 'attributes': {'value' : 'shipping_class'}}
                    ]
                },
                {
                    'type': 'optgroup',
                    'attributes' : {'label' : 'Payment Specific'},
                    'options': [
                        {'name' : 'Payment Gateway', 'attributes': {'value' : 'payment'}},
                    ]
                },
                {
                    'type': 'optgroup',
                    'attributes' : {'label' : 'Shipping Specific'},
                    'options': [
                        {'name' : 'Shipping Method', 'attributes' : {'value' : 'shipping_method'}},
                        {'name' : 'Shipping Total', 'attributes' : {'value' : 'shipping_total'}},
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
            jQuery('#tbl-product-fee').block({
                message: null,
                overlayCSS: {
                    background: 'rgb(255, 255, 255)',
                    opacity: 0.6,
                },
            });

            // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
            jQuery.post(ajaxurl, data, function (response) {
                jQuery('.product_dpad_conditions_is_' + count).empty();
                var column=jQuery('#column_' + count).get(0);
                var condition_is=jQuery('.product_dpad_conditions_is_' + count).get(0);
                
                if (condition === 'cart_total'
                    || condition === 'quantity'
                    || condition === 'product_count'
                    || condition === 'total_spent_order'
                    || condition === 'spent_order_count'
                    || condition === 'last_spent_order'
                    || condition === 'cart_totalafter'
                    || condition === 'weight'
                    || condition === 'shipping_total'
                    || condition === 'product_qty'
                ) {
                    condition_is=insertOptions(condition_is,condition_types('number'));
                } else if( condition === 'user_mail' ) {
                    condition_is=insertOptions(condition_is,condition_types('mail'));
                } else {
                    condition_is=insertOptions(condition_is,condition_types('string'));
                }
                jQuery('.product_dpad_conditions_is_' + count).trigger('chosen:updated');
                jQuery(column).empty();
                
                var condition_values_id='';
                if(condition === 'product'){
                    condition_values_id='product-filter';
                }
                if(condition === 'variableproduct'){
                    condition_values_id='var-product-filter';
                }
                if(condition === 'user_repeat_product'){
                    condition_values_id='product-variation-filter';
                }

                var condition_values;
                if(isJson(response)){
                    condition_values = document.createElement('select');

                    var product_condition_class;
                    if ( condition === 'product' ) {
                        product_condition_class = 'product_filter_select2';
                    } else if ( condition === 'variableproduct' ) {
                        product_condition_class = 'product_var_filter_select2';
                    } else {
                        product_condition_class = '';
                    }

                    if(condition === 'user_repeat_product'){
                        condition_values=setAllAttributes(condition_values,{
                            'name':  'dpad[product_dpad_conditions_values][value_'+count+'][]',
                            'class': 'all-products-variations product_discount_select product_dpad_conditions_values product_dpad_conditions_values_'+count,
                            'multiple': 'multiple',
                            'id':condition_values_id+'-'+count,
                            'placeholder': 'please enter 3 characters'
                        });
                    } else {
                        condition_values=setAllAttributes(condition_values,{
                            'name':  'dpad[product_dpad_conditions_values][value_'+count+'][]',
                            'class': 'product_dpad_conditions_values product_discount_select product_dpad_conditions_values_'+count+' multiselect2 '+product_condition_class+' multiselect2_' + count + '_' + condition,
                            'multiple': 'multiple',
                            'id':condition_values_id+'-'+count,
                            'placeholder': 'please enter 3 characters'
                        });
                    }

                    column.appendChild(condition_values);
                    data=JSON.parse(response);
                    condition_values=insertOptions(condition_values,data);
                } else {
                    var input_extra_class = '';
                    if (condition === 'quantity'
                    || condition === 'spent_order_count'
                    || condition === 'product_count'
                    || condition === 'product_qty'
                    ) {
                        input_extra_class = ' qty-class';
                    }
                    if (condition === 'weight') {
                        input_extra_class = ' weight-class';
                    }
                    if (condition === 'cart_total'
                        || condition === 'total_spent_order'
                        || condition === 'last_spent_order'
                        || condition === 'cart_totalafter'
                        || condition === 'shipping_total'
                    ) {
                        input_extra_class = ' price-class';
                    }
                    if (condition === 'user_mail') {
                        input_extra_class = ' user-mail-class';
                    }
                    
                    let fieldPlaceholder;
                    if ( condition === 'city' ) {
                        fieldPlaceholder = coditional_vars.select_city;
                    } else if ( condition === 'postcode' ) {
                        fieldPlaceholder = coditional_vars.select_postcode;
                    } else if (condition === 'user_mail') {
                        fieldPlaceholder = coditional_vars.select_user_mail;
                    } else if ( condition === 'product_qty' || condition === 'quantity' || condition === 'product_count' ) {
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
                    if (condition === 'user_mail') {
                        var condition_note = document.createElement('p');
                        var condition_strong_note = document.createElement('strong');

                        condition_note=setAllAttributes(condition_note, {
                            'class': 'dpad_conditions_notes',
                        });
                        condition_note.textContent = 'E.g., john.doe@gmail.com where, user name is "john.doe", domain is "gmail.com"';
                        
                        condition_strong_note.textContent = 'Note: ';
                        condition_note.prepend(condition_strong_note);

                        column.appendChild(condition_note);
                    }
                }

                column = $('#column_' + count).get(0);
                var input_node=document.createElement('input');
                input_node=setAllAttributes(input_node,{
                    'type':'hidden',
                    'name':'condition_key[value_'+count+'][]',
                    'value':''
                });
                column.appendChild(input_node);

                var p_node = document.createElement( 'p' );
                var b_node = document.createElement( 'b' );
                var b_text_node = document.createTextNode( coditional_vars.note );
                var text_node;
                if ( condition === 'product_qty' ) {
                    b_node = setAllAttributes( b_node, {
                        'style': 'color: red;',
                    } );
                    b_node.appendChild( b_text_node );
                    if ( condition === 'product_qty' ) {
                        text_node = document.createTextNode( coditional_vars.product_qty_msg );
                    }

                    p_node.appendChild( b_node );
                    p_node.appendChild( text_node );
                    column.appendChild( p_node );
                }

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
                } else if( condition === 'variableproduct' ) {
                    $( '.multiselect2_' + count + '_' + condition ).select2(select2object('wdpad_product_dpad_conditions_varible_values_product'));
                } else {
                    $( '.multiselect2_' + count + '_' + condition ).select2({
                        placeholder: selectCoundition
                    });
                }

                getProductListBasedOnThreeCharAfterUpdate();
                numberValidateForAdvanceRules();
                get_all_products_and_variations_select_init();
                jQuery('#tbl-product-fee').unblock();
            });
        }
        
        function condition_types( text ){
            if( 'number' === text ){
                return [
                    {'name': 'Equal to ( = )','attributes' : {'value':'is_equal_to'} },
                    {'name': 'Less or Equal to ( <= )','attributes' : {'value':'less_equal_to'} },
                    {'name': 'Less then ( < )','attributes' : {'value':'less_then'} },
                    {'name': 'Greater or Equal to ( >= )','attributes' : {'value':'greater_equal_to'} },
                    {'name': 'Greater then ( > )','attributes' : {'value':'greater_then'} },
                    {'name': 'Not Equal to ( != )','attributes' : {'value':'not_in'} },
                ];
            } else if( 'mail' === text ){
                return [
                    {'name': 'User Name ( john.doe )','attributes' : {'value':'user_name'} },
                    {'name': 'Domain ( @gmail.com )','attributes' : {'value':'domain_name'} },
                    {'name': 'Email Address','attributes' : {'value':'full_mail'} },
                ];
            } else if( 'time' === text ){
                return [
                    {'name': 'With in','attributes' : {'value':'within'} },
                    {'name': 'Before','attributes' : {'value':'before'} },
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
        getProductListBasedOnThreeCharAfterUpdate();
        numberValidateForAdvanceRules();
        get_all_products_and_variations_select_init();
        
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

        $('.submitDiscount').click(function(e){

            var discount_type = $('#dpad_settings_select_dpad_type').val();
            var discount_cost = $('#dpad_settings_product_cost').val();
            var adjustment_cost = $('#dpad_settings_adjustment_cost').val();
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
            } else if( 'adjustment' === discount_type && ( '' === adjustment_cost || 0 >= adjustment_cost ) ){
                if ( $( '#warning_msg_6' ).length < 1 ) {
                    var div_5 = document.createElement( 'div' );
                    div_5 = setAllAttributes( div_5, {
                        'class': 'warning_msg',
                        'id': 'warning_msg_6'
                    } );
                    div_5.textContent = coditional_vars.discount_cost_msg;
                    $( '.wdpad-main-table' ).prepend( div_5 );
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

            if( 'fixed' === discount_type || 'percentage' === discount_type ) {
                var price_cartqty_based = $('#price_cartqty_based').val();
                if (price_cartqty_based === 'qty_product_based') {
                    var f = 0;
                    $('.product_dpad_conditions_condition').each(function () {
            
                        if ($(this).val() === 'product' || $(this).val() === 'variableproduct' || $(this).val() === 'category' || $(this).val() === 'tag') {
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
                /* Checking product qty validation start */
                var product_qty_fees_conditions_conditions = $('select[name="dpad[product_dpad_conditions_condition][]"]').map(function () {
                    return $(this).val();
                }).get();

                if ( -1 !== product_qty_fees_conditions_conditions.indexOf('product_qty') ) {
                    if ( product_qty_fees_conditions_conditions.indexOf('product') === -1
                        && product_qty_fees_conditions_conditions.indexOf('variableproduct') === -1
                        && product_qty_fees_conditions_conditions.indexOf('category') === -1
                        && product_qty_fees_conditions_conditions.indexOf('tag') === -1 ) {
                        // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
                        if ( $( '#warning_msg_6' ).length < 1 ) {
                            var div_4 = document.createElement( 'div' );
                            div_4 = setAllAttributes( div_4, {
                                'class': 'warning_msg',
                                'id': 'warning_msg_6'
                            } );
                            div_4.textContent = coditional_vars.warning_msg6;
                            $( '.wdpad-main-table' ).prepend( div_4 );
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
                }
            }

            //BOGO validation
            if( 'bogo' === discount_type ){
                
                var bogo_ruleset_buy_get_values = $('select.bogo-products').map(function () {
                    if( $(this).val().length === 0 ) {
                        $(this).next().find('.select2-selection').css('border', '1px solid red');
                        return false;
                    } else {
                        $(this).next().find('.select2-selection').css('border', '');
                    }
                }).get();

                var bogo_qty_values = $('.bogo-qty').map(function () {
                    if( $(this).val().length === 0 ) {
                        $(this).addClass('error');
                        return false;
                    } else {
                        $(this).removeClass('error');
                    }
                }).get();
                if(bogo_ruleset_buy_get_values.length > 0 || bogo_qty_values > 0 ){
                    if ( $( '#bogo_validate_msg' ).length < 1 ) {
                        var div_3 = document.createElement( 'div' );
                        div_3 = setAllAttributes( div_3, {
                            'class': 'warning_msg',
                            'id': 'bogo_validate_msg'
                        } );
                        div_3.textContent = coditional_vars.bogo_validate_msg;
                        $( '.wdpad-main-table' ).prepend( div_3 );
                    }
                    if ( $( '#bogo_validate_msg' ).length ) {
                        $( 'html, body' ).animate( { scrollTop: 0 }, 'slow' );
                        setTimeout( function() {
                            $( '#bogo_validate_msg' ).remove();
                        }, 7000 );
                    }
                    return false;
                }
            }

            //Adjustment validation
            if( 'adjustment' === discount_type ){
                var adjustment_type = $('#dpad_settings_adjustment_type').val();
                if( 'product' === adjustment_type ){
                    var get_prod = jQuery('#dpad_settings_get_product').val();
                    if( null === get_prod ){
                        if ( $( '#adjustment_validate_msg' ).length < 1 ) {
                            var div_2 = document.createElement( 'div' );
                            div_2 = setAllAttributes( div_2, {
                                'class': 'warning_msg',
                                'id': 'adjustment_validate_msg'
                            } );
                            div_2.textContent = coditional_vars.adjustment_product_validate_msg;
                            $( '.wdpad-main-table' ).prepend( div_2 );
                        }
                        if ( $( '#adjustment_validate_msg' ).length ) {
                            $( 'html, body' ).animate( { scrollTop: 0 }, 'slow' );
                            setTimeout( function() {
                                $( '#adjustment_validate_msg' ).remove();
                            }, 7000 );
                        }
                        return false;
                    }
                } else if( 'category' === adjustment_type ){
                    var get_cat = jQuery('#dpad_settings_get_category').val();
                    if( null === get_cat ){
                        if ( $( '#adjustment_validate_msg' ).length < 1 ) {
                            var div_1 = document.createElement( 'div' );
                            div_1 = setAllAttributes( div_1, {
                                'class': 'warning_msg',
                                'id': 'adjustment_validate_msg'
                            } );
                            div_1.textContent = coditional_vars.adjustment_category_validate_msg;
                            $( '.wdpad-main-table' ).prepend( div_1 );
                        }
                        if ( $( '#adjustment_validate_msg' ).length ) {
                            $( 'html, body' ).animate( { scrollTop: 0 }, 'slow' );
                            setTimeout( function() {
                                $( '#adjustment_validate_msg' ).remove();
                            }, 7000 );
                        }
                        return false;
                    }
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

        function createAdvancePricingRulesField( field_type, qty_or_weight, field_title, field_count, field_title2, category_list_option ) {
			var label_text, min_input_placeholder, max_input_placeholder, inpt_class, inpt_type;
			if ( qty_or_weight === 'qty' ) {
				label_text = coditional_vars.cart_qty;
			} else if ( qty_or_weight === 'weight' ) {
				label_text = coditional_vars.cart_weight;
			} else if ( qty_or_weight === 'subtotal' ) {
				label_text = coditional_vars.cart_subtotal;
			}

			if ( qty_or_weight === 'qty' ) {
				min_input_placeholder = coditional_vars.min_quantity;
			} else if ( qty_or_weight === 'weight' ) {
				min_input_placeholder = coditional_vars.min_weight;
			} else if ( qty_or_weight === 'subtotal' ) {
				min_input_placeholder = coditional_vars.min_subtotal;
			}

			if ( qty_or_weight === 'qty' ) {
				max_input_placeholder = coditional_vars.max_quantity;
			} else if ( qty_or_weight === 'weight' ) {
				max_input_placeholder = coditional_vars.max_weight;
			} else if ( qty_or_weight === 'subtotal' ) {
				max_input_placeholder = coditional_vars.max_subtotal;
			}

			if ( qty_or_weight === 'qty' ) {
				inpt_class = 'qty-class';
				inpt_type = 'number';
			} else if ( qty_or_weight === 'weight' ) {
				inpt_class = 'weight-class';
				inpt_type = 'text';
			} else if ( qty_or_weight === 'subtotal' ) {
				inpt_class = 'price-class';
				inpt_type = 'text';
			}
			var tr = document.createElement( 'tr' );
			tr = setAllAttributes( tr, {
				'class': 'ap_' + field_title + '_row_tr',
				'id': 'ap_' + field_title + '_row_' + field_count,
			} );

			var product_td = document.createElement( 'td' );
			if ( field_type === 'select' ) {
				var product_select = document.createElement( 'select' );
				product_select = setAllAttributes( product_select, {
					'rel-id': field_count,
					'id': 'ap_' + field_title + '_fees_conditions_condition_' + field_count,
					'name': 'dpad[ap_' + field_title + '_fees_conditions_condition][' + field_count + '][]',
					'class': 'wdpad_select ap_' + field_title + ' product_dpad_conditions_values multiselect2',
					'multiple': 'multiple',
				} );

                if (category_list_option === 'product_list') {
					product_select.setAttribute('data-placeholder', coditional_vars.select_product);
				} else if (category_list_option === 'category_list') {
					product_select.setAttribute('data-placeholder', coditional_vars.select_category);
				} else if (category_list_option === 'shipping_class_list') {
					product_select.setAttribute('data-placeholder', coditional_vars.select_shipping_class);
                }

				product_td.appendChild( product_select );
                var all_category_option, option, category_option;
				if ( category_list_option === 'category_list' ) {
					all_category_option = JSON.parse( $( '#all_category_list' ).html() );
					for ( i = 0; i < all_category_option.length; i ++ ) {
						option = document.createElement( 'option' );
						category_option = all_category_option[ i ];
						option.value = category_option.attributes.value;
						option.text = allowSpeicalCharacter( category_option.name );
						product_select.appendChild( option );
					}
				}
				if ( category_list_option === 'shipping_class_list' ) {
				    all_category_option = JSON.parse( $( '#all_shipping_class_list' ).html() );
					for ( i = 0; i < all_category_option.length; i ++ ) {
						option = document.createElement( 'option' );
						category_option = all_category_option[ i ];
						option.value = category_option.attributes.value;
						option.text = allowSpeicalCharacter( category_option.name );
						product_select.appendChild( option );
					}
				}
			}
			if ( field_type === 'label' ) {
				var product_label = document.createElement( 'label' );
				var product_label_text = document.createTextNode( label_text );
				product_label = setAllAttributes( product_label, {
					'for': label_text.toLowerCase(),
				} );
				product_label.appendChild( product_label_text );
				product_td.appendChild( product_label );

				var input_hidden = document.createElement( 'input' );
				input_hidden = setAllAttributes( input_hidden, {
					'id': 'ap_' + field_title + '_fees_conditions_condition_' + field_count,
					'type': 'hidden',
					'name': 'dpad[ap_' + field_title + '_fees_conditions_condition][' + field_count + '][]',
				} );
				product_td.appendChild( input_hidden );
			}
			tr.appendChild( product_td );

			var min_qty_td = document.createElement( 'td' );
			min_qty_td = setAllAttributes( min_qty_td, {
				'class': 'column_' + field_count + ' condition-value',
			} );
			var min_qty_input = document.createElement( 'input' );
			if ( qty_or_weight === 'qty' ) {
				min_qty_input = setAllAttributes( min_qty_input, {
					'type': inpt_type,
					'id': 'ap_fees_ap_' + field_title2 + '_min_' + qty_or_weight + '[]',
					'name': 'dpad[ap_fees_ap_' + field_title2 + '_min_' + qty_or_weight + '][]',
					'class': 'text-class ' + inpt_class,
					'placeholder': min_input_placeholder,
					'value': '',
					'min': '1',
					'required': '1',
				} );
			} else {
				min_qty_input = setAllAttributes( min_qty_input, {
					'type': inpt_type,
					'id': 'ap_fees_ap_' + field_title2 + '_min_' + qty_or_weight + '[]',
					'name': 'dpad[ap_fees_ap_' + field_title2 + '_min_' + qty_or_weight + '][]',
					'class': 'text-class ' + inpt_class,
					'placeholder': min_input_placeholder,
					'value': '',
					'required': '1',
				} );
			}
			min_qty_td.appendChild( min_qty_input );
			tr.appendChild( min_qty_td );

			var max_qty_td = document.createElement( 'td' );
			max_qty_td = setAllAttributes( max_qty_td, {
				'class': 'column_' + field_count + ' condition-value',
			} );
			var max_qty_input = document.createElement( 'input' );
			if ( qty_or_weight === 'qty' ) {
				max_qty_input = setAllAttributes( max_qty_input, {
					'type': inpt_type,
					'id': 'ap_fees_ap_' + field_title2 + '_max_' + qty_or_weight + '[]',
					'name': 'dpad[ap_fees_ap_' + field_title2 + '_max_' + qty_or_weight + '][]',
					'class': 'text-class ' + inpt_class,
					'placeholder': max_input_placeholder,
					'value': '',
					'min': '1',
				} );
			} else {
				max_qty_input = setAllAttributes( max_qty_input, {
					'type': inpt_type,
					'id': 'ap_fees_ap_' + field_title2 + '_max_' + qty_or_weight + '[]',
					'name': 'dpad[ap_fees_ap_' + field_title2 + '_max_' + qty_or_weight + '][]',
					'class': 'text-class ' + inpt_class,
					'placeholder': max_input_placeholder,
					'value': '',
				} );
			}
			max_qty_td.appendChild( max_qty_input );
			tr.appendChild( max_qty_td );

			var price_td = document.createElement( 'td' );
			var price_input = document.createElement( 'input' );
			price_input = setAllAttributes( price_input, {
				'type': 'text',
				'id': 'ap_fees_ap_price_' + field_title + '[]',
				'name': 'dpad[ap_fees_ap_price_' + field_title + '][]',
				'class': 'text-class number-field',
				'placeholder': coditional_vars.amount,
				'value': '',
			} );
			price_td.appendChild( price_input );

			tr.appendChild( price_td );

			var delete_td = document.createElement( 'td' );
			var delete_a = document.createElement( 'a' );
			delete_a = setAllAttributes( delete_a, {
				'id': 'ap_' + field_title + '_delete_field',
				'rel-id': field_count,
				'title': coditional_vars.delete,
				'class': 'delete-row',
				'href': 'javascript:void(0);'
			} );
			var delete_i = document.createElement( 'i' );
			delete_i = setAllAttributes( delete_i, {
				'class': 'dashicons dashicons-trash'
			} );
			delete_a.appendChild( delete_i );
			delete_td.appendChild( delete_a );

			tr.appendChild( delete_td );

			$( '#tbl_ap_' + field_title + '_method tbody tr' ).last().after( tr );
		}

        function getProductListBasedOnThreeCharAfterUpdate() {
            var advance_rule_product_more = false;
            $( '.fees_pricing_rules .ap_product, .fees_pricing_rules .ap_product_subtotal, .fees_pricing_rules .ap_product_weight' ).each( function() {
                $( '.fees_pricing_rules .ap_product, .fees_pricing_rules .ap_product_subtotal, .fees_pricing_rules .ap_product_weight' ).select2( {
                    ajax: {
                        url: coditional_vars.ajaxurl,
                        dataType: 'json',
                        delay: 250,
                        allowSpeicalCharacter: false,
                        dropdownAutoWidth : true,
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
                            advance_rule_product_more = data.length > 0 ? true : false;
                            return {
                                results: options,
                                pagination: {
                                    more : advance_rule_product_more
                                } 
                            };
                        },
                        cache: true
                    },
                    minimumInputLength: 3,
                    allowClear: true,
                } );
            } );
        }

        function numberValidateForAdvanceRules() {
            $( '.number-field' ).keypress( function( e ) {
                var regex = new RegExp( '^[0-9-%.]+$' );
                var str = String.fromCharCode( ! e.charCode ? e.which : e.charCode );
                if ( regex.test( str ) ) {
                    return true;
                }
                e.preventDefault();
                return false;
            } );
            $( '.qty-class' ).keypress( function( e ) {
                var regex = new RegExp( '^[0-9]+$' );
                var str = String.fromCharCode( ! e.charCode ? e.which : e.charCode );
                if ( regex.test( str ) ) {
                    return true;
                }
                e.preventDefault();
                return false;
            } );
            $( '.weight-class, .price-class' ).keypress( function( e ) {
                var regex = new RegExp( '^[0-9.]+$' );
                var str = String.fromCharCode( ! e.charCode ? e.which : e.charCode );
                if ( regex.test( str ) ) {
                    return true;
                }
                e.preventDefault();
                return false;
            } );
        }

        /* Defines AP Rules validate functions */
		function is_percent_valid() {
			//check amount only contains number or percentage
			$( '.percent_only' ).blur( function() {

				//regular expression for the valid amount enter like 20 or 20% or 50.0 or 50.55% etc.. is valid
				var is_valid_percent = /^[-]{0,1}((100)|(\d{1,2}(\.\d{1,2})?))[%]{0,1}$/;
				var percent_val = $( this ).val();
				//check that entered amount for the advanced price is valid or not like 20 or 20% or 50.0 or 50.55% etc.. is valid
				if ( ! is_valid_percent.test( percent_val ) ) {
					$( this ).val( '' );//if percent not in proper format than it will blank the textbox
				}
				//display note that if admin add - price than how message display in shipping method
				var first_char = percent_val.charAt( 0 );
				if ( first_char === '-' ) {
					//remove old notice message if exist
					$( this ).next().remove( 'p' );
					$( this ).after( coditional_vars.warning_msg1 );
				} else {
					//remove notice message if value is possitive
					$( this ).next().remove( 'p' );
				}
			} );
		}
        
        /* Add AP Product functionality start */
		//get total count row from hidden field
		var row_product_ele = $( '#total_row_product' ).val();
		var count_product;
		if ( row_product_ele > 2 ) {
			count_product = row_product_ele;
		} else {
			count_product = 2;
		}

		//on click add rule create new method row
		$( 'body' ).on( 'click', '#ap-product-add-field', function() {
			//design new format of advanced pricing method row html
			createAdvancePricingRulesField( 'select', 'qty', 'product', count_product, 'prd', 'product_list' );
			getProductListBasedOnThreeCharAfterUpdate();
			numberValidateForAdvanceRules();
			is_percent_valid();//bind percent on blur event for checking the amount is proper format or not

            // Active rule status when add new rule
			if ( 2 === parseInt(count_product) ) {
				$('input[name="cost_on_product_status"]').prop('checked', true);
			}

			count_product ++;
		} );
		/* Add AP Product functionality end here */

        /* Add AP product subtotal functionality start */
		//get total count row from hidden field
		var row_total_row_product_subtotal_ele = $( '#total_row_product_subtotal' ).val();
		var count_product_subtotal;
		if ( row_total_row_product_subtotal_ele > 2 ) {
			count_product_subtotal = row_product_ele;
		} else {
			count_product_subtotal = 2;
		}

		//on click add rule create new method row
		$( 'body' ).on( 'click', '#ap-product-subtotal-add-field', function() {
			//design new format of advanced pricing method row html
			createAdvancePricingRulesField( 'select', 'subtotal', 'product_subtotal', count_product_subtotal, 'product_subtotal', 'product_list' );
			getProductListBasedOnThreeCharAfterUpdate();
			numberValidateForAdvanceRules();
			is_percent_valid();//bind percent on blur event for checking the amount is proper format or not

            // Active rule status when add new rule
			if ( 2 === parseInt(count_product_subtotal) ) {
				$('input[name="cost_on_product_subtotal_status"]').prop('checked', true);
			}

			count_product_subtotal ++;
		} );
        /* Add AP product subtotal functionality end here */

        /* Add AP product weight functionality start */
        //get total count row from hidden field for Product Weight
		var count_product_weight_ele = $( '#total_row_product_weight' ).val();
		var count_product_weight;
		if ( count_product_weight_ele > 2 ) {
			count_product_weight = count_product_weight_ele;
		} else {
			count_product_weight = 2;
		}
		//on click add rule create new method row for Product Weight
		$( 'body' ).on( 'click', '#ap-product-weight-add-field', function() {
			createAdvancePricingRulesField( 'select', 'weight', 'product_weight', count_product_weight, 'product_weight', 'product_list' );
			numberValidateForAdvanceRules();
			getProductListBasedOnThreeCharAfterUpdate();
			is_percent_valid();//bind percent on blur event for checking the amount is proper format or not

            // Active rule status when add new rule
            if ( 2 === parseInt(count_product_weight) ) {
                $('input[name="cost_on_product_weight_status"]').prop('checked', true);
            }

			count_product_weight ++;
		} );
        /* Add AP product weight functionality end here */

        /* Add AP Category functionality start here */
		//get total count row from hidden field
		var row_category_ele = $( '#total_row_category' ).val();
		var row_category_count;
		if ( row_category_ele > 2 ) {
			row_category_count = row_category_ele;
		} else {
			row_category_count = 2;
		}
		//on click add rule create new method row
		$( 'body' ).on( 'click', '#ap-category-add-field', function() {
			createAdvancePricingRulesField( 'select', 'qty', 'category', row_category_count, 'cat', 'category_list' );
			jQuery( '.ap_category' ).select2();
			numberValidateForAdvanceRules();
			//set default category list to newly added category dropdown
			//rebide the new row with validation
			is_percent_valid();//bind percent on blur event for checking the amount is proper format or not

            // Active rule status when add new rule
			if ( 2 === parseInt(row_category_count) ) {
				$('input[name="cost_on_category_status"]').prop('checked', true);
			}

			row_category_count ++;
		} );
        /* Add AP Category functionality end here */

        /* Add AP Category subtotal functionality start here*/
		//get total count row from hidden field
		var row_category_subtotal_ele = $( '#total_row_category_subtotal' ).val();
		var row_category_subtotal_count;
		if ( row_category_subtotal_ele > 2 ) {
			row_category_subtotal_count = row_category_subtotal_ele;
		} else {
			row_category_subtotal_count = 2;
		}
		//on click add rule create new method row
		$( 'body' ).on( 'click', '#ap-category-subtotal-add-field', function() {
			createAdvancePricingRulesField( 'select', 'subtotal', 'category_subtotal', row_category_subtotal_count, 'category_subtotal', 'category_list' );
			jQuery( '.ap_category_subtotal' ).select2();
			//set default category list to newly added category dropdown
			numberValidateForAdvanceRules();
			// jQuery('#ap_category_fees_conditions_condition_' + count).trigger("chosen:updated");
			//rebide the new row with validation
			is_percent_valid();//bind percent on blur event for checking the amount is proper format or not

            // Active rule status when add new rule
			if ( 2 === parseInt(row_category_subtotal_count) ) {
				$('input[name="cost_on_category_subtotal_status"]').prop('checked', true);
			}

			row_category_subtotal_count ++;
		} );
        /* Add AP Category subtotal functionality end here*/

        /* Add AP Category weight functionality start here*/
        //get total count row from hidden field for Category Weight
		var category_weight_ele = $( '#total_row_category_weight' ).val();
		var category_weight_count;
		if ( category_weight_ele > 2 ) {
			category_weight_count = category_weight_ele;
		} else {
			category_weight_count = 2;
		}
		//on click add rule create new method row for Category Weight
		$( 'body' ).on( 'click', '#ap-category-weight-add-field', function() {
			createAdvancePricingRulesField( 'select', 'weight', 'category_weight', category_weight_count, 'category_weight', 'category_list' );
			jQuery( '.ap_category_weight' ).select2();
			numberValidateForAdvanceRules();
			// jQuery('#ap_category_weight_fees_conditions_condition_' + count).trigger('change');
			//rebide the new row with validation
			is_percent_valid();//bind percent on blur event for checking the amount is proper format or not

            // Active rule status when add new rule
			if ( 2 === parseInt(category_weight_count) ) {
				$('input[name="cost_on_category_weight_status"]').prop('checked', true);
			}

			category_weight_count ++;
		} );
        /* Add AP Category weight functionality end here*/

        /* Add AP cart qty functionality start here*/
        //get total count row from hidden field fro cart qty
		var total_cart_qty_ele = $( '#total_row_total_cart_qty' ).val();
		var total_cart_qty_count;
		if ( total_cart_qty_ele > 2 ) {
			total_cart_qty_count = total_cart_qty_ele;
		} else {
			total_cart_qty_count = 2;
		}
		//on click add rule create new method row for total cart
		$( 'body' ).on( 'click', '#ap-total-cart-qty-add-field', function() {
			createAdvancePricingRulesField( 'label', 'qty', 'total_cart_qty', total_cart_qty_count, 'total_cart_qty', '' );
			numberValidateForAdvanceRules();
			//rebide the new row with validation
			is_percent_valid();//bind percent on blur event for checking the amount is proper format or not

            // Active rule status when add new rule
			if ( 2 === parseInt(total_cart_qty_count) ) {
				$('input[name="cost_on_total_cart_qty_status"]').prop('checked', true);
			}

			total_cart_qty_count ++;
		} );
        /* Add AP cart qty functionality end here*/

        /* Add AP cart weight functionality start here*/
        //get total count row from hidden field fro cart weight
		var total_cart_weight_ele = $( '#total_row_total_cart_weight' ).val();
		var total_cart_weight_count;
		if ( total_cart_weight_ele > 2 ) {
			total_cart_weight_count = total_cart_weight_ele;
		} else {
			total_cart_weight_count = 2;
		}
		//on click add rule create new method row for total cart weight
		$( 'body' ).on( 'click', '#ap-total-cart-weight-add-field', function() {
			createAdvancePricingRulesField( 'label', 'weight', 'total_cart_weight', total_cart_weight_count, 'total_cart_weight', '' );
			numberValidateForAdvanceRules();
			//rebide the new row with validation
			is_percent_valid();//bind percent on blur event for checking the amount is proper format or not

            // Active rule status when add new rule
			if ( 2 === parseInt(total_cart_weight_count) ) {
				$('input[name="cost_on_total_cart_weight_status"]').prop('checked', true);
			}

			total_cart_weight_count ++;
		} );
        /* Add AP cart weight functionality end here*/

        /* Add AP cart subtotal functionality start here*/
        //get total count row from hidden field fro cart subtotal
		var total_cart_subtotal_ele = $( '#total_row_total_cart_subtotal' ).val();
		var total_cart_subtotal_count;
		if ( total_cart_subtotal_ele > 2 ) {
			total_cart_subtotal_count = total_cart_subtotal_ele;
		} else {
			total_cart_subtotal_count = 2;
		}
		//on click add rule create new method row for total cart weight
		$( 'body' ).on( 'click', '#ap-total-cart-subtotal-add-field', function() {
			createAdvancePricingRulesField( 'label', 'subtotal', 'total_cart_subtotal', total_cart_subtotal_count, 'total_cart_subtotal', '' );
			numberValidateForAdvanceRules();
			//rebide the new row with validation
			is_percent_valid();//bind percent on blur event for checking the amount is proper format or not

            // Active rule status when add new rule
			if ( 2 === parseInt(total_cart_subtotal_count) ) {
				$('input[name="cost_on_total_cart_subtotal_status"]').prop('checked', true);
			}

			total_cart_subtotal_count ++;
		} );
        /* Add AP cart subtotal functionality end here*/

        /* Add AP shipping class subtotal functionality start here*/
        //get total count row from hidden field fro cart weight
		var shipping_class_subtotal_ele = $( '#total_row_shipping_class_subtotal' ).val();
		var shipping_class_subtotal_count;
		if ( shipping_class_subtotal_ele > 2 ) {
			shipping_class_subtotal_count = shipping_class_subtotal_ele;
		} else {
			shipping_class_subtotal_count = 2;
		}
		//on click add rule create new method row for total cart weight
		$( 'body' ).on( 'click', '#ap-shipping-class-subtotal-add-field', function() {
			createAdvancePricingRulesField( 'select', 'subtotal', 'shipping_class_subtotal', shipping_class_subtotal_count, 'shipping_class_subtotal', 'shipping_class_list' );
			jQuery( '.ap_shipping_class_subtotal' ).select2();
			numberValidateForAdvanceRules();
			//rebide the new row with validation
			is_percent_valid();//bind percent on blur event for checking the amount is proper format or not

            // Active rule status when add new rule
			if ( 2 === parseInt(shipping_class_subtotal_count) ) {
				$('input[name="cost_on_shipping_class_subtotal_status"]').prop('checked', true);
			}

			shipping_class_subtotal_count ++;
		} );
        /* Add AP shipping class subtotal functionality end here*/

        $( 'ul.tabs li' ).click( function() {
			var tab_id = $( this ).attr( 'data-tab' );

			$( 'ul.tabs li' ).removeClass( 'current' );
			$( '.tab-content' ).removeClass( 'current' );

			$( this ).addClass( 'current' );
			$( '#' + tab_id ).addClass( 'current' );
		} );
        if ( jQuery( window ).width() <= 980 ) {
            jQuery( '.fees-pricing-rules .fees_pricing_rules .tab-content' ).click( function() {
                var acc_id = jQuery( this ).attr( 'id' );
                jQuery( '.fees-pricing-rules .fees_pricing_rules .tab-content' ).removeClass( 'current' );
                jQuery( '#' + acc_id ).addClass( 'current' );
            } );
        }
        //remove tr on delete icon click
		$( 'body' ).on( 'click', '.delete-row', function() {
			$( this ).parent().parent().remove();
		} );

        function hideShowPricingRulesBasedOnPricingRuleStatus( elem ) {
            if ( jQuery( elem ).prop( 'checked' ) === true ) {
				jQuery( '.fees_pricing_rules' ).show();
				jQuery( '.multiselect2' ).select2();
			} else if ( $( elem ).prop( 'checked' ) === false ) {
				jQuery( '.fees_pricing_rules' ).hide();
			}
        }
        hideShowPricingRulesBasedOnPricingRuleStatus( 'input[name="ap_rule_status"]' );

        jQuery( 'body' ).on( 'click', 'input[name="ap_rule_status"]', function() {
			hideShowPricingRulesBasedOnPricingRuleStatus( this );
            getProductListBasedOnThreeCharAfterUpdate();
		} );
        getProductListBasedOnThreeCharAfterUpdate();
        
        //BOGO ruleset select2
        function getBOGOSelectDropdown(){
            var bogo_product_more = false;
            $( '.bogo-products' ).select2({
                ajax: {
                    url: coditional_vars.ajaxurl,
                    dataType: 'json',
                    delay: 250,
                    allowSpeicalCharacter: false,
                    dropdownAutoWidth : true,
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
            }).on('select2:close', function() {
                if( $(this).hasClass('bogo-buy-products') ) {
                    let count = $(this).select2('data').length;
                    showCopyLink(count);
                }
            });
            if( $( '.bogo-buy-products' ).length > 0 ){
                showCopyLink($( '.bogo-buy-products' ).select2('data').length);
            }
        }
        getBOGOSelectDropdown();
        function showCopyLink( count ){
            if( count > 3 ) {
                $('.copy_buy_product').show();
            } else {
                $('.copy_buy_product').hide();
            }
        }

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

        //Adjustment Product list ajax with select2 
        var product_more = false;
        $( '#dpad_settings_get_product' ).select2({
            ajax: {
                url: coditional_vars.ajaxurl,
                dataType: 'json',
                delay: 250,
                allowSpeicalCharacter: false,
                dropdownAutoWidth : true,
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
                    product_more = data.length > 0 ? true : false;
                    return {
                        results: options,
                        pagination: {
                            more : product_more
                        } 
                    };
                },
                cache: true
            },
            minimumInputLength: 3,
            allowClear: true,
            placeholder: coditional_vars.select2_product_placeholder
        }).on('select2:open', function() {
            //Make selection of search
            $('.select2-search__field:last').focus().select();
        });

        //Adjustment Category list ajax with select2 
        var category_more = false;
        $( '#dpad_settings_get_category' ).select2({
            ajax: {
                url: coditional_vars.ajaxurl,
                dataType: 'json',
                delay: 250,
                allowSpeicalCharacter: false,
                dropdownAutoWidth : true,
                data: function( params ) {
                    return {
                        value: params.term,
                        action: 'wdpad_category_list_ajax',
                        security: coditional_vars.wcdrfc_ajax_verification_nonce,
                        posts_per_page: coditional_vars.select2_per_category_ajax,
                        offset: params.page || 1,
                    };
                },
                processResults: function( data ) {
                    var options = [];
                    if ( data ) {
                        $.each( data, function( index, text ) {
                            options.push( { id: index, text: allowSpeicalCharacter( text ) } );
                        } );
                    }
                    category_more = Object.keys(data).length > 0 ? true : false;
                    return {
                        results: options,
                        pagination: {
                            more : category_more
                        } 
                    };
                },
                cache: true
            },
            minimumInputLength: 3,
            allowClear: true,
            placeholder: coditional_vars.select2_category_placeholder
        }).on('select2:open', function() {
            //Make selection of search
            $('.select2-search__field:last').focus().select();
        });

        //Copy one buy product to Get product in BOGO ruleset
        $(document).on( 'click', '.copy_buy_product', function(){
            var buy_data = $(this).parent().parent().find('.bogo-buy-products').select2('data');
            var already_avail = [];

            //Null before add all new value from buy select2
            var get_select = $(this).parent().find('.bogo-get-products');
            get_select.val(null).trigger('change');
            
            //fire iteration data comes from buy select2
            $(buy_data).each(function(index, d){
                if (get_select.find('option[value="' + d.id + '"]').length ) {
                    // already_avail.push(d.id);
                } else {
                    var newOption = new Option(d.text, d.id, true, true);
                    get_select.append(newOption);
                }
                already_avail.push(d.id);
            });
            //If already appended in select option & selected
            if( 0 !== already_avail.length ){
                get_select.val(already_avail);
            }
            // Append it to the select
            get_select.trigger('change');
        });

        $( '.product_dpad_conditions_values_country' ).select2({
			placeholder: coditional_vars.select_country
		});
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
                placeholder: {
                    element: function(currentItem) {
                        var cols    =   jQuery(currentItem).children('td').not('.hidden').length + 1;
                        return jQuery('<tr class="ui-sortable-placeholder"><td colspan="' + cols + '">&nbsp;</td></tr>')[0];
                    },
                    update: function() {
                        return;
                    }
                },
                'axis': 'y',
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
                        $(div_wrap).insertAfter($('.wp-header-end'));
                        setTimeout( function(){
                            div_wrap.remove();
                        }, 2000 );
                    });
                }
            });
            $( '.wdpad-main-table .wp-list-table tbody' ).disableSelection();
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

            if( !$('#dpad_select_day_of_week').data('select2') ){
                $('#dpad_select_day_of_week').select2({
                    placeholder: 'Select days of the week', 
                    allowClear: true
                });
            }
        });

        $('.bogo-info').click(function(){
            $('.bogo-info-section').slideToggle();
        });

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

        // Export AJAX Call
        $('#wdpad_export_settings').click(function(){
            var action = $('input[name="wdpad_export_action"]').val();
            var security = $('input[name="wdpad_export_action_nonce"]').val();
            var $this = $(this);
            $('.wdpad-main-table').block({
                message: null,
                overlayCSS: {
                    background: 'rgb(255, 255, 255)',
                    opacity: 0.6,
                },
            });
            if( action && security ){
                $this.attr('disabled','disabled');
                $.ajax({
                    type: 'POST',
                    url: coditional_vars.ajaxurl,
                    data: {
                        'action': action,
                        'security': security,
                    },
                    success: function( response ){
                        $('.wdpad-main-table').unblock();
                        var div_wrap = $('<div></div>').addClass('notice');
                        var p_text = $('<p></p>').text(response.data.message);
                        if( response.success ){
                            div_wrap.addClass('notice-success');
                        } else {
                            div_wrap.addClass('notice-error');
                        }
                        div_wrap.append(p_text);
                        $(div_wrap).insertAfter($('.wp-header-end'));

                        //download link generation
                        if( response.data.download_path ){
                            var link = document.createElement('a');
                            link.href = response.data.download_path;
                            link.download = '';
                            document.body.appendChild(link);
                            link.click();
                        }
                        setTimeout(function(){
                            div_wrap.remove();
                            $this.attr('disabled', null);
                            link.remove();
                        }, 2000);
                    }
                });
            }
        });

        // Import AJAX Call
        $('#wdpad_import_setting').click(function(){
            var action = $('input[name="wdpad_import_action"]').val();
            var security = $('input[name="wdpad_import_action_nonce"]').val();
            var $this = $(this);
            $('.wdpad-main-table').block({
                message: null,
                overlayCSS: {
                    background: 'rgb(255, 255, 255)',
                    opacity: 0.6,
                },
            });
            if( action && security ){
                $this.attr('disabled','disabled');
                var fd = new FormData();
                fd.append('import_file', $('input[name="import_file"]')[0].files[0]);  
                fd.append('action', action);
                fd.append('security', security);

                $.ajax({
                    type: 'POST',
                    url: coditional_vars.ajaxurl,
                    data: fd,
                    contentType: false,
                    processData: false,
                    success: function( response ){
                        $('.wdpad-main-table').unblock();
                        var div_wrap = $('<div></div>').addClass('notice');
                        var p_text = $('<p></p>').text(response.data.message);
                        if(response.success){
                            div_wrap.addClass('notice-success');
                        } else {
                            div_wrap.addClass('notice-error');
                            $this.attr('disabled', null);
                        }
                        jQuery('input[name="import_file"]').val('');
                        div_wrap.append(p_text);
                        $(div_wrap).insertAfter($('.wp-header-end'));
                        setTimeout( function(){
                            div_wrap.remove();
                            $this.attr('disabled', null);
                        }, 3000 );
                    }
                });
            }
        });

        //Fixed/Percentage/BOGO/Combinaton rule filed
        var discount_type = $('#dpad_settings_select_dpad_type').val();
        showFieldBasedOnType(discount_type);  

        $('#dpad_settings_select_dpad_type').change(function(){
            var discount_type = $(this).val();
            showFieldBasedOnType(discount_type);
        });
        function showFieldBasedOnType(discount_type){
            $('.type-section').hide();
            if( 'fixed' === discount_type || 'percentage' === discount_type ) {
                $('.fp-section').show();
            } else if( 'bogo' === discount_type ) {
                $('.bogo-section').show();
            } else if( 'adjustment' === discount_type ) {
                var adjustment_type = $('#dpad_settings_adjustment_type').val();
                showFieldBasedOnAdjustmentType(adjustment_type);
            }
        }

        //Combinaton rule filed
        $('#dpad_settings_adjustment_type').change(function(){
            var select_type = $(this).val();
            showFieldBasedOnAdjustmentType(select_type);
        });
        function showFieldBasedOnAdjustmentType(adjustment_type){
            $('.adjustment-section').show();
            if( 'product' === adjustment_type ) {
                $('.cc-section').hide();
            } else if( 'category' === adjustment_type ) {
                $('.cp-section').hide();
            } 
        }

        $('#dpad_gs_adjustment_discount_type').change(function(){
            var select_value = $(this).val();
            adjustment_discount_condition(select_value);
        });
        adjustment_discount_condition($('#dpad_gs_adjustment_discount_type').val());
        function adjustment_discount_condition(select_value){
            $('.adt_all').hide();
            if( 'all' === select_value ){
                $('.adt_all').show();
            }
        }

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