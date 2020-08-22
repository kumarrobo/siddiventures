
            <!-- Personal Information
          ============================================= -->
            <div class="row">
              
       
              <div class="col-lg-6" style="padding: 20px;">

                <h4 class="mb-4">{{ __('Welcome Back,') }}&nbsp;{{ Auth::user()->name }}</h4>
                
               
                  <div class="form-group ">
                    <div class="row">
                      <div class="col-md-12">
                        <ul class="nav secondary-nav">
                            <li class="nav-item"> 
                          <a class="nav-link {{GeneralHelper::isActiveMenu('wallet')}}" href="{{route('balancerequest')}}">
                            <span><i class="fas fa-wallet"></i></span> Wallet Recharge</a> 
                        </li>
                         <li class="nav-item"> 
                            <a class="nav-link"  href="{{route('allretailerlist')}}">
                              <span><i class="fas fa-users"></i></span>Active RO</a> 
                          </li>
                      
                         <li class="nav-item"> 
                          <a class="nav-link {{GeneralHelper::isActiveMenu('myreport')}}" href="recharge-bill-datacard.html">
                            <span><i class="fas fa-chart-line"></i></span>My Report</a>
                        </li>
                        <li class="nav-item"> 
                          <a class="nav-link {{GeneralHelper::isActiveMenu('moneytransfer')}}" href="{{route('pushbalance')}}">
                            <span><i class="fas fa-retweet"></i></span>Push Balance-RO</a>
                        </li>
                    
                        </ul>
                           <ul class="nav secondary-nav">
                         <li class="nav-item"> 
                            <a class="nav-link"  href="{{route('allbalancerequest')}}">
                              <span><i class="fas fa-gavel"></i></span>Balance Request</a> 
                          </li>
                        <li class="nav-item"> 
                          <a class="nav-link {{GeneralHelper::isActiveMenu('wallet')}}" href="{{route('balancerequest')}}">
                            <span><i class="fas fa-user-plus"></i></span>Add RO</a> 
                        </li>
                         <li class="nav-item"> 
                          <a class="nav-link {{GeneralHelper::isActiveMenu('myreport')}}" href="recharge-bill-datacard.html">
                            <span><i class="fas fa-hand-holding-usd" aria-hidden="true"></i></span>Commission</a>
                        </li>
                        <li class="nav-item"> 
                          <a class="nav-link {{GeneralHelper::isActiveMenu('moneytransfer')}}" href="recharge-bill-datacard.html">
                            <span><i class="fas fa-unlink"></i></span>Transaction Status</a>
                        </li>
                    
                        </ul>


                      </div>
                   </div>
                  </div>
              </div>
               <div class="col-lg-6" style="border: solid 1px #eee;padding: 20px;">

                <h4 class="mb-4">{{ __('Report') }}</h4>
                
               
                  <div class="form-group ">
                    <div class="row">
                      <div class="col-md-12">
                       @include('user.Distributor.ReportChart')
                      </div>
                   </div>
                  </div>
              </div>


            </div>