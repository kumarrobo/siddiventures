
@extends('layouts.default')

@section('content')
<!-- Document Wrapper   
============================================= -->
<div id="main-wrapper"> 
  
  <!-- Header
  ============================================= -->
  @include('header')
  <!-- Header end --> 
  
  <!-- Content
  ============================================= -->
  <div id="content"lass="bg-secondary"> 
    <section class="page-header page-header-text-light bg-secondary">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-md-8">
            <h1>Register</h1>
          </div>
          <div class="col-md-4">
            <ul class="breadcrumb justify-content-start justify-content-md-end mb-0">
              <li><a href="{{route('home')}}">Home</a></li>
              <li class="active">Register</li>
            </ul>
          </div>
        </div>
      </div>
    </section>
    
    <!-- Secondary Navigation
    ============================================= -->
  
    <div class="container" >
      <div class="bg-light shadow-md rounded p-4">
        <div class="row"> 
          
          <!-- Mobile Recharge
          ============================================= -->
          <div class="col-lg-8 mb-4 mb-lg-0">
            <h2 class="text-4 mb-3">New Registration- Upload Document</h2>
            <hr/>
            @if (Session::has('error'))
            <div class="card-header alert-danger">{{ __(Session::get('error')) }}</div>
            @endif
          <div class="tab-content pt-4">
          <div class="tab-pane fade show active" id="loginPage" role="tabpanel" aria-labelledby="login-page-tab">
            
            <form id="personalInformation" method="post" action="{{route('uploaddocument',['id'=>$id])}}" method="POST" enctype="multipart/form-data">
                  @csrf
                  <input type="hidden" name="id" value="{{$id}}">
                  <div class="form-group ">
                      <label for="fullName">{{ __('ID Proof') }}</label>
                       <select class="form-control" name="id_proof_file_type">
                        <?php foreach($idProofType as $key=>$item){ ?>
                        <option value="{{$key}}">{{$item}}</option>
                        <?php } ?>
                      </select>

                  </div>
                  <div class="form-group">
                     <label for="fullName">{{ __('Upload ID Proof') }}</label>
                     <input type="file"  class="form-control" data-bv-field="id_proof_file" id="id_proof_file"   placeholder="{{ __('Upload ID Proof') }}" name="id_proof_file" value="{{old('id_proof_file')}}">
                     <small class="" style="color: red">File extension type should be .jpg, .jpeg, .pdf. Max file size 1024KB</small>
                  </div>
                  <div class="form-group">
                    <hr/><br/>
                  </div>
                 
                    <div class="form-group">
                    <label for="emailID">{{ __('Address Proof') }}</label>
                    <select class="form-control" name="address_proof">
                      <?php foreach($addressProofType as $key=>$item){ ?>
                        <option value="{{$key}}">{{$item}}</option>
                        <?php } ?>
                    </select>
                  </div>
                    <div class="form-group">
                    <label for="emailID">{{ __('Upload Address Proof') }}</label>
                    <input type="file"  class="form-control" data-bv-field="address_proof_file" id="address_proof_file"   name="address_proof_file">
                     <small class="" style="color: red">File extension type should be .jpg, .jpeg, .pdf. Max file size 1024KB</small>
                    <hr/><br/>
                  </div>
                     <div class="form-group">
                    <label for="emailID">{{ __('Company Proof') }}</label>
                    <select class="form-control" name="company_proof">
                      <?php foreach($companyProofType as $key=>$item){ ?>
                        <option value="{{$key}}">{{$item}}</option>
                        <?php } ?>
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="emailID">{{ __('Company Proof') }}</label>
                    <input type="file"  class="form-control" data-bv-field="company_proof_file" id="company_proof_file"   name="company_proof_file">
                     <small class="" style="color: red">File extension type should be .jpg, .jpeg, .pdf. Max file size 1024KB</small>
                    
                  </div>
                 
                  
                  <button class="btn btn-primary" type="submit">Upload & Save</button>
                </form>

          </div>
         
       
       
        </div>
             
            
             
            
          </div>

          <div class="col-lg-4 mt-4 mt-lg-0 ">
                <div class="bg-light-2 p-3">
                  <p class="mb-2">We value your Privacy.</p>
                  <p class="text-1 mb-0">We will not sell or distribute your contact information. Read our <a href="#">Privacy Policy</a>.</p>
                  <hr>
                  <p class="mb-2">Billing Enquiries</p>
                  <p class="text-1 mb-0">Do not hesitate to reach our <a href="#">support team</a> if you have any queries.</p>
                </div>
                <hr/>
                <div class="bg-light-2 p-3">
                  <p class="mb-2">We value your Privacy.</p>
                  <p class="text-1 mb-0">We will not sell or distribute your contact information. Read our <a href="#">Privacy Policy</a>.</p>
                  <hr>
                  <p class="mb-2">Billing Enquiries</p>
                  <p class="text-1 mb-0">Do not hesitate to reach our <a href="#">support team</a> if you have any queries.</p>
                </div>
              </div>
          <!-- Mobile Recharge end --> 
          
          <!-- Slideshow
          ============================================= -->
      
        </div>
      </div>
      </div>
    
  
    
   
    <!-- Mobile App
    ============================================= -->

   
    
  </div>
  <!-- Content end --> 
  
  <!-- Footer
  ============================================= -->
  @include('footer')
  <!-- Footer end --> 
  
</div>
<!-- Document Wrapper end --> 
@endsection

