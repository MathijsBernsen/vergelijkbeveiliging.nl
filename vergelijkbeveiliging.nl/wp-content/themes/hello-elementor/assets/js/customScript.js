jQuery(document).ready(function( $ ){

  $('#showMenu').click(function() {
        if($("#hiddenMenu").is(":hidden")){
          $('#hiddenMenu').toggle('slow');
        } else{
          $('#hiddenMenu').hide('slow');
        }
    });

    $('label').click(function() {
      setTimeout(function(){
        var isChecked = $('#form-field-type_offerte-2').attr('checked');
        if($("#form-field-type_offerte-2").is(":checked")){
          $('.elementor-element.elementor-element-f952462').css('display', 'block');
        } else {
          $('.elementor-element.elementor-element-f952462').css('display', 'none');
        }
      },50);

    });
    // $('.elementor-element.elementor-element-f952462').attr('id', 'important-display-none');
    $('.elementor-element.elementor-element-f952462').css('display', 'none');

})
