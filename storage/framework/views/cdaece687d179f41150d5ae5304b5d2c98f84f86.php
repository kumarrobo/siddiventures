<p>
 
  <?php if(Session::has('error')): ?>
  <p class="alert alert-danger"><small>
  <?php $__currentLoopData = Session::get('error'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $err): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
  <b>Error:</b> <?php echo e($err); ?></br>
  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </small>
  </p>
  <?php endif; ?>
   <?php if(Session::has('message')): ?>
  <p class="alert alert-success"><?php echo e(Session::get('message')); ?></p>
  <?php endif; ?>

</p>            <!-- Personal Information
          ============================================= -->
            <div class="row">
              <div class="col-lg-4 col-sm-12">
              <div class="card  mb-3"  style=" 
              -webkit-box-shadow: -5px 8px 24px -17px rgba(0,0,0,0.75);
              -moz-box-shadow: -5px 8px 24px -17px rgba(0,0,0,0.75);
              box-shadow: -5px 8px 24px -17px rgba(0,0,0,0.75); ">
                  <div class="card-header"><b class="mb-4"><?php echo e(__('Sender Details')); ?></b></div>
                  <div class="card-body">
                    <div class="form-group ">
                     <div class="row">
                        <div class="col-md-5 col-sm-5">Mobile Number</div>
                        <div class="col-md-1 col-sm-1">:</div>
                        <div class="col-md-6 col-sm-6"><?php echo e($mobileNumber); ?></div>
                      </div>
                    </div>
                     <div class="form-group ">
                     <div class="row">
                        <div class="col-md-5">Sender Name</div>
                        <div class="col-md-1">:</div>
                        <div class="col-md-6"><?php echo e($senderName); ?></div>
                      </div>
                    </div>
                    <div class="form-group ">
                     <div class="row">
                        <div class="col-md-5">Address</div>
                        <div class="col-md-1">:</div>
                        <div class="col-md-6"><?php echo e($address); ?></div>
                      </div>
                    </div>


                  </div>
              </div>

              
                 
                  

                


             
              </div>
              <div class="col-lg-4">

                <div class="card  mb-3"  style=" 
              -webkit-box-shadow: -5px 8px 24px -17px rgba(0,0,0,0.75);
              -moz-box-shadow: -5px 8px 24px -17px rgba(0,0,0,0.75);
              box-shadow: -5px 8px 24px -17px rgba(0,0,0,0.75); ">
                  <div class="card-header"><b class="mb-4"><?php echo e(__('Monthly Limit Details')); ?></b></div>
                  <div class="card-body">
                    <div class="form-group ">
                     <div class="row">
                        <div class="col-md-5">Monthly Limit</div>
                        <div class="col-md-1">:</div>
                        <div class="col-md-6" style="color:black; font-weight:bold;"><?php echo e(GeneralHelper::getAmount($monthlyLimit)); ?></div>
                      </div>
                    </div>
                     <div class="form-group ">
                     <div class="row">
                        <div class="col-md-5">Utilized</div>
                        <div class="col-md-1">:</div>
                        <div class="col-md-6" style="color:red; font-weight: normal;"><?php echo e(GeneralHelper::getAmount($utilized)); ?></div>
                      </div>
                    </div>
                    <div class="form-group ">
                     <div class="row">
                        <div class="col-md-5">Balance</div>
                        <div class="col-md-1">:</div>
                        <div class="col-md-6" style="color: green; font-weight: bold;"><?php echo e(GeneralHelper::getAmount($balance)); ?></div>
                      </div>
                    </div>


                  </div>
              </div>
              </div>

               <div class="col-lg-4" >
                <div class="cards">
                 <div class="card-bodys mt-2">
                    <div class="form-group ">
                     <div class="row">
                        <div class="col-md-1"></div>
                        <div class="col-md-10">
                          <a href="<?php echo e(route('roaddaccount',['id'=>$id])); ?>" class="btn btn-success"  style="font-size: 14px; width: 100%;text-decoration: none">Account Verification</a>
                         </div>
                         <div class="col-md-1"></div>
                      </div>
                    </div>
                     <div class="form-group ">
                     <div class="row">
                        <div class="col-md-1"></div>
                        <div class="col-md-10">
                          <a href="#" class="btn btn-info" style="font-size: 14px;  width: 100%; text-decoration: none">Add Recipient</a>
                        </div>
                        <div class="col-md-1"></div>
                      </div>
                    </div>
                    <div class="form-group ">
                     <div class="row">
                      <div class="col-md-1"></div>
                       <div class="col-md-10">
                          <a href="#" class="btn btn-danger" style="font-size: 14px;  width: 100%;text-decoration: none" >Refund Tranaction</a>
                        </div>
                        <div class="col-md-1"></div>

                      </div>
                    </div>


                  </div>
              </div>
               </div>

         
          <!-- Orders History end --> 
            </div>

            <?php if(count($bankList)){ ?>

            <div class="tab-content my-3" id="myTabContent">
            <div class="tab-pane fade show active" id="first" role="tabpanel" aria-labelledby="first-tab">
              <div class="table-responsive-md" style=" width:1240px; overflow: auto">
                <table class="table table-hover border" style="font-size: 12px;">
                  <thead class="thead-light">
                    <tr>
                      <th>SN</th>
                      <th>Sender Name</th>
                      <th>Bank Name</th>
                      <th>IFSC Code</th>
                      <th>Account No</th>
                      <th>Mobile</th>
                      <th class="text-center">Status</th>
                      <th>Pay By IFSC</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php $count=1;foreach ($bankList as $key => $value) { //dd($value); ?>
                    <tr>
                      <td class="align-middle"><?php echo e($count); ?></td>
                      <td class="align-middle" nowrap="nowrap"><?php echo e($value['recipient_name']); ?></td>
                      <td class="align-middle"><?php echo e($value['MasterBank']['title']); ?></td>
                      <td class="align-middle"><?php echo e($value['IFSC_code']); ?></td>
                      <td class="align-middle" nowrap="nowrap"><?php echo e($value['account_no']); ?></td>
                      <td class="align-middle"><?php echo e($value['associate_mobile_no']); ?></td>
                      <td class="align-middle text-center">
                        <?php if($value['is_active']=='1'){ ?>
                          <i class="fas fa-check-circle text-4 text-success" data-toggle="tooltip" data-original-title="Active"></i>
                        <?php }else{ ?>
                           <i class="fas fa-times-circle text-4 text-danger" data-toggle="tooltip" data-original-title="InActive"></i>
                        <?php } ?>
                      </td>
                      <td class="align-middle">
                        <?php if($value['is_active']=='1'){ ?>
                         <a href="#" class="btn btn-success" style="padding:10px;font-size: 12px;">Pay Now</a>
                        <?php }else{ ?>
                           <p class="btn btn-default"  style="padding:10px;font-size: 12px; background-color: #CCC">Pay Now</p>
                        <?php } ?>
                      </td>
                     
                    </tr>
                    <?php $count++;} ?>
                     
                   

                  
                  </tbody>
                </table>

              </div>
           
            </div>
           
          </div>
           <?php } ?>
          
<?php /**PATH /var/www/html/siddiventures/resources/views/RO/MoneyTransfer/BankAccountList.blade.php ENDPATH**/ ?>