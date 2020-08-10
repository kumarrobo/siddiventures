<!--Include Distributor Profile Menu-->
@if(Auth::guard('user')->check())

  @include('user.dashboardMenu')

@endif
<!--Include Distributor Ends Menu-->


<!--Include RO Profile Menu-->
@if(Auth::guard('ro')->check())

    @include('RO.dashboardMenu')   

@endif
<!--Include RO Profile Menu-->

