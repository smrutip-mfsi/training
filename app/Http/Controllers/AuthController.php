<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\User;
use App\Models\Address;
use App\Models\CommunicationMedium;
use Auth;
use Mail;

class AuthController extends Controller
{
	public $comm_array = ['1','2','3','4'];

	/**
		 * To validate the fields
		 *
		 * @param  Request  $request
		 * @param  integer  $user_id
	*/
	public function validateRequest($request, $user_id)
	{
		$state_list = config( 'constants.state_list' );

		$messages = [
			'first_name.required' => 'First Name is required!!!',
			'first_name.min' => 'First Name must be between 2 to 15 characters!!!',
			'first_name.max' => 'First Name must be between 2 to 15 characters!!!',
			'first_name.alpha' => 'First Name must can contain only alphabets!!!',
			'r_street.required' => 'Please provide Residence street address',
			'o_street.required' => 'Please provide Office street address',
			'r_city.required' => 'Please provide Residence city address',
			'o_city.required' => 'Please provide Office city address',
			'r_state.required' => 'Please select a valid Residence state',
			'o_state.required' => 'Please select a valid Office state',
			'r_zip.required' => 'Please provide Residence Zip code',
			'o_zip.required' => 'Please provide Office Zip code',
			'r_phone.required' => 'Please provide Residence Phone no.',
			'o_phone.required' => 'Please provide Office Phone no.',
			'r_fax.required' => 'Please provide Residence Fax no.',
			'o_fax.required' => 'Please provide Office Fax no.'

		];

		$state_string = implode(',', array_keys($state_list));
		$val_array = [
							'first_name' => 'required|alpha|min:2|max:15',
							'middle_name' => 'alpha|max:15',
							'last_name' => 'required|alpha|min:2|max:15',
							'email' => 'required|email|unique:users,email,' . $user_id,
							'password' => 'required|min:8|max:12',
							'twitter' => 'max:15',
							'prefix' => 'in:mr,ms,mrs',
							'gender' => 'in:male,female,others',
							'dob' => 'required',
							'marital' => 'in:single,married',
							'employment' => 'in:employed,unemployed',
							'employer' => 'max:20',
							'r_street' => 'alpha_dash|max:20',
							'o_street' => 'alpha_dash|max:20',
							'r_city' => 'alpha|max:20',
							'o_city' => 'alpha|max:20',
							'r_state' => 'in:' . $state_string,
							'o_state' => 'in:' . $state_string,
							'r_zip' => 'numeric|digits_between:5,6',
							'o_zip' => 'numeric|digits_between:5,6',
							'r_phone' => 'numeric|digits_between:7,11',
							'o_phone' => 'numeric|digits_between:7,11',
							'r_fax' => 'numeric|digits_between:7,11',
							'o_fax' => 'numeric|digits_between:7,11',
							'pic' => 'image|mimes:jpeg,jpg,JPG,JPEG|max:6144'

					];

		$this->validate($request, $val_array, $messages);
		return true;
	}

	/**
		 * Show registration form
		 *
		 * @param  Request  $request
	*/
	public function register(Request $request)
	{
		$state_list = config( 'constants.state_list' );
		$comm_medium = CommunicationMedium::retrieveData();
		return view('registration', ['state_list' => $state_list, 'comm_medium' => $comm_medium]);
	}

