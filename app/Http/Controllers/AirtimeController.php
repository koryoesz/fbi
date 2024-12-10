<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\IAirtimeRepository;
use App\Http\Requests\BapAirtimeRequest;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Airtime Vending API",
 *      description="API for vending airtime using MTN and GLO through BAP or Shaggo partners"
 * )
 *
 * @OA\Tag(
 *     name="Vending",
 *     description="Endpoints for airtime vending"
 * )
 */

class AirtimeController extends Controller
{
    protected $airtimeRepository;

    public function __construct(IAirtimeRepository $airtimeRepository)
    {
        $this->airtimeRepository = $airtimeRepository;
    }

     /**
     * @OA\Get(
     *     path="/vend/recharge",
     *     summary="buy airtime",
     *     tags={"Users"},
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */
    public function recharge(Request $request)
    {
        $validated = [];

        if( $request->has("service_type") ){
            
            $validated = $request->validate(
                (new BapAirtimeRequest)->rules()
            );

        } else if($request->has('airtime')) {
            //
        }

        $response = $this->airtimeRepository->vend($validated);
        return $response;
    }
}
