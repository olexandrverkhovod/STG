var nf_excelexport = nf_excelexport || {};


nf_excelexport.serialize_export_fields = function(){
    var $ = jQuery;
    var html_ids = $( '#ninja_forms_metabox_spreadsheet_export_fields_settings .form-table tbody' ).sortable( "toArray");
    // console.log(html_ids);
    var fields = [];
    for (var i = 0; i < html_ids.length; i++) {
        var $field = $('#'+html_ids[i]);
        var field_key = $field.data('key');
        var checked = $field.find('input[type="checkbox"]').prop('checked');
        fields.push({
            'field_key' : field_key,
            'checked' : (checked?1:0)
        });
    }
    // console.log(fields);
    $.post(
    	ajaxurl, 
    	{ 
    		action : 'nf_spreadsheet_save_field_settings', 
    		form_id : $('#spreadsheet_export_form_id').val(),
    		field_settings : fields
    	}
	);
}



nf_excelexport.init_filters = function(){
    var $ = jQuery;
    var $filters = $('#row_spreadsheet_export_filters td');

    if( $filters.find('.spreadsheet-filter-row').length <= 2 )
    	nf_excelexport.add_filter_row();
    
    $('.spreadsheet-filter-field').each(function(){
    	nf_excelexport.filter_field_change( $(this) );
    });
    $('.spreadsheet-filter-condition').each(function(){
    	nf_excelexport.filter_condition_change( $(this) );
    })
    nf_excelexport.serialize_filters();

    
    $filters.on('change','.spreadsheet-filter-field',function(){
    	nf_excelexport.filter_field_change( $(this) );
    	nf_excelexport.serialize_filters();
    })

    $filters.on('change','.spreadsheet-filter-condition',function(){
    	nf_excelexport.filter_condition_change( $(this) );
    	nf_excelexport.serialize_filters();
    })

    $filters.on('change','.spreadsheet-filter-value',function(){
    	nf_excelexport.serialize_filters();
    })

    $filters.on('click','.spreadsheet-filter-line-add', function(e){
    	e.preventDefault();
    	nf_excelexport.add_filter_row();	
    })

    $filters.on('click','.spreadsheet-filter-line-remove', function(e){
    	e.preventDefault();
    	if( !$(this).hasClass('disabled') ){
    		nf_excelexport.remove_filter_row( $(this) );	
    		nf_excelexport.serialize_filters();
    	}
    })
}


nf_excelexport.add_filter_row = function(){
	var $ = jQuery;
	var $filters = $('#row_spreadsheet_export_filters td');
	var $new_row = $filters.find('.spreadsheet-filter-row-template').clone();
	$new_row.removeClass( 'spreadsheet-filter-row-template' );
	$filters.append( $new_row );
	nf_excelexport.filter_field_change( $new_row.find('.spreadsheet-filter-field') );
	nf_excelexport.filter_condition_change( $new_row.find('.spreadsheet-filter-condition') );

	if( $filters.find('.spreadsheet-filter-row').length == 2 )
		$('.spreadsheet-filter-line-remove').addClass('disabled');
	else
		$('.spreadsheet-filter-line-remove').removeClass('disabled');
}

nf_excelexport.remove_filter_row = function( $button ){
	var $ = jQuery;
	var $filters = $('#row_spreadsheet_export_filters td');
	var $row = $button.parents('.spreadsheet-filter-row');
	$row.remove();

	if( $filters.find('.spreadsheet-filter-row').length == 2 )
		$('.spreadsheet-filter-line-remove').addClass('disabled');

	// just in case there's no row left
	if( $filters.find('.spreadsheet-filter-row').length <= 1 )
		nf_excelexport.add_filter_row();
}

