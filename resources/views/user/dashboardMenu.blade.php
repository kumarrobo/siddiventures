
    <!-- Secondary Navigation
    ============================================= -->
    <div class="bg-secondary">
      <div class="container">
        <ul class="nav secondary-nav">
          <li class="nav-item"> 
            <a class="nav-link {{GeneralHelper::isActiveMenu('dashboard')}}" href="{{route('dashboard')}}">
              <span><i class="fas fa-home"></i></span> Dashboard</a> 
          </li>
          <li class="nav-item"> 
            <a class="nav-link 
            {{GeneralHelper::isActiveMenu('addretailer')}} 
            {{GeneralHelper::isActiveMenu('retaileraddress')}} 
            {{GeneralHelper::isActiveMenu('personaldetails')}} 
            {{GeneralHelper::isActiveMenu('documentproof')}} 
            {{GeneralHelper::isActiveMenu('viewrodetails')}} 
            {{GeneralHelper::isActiveMenu('retailercompany')}}" 

            href="{{route('addretailer')}}">
              <span><i class="fas fa-users"></i></span>Retailer</a> 
          </li>
          <li class="nav-item"> 
            <a class="nav-link {{GeneralHelper::isActiveMenu('myreport')}}" href="recharge-bill-datacard.html">
              <span><i class="fas fa-chart-line"></i></span>My Report</a>
          </li>
          <li class="nav-item"> 
            <a class="nav-link {{GeneralHelper::isActiveMenu('moneytransfer')}}" href="recharge-bill-datacard.html">
              <span><i class="fas fa-retweet"></i></span>Money Transfer</a>
          </li>
          <li class="nav-item"> 
            <a class="nav-link {{GeneralHelper::isActiveMenu('settalemnt')}}" href="recharge-bill-broadband.html">
              <span><i class="fas fa-university"></i></span>Bank Settalment</a>
          </li>
          <li class="nav-item"> 
            <a class="nav-link {{GeneralHelper::isActiveMenu('wallet')}}" href="recharge-bill-landline.html">
              <span><i class="fas fa-wallet"></i></span> Wallet</a> 
          </li>
          <li class="nav-item"> 
            <a class="nav-link {{GeneralHelper::isActiveMenu('writeus')}}" href="recharge-bill-gas.html">
              <span><i class="fas fa-envelope"></i></span> Write Us</a>
          </li>
          <li class="nav-item"> 
            <a class="nav-link {{GeneralHelper::isActiveMenu('help')}}" href="recharge-bill-water.html">
              <span><i class="fas fa-life-ring"></i></span> Help</a> 
          </li>
        </ul>
      </div>
    </div>
    <!-- Secondary Navigation end -->
