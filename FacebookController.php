<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Socialite;
use App\User;
use Auth;
use Session;
use Illuminate\Support\Facades\Hash;
use DateTime;
class FacebookController extends Controller
{
    public function FacebookLogIn(){
    	return Socialite::driver('facebook')->redirect();

    }
    public function FacebookGetData(){
    	$req1 = Socialite::driver('facebook')->user();
    	if( (Session()->has('FacebookStatus')) && (Session('FacebookStatus')=='signup')){
    		//return('sdfsdfif cond');
    		return $this->NewFabookAccount($req1);
    	}
    	$email=$req1->email;
    	if($email=='')
    	{
    		Session::flash('message', 'Your Email Id is Protected Or Not Updated Please Add Email Id Or Google In With Google');
			return redirect()->route('login');
    		
    	}
    	else{
    		$val=User::where('email',$email)->first();
    		if(count($val)>0)
    		{
    			$User_id=$val->id;
    			//dd(count($val));
    			Auth::loginUsingId($User_id);
    			return view('home');

    		}
    		else{
    			Session::flash('message', 'Your Record Not Found In Server !! Please Sign Up');
			    return redirect()->route('register');
    		}

    	}

    }
    public function FacebookSignUp(){
    	Session(['FacebookStatus'=>'signup']);
    	return Socialite::driver('facebook')->redirect();

    }
    public function NewFabookAccount($req){
    		//return("sfd");
    		$email=$req->email;
    		//dd($email);

    		$val=User::where('email',$email)->get();
    		if(count($val)>0)
    		{
    			
    			Session()->forget('FacebookStatus');
				Session(['FacebookStatus'=>'login']);
				
    		    Session::flash('message', 'Your Record  Found In Server !! Please Login Up');
    		    return redirect()->route('login');
			  
    		}else{
    			//return("sdfsdfsd");

	    	    $now = new DateTime();
				$User= new User();
				$User->name=$req->name;
				$User->email=$req->email;
				$User->password=Hash::make(str_random(12));
				$User->created_at=$now;
				$User->updated_at=$now;
				$User->save();
				$LogId=User::where('email',$User->email)->first();
				Auth::loginUsingId($LogId->id);
				//session()->flush('status');
				//Session::forget('status');
				Session()->forget('FacebookStatus');
				Session(['FacebookStatus'=>'login']);
				return view('home');
		}

    }
   
}