nf_excelexport.filter_field_change = function( $field ){
	var $ = jQuery;
	var $row = $field.parents('.spreadsheet-filter-row');
	var $selected_option = $field.find( ':selected' );
	var field_type = $selected_option.data('fieldtype');

	// NO FIELD SELECTED
	$row.find( '.spreadsheet-filter-condition, .spreadsheet-filter-value' ).prop('disabled', ( field_type == 'none' ) );
	
	if( field_type == 'checkbox' || field_type.indexOf( 'optin' ) != -1 ){
		// CHECKBOX
		$row.find( '.spreadsheet-filter-condition option:not([value*="EMPTY"])' ).prop('disabled',true).prop('hidden',true);
		
		// if one of the forbidden conditions is selected
		if( $.inArray( $row.find( '.spreadsheet-filter-condition' ).val(),['EMPTY', 'NOTEMPTY']) == -1 )
			$row.find( '.spreadsheet-filter-condition' ).val('NOTEMPTY').change();

	}else if( $.inArray(field_type,['number', 'starrating', 'quantity', 'shipping', 'total']) != -1 ){
		// ANY KIND OF NUMERIC FIELD
		$row.find( '.spreadsheet-filter-condition option[value="CONTAINS"],\
					.spreadsheet-filter-condition option[value="LIKE"]' 
			).prop('disabled',true).prop('hidden',true);

		// if one of the forbidden conditions is selected
		if( $.inArray( $row.find( '.spreadsheet-filter-condition' ).val(),['CONTAINS', 'LIKE']) != -1 )
			$row.find( '.spreadsheet-filter-condition' ).val('EQUAL').change();

	}else if( $.inArray(field_type,['date']) !== -1 ){
		// DATE FIELD
		$row.find( '.spreadsheet-filter-condition option[value="CONTAINS"],\
					.spreadsheet-filter-condition option[value="NE"],\
					.spreadsheet-filter-condition option[value="LIKE"]' 
			).prop('disabled',true).prop('hidden',true);

		// if one of the forbidden conditions is selected
		if( $.inArray( $row.find( '.spreadsheet-filter-condition' ).val(),['CONTAINS', 'NE', 'LIKE']) != -1 )
			$row.find( '.spreadsheet-filter-condition' ).val('EQUAL').change();

		$row.find( '.spreadsheet-filter-value' ).datepicker( {
				dateFormat: "yy-mm-dd"
			} );
	}else{
		$row.find( '.spreadsheet-filter-condition option' ).prop('disabled',false).prop('hidden',false);
		$row.find( '.spreadsheet-filter-value' ).datepicker( 'destroy' );
	}
}


nf_excelexport.filter_condition_change = function( $condition_field ){
	var $ = jQuery;
	var $row = $condition_field.parents('.spreadsheet-filter-row');
	if( $condition_field.val() == 'NOTEMPTY' || $condition_field.val() == 'EMPTY' )
		$row.find('.spreadsheet-filter-value').fadeTo(400, 0.001);
	else
		$row.find('.spreadsheet-filter-value').fadeTo(400, 1.0);
}


nf_excelexport.serialize_filters = function(){
	var $ = jQuery;
	var $filters = $('#row_spreadsheet_export_filters td');
	var filters = [];
	$filters.find( '.spreadsheet-filter-row:not(.spreadsheet-filter-row-template)' ).each(function(){
		var $row = $(this);
		var field_key = $row.find('.spreadsheet-filter-field').val();
		var $selected_option = $row.find('.spreadsheet-filter-field').find( ':selected' );
		var field_type = $selected_option.data('fieldtype');
		var dateformat = $selected_option.data('dateformat');
		var field_id = $selected_option.data('fieldid');

		var condition = $row.find('.spreadsheet-filter-condition').val();
		var value = $row.find('.spreadsheet-filter-value').val();
		if( value != '' || condition == 'EMPTY' || condition == 'NOTEMPTY' )
			filters.push( {
				field_key: 	field_key,
				field_type: field_type,
				field_id: 	field_id,
				dateformat: dateformat,
				condition: 	condition, 
				value: 		value
			} );
	});

	$('#spreadsheet-serialized-filter').val( JSON.stringify( filters ) );

    $.post(
    	ajaxurl, 
    	{ 
    		action : 'nf_spreadsheet_save_filter', 
    		form_id : $('#spreadsheet_export_form_id').val(),
    		filter : filters
    	}
	);
}




