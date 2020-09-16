<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1.0, shrink-to-fit=no">
<link href="images/favicon.png" rel="icon" />
<title>{{env('APP_NAME')}} - Recharge & Bill Payment, Booking App</title>
<meta name="description" content="{{env('APP_NAME')}} - Recharge & Bill Payment">
<meta name="author" content="Pradeep Kumar|go4shoponline@gmail.com">

<!-- Web Fonts
============================================= -->
<link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Poppins:100,200,300,400,500,600,700,800,900' type='text/css'>

<!-- Stylesheet
============================================= -->
<link rel="stylesheet" type="text/css" href="{{config('global.THEME_PATH')}}/vendor/bootstrap/css/bootstrap.min.css" />
<link rel="stylesheet" type="text/css" href="{{config('global.THEME_PATH')}}/vendor/font-awesome/css/all.min.css" />
<link rel="stylesheet" type="text/css" href="{{config('global.THEME_PATH')}}/vendor/owl.carousel/assets/owl.carousel.min.css" />
<link rel="stylesheet" type="text/css" href="{{config('global.THEME_PATH')}}/vendor/owl.carousel/assets/owl.theme.default.min.css" />
<link rel="stylesheet" type="text/css" href="{{config('global.THEME_PATH')}}/vendor/jquery-ui/jquery-ui.css" />
<link rel="stylesheet" type="text/css" href="{{config('global.THEME_PATH')}}/vendor/daterangepicker/daterangepicker.css" />

<link rel="stylesheet" type="text/css" href="{{config('global.THEME_PATH')}}/css/stylesheet.css" />
</head>
<body>
<!-- Preloader -->
<div id="preloader">
  <div data-loader="dual-ring"></div>
</div>
<!-- Document Wrapper   
============================================= -->
<div id="main-wrapper"> 

<!-- Header
============================================= -->
  @include('header_user')
<!-- Header end --> 


<!-- Content
============================================= -->
<div id="content"> 

 @include('dashboardMenu')

  
<!-- Preloader End --> 
@yield('content')

</div>  
<!-- Footer
============================================= -->
@include('footer')
<!-- Footer end --> 
</div>
<!-- Back to Top
============================================= --> 
<a id="back-to-top" data-toggle="tooltip" title="Back to Top" href="javascript:void(0)">
    <i class="fa fa-chevron-up"></i>
</a> 

<!-- Modal Dialog - View Plans
============================================= -->
@include('dialog_plans')
<!-- Modal Dialog - View Plans end --> 

<!-- Modal Dialog - Login/Signup
============================================= -->
<!-- @include('dialog') -->
<!-- Modal Dialog - Login/Signup end --> 

<!-- Script --> 
<script src="{{config('global.THEME_PATH')}}/vendor/jquery/jquery.min.js"></script> 
<script src="{{config('global.THEME_PATH')}}/vendor/bootstrap/js/bootstrap.bundle.min.js"></script> 
<script src="{{config('global.THEME_PATH')}}/vendor/owl.carousel/owl.carousel.min.js"></script> 
<script src="{{config('global.THEME_PATH')}}/js/theme.js"></script>
<script src="{{config('global.THEME_PATH')}}/vendor/daterangepicker/moment.min.js"></script> 
<script src="{{config('global.THEME_PATH')}}/vendor/daterangepicker/daterangepicker.js"></script> 

<script type="text/javascript">

  /*
   * Script For Choose Payment Mode While "Upload Wallet Balance For Distributor"
   */
  $(document).ready(function(){
    $("#paymentMode").click(function(){
      	var id = $(this).val();
      	$(".paymentmode").hide();
      	$("#paymentmode"+id).fadeIn();
    });


    //Datepicker

  // Hotels Check Out Date
  $('#payment_date').daterangepicker({
    singleDatePicker: true,
    minDate: moment(),
    autoUpdateInput: false,
    }, function(chosen_date) {
      $('#payment_date').val(chosen_date.format('DD-MM-YYYY'));
  });


  $('#transfer_date').daterangepicker({
    singleDatePicker: true,
    minDate: moment(),
    autoUpdateInput: false,
    }, function(chosen_date) {
      $('#transfer_date').val(chosen_date.format('DD-MM-YYYY'));
  });



   $('#neft_transfer_date').daterangepicker({
    singleDatePicker: true,
    minDate: moment(),
    autoUpdateInput: false,
    }, function(chosen_date) {
      $('#neft_transfer_date').val(chosen_date.format('DD-MM-YYYY'));
  });




  });
</script>
<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
   
    $("#verifyOTP").click(function(e){
        $("#verifyOTP").html("Please wait...");
        $("#verifyOTP").attr("disabled","disabled");
        e.preventDefault();
        var OTP       = $("input[name=OTP]").val();
        $.ajax({
           type:'POST',
           url:"{{ route('roverifyotp') }}",
           data:{otp:OTP},
           success:function(data){
              if(data.status === true){
                $("#oldSuccessDiv").hide();
                $("#errorDiv").hide();
                $("#successDiv").show();
                $("#successDiv").html(data.message);
                $("#otp").val('');
                $("#personalInformation").submit();
              }else{
                $("#oldSuccessDiv").hide();
                $("#errorDiv").show();
                $("#errorDiv").html(data.message);
                $("#verifyOTP").html("Verify & Submit");
                $("#verifyOTP").removeAttr("disabled");
              }
           }
        });
  });



  //Add Bank Account
   $("#addAccountBtn").click(function(e){
      var account_no      = $("#account_no").val();
      if(account_no.length==0){
        $("#account_no").addClass('alert-danger');
      }else{
        $("#account_no").removeClass('alert-danger');
      }
      var master_bank_id  = $("#master_bank_id").val();
      if(master_bank_id.length==0){
        $("#master_bank_id").addClass('alert-danger');
      }else{
        $("#master_bank_id").removeClass('alert-danger');
      }

      var ifsccodeStr     = $("#IFSCCode").val();
      if(ifsccodeStr.length==0){
        $("#IFSCCode").addClass('alert-danger');
      }else{
        $("#IFSCCode").removeClass('alert-danger');
      }
      var hiddenid        = $("#hiddenid").val();
      e.preventDefault();
       $.ajax({
           type:'POST',
           url:"{{ route('roaddaccountrequest') }}",
           data:{account_no:account_no,master_bank_id:master_bank_id,IFSCCode:ifsccodeStr,id:hiddenid},
           success:function(data){
              if(data.success === true){
                alert("ok");
              }else{
                var str = "<div class='alert alert-danger'>"+data.message+"</div>";
                $("#msg").html(str);
              }
           }
        });
   });
</script>
</body>
</html>