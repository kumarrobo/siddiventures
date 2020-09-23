
            <!-- Personal Information
          ============================================= -->
            <div class="row">
              <div class="col-lg-3"> 
          <!-- Nav Link
          ============================================= -->
          <ul class="nav nav-pills alternate flex-lg-column sticky-top">
            <li class="nav-item"><a class="nav-link <?php echo e(GeneralHelper::isActiveMenu('roprofile')); ?>" href="<?php echo e(route('roprofile',['id'=>$RODetails['id']])); ?>"><i class="fas fa-user"></i><?php echo e(__('Personal Details')); ?></a></li>
          
             <li class="nav-item"><a class="nav-link <?php echo e(GeneralHelper::isActiveMenu('addretailer')); ?>" href="<?php echo e(route('rocompanyprofile',['id'=>$RODetails['id']])); ?>"><i class="fas fa-file"></i><?php echo e(__('Company Details')); ?></a></li>
               <li class="nav-item"><a class="nav-link" href="<?php echo e(route('allretailerlist')); ?>"><i class="fas fa-users"></i><?php echo e(__('View All RO')); ?></a></li>

       
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
       
              <div class="col-lg-6" style="border: solid 1px #eee;padding:20px; 
              -webkit-box-shadow: -5px 8px 24px -17px rgba(0,0,0,0.75);
              -moz-box-shadow: -5px 8px 24px -17px rgba(0,0,0,0.75);
              box-shadow: -5px 8px 24px -17px rgba(0,0,0,0.75); ">

                <h4 class="mb-4"><?php echo e(__('RO Profile')); ?></h4>
                <hr/>
                 
                   <div class="form-group ">
                     <div class="row">
                      <div class="col-md-4">
                      <label for="fullName" style="font-weight: bold;"><?php echo e(__('Wallet Balance')); ?></label>
                      </div>
                       <div class="col-1">
                        <p style="font-weight: bold;"><?php echo e(__(':')); ?></p>
                      </div>
                      <div class="col-md-7" style="font-weight: bold;">
                       Rs. <?php echo e(number_format($RODetails['wallet_balance'],2)); ?>

                      </div>
                      </div>
                  </div>
                   <div class="form-group ">
                     <div class="row">
                      <div class="col-md-4">
                      <label for="fullName" style="font-weight: bold;"><?php echo e(__('First Name')); ?></label>
                      </div>
                       <div class="col-1">
                        <p style="font-weight: bold;"><?php echo e(__(':')); ?></p>
                      </div>
                      <div class="col-md-7">
                        <?php echo e($RODetails['first_name']); ?>

                      </div>
                      </div>
                  </div>
                  <div class="form-group ">
                     <div class="row">
                      <div class="col-md-4">
                      <label for="fullName" style="font-weight: bold;"><?php echo e(__('Last Name')); ?></label>
                      </div>
                       <div class="col-1">
                        <p style="font-weight: bold;"><?php echo e(__(':')); ?></p>
                      </div>
                      <div class="col-md-7">
                        <?php echo e($RODetails['last_name']); ?>

                      </div>
                      </div>
                  </div>

                  <div class="form-group ">
                     <div class="row">
                      <div class="col-md-4">
                      <label for="fullName" style="font-weight: bold;"><?php echo e(__('Email Address')); ?></label>
                      </div>
                       <div class="col-1">
                        <p style="font-weight: bold;"><?php echo e(__(':')); ?></p>
                      </div>
                      <div class="col-md-7">
                        <?php echo e($RODetails['email']); ?>

                      </div>
                      </div>
                  </div>

                  <div class="form-group ">
                     <div class="row">
                      <div class="col-md-4">
                      <label for="fullName" style="font-weight: bold;"><?php echo e(__('Mobile')); ?></label>
                      </div>
                       <div class="col-1">
                        <p style="font-weight: bold;"><?php echo e(__(':')); ?></p>
                      </div>
                      <div class="col-md-7">
                        <?php echo e($RODetails['mobile']); ?>

                      </div>
                      </div>
                  </div>
                  
                  <div class="form-group ">
                     <div class="row">
                      <div class="col-md-4">
                      <label for="fullName" style="font-weight: bold;"><?php echo e(__('Date Of Birth')); ?></label>
                      </div>
                       <div class="col-1">
                        <p style="font-weight: bold;"><?php echo e(__(':')); ?></p>
                      </div>
                      <div class="col-md-7">
                        <?php echo e($RODetails['UserDetail']['date_of_birth']); ?>

                      </div>
                      </div>
                  </div>


                  


                   <div class="form-group ">
                     <div class="row">
                      <div class="col-md-4">
                      <label for="fullName" style="font-weight: bold;"><?php echo e(__('Address-1')); ?></label>
                      </div>
                       <div class="col-1">
                        <p style="font-weight: bold;"><?php echo e(__(':')); ?></p>
                      </div>
                      <div class="col-md-7">
                        <?php echo e($RODetails['UserDetail']['address_line_1']); ?>

                      </div>
                      </div>
                  </div>


                   <div class="form-group ">
                     <div class="row">
                      <div class="col-md-4">
                      <label for="fullName" style="font-weight: bold;"><?php echo e(__('Address-2')); ?></label>
                      </div>
                       <div class="col-1">
                        <p style="font-weight: bold;"><?php echo e(__(':')); ?></p>
                      </div>
                      <div class="col-md-7">
                        <?php echo e($RODetails['UserDetail']['address_line_2']); ?>

                      </div>
                      </div>
                  </div>


                   <div class="form-group ">
                     <div class="row">
                      <div class="col-md-4">
                      <label for="fullName" style="font-weight: bold;"><?php echo e(__('Country / State / City')); ?></label>
                      </div>
                       <div class="col-1">
                        <p style="font-weight: bold;"><?php echo e(__(':')); ?></p>
                      </div>
                      <div class="col-md-7">
                        <?php echo e(GeneralHelper::getCountryName($RODetails['UserDetail']['country_id'])); ?> / <?php echo e(GeneralHelper::getStateName($RODetails['UserDetail']['state_id'])); ?> /
                        <?php echo e(GeneralHelper::getCityName($RODetails['UserDetail']['city_id'])); ?>

                      </div>
                      </div>
                  </div>
                
                  <div class="form-group ">
                     <div class="row">
                      <div class="col-md-4">
                      <label for="fullName" style="font-weight: bold;"><?php echo e(__('Pincode')); ?></label>
                      </div>
                       <div class="col-1">
                        <p style="font-weight: bold;"><?php echo e(__(':')); ?></p>
                      </div>
                      <div class="col-md-7">
                        <?php echo e($RODetails['UserDetail']['pincode']); ?>

                      </div>
                      </div>
                  </div>

                
                  
              </div>
             
            </div>
          <?php /**PATH /var/www/html/siddiventures/resources/views/user/Distributor/RO/ROProfile.blade.php ENDPATH**/ ?>