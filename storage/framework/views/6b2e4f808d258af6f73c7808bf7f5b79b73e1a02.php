<!--  <?php if(Session::has('message')): ?>
<p class="alert alert-success"><?php echo e(Session::get('message')); ?></p>
<?php endif; ?>        
 -->
 <!-- Personal Information
          ============================================= -->
            <div class="row">

              <div class="col-lg-12 col-sm-12">
                <?php if(Session::has('message')){ ?>
                  <div class="alert alert-danger"><?php echo Session::get('message'); ?></div>
                <?php } ?>
              </div>
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
                          <a href="#" class="btn btn-info" data-target="#view-plans" data-toggle="modal" style="font-size: 14px;  width: 100%; text-decoration: none">Add Recipient</a>
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

        


           <div class="row">
            <div class="col-md-12">
            <h5 class="mb-4"></h5>
            <div class="accordion" id="accordionDefault">
              <div class="card">
                <div class="card-header" id="headingOne">
                  <h5 class="mb-0"> <a href="#" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne" class="">All Beneficiries List</a> </h5>
                </div>
                <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordionDefault"   style="border:solid 1px #CCC; padding: 1px;">
                  <div class="card-body" style="padding:5px;"> 
            <?php if(count($bankList)){ ?>
            <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="first" role="tabpanel" aria-labelledby="first-tab">
              <div class="table-responsive-md" style="overflow: auto">
                <table class="table table-hover border" style="font-size: 13px;">
                  <thead class="thead-light">
                    <tr>
                      <th>SN</th>
                      <th>Recipient Name</th>
                      <th>Mobile</th>
                      <th>Bank Name</th>
                      <th>Account No</th>
                      <th>IFSC Code</th>
                      <th class="text-center">Status</th>
                      <th>Pay By IFSC</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php $count=1;foreach ($bankList as $key => $value) {   ?>
                    <tr>
                      <td class="align-middle"><?php echo e($count); ?></td>
                      <td class="align-middle" nowrap="nowrap"><?php echo e($value['VerifyBeneficiariesBankAccount']['account_name']); ?></td>
                      <td class="align-middle" nowrap="nowrap"><?php echo e($value['VerifyBeneficiariesBankAccount']['recipient_number']); ?></td>
                      <td class="align-middle"><?php echo e($value['VerifyBeneficiariesBankAccount']['bank_name']); ?></td>
                      <td class="align-middle"><?php echo e($value['VerifyBeneficiariesBankAccount']['account_number']); ?></td>
                      <td class="align-middle"><?php echo e($value['VerifyBeneficiariesBankAccount']['account_ifsc']); ?></td>
                      <td class="align-middle text-center">
                        <?php if($value['VerifyBeneficiariesBankAccount']['is_active']=='1'){ ?>
                          <i class="fas fa-check-circle text-4 text-success" data-toggle="tooltip" data-original-title="Active"></i>
                        <?php }else{ ?>
                           <i class="fas fa-times-circle text-4 text-danger" data-toggle="tooltip" data-original-title="InActive"></i>
                        <?php } ?>
                      </td>
                      <td class="pull-right" align="text-right" style="width: 15%">
                        <?php if($value['VerifyBeneficiariesBankAccount']['is_active']=='1'){ ?>
                         <a href="<?php echo e(route('rotransfermoney',['id'=>Crypt::encryptString($value['id'])])); ?>" class="btn btn-success " style="padding:10px;font-size: 12px;">&nbsp;&nbsp;Pay Now&nbsp;&nbsp;</a>
                        <?php }else{ ?>
                           <a  href="#" class="btn btn-primary recipentRow" data-target="#verify-account" data-account="<?php echo e($value['VerifyBeneficiariesBankAccount']['account_name']); ?>|<?php echo e($value['VerifyBeneficiariesBankAccount']['recipient_number']); ?>|<?php echo e($value['VerifyBeneficiariesBankAccount']['bank_name']); ?>|<?php echo e($value['VerifyBeneficiariesBankAccount']['account_number']); ?>|<?php echo e($value['VerifyBeneficiariesBankAccount']['account_ifsc']); ?>" data-toggle="modal" style="padding:10px;font-size: 12px;">Verify Now</a>
                        <?php } ?>
                        &nbsp;
                        <a href="<?php echo e(route('rodeleteaccount',['id'=>$value['id']])); ?>" class="btn btn-danger" style="padding:10px;font-size: 12px;" onclick="return confirm('Are you sure you want to delete?')">Delete</a>
                      </td>
                     
                    </tr>
                    <?php $count++;} ?>
                  </tbody>
                </table>
                <?php echo e($bankList->links()); ?>

              </div>
            </div>
          </div>
           <?php }else{ ?>
            <div class="alert alert-danger">No Beneficiries Found</div>
           <?php }?>
                  </div>
                </div>
              </div>
              <div class="card">
                <div class="card-header" id="headingTwo">
                  <h5 class="mb-0"> <a href="#" class="collapsed" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo" >All Transaction History On - <?php echo e($mobileNumber); ?></a> </h5>
                </div>
                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionDefault" style="">
                  <div class="card-body" style="padding:5px;"> 
                  
                    <div class=" bg-default table-responsive mb-3">
                         <table class="card-text table table-striped" width="100%" style="font-size: 12px; color: #000">
                          
                            <?php if($AllMobileTxn->total()>0){ ?>
                                <tr>
                                    <td nowrap="nowrap">Date</td>
                                    <td nowrap="nowrap">Debit</td>
                                    <td nowrap="nowrap ">Credit</td>
                                    <td nowrap="nowrap">Txn No</td>
                                    <td nowrap="nowrap">Remarks</td>
                                    <td nowrap="nowrap">Charge</td>
                                    <td nowrap="nowrap">Status</td>
                                    <td nowrap="nowrap">Balance</td>
                                    <td nowrap="nowrap">A/C Name</td>
                                    <td nowrap="nowrap">Bank</td>
                                    <td nowrap="nowrap">A/C No</td>
                              </tr>

                              <?php  foreach($AllMobileTxn as $itemList){ ?>
                              <tr>
                                <td nowrap="nowrap"><?php echo e($itemList['transaction_date']); ?></td>
                                <td nowrap="nowrap">
                                <?php if($itemList['debit_amount']>0){ ?>  
                                <font color="red" style="font-weight:500">
                                  <?php echo e(GeneralHelper::getAmount($itemList['debit_amount'])); ?>

                                </font>
                                <?php }else{ ?>
                                  <?php echo e(GeneralHelper::getAmount($itemList['debit_amount'])); ?>

                                <?php } ?>

                                
                                </td>
                                <td nowrap="nowrap">
                                <?php if($itemList['credit_amount']>0){ ?>  
                                <font color="green" style="font-weight:500">
                                  <?php echo e(GeneralHelper::getAmount($itemList['credit_amount'])); ?>

                                </font>
                                <?php }else{ ?>
                                  <?php echo e(GeneralHelper::getAmount($itemList['credit_amount'])); ?>

                                <?php } ?>
                                </td>
                                <td nowrap="nowrap"><?php echo e($itemList['transaction_number']); ?></td>
                                <td title="<?php echo e($itemList['remarks']); ?>"><?php echo e(Str::limit($itemList['remarks'], 20)); ?></td>

                                <td nowrap="nowrap">
                                
                                <?php if($itemList['transfer_charge']>0){ ?>  
                                <font color="red" style="font-weight:500">
                                  <?php echo e(GeneralHelper::getAmount($itemList['transfer_charge'])); ?>

                                </font>
                                <?php }else{ ?>
                                  <?php echo e(GeneralHelper::getAmount($itemList['transfer_charge'])); ?>

                                <?php } ?>


                                </td>
                                <td><?php echo e($itemList['status']); ?></td>
                                <td nowrap="nowrap">
                                <?php if($itemList['credit_amount']>0){ ?>
                                  <font color="red" style="font-weight:500"><i class="fas fa-arrow-up"></i>&nbsp;</font><?php } ?>
                                <?php if($itemList['debit_amount']>0){ ?>
                                   <font color="red" style="font-weight:500"><i class="fas fa-arrow-down"></i></font>&nbsp;
                                <?php } ?>
                                <?php echo e(GeneralHelper::getAmount($itemList['updated_wallet_balance'])); ?></td>
                                <td><?php echo e($itemList['VerifyBeneficiariesBankAccount']['account_name']); ?></td>
                                <td title="<?php echo e($itemList['VerifyBeneficiariesBankAccount']['bank_name']); ?>"><?php echo e(Str::limit($itemList['VerifyBeneficiariesBankAccount']['bank_name'],10)); ?></td>
                                <td><?php echo e($itemList['VerifyBeneficiariesBankAccount']['account_number']); ?></td>
                              </tr>
                            <?php }}else{ ?>
                              <tr style="background-color: #FFF">
                                  <td nowrap="nowrap" colspan="12">
                                    <div class="alert alert-danger">No Transaction Found</div>
                                    </td>
                              </tr>

                            <?php } ?> 

                            </table>
                            <?php echo e($AllMobileTxn->links()); ?>

                      </div>
                  </div>
                </div>
              </div>

              <div class="card">
                <div class="card-header" id="headingThree">
                  <h5 class="mb-0"> <a href="#" class="collapsed" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">Transaction Charges</a> </h5>
                </div>
                <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordionDefault" style="">
                  <div class="card-body" style="padding:5px;"> 
                      <div class=" bg-default table-responsive mb-3">
                         <table class="card-text table table-striped" width="100%" style="font-size: 13px; color: #000">
                            <?php if(!empty($MoneyTransferCharge)){ 
                                  foreach($MoneyTransferCharge as $itemList){ ?>
                              <tr>
                                <td width="30%"><?php echo e($itemList['AmountType']['transaction_amount']); ?></td>
                                <td width="10%">:</td>
                                <td width="30%"><?php echo e(number_format($itemList['value'],2)); ?><?php echo e('@'); ?><?php echo e($itemList['AmountType']['type']); ?></td>
                              </tr>
                            <?php }} ?>

                            </table>
                      </div>
                  </div>
                </div>
              </div>
           
            </div>
          </div>
             
           </div>

