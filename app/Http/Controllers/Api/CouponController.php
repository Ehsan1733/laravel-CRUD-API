<?php

namespace App\Http\Controllers\Api;

use App\Brand;
use App\Coupon;
use App\Http\Controllers\Controller;
use App\Http\Resources\CouponResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CouponController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //  Get Coupon and brand
        $coupons = Coupon::select('id','name','link','amount','brand_id','type')
            ->with('brand')
            ->get();

        //check value and send to resource class
        list($successStatus, $messageReply, $responseStatus) = $this->checkResponse($coupons,'received');

        return CouponResource::collection($coupons)->additional([
            'success' => '' . $successStatus . '',
            'message' => '' . $messageReply . '',
        ])->response()->setStatusCode($responseStatus);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate input
        $validator = $this->checkValidator($request->all());
        if ($validator->fails()) {
            return response($validator->errors(), 400);
        }

        // Brand ID validation
        $findBrand = Brand::find($request->get('brand_id'));
        if (is_null($findBrand)) {
            return response('Not Found Brand Id', 400);
        }

        // Create Coupon
       $crate  = Coupon::create([
           'user_id' => $request->get('user_id'),
            'name' => $request->get('name'),
            'link' => $request->get('link'),
            'amount' => $request->get('amount'),
            'brand_id' => $findBrand->id,
            'code' => Str::random(10),
            'type' => $request->get('type')
        ]);

        //check value and send to resource class
        list($successStatus, $messageReply, $responseStatus) = $this->checkResponse($crate,'save');

        return (new CouponResource($crate))->additional([
            'success' => '' . $successStatus . '',
            'message' => '' . $messageReply . '',
        ])->response()->setStatusCode($responseStatus);


    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // find id Coupon
        $findId = Coupon::with('brand')->find($id);

        if (!$findId){
            return response('Not Found',404);
        }

        //check value and send to resource class
        list($successStatus, $messageReply, $responseStatus) = $this->checkResponse($findId,'show');

        return (new CouponResource($findId))->additional([
            'success' => '' . $successStatus . '',
            'message' => '' . $messageReply . '',
        ])->response()->setStatusCode($responseStatus);
    }

     /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Validate input
        $validator = $this->checkValidator($request->all());
        if ($validator->fails()) {
            return response($validator->errors(), 400);
        }

        // Brand ID validation
        $findBrand = Brand::find($request->get('brand_id'));
        if (is_null($findBrand)) {
            return response('Not Found Brand Id', 400);
        }

        // find id Coupon
        $findId = Coupon::find($id);
        if (!$findId){
            return response('Not Found',404);
        }

       // If the coupon ID is found, we will update it
      $findId->update([
            'user_id' => $request->get('user_id'),
            'name' => $request->get('name'),
            'link' => $request->get('link'),
            'amount' => $request->get('amount'),
            'brand_id' => $findBrand->id,
            'type' => $request->get('type'),
        ]);

        //check value and send to resource class
        list($successStatus, $messageReply, $responseStatus) = $this->checkResponse($findId,'update');

        return (new CouponResource($findId))->additional([
            'success' => '' . $successStatus . '',
            'message' => '' . $messageReply . '',
        ])->response()->setStatusCode($responseStatus);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $findId = Coupon::find($id);

        // If no coupon ID is found
        if (!$findId){
            return response('Not Found',404);
        }

        // If the coupon ID is found, we will remove it
        $findId->delete();

        // return response for user
        return response('It was successfully removed',200);
    }

    /**
     * @param string $code
     */
    public function checkCode($code){
        $findCode = Coupon::where('code',$code)->first();

        if (is_null($findCode)){
            return response('The entered code is invalid',400);
        }

        if ($findCode->type == 'public' && $findCode->status == 'true'){
            return response('Your coupon has been activated',200);
        }
        elseif ($findCode->user_id == auth()->id() && $findCode->status == 'true'){
            Coupon::find($findCode->id)->update(['satus' => 'false']);
            return response('Your coupon has been activated',200);
        }
        else{
            return response('The entered code is invalid',400);
        }
    }

    /**
     * @param $request
     * @param  string $message
     * @return array
     */
    private function checkResponse($request, $message)
    {
        $successStatus = is_null($request) ? 'false' : 'true';

        $messageReply = is_null($request)
            ? 'Unsuccessful '.$message.' '
            : 'Successful '.$message.'';

        $responseStatus = is_null($request) ? 400 : 200;

        return [$successStatus,$messageReply, $responseStatus];
    }

    /**
     * @param $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    private function checkValidator($request)
    {
        $rules = [
            'name' => 'required',
            'link' => 'required',
            'amount' => 'required',
            'brand_id' => 'required',
            'type' => 'required',
        ];
        return Validator::make($request, $rules);
    }
}
