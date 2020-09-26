 <div id="horizontalTab" class="resp-htabs">
          <ul class="resp-tabs-list">
            <li><?php echo e(__('Personal Details')); ?></li>
            <li><?php echo e(__('Address Details')); ?></li>
            <li><?php echo e(__('Company Details')); ?></li>
            <!-- <li>Responsive Tab-3</li> -->
          </ul>
          <div class="resp-tabs-container">
            <div>
              <p>
               
                   <div class="form-group ">
                     <div class="row">
                      <div class="col-md-2 col-xs-4">
                      <label for="fullName" style="font-weight: bold;"><?php echo e(__('First Name')); ?></label>
                      </div>
                       <div class="col-1 col-xs-1">
                        <p style="font-weight: bold;"><?php echo e(__(':')); ?></p>
                      </div>
                      <div class="col-md-4 col-xs-7">
                        <?php echo e($RODetails['first_name']); ?>

                      </div>
                      </div>
                  </div>
                  <div class="form-group ">
                     <div class="row">
                      <div class="col-md-2">
                      <label for="fullName" style="font-weight: bold;"><?php echo e(__('Last Name')); ?></label>
                      </div>
                       <div class="col-1">
                        <p style="font-weight: bold;"><?php echo e(__(':')); ?></p>
                      </div>
                      <div class="col-md-4">
                        <?php echo e($RODetails['last_name']); ?>

                      </div>
                      </div>
                  </div>

                  <div class="form-group ">
                     <div class="row">
                      <div class="col-md-2">
                      <label for="fullName" style="font-weight: bold;"><?php echo e(__('Email Address')); ?></label>
                      </div>
                       <div class="col-1">
                        <p style="font-weight: bold;"><?php echo e(__(':')); ?></p>
                      </div>
                      <div class="col-md-4">
                        <?php echo e($RODetails['email']); ?>

                      </div>
                      </div>
                  </div>

                  <div class="form-group ">
                     <div class="row">
                      <div class="col-md-2">
                      <label for="fullName" style="font-weight: bold;"><?php echo e(__('Mobile')); ?></label>
                      </div>
                       <div class="col-1">
                        <p style="font-weight: bold;"><?php echo e(__(':')); ?></p>
                      </div>
                      <div class="col-md-4">
                        <?php echo e($RODetails['mobile']); ?>

                      </div>
                      </div>
                  </div>
                  
                  <div class="form-group ">
                     <div class="row">
                      <div class="col-md-2">
                      <label for="fullName" style="font-weight: bold;"><?php echo e(__('Date Of Birth')); ?></label>
                      </div>
                       <div class="col-1">
                        <p style="font-weight: bold;"><?php echo e(__(':')); ?></p>
                      </div>
                      <div class="col-md-4">
                        <?php echo e($RODetails['UserDetail']['date_of_birth']); ?>

                      </div>
                      </div>
                  </div>


                  


                
              </p>
            </div>
            <div>
                 <div class="form-group ">
                     <div class="row">
                      <div class="col-md-2">
                      <label for="fullName" style="font-weight: bold;"><?php echo e(__('Address-1')); ?></label>
                      </div>
                       <div class="col-1">
                        <p style="font-weight: bold;"><?php echo e(__(':')); ?></p>
                      </div>
                      <div class="col-md-4">
                        <?php echo e($RODetails['UserDetail']['address_line_1']); ?>

                      </div>
                      </div>
                  </div>


                   <div class="form-group ">
                     <div class="row">
                      <div class="col-md-2">
                      <label for="fullName" style="font-weight: bold;"><?php echo e(__('Address-2')); ?></label>
                      </div>
                       <div class="col-1">
                        <p style="font-weight: bold;"><?php echo e(__(':')); ?></p>
                      </div>
                      <div class="col-md-4">
                        <?php echo e($RODetails['UserDetail']['address_line_2']); ?>

                      </div>
                      </div>
                  </div>


                   <div class="form-group ">
                     <div class="row">
                      <div class="col-md-2">
                      <label for="fullName" style="font-weight: bold;"><?php echo e(__('Country / State / City')); ?></label>
                      </div>
                       <div class="col-1">
                        <p style="font-weight: bold;"><?php echo e(__(':')); ?></p>
                      </div>
                      <div class="col-md-6">
                        <?php echo e(GeneralHelper::getCountryName($RODetails['UserDetail']['country_id'])); ?> / <?php echo e(GeneralHelper::getStateName($RODetails['UserDetail']['state_id'])); ?> /
                        <?php echo e(GeneralHelper::getCityName($RODetails['UserDetail']['city_id'])); ?>

                      </div>
                      </div>
                  </div>
                
                  <div class="form-group ">
                     <div class="row">
                      <div class="col-md-2">
                      <label for="fullName" style="font-weight: bold;"><?php echo e(__('Pincode')); ?></label>
                      </div>
                       <div class="col-1">
                        <p style="font-weight: bold;"><?php echo e(__(':')); ?></p>
                      </div>
                      <div class="col-md-4">
                        <?php echo e($RODetails['UserDetail']['pincode']); ?>

                      </div>
                      </div>
                  </div>
            </div>
            <div>
              <?php echo $__env->make('user.Distributor.RO.ROCompanyProfile', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>
          </div>
        </div>


<?php /**PATH /var/www/html/siddiventures/resources/views/user/Distributor/RO/ROProfile.blade.php ENDPATH**/ ?>