	/**
		 * Process registration form
		 *
		 * @param  Request  $request
	*/
	public function doRegister(Request $request)
	{
		// Update Data
		if($request->id)
		{
			if($this->validateRequest($request, $request->id))
			{
				$data = $request->all();
				$comm = $request->get('comm');

				if ( !empty( $comm ) && empty( array_intersect( $comm, $this->comm_array) ) )
				{
					return redirect('register')->with( 'redirect_error', 'invalid com selection' )
																			->withInput();
				}

				if(isset($comm))
				{
					$data['comm_val'] = implode(', ', $comm);
				}
				else
				{
					$data['comm_val'] = '';
				}

				if($request->hasFile('pic'))
				{
					$pic_name = AuthController::imageUpload($request, $request->id);
				}
				else
				{
					$pic_name = User::retrieveData($request->id)[0]->photo;
				}

				$data['pic_name'] = $pic_name;

				User::updateUser($data);
				Address::updateAddress($data);
				return redirect('details');
			}

		}
		// Create data
		else
		{
			if($this->validateRequest($request, 0))
			{
				// $user_id = auth()->user()->id;
				$comm = $request->get('comm');

				if ( !empty( $comm ) && empty( array_intersect( $comm, $this->comm_array) ) )
				{
					return redirect('register')->with( 'redirect_error', 'invalid com selection' )
																			->withInput();
				}

				$data = $request->all();

				if(isset($comm))
				{
					$data['comm_val'] = implode(', ', $comm);
				}
				else
				{
					$data['comm_val'] = '';
				}

				$user_insert_id = User::store($data);

				if($request->hasFile('pic'))
				{
					$pic_name = AuthController::imageUpload($request, $user_insert_id);
				}
				else
				{
					$pic_name = '';
				}

				User::imageUpload($user_insert_id, $pic_name);

				if($user_insert_id)
				{
					$data['id'] = $user_insert_id;
					$address_insert_success = Address::store($data);

					if($address_insert_success == 1)
					{
						\Session::flash('flash_message', 'A verification link has been sent to the registered mail id');

						$key = User::find($user_insert_id)->key;
						$url = config('constants.verification_path') . 'login/verify?key=' . $key;

						Mail::send('email', ['url' => $url], function ($message)
						{
							$message->from('1234asdf56789@gmail.com', 'Laravel');
							$message->to('smrutip@mindfiresolutions.com', 'Hello User')->subject('Email Verification');
						});

						if(auth()->user() != null)
						{
							return redirect('details');
						}
						else
						{
							return redirect('login');							
						}
					}
					else
					{
						User::deleteRecord($user_insert_id);
						return view('registration', ['db_insert_error', 'Please try again after sometime']);
					}

				}
				else
				{
					return view('registration', ['db_insert_error', 'Please try again after sometime']);
				}
			}
		}
		
	}

	/**
	 * To upload the profile image
	 *
	 * @param  Request  $request
	 * @param  integer  $id
	*/
	public static function imageUpload($request, $id)
	{
		$image_name = $request->file('pic')->getClientOriginalName();
		$temp_file = public_path( 'images/profile_pic/' );
		$request->file('pic')->move( $temp_file, $id . '_' . $image_name );

		return $id . '_' . $image_name;
	}

	/**
	 * Show login form
	 *
	 * @param  Request  $request
	*/
	public function login(Request $request)
	{

		if(Auth::check())
		{
			return redirect('dashboard');
		}
		else
		{
			return view('login');
		}

	}

	/**
	 * Process login form
	 *
	 * @param  Request  $request
	*/
	public function doLogin(Request $request)
	{

		$messages = [
			'email.required' => 'Email is required!!!',
			'password.required' => 'Password is required!!!',
			'password.min' => 	'Password must be between 8 to 12!!!',
			'password.max' => 	'Password must be between 8 to 12!!!',
		];

		$this->validate($request, [
				'email' => 'required|email',
				'password' => 'required|min:8|max:12'
			], $messages);

		if (Auth::attempt(['email' => $request->email, 'password' => $request->password, 'is_active' => 1]))
		{
			return redirect('dashboard');
		}
		else
		{
			return redirect('login')->with( 'redirect_error', 'Login Failed' );
		}

	}

	/**
	 * To verify though mail
	 *
	 * @param  string  $key
	*/
	public function emailVerification(Request $request)
	{
		$verification_success = User::verifyLink($request->key);

		if($verification_success)
		{
			\Session::flash('flash_message', 'Your account is active now');
			return redirect('login');			
		}
		else
		{
			return redirect('login')->with( 'redirect_error', 'You have clicked on an invalid verification link' );
		}
	}

}
