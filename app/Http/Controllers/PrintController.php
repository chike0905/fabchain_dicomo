<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;

class PrintController extends Controller
{
    public function index(Request $request){
        $gcodepath = $request->file('g-code')->getRealPath();
        $data = [
            "maker" => $request->maker,
            "designer" => $request->designer,
            "name" => $request->objname,
            "hash" => $request->gcodehash,
            "ApiKey" => $request->apikey
        ];
        return Response::json($data);
    }
}
