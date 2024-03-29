<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;

use Ixudra\Curl\Facades\Curl;

class PrintController extends Controller
{
    public function getcnt(Request $request){
        $txadd = $request->tx;
        $postdata = [
            'jsonrpc' => '2.0',
            'method' => 'eth_getTransactionReceipt',
            'params' => [$txadd],
            'id' => 1
            ];
        $ethres = Curl::to('http://localhost:8545')
                        ->withData($postdata)
                        ->asJsonRequest(true)
                        ->post();
        $res = json_decode($ethres,true);
        if($res["result"] == null){
            $return = [
                "result"=> false,
                "data" => $res["result"]
                ];
        }else{
            //Get info from contract
            $cntadd = $res["result"]["contractAddress"];
            //Get contract info
            //getname()
            $data = [
                "jsonrpc" => "2.0",
                "method" => "eth_call",
                "params" => [[
                "to" => $cntadd,
                "data" => "0xc6ea59b9"
                ],"latest"],
                "id" => 3
                ];
            $res = Curl::to('http://localhost:8545')
                        ->withData($data)
                        ->asJsonRequest(true)
                        ->post();
            $res = json_decode($res,true);
            $namedata = str_split(ltrim(ltrim($res["result"],"0"),"x"),64);
            $name = hex2bin($namedata[2]);

            //getmaker()
            $data = [
                "jsonrpc" => "2.0",
                "method" => "eth_call",
                "params" => [[
                "to" => $cntadd,
                "data" => "0x10eeba10"
                ],"latest"],
                "id" => 3
                ];
            $res = Curl::to('http://localhost:8545')
                        ->withData($data)
                        ->asJsonRequest(true)
                        ->post();
            $res = json_decode($res,true);
            $maker = "0x".ltrim(ltrim(ltrim($res["result"],"0"),"x"),"0");

            //getdsigner()
            $data = [
                "jsonrpc" => "2.0",
                "method" => "eth_call",
                "params" => [[
                "to" => $cntadd,
                "data" => "0x5253fb83"
                ],"latest"],
                "id" => 3
                ];
            $res = Curl::to('http://localhost:8545')
                        ->withData($data)
                        ->asJsonRequest(true)
                        ->post();
            $res = json_decode($res,true);
            $desgin = "0x".ltrim(ltrim(ltrim($res["result"],"0"),"x"),"0");

            //getgcodehash()
            $data = [
                "jsonrpc" => "2.0",
                "method" => "eth_call",
                "params" => [[
                "to" => $cntadd,
                "data" => "0xc6d078ce"
                ],"latest"],
                "id" => 3
                ];
            $res = Curl::to('http://localhost:8545')
                        ->withData($data)
                        ->asJsonRequest(true)
                        ->post();
            $res = json_decode($res,true);
            $hashdata = str_split(ltrim(ltrim($res["result"],"0"),"x"),64);
            $hashascii = $hashdata[2].$hashdata[3];
            $hash = hex2bin($hashascii);

            $return = [
                "result"=> true,
                "data" => [
                "cntadd" => $cntadd,
                "makeradd" => $maker,
                "designadd" => $desgin,
                "objname" => $name,
                "ghash" => $hash
                ]];
        }
        return Response::json($return);
    }

