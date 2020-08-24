<?php

namespace App\Http\Controllers\Auth\User;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Log;
use Session;
use App\DocumentType;
use App\UserDetail;


class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;
    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = "/login";

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'mobile' => ['required', 'string',  'max:10', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
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
            'name'      => $data['name'],
            'first_name'=> $data['name'],
            'last_name' => $data['last_name'],
            'email'     => $data['email'],
            'mobile'    => $data['mobile'],
            'status'    => 0,
            'role_id'   => 3,
            'password'  => Hash::make($data['password']),
        ]);
    }

   

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm()
    {
        return view('auth.user.registerNow');
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request) {

        $validator = $this->validator($request->all());
        if($validator->fails()) {
                $error=$validator->errors()->all();
                Session::flash('error', $error);
                foreach($request->all() as $k=>$value){
                    Session::flash($k, $request->get($k));
                }
                return redirect('/register')
                 ->withErrors($validator)
                 ->withInput();
        }else{
            //dd($request->all());
            $user = $this->create($request->all())->toArray();
            $user_id = $user['id']; 
            $id = \Crypt::encryptString($user_id);
            Log::channel('newuser')->info('Request', array('Name'=>$user['name'],'Date'=>$user['created_at'])); 
            return redirect('/uploaddocument/'.$id)->with('status', 'You are registred, Please uplaod docuemnt for more details.');
        }
        
    }





    // override default register method
    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function registerUploadDocument(Request $request,$id) {
        $addressProofType   = DocumentType::getAddressTypeDocument();
        $idProofType        = DocumentType::getIDTypeDocument();
        $companyProofType   = DocumentType::getCompanyTypeDocument();
        

         if ($request->isMethod('post')) {
            if(null == ($request->file('id_proof_file'))){
                 //dd('dasd');
             return redirect('/uploaddocument/'.$id)->with('error','Please choose ID proof document.');
            }
            if(null == ($request->file('address_proof_file'))){
                //dd('dasd');
                return redirect('/uploaddocument/'.$id)->with('error','Please choose address proof document.');
            }
            if(null == ($request->file('company_proof_file'))){
                return redirect('/uploaddocument/'.$id)->with('error','Please choose company proof file document.');
            }

            $user = $this->saveCompanyProof($request->all())->toArray();
            //Save Resume
            Log::channel('userDetails')
                ->info('Request', array('Document'=>"Document Uploaded",'Date'=>$user['created_at'])); 
                return redirect('/register/')->with('message', 'You Registered successfully! Awaiting for  approval of your account.');
        }
        

        return view('auth.user.registerUploadNow',
         [
                'id'                =>  $id,
                'addressProofType'  =>  $addressProofType,
                'companyProofType'  =>  $companyProofType,
                'idProofType'       =>  $idProofType,
            ]
        );
        
    }
    // override default register method





    /**
     * Save Company Proof and Upload Document By the Distributor.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function saveCompanyProof(array $data)
    {       
        $user_id   = Crypt::decryptString($data['id']); 
        $userDetails = [];
        
        $userDetails = UserDetail::where('user_id','=',$user_id)->first();

        if($userDetails == null ){
            $userDetails = new UserDetail();
        }else{
            $userDetails['id'] = $userDetails['id'];    
        }

        //dd($userDetails);
        $userDetails['user_id']                 = $user_id;
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
        //dd($userDetails);
        if($userDetails->save()){
            return $userDetails;
        }

    }

   
}
