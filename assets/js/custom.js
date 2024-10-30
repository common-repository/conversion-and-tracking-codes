jQuery(document).on('click','#add_more_pages',function(){
    dat = jQuery('.parent_data').html();
    jQuery(".pages_table tr:last").after('<tr>'+dat+'</tr>');
});

jQuery(document).on('click','.remove_row',function(){
    jQuery(this).parent().parent().remove();
});