    public function index(Request $request){
        $gcode = $request->file('g-code');
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
        $contract = "0x6060604052341561000c57fe5b6040516108d33803806108d383398101604090815281516020830151918301519083019291909101905b8251610049906000906020860190610097565b50815161005d906001906020850190610097565b5060028054600160a060020a03338116600160a060020a03199283161790925560038054928416929091169190911790555b505050610137565b828054600181600116156101000203166002900490600052602060002090601f016020900481019282601f106100d857805160ff1916838001178555610105565b82800160010185558215610105579182015b828111156101055782518255916020019190600101906100ea565b5b50610112929150610116565b5090565b61013491905b80821115610112576000815560010161011c565b5090565b90565b61078d806101466000396000f300606060405236156100a15763ffffffff7c010000000000000000000000000000000000000000000000000000000060003504166310eeba1081146100a357806338f43a72146100cf5780635253fb83146100ed578063998a904614610119578063af8055d114610145578063c6d078ce14610163578063c6ea59b9146101f3578063d28d885214610283578063fc2e9e7b14610313578063ff1a92a51461033f575bfe5b34156100ab57fe5b6100b36103cf565b60408051600160a060020a039092168252519081900360200190f35b34156100d757fe5b6100eb600160a060020a03600435166103df565b005b34156100f557fe5b6100b361045a565b60408051600160a060020a039092168252519081900360200190f35b341561012157fe5b6100b361046a565b60408051600160a060020a039092168252519081900360200190f35b341561014d57fe5b6100eb600160a060020a0360043516610479565b005b341561016b57fe5b6101736104f4565b6040805160208082528351818301528351919283929083019185019080838382156101b9575b8051825260208311156101b957601f199092019160209182019101610199565b505050905090810190601f1680156101e55780820380516001836020036101000a031916815260200191505b509250505060405180910390f35b34156101fb57fe5b61017361058c565b6040805160208082528351818301528351919283929083019185019080838382156101b9575b8051825260208311156101b957601f199092019160209182019101610199565b505050905090810190601f1680156101e55780820380516001836020036101000a031916815260200191505b509250505060405180910390f35b341561028b57fe5b610173610625565b6040805160208082528351818301528351919283929083019185019080838382156101b9575b8051825260208311156101b957601f199092019160209182019101610199565b505050905090810190601f1680156101e55780820380516001836020036101000a031916815260200191505b509250505060405180910390f35b341561031b57fe5b6100b36106b3565b60408051600160a060020a039092168252519081900360200190f35b341561034757fe5b6101736106c2565b6040805160208082528351818301528351919283929083019185019080838382156101b9575b8051825260208311156101b957601f199092019160209182019101610199565b505050905090810190601f1680156101e55780820380516001836020036101000a031916815260200191505b509250505060405180910390f35b600254600160a060020a03165b90565b60035433600160a060020a03908116911614156103fc5760006000fd5b6003805473ffffffffffffffffffffffffffffffffffffffff1916600160a060020a0383811691821790925560405190913316907f31abfb3a12ecbcb3ccee597166d5c46faeaae18c0c04dc417f01a55d03ab1d3a90600090a35b50565b600354600160a060020a03165b90565b600354600160a060020a031681565b60025433600160a060020a03908116911614156104965760006000fd5b6002805473ffffffffffffffffffffffffffffffffffffffff1916600160a060020a0383811691821790925560405190913316907f7cb659da62c6f14d5cc2674610c5823cc49da215812d88c329dbe7be7b6a04fb90600090a35b50565b6104fc61074f565b60018054604080516020600284861615610100026000190190941693909304601f810184900484028201840190925281815292918301828280156105815780601f1061055657610100808354040283529160200191610581565b820191906000526020600020905b81548152906001019060200180831161056457829003601f168201915b505050505090505b90565b61059461074f565b6000805460408051602060026001851615610100026000190190941693909304601f810184900484028201840190925281815292918301828280156105815780601f1061055657610100808354040283529160200191610581565b820191906000526020600020905b81548152906001019060200180831161056457829003601f168201915b505050505090505b90565b6000805460408051602060026001851615610100026000190190941693909304601f810184900484028201840190925281815292918301828280156106ab5780601f10610680576101008083540402835291602001916106ab565b820191906000526020600020905b81548152906001019060200180831161068e57829003601f168201915b505050505081565b600254600160a060020a031681565b60018054604080516020600284861615610100026000190190941693909304601f810184900484028201840190925281815292918301828280156106ab5780601f10610680576101008083540402835291602001916106ab565b820191906000526020600020905b81548152906001019060200180831161068e57829003601f168201915b505050505081565b604080516020810190915260008152905600a165627a7a723058207b0159652f870c9e550ffbaf34ae211ae4fe2495ca703ea32c71cf97563eee8f0029";
        $nameascii = '';
        foreach(str_split($data["name"]) as $char){
            $nameascii .= dechex(ord($char));
        }
        $namelen = dechex(strlen($data["name"]));
        $namelen = str_pad($namelen, 64, 0, STR_PAD_LEFT);
        $nameascii = str_pad($nameascii, 64, 0, STR_PAD_RIGHT);

        $hashascii = '';
        foreach(str_split($data["hash"]) as $char){
            $hashascii .= dechex(ord($char));
        }
        $hashlen = dechex(strlen($data["hash"]));
        $hashlen = str_pad($hashlen, 64, 0, STR_PAD_LEFT);
        $hashascii = str_pad($hashascii, 64, 0, STR_PAD_RIGHT);

        $dsgnlen = dechex(strlen($data["designer"]));
        $lendsgn = str_pad($dsgnlen, 64, 0, STR_PAD_LEFT);

        //point of data parametara
        $point_of_name = str_pad("60", 64, 0, STR_PAD_LEFT);
        $point_of_gcodehash = str_pad("a0", 64, 0, STR_PAD_LEFT);
        $designer = str_pad(mb_strtolower(ltrim($data["designer"],"0x")), 64, 0, STR_PAD_LEFT);
        $cntdata = $contract.$point_of_name.$point_of_gcodehash.$designer.$namelen.$nameascii.$hashlen.$hashascii;

        $postdata = [
            'jsonrpc' => '2.0',
            'method' => 'eth_sendTransaction',
            'params' => [[
                'from' => $data['maker'],
                'gas' => '0x'.hexdec(50000),
                'data' => $cntdata
            ]],
            'id' => 1
            ];
        $ethres = Curl::to('http://localhost:8545')
                        ->withData($postdata)
                        ->asJsonRequest(true)
                        ->post();
        $res["geth"] = json_decode($ethres,true);
        //DOTO error handling for send TX
        //printing
        $filename = $gcode->storeAs('public',$gcode->getClientOriginalName());
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL         => 'http://chikersp.sfc.wide.ad.jp:5000/api/files/local?apikey=54A4E3EFB7F34008A205C9E46EF1A25C',
            CURLOPT_POST        => true,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_POSTFIELDS  => [
                'file' => new \CURLFile(storage_path()."/app/".$filename,"application/octet-stream",$gcode->getClientOriginalName()),
                'select' => true,
                'print' => false //not printing for test
            ],
            ]);
        $octres = curl_exec($ch);
        $res["oct"] = json_decode($octres,true);
        curl_close($ch);
        return Response::json($res);
    }
}
