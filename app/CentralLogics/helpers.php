<?php
use App\Models\{
    User,
    BusinessSetting,
    ProductReview,
    CategoryDiscount
};
use Illuminate\Support\Facades\File;
use App\Models\Currency;

if(! function_exists('Helpers_get_business_settings')) {
    function Helpers_get_business_settings($name)
    {
        if(BusinessSetting::where(['key' => $name])->exists()){
            $data = BusinessSetting::where(['key' => $name])->first();
            $config = json_decode($data['value'], true);
            if (is_null($config)) {
                $config = $data->value;
            }
            
        }else{
            BusinessSetting::updateOrInsert(['key' => $name], [
                'value' => ''
            ]);
            $config = null;
        }
        return $config;
    }
}

if(! function_exists('Helpers_set_business_settings')) {
    function Helpers_set_business_settings($name , $value)
    {
        try {
            if(BusinessSetting::where(['key' => $name])->exists()){
                $data = BusinessSetting::where(['key' => $name])->first();
                $data->value = $value;
                $data->save();
            }else{
                $data = new BusinessSetting();
                $data->key = $name;
                $data->value = $value;
                $data->save();
            }

            $message = ['message' => true];
            return $message;

        } catch (\Throwable $th) {
            $message = ['message' => false];
            return $message;
        }
    }
}

if(! function_exists('Helpers_module_permission_check')) {
    function Helpers_module_permission_check($mod_name)
    {
        $permission = auth('admins')->user()->role->module_access;

        if (isset($permission) && in_array($mod_name, (array)json_decode($permission)) == true) {
            return true;
        }

        if (auth('admins')->user()->role_id == 1) {
            return true;
        }
        return false;
    }
}

if(! function_exists('translate')) {
    function translate($value)
    {
        $val = ucwords($value);
        return str_replace('_',' ',$val);
    }
}

if(! function_exists('Helpers_error_processor')) {
    function Helpers_error_processor($validator)
    {
        $err_keeper = [];
        foreach ($validator->errors()->getMessages() as $index => $error) {
            $err_keeper[] = ['code' => $index, 'message' => $error[0]];
        }
        return $err_keeper;
    }
}

if(! function_exists('Helpers_generate_referer_code')) {
    function Helpers_generate_referer_code()
    {
        $notunique = true;

        while ($notunique) {
            $code = 'ser' . rand(1000000,9999999);
            if(!User::where('referral_code',$code)->exists()){
                $notunique = false;
                return $code;
            }
        }
    }
}

if(! function_exists('Helpers_upload')) {
    function Helpers_upload(string $dir, string $format, $image = null)
    {
        if ($image != null) {
            $imageName = \Carbon\Carbon::now()->toDateString() . "-" . uniqid() . "." . $format;
            $image->move(public_path($dir), $imageName);
            $imagePath = $dir.$imageName;
        } else {
            $imagePath = 'def.png';
        }

        return $imagePath;
    }
}

if(! function_exists('Helpers_update')) {
    function Helpers_update(string $dir, $old_image, string $format, $image = null)
    {
        if (File::exists($dir.'/'.$old_image)) {
            File::delete($dir.'/'.$old_image);
        }
        $imageName = Helpers_upload($dir, $format, $image);
        return $imageName;
    }
}

if(! function_exists('Helpers_getPagination')) {
    function Helpers_getPagination()
    {
        $pagination_limit = Helpers_get_business_settings('pagination_limit');
        $paginate = $pagination_limit->value ?? 10;
        return $paginate; 
    }
}

if(! function_exists('Helpers_combinations')) {
    function Helpers_combinations($arrays)
    {
        $result = [[]];
        foreach ($arrays as $property => $property_values) {
            $tmp = [];
            foreach ($result as $result_item) {
                foreach ($property_values as $property_value) {
                    $tmp[] = array_merge($result_item, [$property => $property_value]);
                }
            }
            $result = $tmp;
        }
        return $result;
    }
}

if(! function_exists('Helpers_set_symbol')) {
    function Helpers_set_symbol($number)
    {
        return '₹ '.$number;
    }
}

if(! function_exists('Helpers_rating_count')) {
    function Helpers_rating_count($product_id, $rating)
    {
        return ProductReview::where(['product_id' => $product_id, 'rating' => $rating])->count();
    }
}

