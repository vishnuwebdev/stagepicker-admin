<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Saved shipping addresses for the Marketplace checkout screen
 * (platform/lib/marketplace/screens/mp_checkout.dart) — lets a user pick a
 * previously-used address instead of retyping it every time, or add a new
 * one. Follows the same conventions as OrderController in this folder
 * (user_id-scoped, {"status","message","data"} envelope).
 */
class AddressController extends ApiController
{
    public function get_addresses(Request $request)
    {
        $rules = ['user_id' => 'required'];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $response['status'] = "false";
            $response['message'] = $this->validationHandle($validator->messages());
            return response()->json($response);
        }

        $addresses = Address::where('user_id', $request->user_id)
            ->orderBy('is_default', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        $response['status'] = "true";
        $response['message'] = "Addresses";
        $response['data'] = ['addresses' => $addresses];
        return response()->json($response);
    }

    public function add_address(Request $request)
    {
        $rules = [
            'user_id' => 'required',
            'full_name' => 'required',
            'phone' => 'required',
            'address_line1' => 'required',
            'city' => 'required',
            'state' => 'required',
            'postal_code' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $response['status'] = "false";
            $response['message'] = $this->validationHandle($validator->messages());
            return response()->json($response);
        }

        $isFirstAddress = Address::where('user_id', $request->user_id)->count() === 0;
        $makeDefault = $isFirstAddress || $request->boolean('is_default');

        if ($makeDefault) {
            Address::where('user_id', $request->user_id)->update(['is_default' => false]);
        }

        $address = new Address();
        $address->user_id = $request->user_id;
        $address->full_name = $request->full_name;
        $address->phone = $request->phone;
        $address->address_line1 = $request->address_line1;
        $address->address_line2 = $request->address_line2;
        $address->city = $request->city;
        $address->state = $request->state;
        $address->postal_code = $request->postal_code;
        $address->is_default = $makeDefault;
        $address->save();

        $response['status'] = "true";
        $response['message'] = "Address saved";
        $response['data'] = $address;
        return response()->json($response);
    }

    public function delete_address(Request $request)
    {
        $rules = [
            'user_id' => 'required',
            'address_id' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $response['status'] = "false";
            $response['message'] = $this->validationHandle($validator->messages());
            return response()->json($response);
        }

        $address = Address::where('user_id', $request->user_id)
            ->where('id', $request->address_id)
            ->first();

        if (!$address) {
            $response['status'] = "false";
            $response['message'] = "Address not found";
            return response()->json($response);
        }

        $address->delete();

        $response['status'] = "true";
        $response['message'] = "Address removed";
        return response()->json($response);
    }
}
