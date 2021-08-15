<?php

namespace App\Http\Controllers;

use App\Models\Translation;
use App\Models\User;
use App\Models\Language;
use App\Models\Key;
use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;


class TranslationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
            'key' => 'required|string',
        ]);
 
        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }
 
        //Get user data
        $user = $this->verify_user();
 
        if($user['type'] == 'write'){
            $keyValue = $request->json('key');
            $languageValue = $request->json('iso');
            $translationValue = $request->json('name');
            $key = $this->verify_key($keyValue, $user['id']);
            $language = $this->verify_language($languageValue, $user['id']);
            //Validate key and language data
            if($key || $language){
                $translation = $this->verify_translation($user['id'], $key, $language);
                //Verify if translation exist
                if($translation){
                    //Request is valid, update translation
                    $translation = Translation::where('user_id', $user['id'])
                        ->where('id_lang', $language)
                        ->where('id_key', $key)
                        ->update(['name' => $translationValue]);
                    //Translation update, return success response
                    return response()->json([
                        'success' => true,
                        'message' => 'Translation update successfully',
                        'data' => $translation
                    ], Response::HTTP_OK); 
                }else{
                   //Request is valid, create new translation
                    $translation = Translation::create([
                        'user_id' => $user['id'],
                        'id_lang' => $language, 
                        'id_key' => $key,
                        'name' => $request->json('name'),
                    ]);
                    //Translation created, return success response
                    return response()->json([
                        'success' => true,
                        'message' => 'Translation created successfully',
                        'data' => $translation
                    ], Response::HTTP_OK); 
                }
            }else{
                return response()->json([
                    'success' => false,
                    'message' => 'Please check if the key or language sent exists'
                ], Response::HTTP_OK);
            }
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
     * @param  \App\Models\Translation  $translation
     * @return \Illuminate\Http\Response
     */
    public function show(Translation $translation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Translation  $translation
     * @return \Illuminate\Http\Response
     */
    public function edit(Translation $translation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Translation  $translation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Translation $translation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Translation  $translation
     * @return \Illuminate\Http\Response
     */
    public function destroy(Translation $translation)
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

    /**
     * Verify if key exist
     *
     * @param  \App\Models\Language  $language
     * @return \Illuminate\Http\Response
     */
    public function verify_key($value, $user)
    {
        //key verification
        $data = Key::where('user_id', $user)->where('name', $value)->first();
        if(!$data){
            return false;
        }else{
            return $data->id;
        }
    }

    /**
     * Verify if language exist
     *
     * @param  \App\Models\Language  $language
     * @return \Illuminate\Http\Response
     */
    public function verify_language($value, $user)
    {
        //language verification
        $data = Language::where('user_id', $user)->where('iso', $value)->first();

        if(!$data){
            return false;
        }else{
            return $data->id;
        }
    }

    /**
     * Verify translation exist
     *
     * @param  \App\Models\Language  $language
     * @return \Illuminate\Http\Response
     */
    public function verify_translation($user, $key, $language)
    {
        //Translation verification
        $data = Translation::where('user_id', $user)->where('id_lang', $language)->where('id_key', $key)->first();

        if(!$data){
            return false;
        }else{
            return true;
        }
    }
}
