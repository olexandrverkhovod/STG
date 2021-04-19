jQuery(document).ready(function($) {
	$('#spreadsheet_export_begin_date,#spreadsheet_export_end_date').datepicker( {
		dateFormat: "yy-mm-dd"
	} );
	$('#spreadsheet_export_form_id').change(function(){
		var $form_select = $(this);
		var url_array = window.location.href.split( '?' );
		$( '#ninja_forms_metabox_spreadsheet-export-field-selection' ).addClass('loading');
		$('#ninja_forms_spreadsheet_submit').addClass('disabled');
		$.get( url_array[0],{
				'page'	: 'ninja-forms-spreadsheet',
				'tab'	:	'spreadsheet-export',
				'spreadsheet_export_form_id'	:	$form_select.val()
			}, function( data ) {
			$( '#ninja_forms_metabox_spreadsheet-export-field-selection' ).replaceWith( 
				$($.parseHTML(data)).find("#ninja_forms_metabox_spreadsheet-export-field-selection")
			);
			$('#ninja_forms_spreadsheet_submit').removeClass('disabled');
		});
		
	});
	$('#spreadsheet_export_form_id').change();

	$('#ninja_forms_spreadsheet_submit').live('click',function(e){
		var $button = $(this);
		if( !$button.hasClass('disabled') ){
			$('form#ninja_forms_admin').find('input[name],select').prop('disabled',true);
			$('form#ninja_forms_admin .postbox').addClass('exporting');
			$button.addClass('disabled');
			$('.spreadsheet-export-progress .percent').text( '0 %' );
			$('.spreadsheet-export-progress progress').val( 0 );

			$('form#ninja_forms_admin input[name="spreadsheet_export_iteration"]').val( 0 );
			var formData = {};
			$('form#ninja_forms_admin').find('input[name],select').each(function(){
				var $field = $(this);
				if( $field.attr('type') == 'checkbox' )
					formData[$field.attr('name')] = ( $field.prop('checked')?1:0 );
				else if( $field.attr('type') == 'radio' ){
					if(  $field.prop('checked') )
						formData[$field.attr('name')] = $field.val();
				}else
					formData[$field.attr('name')] = $field.val();
				
			});

			haet_export_spreadsheet_iteration(formData);
		}

		e.preventDefault();
	});
});

function haet_export_spreadsheet_iteration(formData){
	var $ = jQuery;
	$.ajax({
	    type        : 'POST', 
	    url         : window.location.href, 
	    data        : formData, 
	    dataType    : 'json', 
	    encode      : true
	}).done(function(data) {
		//console.log(data); 
		if( data.iteration < data.num_iterations-1 ){
			formData['spreadsheet_export_iteration'] = (data.iteration+1);
			haet_export_spreadsheet_iteration(formData);
			$('.spreadsheet-export-progress .percent').text( Math.floor((data.iteration+1)/data.num_iterations*100) + ' %' );
			$('.spreadsheet-export-progress progress').val( Math.floor((data.iteration+1)/data.num_iterations*100) );
		}else{
			$('form#ninja_forms_admin input[name="spreadsheet_export_iteration"]').val( data.iteration+1 );
			$('form#ninja_forms_admin input[name],select').prop('disabled',false);
			$('form#ninja_forms_admin .postbox').removeClass('exporting');
			$('.spreadsheet-export-progress .percent').text( '100 %' );
			$('.spreadsheet-export-progress progress').val( 100 );
			$('#ninja_forms_spreadsheet_submit').removeClass('disabled');
			$('form#ninja_forms_admin').submit();
		}
	});
}