<?php

namespace App\Http\Controllers\Api\user\product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Models\CustomerAddresses;
use Illuminate\Support\Facades\Auth;


class AddressController extends Controller
{
    public function __construct(

        private CustomerAddresses $address,
        
    ){}

    /**
     * 
     * @return JsonResponse
     * 
     */
    public function addresslist() : JsonResponse
    {   
        $user_id  = Auth::user()->id;
        $address_data = $this->address->where('user_id',$user_id)->get();

        return response()->json([
            'status' => true,
            'message' => 'address list',
            'data' => $address_data
        ],200);               
    }

    /**
     * 
     * @return JsonResponse
     * 
     */
    public function addressStore(Request $request) : JsonResponse
    {
    
        $validator = Validator::make($request->all(), [
            'address_name' => 'required',
            'person_name' => 'required',
            'person_number' => 'required',
            'house_road' => 'required',
            'address1' => 'required',
            'city' => 'required',
            'state' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => Helpers_error_processor($validator)
            ], 406);
        } 

        $user_id  = Auth::user()->id;

        $Custmor_address = new CustomerAddresses();
        $Custmor_address->user_id = $user_id;
        $Custmor_address->address_type = $request->address_name;
        $Custmor_address->contact_person_number = $request->person_number;
        $Custmor_address->contact_person_name = $request->person_name;
        $Custmor_address->landmark = $request->landmark;
        $Custmor_address->house_road = $request->house_road;
        $Custmor_address->address1 = $request->address1;
        $Custmor_address->address2 = $request->address2;
        $Custmor_address->city = $request->city;
        $Custmor_address->state = $request->state;
        $Custmor_address->latitude = $request->latitude;
        $Custmor_address->longitude = $request->longitude;
        $Custmor_address->save();   
        
        
        $address_data = $this->address->where('user_id',$user_id)->get();
        
        return response()->json([
            'status' => true,
            'message' => 'Insert address sucessfully',
            'data' => $address_data
        ],201); 
    }             

    /**
     * 
     * @return JsonResponse
     * 
     */
    public function addressUpdate(Request $request,$id) : JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'address_name' => 'required',
            'person_name' => 'required',
            'person_number' => 'required',
            'house_road' => 'required',
            'address1' => 'required',
            'city' => 'required',
            'state' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => Helpers_error_processor($validator)
            ], 406);
        }       

        $user_id = Auth::user()->id;
    
        $Custmor_address = $this->address->find($id);
        $Custmor_address->user_id = $user_id;
        $Custmor_address->address_type = $request->address_name;
        $Custmor_address->contact_person_number = $request->person_number;
        $Custmor_address->contact_person_name = $request->person_name;
        $Custmor_address->landmark = $request->landmark;
        $Custmor_address->house_road = $request->house_road;
        $Custmor_address->address1 = $request->address1;
        $Custmor_address->address2 = $request->address2;
        $Custmor_address->city = $request->city;
        $Custmor_address->state = $request->state;
        $Custmor_address->latitude = $request->latitude;
        $Custmor_address->longitude = $request->longitude;
        $Custmor_address->save();   
            
        
        $address_data = $this->address->where('user_id',$user_id)->get();

        return response()->json([
            'status' => true,
            'message' => 'Address updated sucessfully',
            'data' => $address_data
        ],202);               
    }

    /**
     * 
     * @return JsonResponse
     * 
     */
    public function addressDelete(Request $request,$id) : JsonResponse
    {
        if($this->address->where('id',$id)->exists())
        {
            $Custmor_address= $this->address->find($id);  
            $Custmor_address->delete();        
            
            $user_id  = Auth::user()->id;
            $address_data = $this->address->where('user_id',$user_id)->get();
    
            return response()->json([
                'status' => true,
                'message' => 'Address Deleted sucessfully',
                'data' => $address_data
            ],202);       
        }
        return response()->json([
            'status' => false,
            'message' => 'Data Not exists',
            'data' => []
        ],404);     
    }

}
