<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\IAirtimeRepository;
use App\Http\Requests\BapAirtimeRequest;
use App\Http\Requests\ShagoAirtimeRequest;

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
     *     path="/vend/recharge/{type}",
     *     summary="Buy airtime using BAP or Shago",
     *     tags={"Airtime"},
     *     description="Endpoint for purchasing airtime via BAP or Shago services. The 'type' parameter determines which provider to use.",
     * 
     *     @OA\Parameter(
     *         name="type",
     *         in="path",
     *         required=true,
     *         description="The provider type: 'bap' or 'shago'",
     *         @OA\Schema(
     *             type="string",
     *             enum={"bap", "shago"},
     *             example="bap"
     *         )
     *     ),
     * 
     *     @OA\Parameter(
     *         name="phone_number",
     *         in="query",
     *         required=true,
     *         description="The phone number to recharge (format: 07012345678). For BAP only.",
     *         @OA\Schema(
     *             type="string",
     *             example="07065788819"
     *         )
     *     ),
     * 
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
     * 
     *     @OA\Parameter(
     *         name="service_type",
     *         in="query",
     *         required=false,
     *         description="The network provider for BAP (e.g., MTN, GLO)",
     *         @OA\Schema(
     *             type="string",
     *             enum={"MTN", "GLO"},
     *             example="MTN"
     *         )
     *     ),
     * 
     *     @OA\Parameter(
     *         name="phone",
     *         in="query",
     *         required=false,
     *         description="The phone number to recharge (for Shago only)",
     *         @OA\Schema(
     *             type="string",
     *             example="07065788819"
     *         )
     *     ),
     * 
     *     @OA\Parameter(
     *         name="network",
     *         in="query",
     *         required=false,
     *         description="The network provider for Shago (e.g., MTN, GLO)",
     *         @OA\Schema(
     *             type="string",
     *             enum={"MTN", "GLO"},
     *             example="MTN"
     *         )
     *     ),
     * 
     *     @OA\Parameter(
     *         name="vend_type",
     *         in="query",
     *         required=false,
     *         description="Vend type for Shago service",
     *         @OA\Schema(
     *             type="string",
     *             example="VTU"
     *         )
     *     ),
     * 
     *     @OA\Parameter(
     *         name="serviceCode",
     *         in="query",
     *         required=false,
     *         description="Service code for Shago service",
     *         @OA\Schema(
     *             type="string",
     *             example="QAB"
     *         )
     *     ),
     * 
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     ),
     * 
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request or missing required parameters"
     *     )
     * )
     */
    public function recharge(Request $request, $type)
    {
        $validated = [];
        $response = null;
        $message = '';
        $success = false;

        if( $type == "bap" ){
            // switch for first provider
            $validated = $request->validate(
                (new BapAirtimeRequest)->rules()
            );

            $response = $this->airtimeRepository->vend($validated, 'bap');
            $message = $response['message'];
            $success = $response['status'];

        } else if($type = "shago") {
            // switch for second provider
            $validated = $request->validate(
                (new ShagoAirtimeRequest)->rules()
            );

            $response = $this->airtimeRepository->vend($validated, 'shago');
            $message = $response['message'];
            $success = $response['status'];
        }

        return response()->json(
            [   
                'success' => $success,
                'data' => $response,
                'message' => $message
            ],200);
    }
}
