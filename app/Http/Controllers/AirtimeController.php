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
     *     tags={"Airtime"},
     *     description="Endpoint for purchasing airtime, either from Bap or Shago Services",
        * @OA\Parameter(
        *         name="phone_number",
        *         in="query",
        *         required=true,
        *         description="The phone number to recharge (format: 07012345678)",
        *         @OA\Schema(
        *             type="string",
        *             example="07065788819"
        *         )
        *     ),
        *     @OA\Parameter(
        *         name="amount",
        *         in="query",
        *         required=true,
        *         description="The amount of airtime to purchase (in NGN)",
        *         @OA\Schema(
        *             type="number",
        *             format="float",
        *             example=100.00
        *         )
        *     ),
        *     @OA\Parameter(
        *         name="service_type",
        *         in="query",
        *         required=true,
        *         description="The network provider for the airtime (e.g., MTN, AIRTEL, GLO, 9MOBILE)",
        *         @OA\Schema(
        *             type="string",
        *             enum={"MTN", "GLO"},
        *             example="MTN"
        *         )
        *     ),
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */
    public function recharge(Request $request)
    {
        $validated = [];

        if( $request->has("service_type") ){
            // switch for first provider
            $validated = $request->validate(
                (new BapAirtimeRequest)->rules()
            );

            $response = $this->airtimeRepository->vend($validated);

        } else if($request->has('airtime')) {
            // switch for second provider
        }

        return response()->json(
            [   
                'success' => true,
                'data' => $response,
                'message' => 'Airtime was successfully purchased'
            ],200);
    }
}
