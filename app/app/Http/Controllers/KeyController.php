<?php

namespace App\Http\Controllers;

use App\Models\Key;
use App\Models\User;
use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class KeyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = $this->verify_user();
        $key = Key::where('user_id', $user['id'])->get();

        //Key read, return success response
        return response()->json([
            'success' => true,
            'message' => 'Keys read successfully',
            'data' => $key
        ], Response::HTTP_OK);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //Validate data
        $data = $request->json()->all();
        $validator = Validator::make($data, [
            'name' => 'required|string',
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }

        //Get user data
        $user = $this->verify_user();

        if($user['type'] == 'write'){
            //Request is valid, create new key
            $key = Key::create([
                'user_id' => $user['id'],
                'name' => $request->json('name')
            ]);

            //Key created, return success response
            return response()->json([
                'success' => true,
                'message' => 'Key created successfully',
                'data' => $key
            ], Response::HTTP_OK);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'The user does not have permission to write'
            ], Response::HTTP_OK);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Key  $key
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = $this->verify_user();
        $key = Key::where('user_id', $user['id'])->where('id', $id)->first();

        //Key read, return success response
        return response()->json([
            'success' => true,
            'message' => 'Key read successfully',
            'data' => $key
        ], Response::HTTP_OK);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Key  $key
     * @return \Illuminate\Http\Response
     */
    public function edit(Key $key)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Key  $key
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Key $key)
    {
        //Validate data
        $data = $request->json()->all();
        $validator = Validator::make($data, [
            'name' => 'required|string',
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }

        //Get user data
        $user = $this->verify_user();

        if($user['type'] == 'write'){
            //Request is valid, update key
            $key = $key->update([
                'name' => $request->name,
            ]);

            //Key updated, return success response
            return response()->json([
                'success' => true,
                'message' => 'Key updated successfully',
                'data' => $key
            ], Response::HTTP_OK);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'The user does not have permission to write'
            ], Response::HTTP_OK);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Key  $key
     * @return \Illuminate\Http\Response
     */
    public function destroy(Key $key)
    {
        //Get user data
        $user = $this->verify_user();

        if($user['type'] == 'write'){
            $key->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Key deleted successfully'
            ], Response::HTTP_OK);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'The user does not have permission to delete'
            ], Response::HTTP_OK);
        }    
    }

    /**
     * Verify user permission
     *
     * @param  \App\Models\Language  $language
     * @return \Illuminate\Http\Response
     */
    public function verify_user()
    {
        $user = JWTAuth::user();

        //user permission
        $data = User::where('id', $user['id'])->first();

        //set permission
        $permission = [
            'id' => $data->id,
            'type' => $data->type,
        ];

        return $permission;
    }
}
