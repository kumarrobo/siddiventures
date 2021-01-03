@extends('layouts.defaultRODashboard')
@section('content')
<section class="containers">
<div class="bg-light shadow-md rounded p-4">
  <div class="row"> 
    <div class="col-lg-12">
    <div class="bg-light shadow-md rounded p-4"> 
      <!--User Profile Section

      ============================================= -->
      <h5 class="mb-4">My Waller Transactions</h5>
      <hr/>
      <?php if(count($payment_wallet_transactions)){ ?>

            <div class="tab-content my-3" id="myTabContent">
            <div class="tab-pane fade show active" id="first" role="tabpanel" aria-labelledby="first-tab">
              <div class="table-responsive-md" style=" width:1240px; overflow: auto">
                <table class="table table-hover border" style="font-size: 12px;">
                  <thead class="thead-light">
                    <tr>
                      <th>SN</th>
                      <th>Date</th>
                      <th>Credit/Debit</th>
                      <th>Ref No</th>
                      <th>Payment</th>
                      <th>Status</th>
                      <th>Remarks</th>
                      <th>Charge</th>
                      <th>Transfer</th>
                      <th>Recharge</th>
                      <th>Balance</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php $count=1;foreach ($payment_wallet_transactions as $key => $value) { //dd($value); ?>
                    <tr>
                      <td class="align-middle">{{$count}}</td>
                      <td class="align-middle" nowrap="nowrap">{{GeneralHelper::getFullDateFormate($value['transaction_date'])}}</td>
                      <td class="align-middle" nowrap="nowrap">
                        <?php if($value['credit_amount']>0){ ?>
                        <font color="green" style="font-weight:500">{{GeneralHelper::getAmount($value['credit_amount'])}}</font>
                        <?php } ?>  
                         <?php if($value['debit_amount']>0){ ?>
                        <font color="red" style="font-weight:500">{{GeneralHelper::getAmount($value['debit_amount'])}}</font>
                        <?php } ?> 
                      </td>
                      <td class="align-middle" nowrap="nowrap">
                        <?php if(isset($value['WalletRechargePayment']['payment_ref_key'])){ 
                                     echo $value['WalletRechargePayment']['payment_ref_key'];
                              }else{ echo $value['transaction_number']; }
                        ?>
                    </td>
                      <td class="align-middle" nowrap="nowrap"> <?php if(isset($value['WalletRechargePayment'])){ ?>{{GeneralHelper::getTransactionTypeName($value['WalletRechargePayment']['payment_mode'])}}
                        <?php }else{ ?> Wallet Transfer<?php } ?></td>
                      <td class="align-middle"><?php if($value['status']=='Success'){ ?>
                                              <font color="green"><b>{{$value['status']}}</b></font>
                      <?php }else{ ?>
                        <font color="black"><b>{{$value['status']}}</b></font>
                        <?php } ?></td>
                      <td class="align-middle" title="{{$value['remarks']}}">{{Str::limit($value['remarks'],10)}}</td>
                      <td class="align-middle">{{GeneralHelper::getAmount($value['transfer_charge'])}}</td>
                      <td class="align-middle">
                       <?php if($value['wallet_recharge_payment_id']==null){ ?>
                          <i class="fas fa-check-circle text-4 text-success" data-toggle="tooltip" data-original-title="Yes"></i>
                      <?php }else{ ?>
                          <i class="fas fa-times-circle text-4 text-danger" data-toggle="tooltip" data-original-title="No"></i>
                      <?php } ?>
                      </td>
                      <td class="align-middle">
                       <?php if($value['wallet_recharge_payment_id']==null){ ?>
                          <i class="fas fa-times-circle text-4 text-danger" data-toggle="tooltip" data-original-title="No"></i>
                      <?php }else{ ?>
                           <i class="fas fa-check-circle text-4 text-success" data-toggle="tooltip" data-original-title="Yes"></i>
                      <?php } ?>
                      </td>
                      <td class="align-middle" nowrap="nowrap">
                      <?php if($value['credit_amount']>0){ ?>
                        <font color="green" style="font-weight:500"><i class="fas fa-arrow-up"></i>&nbsp;</font><?php } ?>
                      <?php if($value['debit_amount']>0){ ?>
                         <font color="red" style="font-weight:500"><i class="fas fa-arrow-down"></i></font>&nbsp;
                      <?php } ?>
                      {{GeneralHelper::getAmount($value['updated_wallet_balance'])}}
                      </td>
                    </tr>
                    <?php $count++;} ?>
                     
                   

                  
                  </tbody>
                </table>
                <p class="pull-right" style="float: right;">{{ $payment_wallet_transactions->onEachSide(5)->links() }}</p>

              </div>

            </div>
           
          </div>
           <?php }else{?>
            <div class="alert alert-danger col-md-12">No Records Found</div>
           <?php } ?>
      <!-- Personal Information end --> 
    </div>
  </div>
  </div>
</div>
</section>
<!-- Document Wrapper end --> 
@endsection

