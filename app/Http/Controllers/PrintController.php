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
        /*
         * Contract code
            contract Fabchain {
                string public _name;
                string public _gcodehash;
                address public _maker;
                address public _designer;

                event Transfarmaker(address indexed from, address indexed to);
                event Transfardesigner(address indexed from, address indexed to);

                function Fabchain(string name, string gcodehash, adderss designer){
                    _name = name;
                    _gcodehash = gcodehash;
                    _maker = msg.sender;
                    _maker = designer;
                }
                function transfarmaker(address to){
                    if(msg.sender == _maker){
                        throw;
                    }
                    _maker = to;
                    Transfarmaker(msg.sender,to);
                }
                function transfardesigner(address to){
                    if(msg.sender == _designer){
                        throw;
                    }
                    _designer = to;
                    Transfardesigner(msg.sender,to);
                }
                function getname() constant returns (string name){
                    return _name;
                }
                function getmaker() constant returns (address maker){
                    return _maker;
                }
                function getdesigner() constant returns (address designer){
                    return _designer;
                }
                function getgcodehash() constant returns (string gcodehash){
                    return _gcodehash;
                }
            }
         */
        $contract = "";
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
