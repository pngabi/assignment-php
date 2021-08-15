<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Models\User;
use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class LanguageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = $this->verify_user();
        $languages = Language::where('user_id', $user['id'])->get();

        //Language read, return success response
        return response()->json([
            'success' => true,
            'message' => 'Languages read successfully',
            'data' => $languages
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
            'iso' => 'required|string',
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }

        //Get user data
        $user = $this->verify_user();

        if($user['type'] == 'write'){
            //Request is valid, create new language
            $language = Language::create([
                'user_id' => $user['id'],
                'name' => $request->json('name'),
                'iso' => $request->json('iso')
            ]);

            //Language created, return success response
            return response()->json([
                'success' => true,
                'message' => 'Language created successfully',
                'data' => $language
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
     * @param  \App\Models\Language  $language
     * @return \Illuminate\Http\Response
     */
    public function show(Language $language)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Language  $language
     * @return \Illuminate\Http\Response
     */
    public function edit(Language $language)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Language  $language
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Language $language)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Language  $language
     * @return \Illuminate\Http\Response
     */
    public function destroy(Language $language)
    {
        //
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
