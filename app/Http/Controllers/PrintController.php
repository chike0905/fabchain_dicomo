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

        //make Transaction data
        $nameascii = '';
        $i = 0;
        while (($char = substr($data["name"], $i++, 1)) !=='') {
            $nameascii .= sprintf('%x', ord($char));
        }
        $namelen = dechex(strlen($data["name"]));
        $namelen = str_pad($namelen, 64, 0, STR_PAD_LEFT);
        $nameascii = str_pad($nameascii, 64, 0, STR_PAD_RIGHT);
        $hashascii = '';
        $i = 0;
        while (($char = substr($data["hash"], $i++, 1)) !== '') {
            $hashascii .= sprintf('%x', ord($char));
        }

        //printing
        return Response::json($data);
    }
}
