
            <!-- Personal Information
          ============================================= -->
            <div class="row">
              <div class="col-lg-12 col-sm-12">
              <p id="msgBank" style="font-size: 12px;"></p>
              <p id="msg" style="font-size: 12px;"></p>
            </div>
              <div class="col-lg-2 col-sm-12"></div>
                <div class="col-lg-8 col-sm-12">
              <?php //dd($chargesArr);?>
               @if(Session::has('message'))
                    <p class="alert alert-danger">{{Session::get('message')}}</p>
                    @endif
              <div class="card  mb-3"  style=" 
              -webkit-box-shadow: -5px 8px 24px -17px rgba(0,0,0,0.75);
              -moz-box-shadow: -5px 8px 24px -17px rgba(0,0,0,0.75);
              box-shadow: -5px 8px 24px -17px rgba(0,0,0,0.75); ">
                  <div class="card-header"><b class="mb-4">{{ __('Transfer Money To Bank Account') }}</b></div>
                  <div class="card-body">
                    <form action="{{route('rotransferaction')}}" method="POST">
                      @csrf
                    <div class="form-group ">
                     <div class="row">
                        <div class="col-md-4 col-sm-5">SENDER MOBILE NUMBER</div>
                        <div class="col-md-1 col-sm-1">&nbsp;:</div>
                        <div class="col-md-7 col-sm-6">{{$verifyMobile['mobile']}}
                          <input type="hidden" name="mobile" id="mobile" class="form-control" required="required" readonly="readonly" value="{{$verifyMobile['mobile']}}"></div>
                      </div>
                    </div>
                    <div class="form-group ">
                     <div class="row">
                        <div class="col-md-4 col-sm-5">SENDER REMAINING LIMIT</div>
                        <div class="col-md-1 col-sm-1">&nbsp;:</div>
                        <div class="col-md-7 col-sm-6">RS. {{number_format($mobileTransactionBalanceAmount,2)}}
                          <input type="hidden" name="balance" id="balance" class="form-control" required="required" readonly="readonly" value="{{number_format($mobileTransactionBalanceAmount,2)}}"></div>
                      </div>
                    </div>
                     <div class="form-group ">
                     <div class="row">
                        <div class="col-md-4">RECIPIENT MOBILE NUMBER</div>
                        <div class="col-md-1">&nbsp;:</div>
                        <div class="col-md-7">{{$verifyMobile['mobile']}}
                        <input type="hidden" name="r_mobile" id="r_mobile" class="form-control" required="required" readonly="readonly" value="{{$verifyMobile['mobile']}}">
                        </div>
                      </div>
                    </div>
                     <div class="form-group ">
                     <div class="row">
                        <div class="col-md-4">RECIPIENT NAME</div>
                        <div class="col-md-1">&nbsp;:</div>
                        <div class="col-md-7">{{$result['VerifybeneficiariesBankAccount']['account_name']}}
                        <input type="hidden" name="account_name" id="account_name" class="form-control" required="required" readonly="readonly" value="{{$result['VerifybeneficiariesBankAccount']['account_name']}}">
                        </div>
                      </div>
                    </div>
                     <div class="form-group ">
                     <div class="row">
                        <div class="col-md-4">RECIPIENT ACCOUNT NUMBER</div>
                        <div class="col-md-1">&nbsp;:</div>
                        <div class="col-md-7">{{$result['VerifybeneficiariesBankAccount']['account_number']}}
                        <input type="hidden" name="account_number" id="account_number" class="form-control" required="required" readonly="readonly" value="{{$result['VerifybeneficiariesBankAccount']['account_number']}}">
                        </div>
                      </div>
                    </div>
                     <div class="form-group ">
                     <div class="row">
                        <div class="col-md-4">RECIPIENT BANK NAME</div>
                        <div class="col-md-1">&nbsp;:</div>
                        <div class="col-md-7">{{$result['VerifybeneficiariesBankAccount']['bank_name']}}
                        <input type="hidden" name="bank_name" id="bank_name" class="form-control" required="required" readonly="readonly" value="{{$result['VerifybeneficiariesBankAccount']['bank_name']}}">
                        </div>
                      </div>
                    </div>
                     <div class="form-group ">
                     <div class="row">
                        <div class="col-md-4">RECIPIENT BANK IFSC CODE</div>
                        <div class="col-md-1">&nbsp;:</div>
                        <div class="col-md-7">{{$result['VerifybeneficiariesBankAccount']['account_ifsc']}}
                        <input type="hidden" name="account_ifsc" id="account_ifsc" class="form-control" required="required" readonly="readonly" value="{{$result['VerifybeneficiariesBankAccount']['account_ifsc']}}">
                        </div>
                      </div>
                    </div>
                     <div class="form-group ">
                     <div class="row">
                        <div class="col-md-4">TRANSFER AMOUNT</div>
                        <div class="col-md-1">&nbsp;:</div>
                        <div class="col-md-7"><b>Rs. {{$amount}}</b>
                        <input type="hidden" name="amount" id="amount" class="form-control" required="required" placeholder="e.g 5000.00">
                        </div>
                      </div>
                    </div>
                     <div class="form-group ">
                     <div class="row"><?php //dd($chargesArr['type']);?>
                        <div class="col-md-4">TRANSFER FEE</div>
                        <div class="col-md-1">&nbsp;:</div>
                        <div class="col-md-7"><b>RS.<?php echo $chargeAmount;?>&nbsp;<?php echo $chargeType; ?> Per Transaction</b>
                        <input type="hidden" name="fee" id="fee" class="form-control" required="required" placeholder="0.00" value="0.00" readonly="readonly">
                        </div>
                      </div>
                    </div>
                   
                   
                     <div class="form-group ">
                     <div class="row">
                        <div class="col-md-4">REMARKS</div>
                        <div class="col-md-1">&nbsp;:</div>
                        <div class="col-md-7">{{$remarks}}
                        <input type="hidden" name="remarks" id="remarks" class="form-control" required="required">
                        </div>
                      </div>
                    </div>
                     <div class="form-group ">
                     <div class="row">
                        <div class="col-md-4">PAYMENT MODE</div>
                        <div class="col-md-1">&nbsp;</div>
                        <div class="col-md-7">{{$payment_mode}}
                          <input type="hidden" name="payment_mode" id="payment_mode" class="form-control" required="required" value="{{$payment_mode}}">

                        </div>
                      </div>
                    </div>

                    <div class="form-group ">
                     <div class="row">
                      <div  class="col-sm-12 form-group">
                      <small class="alert alert-info mt-4"> <span class="badge badge-danger" style="padding: 5px;">Note:</span> Amount will be transfer in Rs.{{DEFAUT_TRANSFER_AMOUNT}} maximum per transaction. You will get SMS and email confirmation on each transaction.</small>
                    </div>
                        <div class="col-md-3">&nbsp;</div>
                        <div class="col-md-9">
                          <button type="button" name="cancel" class="btn btn-danger" style="font-size: 14px; text-decoration: none">Cancel</button>
                          
                          <input type="submit" name="submit" id="verifyAccountBtn"   class="btn btn-success" style="font-size: 14px; text-decoration: none" value="Transfer Now">
                          <input type="hidden" name="beneficiaries_bank_account_id" value="{{$result['id']}}">
                          <input type="hidden" name="verify_mobile_number_id" value="{{$result['verify_mobile_number_id']}}">
                          <input type="hidden" name="verify_mobile_beneficiaries_bank_account_id" value="{{$result['id']}}">
                          <input type="hidden" name="enPostStr" value="{{$enPostStr}}">
                          <input type="hidden" name="id" value="{{$id}}">
                        </div>
                      </div>
                    </div>
                  </form>
                  </div>
              </div>
              </div> 
              <div class="col-lg-2 col-sm-12"></div>
   
              
          <!-- Orders History end --> 
            </div>     
