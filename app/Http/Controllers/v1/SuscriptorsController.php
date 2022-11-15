<?php

namespace App\Http\Controllers\v1;

use App\Http\Requests\NewSuscriptorRequest;
use App\Models\Suscription;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class SuscriptorsController extends Controller
{
    //section getSuscriptors
    public function getSuscriptors(){

        $suscriptors  = Suscription::all();

        if($suscriptors){
            foreach ($suscriptors as $suscript){
                unset($suscript->created_at);
                unset($suscript->updated_at);
            }
        }

        return response()->json(
            [
                'code' => 'ok',
                'message' => 'Suscriptors',
                'suscriptors' => $suscriptors
            ]
        );

    }

    //section newSuscriptor
    public function newSuscriptor(NewSuscriptorRequest $request){
        try{
            DB::beginTransaction();

            $suscriptors  = Suscription::whereEmail($request->suscriptorEmail)->first();


            if($suscriptors){

                return response()->json(
                    [
                        'code' => 'error',
                        'message' => 'There is a subscriber with that email'
                    ]
                );

            }

            $suscriptor = new Suscription();

            $suscriptor->name = $request->suscriptorName;
            $suscriptor->email = $request->suscriptorEmail;
            $suscriptor->category = $request->suscriptorCategory;

            $suscriptor->save();

            DB::commit();

            return response()->json(
                [
                    'code' => 'ok',
                    'message' => 'The subscription has been successful'
                ]
            );
        }
        catch(\Throwable $th){
            return response()->json(
                ['code' => 'error', 'message' => $th->getMessage()]
            );
        }

    }


    //section deleteSuscriptor
    public function deleteSuscriptor(Request $request){
        try {
            DB::beginTransaction();
            $result = Suscription::whereEmail($request->suscriptorEmail)->delete();

            DB::commit();

            if($result){
                return response()->json(
                    [
                        'code' => 'ok',
                        'message' => 'Suscriptor deleted successfully'
                    ]
                );
            }

            return response()->json(
                [
                    'code' => 'error',
                    'message' => 'Email not found'
                ]
            );

        }
        catch(\Throwable $th){
            return response()->json(
                ['code' => 'error', 'message' => $th->getMessage()]
            );
        }
    }
}
