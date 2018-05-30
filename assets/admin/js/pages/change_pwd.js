
   $('.join_now').click(function(){
      var myurl = "<?php echo site_url('../../sign-up');?>"
      location.href = myurl; 
   });

   var isValidGuid = function(value) {

  var validGuid = /^[a-zA-Z0-9 .!"'?&#^*_=`@:;-]+$/;
  return validGuid.test(value);

}
   
   $(document).ready(function () {

    $.validator.addMethod("noSpace", function(value, element) { 
        return value.indexOf(" ") < 0 && value != ""; 
    }, "Space Not Allowed");  

    




$.validator.addMethod("isValidGuid", function(value, element) {

  return isValidGuid(value);

}, 'Please select a valid Charcters');




    $(document).on('keydown', '#n_pwd', function(e) {
      if (e.keyCode == 32) return false;
    });
    $(document).on('keydown', '#c_pwd', function(e) {
      if (e.keyCode == 32) return false;
    });

      $("#chang_pwd").validate({ 
          rules: {
              n_pwd: {
                  required: true,
                  noSpace:true,
                  minlength: 6,
                  isValidGuid:true,
                  maxlength: 16

              },
              c_pwd: {
                  required: true,
                  noSpace: true,
                   equalTo: '#n_pwd',
                  maxlength: 16

              }
          },
          messages: {
               n_pwd: {
                  required: "Please enter password",
                  minlength: "Please enter atleast six characters",
                  maxlength:"morethan 16 charcter not allowed"

               },
               c_pwd: {
                  required: 'Please enter Confirm password too',
                  equalTo:  'Password and Confirm Password does not match',
                                    maxlength:"morethan 16 charcter not allowed"
               }
          },
          errorPlacement: function (error, element) {
              var attr_name = element.attr('name');
              if (attr_name == 'type') {
                  error.appendTo('.type_err');
              } else {
                  error.insertAfter(element.parent());
              }
          }
      });


   });

       