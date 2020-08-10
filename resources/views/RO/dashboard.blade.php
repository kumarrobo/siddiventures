
@extends('layouts.defaultDashboard')

@section('content')

<section class="containers">
<div class="bg-light shadow-md rounded p-4">
  <div class="row"> 
    <div class="col-lg-12">
    <div class="bg-light shadow-md rounded p-4"> 
      <!--User Profile Section
      ============================================= -->
      <h2> Welcome Back, {{Auth::user()->first_name}} {{Auth::user()->last_name}}!</h2>
      <!-- Personal Information end --> 
    </div>
  </div>
  </div>
</div>
</section>

<section class="containers section pb-4">
  <div class="bg-light shadow-md rounded p-4">
  <div class="row"> 
    <div class="col-lg-12">

        <h2 class="text-9 font-weight-600 text-center">Welcome to the world of SiddiVenture Pvt Ltd!</h2>
        <p class="lead mb-5 text-center">Best Recharge , Topup and Bill Payment Appication!</p>
        <div class="row">
          <div class="col-md-4">
            <div class="featured-box style-1 mb-4">
              <div class="featured-box-icon text-primary"> <i class="fas fa-dollar-sign"></i> </div>
              <h3>Easy To Understand</h3>
              <p>No hidden charges, no payment fees, and free customer service. So you get the best deal every time!</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="featured-box style-1 mb-4">
              <div class="featured-box-icon text-primary"> <i class="fas fa-search"></i> </div>
              <h3>Quick and Easy Bill Payment</h3>
              <p>We'll find you the best deals available from top bus companies for you to choose from, combining quality and economy. </p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="featured-box style-1 mb-4">
              <div class="featured-box-icon text-primary"> <i class="fas fa-percentage"></i> </div>
              <h3>Instant Money Transfer</h3>
              <p>Always get cheapest price with the best in the industry. So you get the best deal every time.</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="featured-box style-1 mb-4">
              <div class="featured-box-icon text-primary"> <i class="fas fa-road"></i> </div>
              <h3>100+ Cities</h3>
              <p>Make your road journeys easier across world with 10000+ Operators.</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="featured-box style-1 mb-4">
              <div class="featured-box-icon text-primary"> <i class="far fa-times-circle"></i> </div>
              <h3>Easy Cancellation &amp; Refunds</h3>
              <p>Get instant refund and get any booking fees waived off!</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="featured-box style-1 mb-4">
              <div class="featured-box-icon text-primary"> <i class="fas fa-heart"></i> </div>
              <h3>Every time, anywhere</h3>
              <p>Because your trip doesn't end with a ticket, we’re here for you all the way</p>
            </div>
          </div>
        </div>
    </div>
  </div>
</div>
      </section>
<!-- Document Wrapper end --> 
@endsection

