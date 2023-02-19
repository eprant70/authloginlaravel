<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
// use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Validator;

class AuthController extends Controller
{
    // public function register(Request $request)
    // {
    //     $validator = Validator::make($request->all(),[
    //         'name' => 'required|string|max:255',
    //         'email' => 'required|string|email|max:255|unique:users',
    //         'password' => 'required|string|min:8'
    //     ]);

    //     if($validator->fails()){
    //         return response()->json($validator->errors());       
    //     }

    //     $user = User::create([
    //         'name' => $request->name,
    //         'email' => $request->email,
    //         'password' => Hash::make($request->password)
    //      ]);

    //     $token = $user->createToken('auth_token')->plainTextToken;

    //     return response()
    //         ->json(['data' => $user,'access_token' => $token, 'token_type' => 'Bearer', ]);
    // }

    private $response = [
        'message' => null,
        'data' => null
    ];
    public function register(Request $req)
    {
        $req->validate(([
            'name' => 'required',
            'email' => 'required',
            'password' => 'required',
        ]));
        $data = User::create([
            'name' => $req->name,
            'email' => $req->email,
            'password' => Hash::make($req->password),
        ]);
        $token = $data->createToken('auth_token')->plainTextToken;
        // $this->response['message'] = 'succes';
        // return response()->json($this->response, 200);
        return response()
            ->json(['data' => $data,'access_token' => $token, 'token_type' => 'Bearer', ]);
    }
    // public function login(Request $req)
    public function login(Request $request)
    {
        // $req->validate([
        //     'email' => 'required',
        //     'password' => 'required',
        // ]);
        // $user = User::where('email', $req->email)->first();
        // if (!$user || !Hash::check($req->password, $user->password)) {
        //     return response()->json([
        //         'message' => 'failed',
        //         'message' => 'Email or password is incorrect',
        //     ]);
        // }
        // $token = $user->createToken($req->device_name)->plainTextToken;
        // $this->response['message'] = 'succes';
        // $this->response['data'] = ['token' => $token];
        // return response()->json($this->response, 200);
        if (!Auth::attempt($request->only('email', 'password')))
        {
            return response()
                ->json(['message' => 'Unauthorized'], 401);
        }

        $user = User::where('email', $request['email'])->firstOrFail();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()
            ->json(['message' => 'Hi '.$user->name.', welcome to home','access_token' => $token, 'token_type' => 'Bearer', ]);
    
    }
    public function me()
    {
        $user=Auth::user();
        $this->response['message']='succes';
        $this->response['data']=$user;
        return response()->json($this->response,200);
    }
    public function logout()
    {
        // $logout=auth()->user()->currentAccesToken()->delete();
        // $this->response['message']='succes';
        // return response()->json($this->response,200);     
        auth()->user()->tokens()->delete();

        return [
            'message' => 'Hai ... You have successfully logged out and the token was successfully deleted'
        ];   
    }

}