<!--Popup Start Here-->
<div id="view-plans" class="modal fade" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add New Recipient </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">×</span> </button>
      </div>
      <div class="modal-body">
        <div class="form-row mb-4 mb-sm-2" >
          <div  class="col-sm-12 form-group">
            <small class="alert alert-info mt-4"> <span class="badge badge-danger" style="padding: 5px;">IMPORTANT NOTE:</span> &nbsp;Sender as <b>"<?php echo e($senderName); ?>"</b> From  Mobile No. <b>"<?php echo e($mobileNumber); ?>"</b> , SMS confirmation will sent to recipient mobile number.</small>
          </div>
        <!--https://docs.easebuzz.in/wire/contacts-->
         <div class="col-sm-6 form-group">
             <label for="subject"><b>Recipent Name <font color="red">*</font></b></label>
            <input class="form-control" id="name" required="" placeholder="Enter Full Name" type="text" required="required">
            <small><span id="spanname" class="errorSpan"></span></small>
          </div>
       
          <div class="col-sm-6 form-group">
            <label for="subject"><b>Recipent Mobile <font color="red">*</font></b></label>
            <input class="form-control"  id="mobile" required="" placeholder="e.g: 9015446567" type="number" autocomplete="off">
            <?php echo GeneralHelper::getErrorSpan('mobile'); ?>

          </div>
          
           <div class="col-sm-6 form-group">
            <label for="subject"><b>Account Number <font color="red">*</font></b></label>
            <input class="form-control"  id="account_no" required="" placeholder="Enter Account Number" type="number" autocomplete="off">
            <?php echo GeneralHelper::getErrorSpan('account_no'); ?>

          </div>
          <div class="col-sm-6 form-group">
            <label for="subject"><b>Confirm Account Number <font color="red">*</font></b></label>
            <input class="form-control"  id="confirm_account_no" required="" placeholder="Enter Confirm Account Number" type="number" autocomplete="off">
            <?php echo GeneralHelper::getErrorSpan('confirm_account_no'); ?>

          </div>
          <div class="col-sm-6 form-group">
            <label for="subject"><b>Choose Bank <font color="red">*</font></b></label>
            <select name="master_bank_id" class="form-control" id="master_bank_id">
                <option value="">Select Bank</option>
                <?php foreach($bankMasterList as $item){ ?>
                <option value="<?php echo e($item['title']); ?>"><?php echo e($item['title']); ?></option>
                <?php } ?>
            </select>
            <?php echo GeneralHelper::getErrorSpan('master_bank_id'); ?>

          </div>
          <div class="col-sm-6 form-group">
            <label for="subject"><b>IFSC Code <font color="red">*</font></b></label>
            <input class="form-control"  id="IFSCCode" required="" placeholder="Enter IFSC Code" type="text" autocomplete="off">
            <?php echo GeneralHelper::getErrorSpan('IFSCCode'); ?>

          </div>
          
          <div class="col-sm-6" id="msg">
             <!-- <button class="btn btn-success btn-block" id="verify" type="submit">ADD AND VERIFY RECIPIENT</button> -->
          </div>
          <div class="col-sm-6">
             <button class="btn btn-primary btn-block" id="addonly" type="submit">ADD RECIPIENT</button>
          </div>
         <input type="hidden" id="mobileNumber" value="<?php echo e($mobileNumber); ?>">
         <input type="hidden" id="verify_mobile_id" value="<?php echo e($id); ?>">
         <input type="hidden" id="url" value="<?php echo e(route('bankaccountlist',['mdstr'=>$id])); ?>">
        </div>
      </div>
    </div>
  </div>
