<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;
use View;
use File;
use ZipArchive;
use DB;

class ExportController extends Controller
{
    /**
     * Export the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $year = date("Y");
        $month = date("m");
        $day = date("d");
        $time = time();
        $path_storage = public_path().'/upload/'.$year.'/'.$month.'/'.$day.'/'.$time;

        $language = DB::table('languages')->get();
        foreach($language as $lang){
            $selectData = DB::table('translations')->select('translations.id', 'keys.name as key', 'translations.name as translation')->leftjoin("keys","translations.id_key","=","keys.id")->where('id_lang', $lang->id)->get();
            $fileData = [];
            foreach($selectData as $row){
                $fileData[$row->key] = $row->translation;
            }

            $data = json_encode($fileData, JSON_PRETTY_PRINT);
            $jsongFile = $lang->iso.'.json';
            File::makeDirectory($path_storage, $mode = 0777, true, true);

            File::put($path_storage.'/'.$jsongFile, $data);
        }
        $zip = new \ZipArchive();
        $fileName = $time.'.zip';
        if ($zip->open(public_path($fileName), \ZipArchive::CREATE)== TRUE)
        {
            $files = File::files($path_storage);
            foreach ($files as $key => $value){
                $relativeName = basename($value);
                $zip->addFile($value, $relativeName);
            }
            $zip->close();
        }
        return response()->download(public_path($fileName));
    }
}
