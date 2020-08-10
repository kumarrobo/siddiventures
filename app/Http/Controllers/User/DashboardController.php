<?php

namespace App\Http\Controllers\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Session;
use Log;
use Hash;
use App\User;
use App\State;
use App\City;
use App\UserDetail;
use App\DocumentType;

class DashboardController extends Controller
{
    //


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:user');
    }


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        //dd("ok");
        return view('user.dashboard');
    }






     /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:25'],
            'email' => ['required', 'string', 'email', 'max:100', 'unique:users'],
            'mobile' => ['required', 'string',  'max:10', 'unique:users'],
            'password' => ['required', 'string', 'min:6'],
        ]);
    }


    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validatorAddress(array $data)
    {
        return Validator::make($data, [
            'address_line_1' => ['required', 'string', 'max:255'],
            'state_id' => ['required'],
            'city_id' => ['required'],
        ]);
    }


    /**
     * Add New Retailer By the Distributor
     * @param \Illuminate\Http\Request;
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function retailerPersonalDetails(Request $request, $id)
    {
      
        return view('user.Distributor.RO.retailerPersonalDetails',['id'=>$id]);
    }

    /**
     * Add New Retailer By the Distributor
     * @param \Illuminate\Http\Request;
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function viewrodetails(Request $request, $id)
    {
      
        return view('user.Distributor.RO.viewrodetails',['id'=>$id]);
    }




     /**
     * Upload Document For Retailer By the Distributor
     * @param \Illuminate\Http\Request;
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function retailerDocumentProof(Request $request, $id)
    {
        $addressProofType   = DocumentType::getAddressTypeDocument();
        $idProofType        = DocumentType::getIDTypeDocument();
        $companyProofType   = DocumentType::getCompanyTypeDocument();

        if ($request->isMethod('post')) {
            $user = $this->saveCompanyProof($request->all())->toArray();
            //dd($request->all());
            //Save Resume
            Log::channel('userDetails')
                ->info('Request', array('Document'=>"Document Uploaded",'Date'=>$user['created_at'])); 
                return redirect('/user/retailercompany/'.$user['id'])
                ->with('message', 'Retailer Details Saved !!');
            
           
        }
        

        return view('user.Distributor.RO.retailerDocumentProof',
            [
                'id'                =>  $id,
                'addressProofType'  =>  $addressProofType,
                'companyProofType'  =>  $companyProofType,
                'idProofType'       =>  $idProofType,
            ]
        );
    }


    /**
     * Save Company Proof and Upload Document By the Distributor.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function saveCompanyProof(array $data)
    {       

        $user_id     = $data['id']; 
        $userDetails = UserDetail::find($user_id);

        $userDetails['id']                      = $user_id;
        $userDetails['id_proof_type_id']        = $data['id_proof_file_type'];
        $userDetails['address_proof_type_id']   = $data['address_proof'];
        $userDetails['business_proof_type_id']  = $data['company_proof'];
        // dd($data);
        if(!empty($data['id_proof_file'])){
            $file                               =   $data['id_proof_file'];
            $fileName                           =   $file->getClientOriginalName();
            $fileName                           =   str_replace(" ","_",strtolower($fileName));
            $userDetails['id_proof_document']   =   $fileName;
            $destinationPath                    =   'storage/uploads/RO';
            $file->move($destinationPath,$fileName);
        }


        if(!empty($data['address_proof_file'])){
            $file                               =   $data['address_proof_file'];
            $fileName                           =   $file->getClientOriginalName();
            $fileName                           =   str_replace(" ","_",strtolower($fileName));
            $userDetails['address_proof']       =   $fileName;
            $destinationPath                    =   'storage/uploads/RO';
            $file->move($destinationPath,$fileName);
        }


        if(!empty($data['company_proof_file'])){
            $file                               =   $data['company_proof_file'];
            $fileName                           =   $file->getClientOriginalName();
            $fileName                           =   str_replace(" ","_",strtolower($fileName));
            $userDetails['business_proof']      =   $fileName;
            $destinationPath                    =   'storage/uploads/RO';
            $file->move($destinationPath,$fileName);
        }

        if($userDetails->save()){
            return $userDetails;
        }

    }




    /**
     * Add New Retailer By the Distributor
     * @param \Illuminate\Http\Request;
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function addretailer(Request $request)
    {
      
        return view('user.addRetailer');
    }


    /**
     * Add New Retailer By the Distributor
     * @param \Illuminate\Http\Request;
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function retailerAddress(Request $request,$id)
    {
        $stateList = State::where('status','=',1)->get();
        $cityList = City::where('status','=',1)->get();
        // dd($cityList);
        
        if ($request->isMethod('post')) {
            $validator = $this->validatorAddress($request->all());
            if($validator->fails()) {
                    $error=$validator->errors()->all();
                    Session::flash('error', $error);
                    foreach($request->all() as $k=>$value){
                        Session::flash($k, $request->get($k));
                    }
                    return redirect()->back()
                     ->withErrors($validator)
                     ->withInput();
            }else{
                $user = $this->saveAddress($request->all())->toArray();
                Log::channel('userDetails')
                ->info('Request', array('Name'=>$user['user_id'],'Date'=>$user['created_at'])); 
                return redirect('/user/retailercompany/'.$user['id'])
                ->with('message', 'Retailer Details Saved !!');
            }

        }
        $userDetails = UserDetail::find($id);
        if($userDetails == null){
            // $userDetails[''] = '';
        }

        return view('user.Distributor.RO.retailerAddress',
            [
                'id'            =>  $id,
                'cityList'      =>  $cityList,
                'stateList'     =>  $stateList,
                'userDetails'   =>  $userDetails
            ]
        );
    }

     /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function saveAddress(array $data)
    {       

        $user_id = $data['id']; 
        $userDetails = UserDetail::where('user_id','=',$user_id)->first();
        if($userDetails == null){
            return UserDetail::create([
                    'address_line_1'    => $data['address_line_1'],
                    'address_line_2'    => $data['address_line_2'],
                    'country_id'        => $data['country_id'],
                    'state_id'          => $data['state_id'],
                    'city_id'           => $data['city_id'],
                    'district'          => $data['district'],
                    'pincode'           => $data['pincode'],
                    'user_id'           => $data['id'],
            ]);

        }else{
            $userDetails['address_line_1']  = $data['address_line_1'];
            $userDetails['address_line_2']  = $data['address_line_2'];
            $userDetails['country_id']      = $data['country_id'];
            $userDetails['state_id']        = $data['state_id'];
            $userDetails['city_id']         = $data['city_id'];
            $userDetails['district']        = $data['district'];
            $userDetails['pincode']         = $data['pincode'];
            if($userDetails->save()){
                return $userDetails;
            }
        }

    }


     /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validatorCompany(array $data)
    {
        return Validator::make($data, [
            'company_name'          => ['required', 'string', 'max:255'],
            'company_type'          => ['required'],
            'service_by'            => ['required'],
            'zone'                  => ['required'],
            'identification_type'   => ['required'],
            'is_name_on_pan_card'   => ['required'],
            'pan_card_number'       => ['required'],
        ]);
    }

     /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\UserDetail
     */
    protected function saveCompany(array $data)
    {   

        $user_id = $data['id']; 
        $userDetails = UserDetail::where('id','=',$user_id)->first();
        if($userDetails == null){
            return UserDetail::create([
                    'company_name'        => $data['company_name'],
                    'company_type'        => $data['company_type'],
                    'service_by'          => $data['service_by'],
                    'zone'                => $data['zone'],
                    'identification_type' => $data['identification_type'],
                    'is_name_on_pan_card' => $data['is_name_on_pan_card'],
                    'pan_card_number'     => $data['pan_card_number'],
                    'user_id'             => $data['id'],
            ]);

        }else{
            $userDetails['company_name']        = $data['company_name'];
            $userDetails['company_type']        = $data['company_type'];
            $userDetails['service_by']          = $data['service_by'];
            $userDetails['zone']                = $data['zone'];
            $userDetails['identification_type'] = $data['identification_type'];
            $userDetails['is_name_on_pan_card'] = $data['is_name_on_pan_card'];
            $userDetails['pan_card_number']     = $data['pan_card_number'];
            if($userDetails->save()){
                //dd($userDetails);
                return $userDetails;
            }
        }

    }


    /**
     * Add New Retailer By the Distributor
     * @param \Illuminate\Http\Request;
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function retailerCompanyProof(Request $request,$id)
    {
     
        if ($request->isMethod('post')) {
            $validator = $this->validatorCompany($request->all());
            if($validator->fails()) {
                    $error=$validator->errors()->all();
                    Session::flash('error', $error);
                    foreach($request->all() as $k=>$value){
                        Session::flash($k, $request->get($k));
                    }
                    return redirect()->back()
                     ->withErrors($validator)
                     ->withInput();
            }else{
                $user = $this->saveCompany($request->all())->toArray();
                Log::channel('userDetails')
                ->info('Request', array('Name'=>$user['company_name'],'Date'=>$user['created_at'])); 
                return redirect('/user/retailercompany/'.$user['id'])
                ->with('message', 'Retailer Details Saved !!');
            }

        }
        $userDetails = UserDetail::find($id);
        return view('user.Distributor.RO.retailerCompanyProof',[
            'id'=>$id,
            'userDetails'=>$userDetails
        ]);
    }



     /**
     * Add New Retailer By the Distributor
     * @param \Illuminate\Http\Request;
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function postaddretailer(Request $request)
    {
        if ($request->isMethod('post')) {
            $validator = $this->validator($request->all());
            if($validator->fails()) {
                    $error=$validator->errors()->all();
                    Session::flash('error', $error);
                    foreach($request->all() as $k=>$value){
                        Session::flash($k, $request->get($k));
                    }
                    return redirect()->back()
                     ->withErrors($validator)
                     ->withInput();
            }else{
                $user = $this->create($request->all())->toArray();

                $userDetail = UserDetail::create(['user_id'    => $user['id']]);
                Log::channel('newuser')
                ->info('Request', array('Name'=>$user['name'],'Date'=>$user['created_at'])); 
                return redirect('/user/retaileraddress/'.$userDetail['id'])
                ->with('message', 'We sent a comfirmation email to your email, please click on link inside before login');
            }

        }
        return view('user.addRetailer');
    }




    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {   

        return User::create([
                'first_name'    => $data['first_name'],
                'last_name'     => $data['last_name'],
                'name'          => $data['first_name'].' '.$data['last_name'],
                'email'         => $data['email'],
                'mobile'        => $data['mobile'],
                'password'      => Hash::make($data['password']),
                'role_id'       => 3,
        ]);


    }



   

}
