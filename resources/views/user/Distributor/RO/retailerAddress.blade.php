
@extends('layouts.defaultDashboard')

@section('content')

<section class="containers">
<div class="bg-light shadow-md rounded p-4">
  <div class="row"> 
    <div class="col-lg-12">
    <div class="bg-light shadow-md rounded p-4"> 
      
            <!-- Personal Information
          ============================================= -->
            <div class="row">
              <div class="col-lg-3"> 
          <!-- Nav Link
          ============================================= -->
          <ul class="nav nav-pills alternate flex-lg-column sticky-top">
            <li class="nav-item"><a class="nav-link {{GeneralHelper::isActiveMenu('personaldetails')}}" href="{{route('personaldetails',['id'=>$id])}}"><i class="fas fa-user"></i>{{ __('Personal Details') }}</a></li>
            <li class="nav-item"><a class="nav-link {{GeneralHelper::isActiveMenu('retaileraddress')}}" href="{{route('retaileraddress',['id'=>$id])}}" ><i class="fas fa-bookmark"></i>{{ __('Address Details')}}</a></li>
            <li class="nav-item"><a class="nav-link {{GeneralHelper::isActiveMenu('retailercompany')}}" href="{{route('retailercompany',['id'=>$id])}}" ><i class="fas fa-bookmark"></i>{{ __('Company Proof')}}</a></li>
            <li class="nav-item"><a class="nav-link {{GeneralHelper::isActiveMenu('documentproof')}}" href="{{route('documentproof',['id'=>$id])}}" ><i class="fas fa-bookmark"></i>{{ __('Document Proof')}}</a></li>
            
          </ul>
          <!-- Nav Link end --> 
          <!--  <div class="bg-light-2 p-3">
                  <p class="mb-2">We value your Privacy.</p>
                  <p class="text-1 mb-0">We will not sell or distribute your contact information. Read our <a href="#">Privacy Policy</a>.</p>
                  <hr>
                  <p class="mb-2">Billing Enquiries</p>
                  <p class="text-1 mb-0">Do not hesitate to reach our <a href="#">support team</a> if you have any queries.</p>
                </div> -->
        </div>
       
              <div class="col-lg-6">

                <h4 class="mb-4">{{ __('Retailer Address Detail') }}</h4>
                 <p>
                    @if(Session::has('message'))
                    <p class="alert alert-success">Retailer Updated Successfully.</p>
                    @endif
                    @if(Session::has('error'))
                    <p class="alert alert-danger"><small>
                    @foreach(Session::get('error') as $err)
                    <b>Error:</b> {{ $err }}</br>
                    @endforeach
                    </small>
                    </p>
                    @endif

                  </p>
                <form id="personalInformation" method="post" action="{{route('retaileraddress',['id'=>$id])}}" method="POST">
                  @csrf
                  <input type="hidden" name="id" value="{{$id}}">
                  <div class="form-group ">
                      <label for="fullName">{{ __('Address Line-1') }}</label>
                      <input type="text"  class="form-control" data-bv-field="address_line_1" id="address_line_1" name="address_line_1"  placeholder="Enter Address Line-1" value="{{old('address_line_1', $userDetails['address_line_1'])}}">
                  </div>
                   <div class="form-group ">
                      <label for="fullName">{{ __('Address Line-2') }}</label>
                      <input type="text"  class="form-control" data-bv-field="address_line_2" id="address_line_2" name="address_line_2"  placeholder="Enter Address Line-2" value="{{ old('address_line_2',$userDetails['address_line_2']) }}">
                  </div>
                
                  <div class="form-group ">
                      <label for="fullName">{{ __('Country') }}</label>
                      <select class="form-control" name="country_id">
                        <option value="1">India</option>
                        <option value="1">Other</option>
                      </select>
                  </div>
                
                 
                    <div class="form-group">
                    <label for="emailID">{{ __('Choose State') }}</label>
                    <select class="form-control" name="state_id">
                        <option value="">Choose State</option>
                        <?php foreach($cityList as $item){ ?>
                        <option value="{{$item['id']}}">{{$item['state_name']}}</option>
                        <?php } ?>
                      </select>
                  </div>

                    <div class="form-group">
                    <label for="emailID">{{ __('Choose City') }}</label>
                    <select class="form-control" name="city_id">
                        <option value="">Choose City</option>
                        <?php foreach($cityList as $item){ ?>
                        <option value="{{$item['id']}}">{{$item['city_name']}}</option>
                        <?php } ?>
                        
                      </select>
                  </div>
                  

                    <div class="form-group">
                    <label for="emailID">{{ __('District') }}</label>
                    <input type="text"  class="form-control" data-bv-field="district" id="district"  placeholder="{{ __('Enter District Name') }}" maxlength="10" name="district" value="{{ old('district', $userDetails['district']) }}">
                  </div>
                  <div class="form-group">
                    <label for="emailID">{{ __('Pincode') }}</label>
                    <input type="text"  class="form-control" data-bv-field="pincode" id="pincode"  placeholder="{{ __('Enter pincode') }}" name="pincode"  value="{{ old('district', $userDetails['pincode']) }}">
                      @error('pincode')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                      @enderror
                    
                  </div>
                 
                  
                  <button class="btn btn-primary" type="submit">Save</button>
                </form>
              </div>
             
            </div>
          
    </div>
  </div>
  </div>
</div>
</section>

<!-- Document Wrapper end --> 
@endsection

