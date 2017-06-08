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

        foreach(str_split($data["name"]) as $char){
            $nameascii .= dechex(ord($char));
        }
        $namelen = dechex(strlen($data["name"]));
        $namelen = str_pad($namelen, 64, 0, STR_PAD_LEFT);
        $nameascii = str_pad($nameascii, 64, 0, STR_PAD_RIGHT);

        $hashascii = '';
        foreach(str_split($data["hash"]) as $char){
            $nameascii .= dechex(ord($char));
        }
        $hashlen = dechex(strlen($data["hash"]));
        $hashlen = str_pad($hashlen, 64, 0, STR_PAD_LEFT);
        $hashascii = str_pad($hashascii, 64, 0, STR_PAD_RIGHT);

        $dsgnlen = dechex(strlen($data["designer"]));
        $lendsgn = str_pad($dsgnlen, 64, 0, STR_PAD_LEFT);

        //point of data parametara
        $point_of_name = str_pad("1", 64, 0, STR_PAD_LEFT);
        $point_of_gcodehash = str_pad("2", 64, 0, STR_PAD_LEFT);
        $point_of_designer = str_pad("3", 64, 0, STR_PAD_LEFT);

        $data = $point_of_name.$point_of_gcodehash.$point_of_designer.$namelen.$nameascii.$hashlen.$hashascii.$lendsgn.$data["designer"];
        $postdata = $conntact.$data;
        //printing
        return Response::json($data);
    }
}