if(! function_exists('Helpers_set_price')) {
    function Helpers_set_price($amount)
    {
        $decimal_point_settings = Helpers_get_business_settings('decimal_point_settings');
        $amount = number_format($amount, gettype($decimal_point_settings) == "integer" ? $decimal_point_settings: 0, '.', '');

        return $amount;
    }
}

if(! function_exists('Helpers_currency_symbol')) {
    function Helpers_currency_symbol()
    {
        $currency_symbol = Helpers_get_business_settings("currency_code");
        return $currency_symbol ?? "₹";
    }

}

if(! function_exists('Helpers_send_push_notif_to_topic')) {
    function Helpers_send_push_notif_to_topic($data)
    {
        /*https://fcm.googleapis.com/v1/projects/myproject-b5ae1/messages:send*/
        $key = BusinessSetting::where(['key' => 'push_notification_key'])->first()->value;
        /*$topic = BusinessSetting::where(['key' => 'fcm_topic'])->first()->value;*/
        /*$project_id = BusinessSetting::where(['key' => 'fcm_project_id'])->first()->value;*/
        $url = "https://fcm.googleapis.com/fcm/send";
        $header = array("authorization: key=" . $key . "",
            "content-type: application/json"
        );


        $image = asset('storage/app/public/notification') . '/' . $data['image'];
        $postdata = '{
            "to" : "/topics/grofresh",
            "mutable-content": "true",
            "data" : {
                "title" :"' . $data['title'] . '",
                "body" : "' . $data['description'] . '",
                "image" : "' . $image . '",
                "order_id":"' . $data['order_id'] . '",
                "type":"' . $data['type'] . '",
                "is_read": 0
            },
            "notification" : {
                "title" :"' . $data['title'] . '",
                "body" : "' . $data['description'] . '",
                "image" : "' . $image . '",
                "order_id":"' . $data['order_id'] . '",
                "title_loc_key":"' . $data['order_id'] . '",
                "type":"' . $data['type'] . '",
                "is_read": 0,
                "icon" : "new",
                "sound" : "default"
            }
        }';

        $ch = curl_init();
        $timeout = 120;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        // Get URL content
        $result = curl_exec($ch);
        // close handle to release resources
        curl_close($ch);

        return $result;
    }
}

if(! function_exists('Helpers_discount_calculate')) {
    function Helpers_discount_calculate($product, $price)
    {
        if ($product->discount_type == 'percent') {
            $price_discount = ($price / 100) * $product->discount;
        } else {
            $price_discount = $product->discount;
        }
        return Helpers_set_price($price_discount);
    }
}

if(! function_exists('Helpers_category_discount_calculate')) {
    function Helpers_category_discount_calculate($product, $price)
    {
        $category_discount = CategoryDiscount::active()->where(['category_id' => $product->category_id])->first();
        if ($category_discount) {
            if ($category_discount->discount_type == 'percent') {
                $price_discount = ($price / 100) * $category_discount->discount_amount;
                if ($category_discount->maximum_amount < $price_discount) {
                    $price_discount = $category_discount->maximum_amount;
                }
            } else {
                $price_discount = $category_discount->discount_amount;
            }
        } else {
            $price_discount = 0;
        }
        return Helpers_set_price($price_discount);
    }
}

if(! function_exists('Helpers_tax_calculate')) {
    function Helpers_tax_calculate($product, $price)
    {
        if ($product->tax_type == 'percent') {
            $category_id = null;
            foreach (json_decode($product->category_ids, true) as $cat) {
                if ($cat->position == 1) {
                    $category_id = ($cat->id);
                }
            }

            $category_discount = Helpers_category_discount_calculate($category_id, $price);
            $product_discount = Helpers_discount_calculate($product, $price);
            if ($category_discount >= $price) {
                $discount = $product_discount;
                $discount_type = 'discount_on_product';
            } else {
                $discount = max($category_discount, $product_discount);
                $discount_type = $product_discount > $category_discount ? 'discount_on_product' : 'discount_on_category';
            }
            
            $item_price = $price; // The total price including tax
            $tax_rate = $product->tax; // Tax rate percentage
            
            $discounted_price = $price - $discount;
            
            // Calculate the price without tax
            $price_without_tax = $discounted_price / (1 + ($tax_rate / 100));

            // Calculate the tax amount
            $price_tax = $discounted_price - $price_without_tax;
            // $price_tax = ($price / 100) * $product['tax'];
        } else {
            $price_tax = $product->tax;
        }
        return $price_tax;
    }
}