nf_excelexport.spreadsheet_iteration = function(formData){
	var $ = jQuery;
	
	formData['action'] = 'nf_spreadsheet_export';

	$.post(ajaxurl, formData, function(data) {	
		data = JSON.parse(data);	
		// console.log(data); 
		if( data.iteration < data.num_iterations-2 ){
			formData['spreadsheet_export_iteration'] = (data.iteration+1);
			nf_excelexport.spreadsheet_iteration(formData);
			$('.spreadsheet-export-progress .percent').text( Math.floor((data.iteration+1)/data.num_iterations*100) + ' %' );
			$('.spreadsheet-export-progress progress').val( Math.floor((data.iteration+1)/data.num_iterations*100) );
		}else{
			$('#spreadsheet_export_iteration').val( data.iteration+1 );
			$('#nf_spreadsheet_export_form input,select').prop('disabled',false);
			$('#nf_spreadsheet_export_form .postbox').removeClass('exporting');
			$('.spreadsheet-export-progress .percent').text( '100 %' );
			$('.spreadsheet-export-progress progress').val( 100 );
			$('#ninja_forms_spreadsheet_submit').removeClass('disabled');
			$('#nf_spreadsheet_export_form').submit();
		}
	});
}


jQuery(document).ready(function($) {
	$('#spreadsheet_export_begin_date,#spreadsheet_export_end_date').datepicker( {
		dateFormat: "yy-mm-dd"
	} );


	$('#spreadsheet_export_form_id').change(function(){
		var $form_select = $(this);
		var url_array = window.location.href.split( '?' );
		$( '#ninja_forms_metabox_spreadsheet_export_fields_settings, #ninja_forms_metabox_spreadsheet_export_filter_settings' ).addClass('loading');
		$( '#ninja_forms_spreadsheet_submit').addClass('disabled');
		$.get( url_array[0],{
				'page'	: 'nf-excel-export',
				'spreadsheet_export_form_id'	:	$form_select.val()
			}, function( data ) {
			$( '#ninja_forms_metabox_spreadsheet_export_fields_settings' ).replaceWith( 
				$($.parseHTML(data)).find("#ninja_forms_metabox_spreadsheet_export_fields_settings")
			);
			$( '#ninja_forms_metabox_spreadsheet_export_fields_settings .form-table tbody' ).sortable({
				stop: function( event, ui ) {
				    nf_excelexport.serialize_export_fields();
				}});
			$('#ninja_forms_metabox_spreadsheet_export_fields_settings .form-table tbody input[type="checkbox"]').change(function(){
				nf_excelexport.serialize_export_fields();
			});
			$( '#ninja_forms_metabox_spreadsheet_export_filter_settings' ).replaceWith( 
				$($.parseHTML(data)).find("#ninja_forms_metabox_spreadsheet_export_filter_settings")
			);
			nf_excelexport.init_filters();
			$('#ninja_forms_spreadsheet_submit').removeClass('disabled');
		});
		
	});
	$('#spreadsheet_export_form_id').change();

	$('#ninja_forms_spreadsheet_submit').live('click',function(e){
		e.preventDefault();
		var $button = $(this);
		if( !$button.hasClass('disabled') ){
			$('#nf_spreadsheet_export_form').find('input,select').prop('disabled',true);
			$('#nf_spreadsheet_export_form .postbox').addClass('exporting');
			$button.addClass('disabled');
			$('.spreadsheet-export-progress .percent').text( '0 %' );
			$('.spreadsheet-export-progress progress').val( 0 );

			$('#spreadsheet_export_iteration').val( 0 );
			var formData = {};
			$('#nf_spreadsheet_export_form').find('input[name],select[name]').each(function(){
				var $field = $(this);
				if( $field.attr('type') == 'checkbox' )
					formData[$field.attr('name')] = ( $field.prop('checked')?1:0 );
				else if( $field.attr('type') == 'radio' ){
					if(  $field.prop('checked') )
						formData[$field.attr('name')] = $field.val();
				}else
					formData[$field.attr('name')] = $field.val();
				
			});

			nf_excelexport.spreadsheet_iteration(formData);
		}
		return false;
	});


	$( '#ninja_forms_metabox_spreadsheet_export_fields_settings .form-table' ).sortable();
});

