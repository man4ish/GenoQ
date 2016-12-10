
function isInArray(value, array) {
  return array.indexOf(value) > -1;
}


function selectRows() {
    var el = $(this);
    var ids = el.data('ids').split(',');
    var display; 

    $('#dataTable tr td:first-child + td').each(function(){
        if( isInArray( $(this).text(), ids ) ) {
            display = 'table-row'; 
        }
        else {
            display = 'none'
        }
        $(this).parent().css('display', display);
    });

}

$("#dataTable tr").click(function(){
   $(this).addClass('selected').siblings().removeClass('selected');    
   var id=$(this).find('td:first').html();
   var value=$(this).find('td:first +td').html();
   $('#snps_region_chr').val(id);		
   $('#snps_region_position').val(value); 
   $("#submit-button").click();
});

$('#summary-table').on('click', '#td-containing-ids', selectRows);