</div>
<!--Popup Ends Hrer-->

<!--Popup Start Here-->
<div id="verify-account" class="modal fade" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Verify Recipient Bank Account</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">×</span> </button>
      </div>
      <div class="modal-body">
        <div class="form-row mb-4 mb-sm-2" >
          <div  class="col-sm-12 form-group">
            <small class="alert alert-info mt-4"> <span class="badge badge-danger" style="padding: 5px;">IMPORTANT NOTE:</span> &nbsp;Sender as <b>"<?php echo e($senderName); ?>"</b> From  Mobile No. <b>"<?php echo e($mobileNumber); ?>"</b> , SMS confirmation will sent to recipient mobile number.</small>
          </div>
        <!--https://docs.easebuzz.in/wire/contacts-->
         <div class="col-sm-6 form-group">
             <label for="subject"><b>Recipent Name <font color="red">*</font></b></label>
            <input class="form-control" id="rname" required="" placeholder="Enter Full Name" type="text" required="required" readonly="readonly">
            <small><span id="spanname" class="errorSpan"></span></small>
          </div>
       
          <div class="col-sm-6 form-group">
            <label for="subject"><b>Recipent Mobile <font color="red">*</font></b></label>
            <input class="form-control"  id="rmobile" required="" placeholder="e.g: 9015446567" type="number" autocomplete="off" readonly="readonly">
            <?php echo GeneralHelper::getErrorSpan('mobile'); ?>

          </div>
            <div class="col-sm-6 form-group">
            <label for="subject"><b>Choose Bank <font color="red">*</font></b></label>
            <select name="rmaster_bank_id" class="form-control" id="rmaster_bank_id" disabled="disabled">
                <option value="">Select Bank</option>
                <?php foreach($bankMasterList as $item){ ?>
                <option value="<?php echo e($item['title']); ?>"><?php echo e($item['title']); ?></option>
                <?php } ?>
            </select>
            <?php echo GeneralHelper::getErrorSpan('master_bank_id'); ?>

          </div>
          
           <div class="col-sm-6 form-group">
            <label for="subject"><b>Account Number <font color="red">*</font></b></label>
            <input class="form-control"  id="raccount_no" required="" placeholder="Enter Account Number" type="number" autocomplete="off" readonly="readonly">
            <?php echo GeneralHelper::getErrorSpan('account_no'); ?>

          </div>
         
        
          <div class="col-sm-6 form-group">
            <label for="subject"><b>IFSC Code <font color="red">*</font></b></label>
            <input class="form-control"  id="rIFSCCode" required="" placeholder="Enter IFSC Code" type="text" autocomplete="off" readonly="readonly">
            <?php echo GeneralHelper::getErrorSpan('IFSCCode'); ?>

          </div>
            <div class="col-sm-6">
              <label>&nbsp;</label>
             <button class="btn btn-success btn-block" id="btn-success" type="submit">Verify Bank Account</button>
          </div>
          
          <div class="col-sm-12" id="rmsg">
             <p class="alert alert-warning"><i>
              <small>Make sure above information is valid before verifacition bank account details, it will be charge from your wallet. </small></i>
              </p>
             <!-- <button class="btn btn-success btn-block" id="verify" type="submit">ADD AND VERIFY RECIPIENT</button> -->
          </div>
        
         <input type="hidden" id="rmobileNumber" value="<?php echo e($mobileNumber); ?>">
         <input type="hidden" id="rverify_mobile_id" value="<?php echo e($id); ?>">
         <input type="hidden" id="rurl" value="<?php echo e(route('bankaccountlist',['mdstr'=>$id])); ?>">
        </div>
      </div>
    </div>
  </div>
</div>
<!--Popup Ends Hrer--><?php /**PATH /home/pradeep/Projects/www/html/siddiventures/resources/views/RO/MoneyTransfer/BankAccountList.blade.php ENDPATH**/ ?>