jQuery(document).ready(function(){
  
    jQuery('.volume_row').hide();
    
    jQuery('.new_parent_page').hide();
    
    jQuery('#genmode').change(function(){
        if(jQuery(this).val()==1){
            jQuery('.volume_row').hide();
        }else{
            jQuery('.volume_row').show();
        }
    });
    
    if(jQuery('#page_id').size()==0){
        jQuery('#page_id').append('<select id="page_id" name="page_id"><option value="">(No Parent)</option></select>');
    }
    
    /**
     * This method will allow for new 'parent page' creation in future.
     */
//      jQuery('#parent_page_select').change(function(){
// 
//         if( jQuery("#parent_page_select option:selected").text() != '(New Page, Name:)' ){
//            // alert('not equal');
//             jQuery('.new_parent_page').hide();
//         }else{
//             // alert('equal');
//             jQuery('.new_parent_page').show();
//         }
//     });
     
    /**
     * This is for dynamically added volume rows
     */
//      //Compose template string
//     String.prototype.compose = (function (){
//         var re = /\{{(.+?)\}}/g;
//         return function (o){
//             return this.replace(re, function (_, k){
//                 return typeof o[k] != 'undefined' ? o[k] : '';
//             });
//         }
//     }());
//     
//     var id = 1;
//      
//     var tbody = jQuery('#myTable').children('tbody');
//     var table = tbody.length ? tbody : jQuery('#myTable');
//     
//     var row = '<tr><td>' + 'Vol {{id}}: ' +
//                 '<input type="text" id="{{id}}" name="vol_row[{{id}}]" value="" /></td></tr>';
// 
//     jQuery("#newvolumebutton").click( function(){
//         id++;
//         table.append(row.compose({
//             'id': id
//         }));
//     });
     
});
