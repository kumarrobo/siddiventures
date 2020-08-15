
            <!-- Personal Information
          ============================================= -->
            <div class="row">
              <div class="col-lg-3"> 
          <!-- Nav Link
          ============================================= -->
          <ul class="nav nav-pills alternate flex-lg-column sticky-top">
            <li class="nav-item"><a class="nav-link {{GeneralHelper::isActiveMenu('addretailer')}}" href="{{route('roprofile',['id'=>$RODetails['id']])}}"><i class="fas fa-user"></i>{{ __('Personal Details') }}</a></li>
         
             <li class="nav-item"><a class="nav-link {{GeneralHelper::isActiveMenu('rocompanyprofile')}}" href="{{route('rocompanyprofile',['id'=>$RODetails['id']])}}"><i class="fas fa-file"></i>{{ __('Company Details') }}</a></li>

                <li class="nav-item"><a class="nav-link" href="{{route('allretailerlist')}}"><i class="fas fa-users"></i>{{ __('View All RO')}}</a></li>

       
          </ul>
       
        </div>
       
              <div class="col-lg-6" style="border: solid 1px #eee;padding:20px; 
              -webkit-box-shadow: -5px 8px 24px -17px rgba(0,0,0,0.75);
              -moz-box-shadow: -5px 8px 24px -17px rgba(0,0,0,0.75);
              box-shadow: -5px 8px 24px -17px rgba(0,0,0,0.75); ">

                <h4 class="mb-4">{{ __('RO Company Profile') }}</h4>
                <hr/>
                 
                   <div class="form-group ">
                     <div class="row">
                      <div class="col-md-4">
                      <label for="fullName" style="font-weight: bold;">{{ __('Company Type') }}</label>
                      </div>
                       <div class="col-1">
                        <p style="font-weight: bold;">{{__(':')}}</p>
                      </div>
                      <div class="col-md-7">
                       {{GeneralHelper::getCompanyTypeList($RODetails['UserDetail']['company_type'])}}
                      </div>
                      </div>
                  </div>
                   <div class="form-group ">
                     <div class="row">
                      <div class="col-md-4">
                      <label for="fullName" style="font-weight: bold;">{{ __('Company Name') }}</label>
                      </div>
                       <div class="col-1">
                        <p style="font-weight: bold;">{{__(':')}}</p>
                      </div>
                      <div class="col-md-7">
                        {{$RODetails['UserDetail']['company_name']}}
                      </div>
                      </div>
                  </div>
                  <div class="form-group ">
                     <div class="row">
                      <div class="col-md-4">
                      <label for="fullName" style="font-weight: bold;">{{ __('Service By') }}</label>
                      </div>
                       <div class="col-1">
                        <p style="font-weight: bold;">{{__(':')}}</p>
                      </div>
                      <div class="col-md-7">
                        {{GeneralHelper::getServiceTypeList($RODetails['UserDetail']['service_by'])}}
                      </div>
                      </div>
                  </div>

                  <div class="form-group ">
                     <div class="row">
                      <div class="col-md-4">
                      <label for="fullName" style="font-weight: bold;">{{ __('Zone') }}</label>
                      </div>
                       <div class="col-1">
                        <p style="font-weight: bold;">{{__(':')}}</p>
                      </div>
                      <div class="col-md-7">
                         {{GeneralHelper::getZoneTypeList($RODetails['UserDetail']['zone'])}}
                      </div>
                      </div>
                  </div>

                  <div class="form-group ">
                     <div class="row">
                      <div class="col-md-4">
                      <label for="fullName" style="font-weight: bold;">{{ __('ID Proof Type') }}</label>
                      </div>
                       <div class="col-1">
                        <p style="font-weight: bold;">{{__(':')}}</p>
                      </div>
                      <div class="col-md-7">
                        {{GeneralHelper::getDocumentProofType($RODetails['UserDetail']['id_proof_type_id'])}}<br/>
                        
                      </div>
                      </div>
                  </div>
                  
                    <div class="form-group ">
                     <div class="row">
                      <div class="col-md-4">
                      <label for="fullName" style="font-weight: bold;">{{ __('ID Proof Document') }}</label>
                      </div>
                       <div class="col-1">
                        <p style="font-weight: bold;">{{__(':')}}</p>
                      </div>
                      <div class="col-md-7">
                        <img src="{{Config('global.FILE_PATH')}}/{{$RODetails['UserDetail']['id_proof_document']}}" width="350">
                      </div>
                      </div>
                  </div>


                   <div class="form-group ">
                     <div class="row">
                      <div class="col-md-4">
                      <label for="fullName" style="font-weight: bold;">{{ __('Address Proof ID Type') }}</label>
                      </div>
                       <div class="col-1">
                        <p style="font-weight: bold;">{{__(':')}}</p>
                      </div>
                      <div class="col-md-7">
                        {{GeneralHelper::getDocumentProofType($RODetails['UserDetail']['address_proof_type_id'])}}<br/>
                        
                      </div>
                      </div>
                  </div>
                  
                    <div class="form-group ">
                     <div class="row">
                      <div class="col-md-4">
                      <label for="fullName" style="font-weight: bold;">{{ __('Address Proof Document') }}</label>
                      </div>
                       <div class="col-1">
                        <p style="font-weight: bold;">{{__(':')}}</p>
                      </div>
                      <div class="col-md-7">
                        <img src="{{Config('global.FILE_PATH')}}/{{$RODetails['UserDetail']['address_proof']}}" width="350">
                      </div>
                      </div>
                  </div>


                    <div class="form-group ">
                     <div class="row">
                      <div class="col-md-4">
                      <label for="fullName" style="font-weight: bold;">{{ __('Business Proof Type') }}</label>
                      </div>
                       <div class="col-1">
                        <p style="font-weight: bold;">{{__(':')}}</p>
                      </div>
                      <div class="col-md-7">
                        {{GeneralHelper::getDocumentProofType($RODetails['UserDetail']['business_proof_type_id'])}}<br/>
                        
                      </div>
                      </div>
                  </div>
                  
                    <div class="form-group ">
                     <div class="row">
                      <div class="col-md-4">
                      <label for="fullName" style="font-weight: bold;">{{ __('Business Proof Document') }}</label>
                      </div>
                       <div class="col-1">
                        <p style="font-weight: bold;">{{__(':')}}</p>
                      </div>
                      <div class="col-md-7">
                        <img src="{{Config('global.FILE_PATH')}}/{{$RODetails['UserDetail']['business_proof']}}" width="350">
                      </div>
                      </div>
                  </div>
                  
                


                
             
                
                  
              </div>
             
            </div>
          