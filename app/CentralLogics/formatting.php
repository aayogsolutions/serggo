<?php

if(! function_exists('product_data_formatting')) {
    function product_data_formatting($data, $multi_data = false)
    {
        $storage = [];
        if ($multi_data == true) {
            foreach ($data as $item) {
                $variations = [];
                $item->brand_name = json_decode($item->brand_name);
                $item->category_ids = json_decode($item->category_ids);
                $item->image = json_decode($item->image);
                $item->attributes = json_decode($item->attributes);
                $item->choice_options = json_decode($item->choice_options);
                $categories = gettype($item->category_ids) == 'array' ? $item->category_ids : json_decode($item->category_ids);
                // if(!is_null($categories) && count($categories) > 0) {
                //     $ids = [];
                //     foreach ($categories as $value) {
                //         if ($value->position == 1) {
                //             $ids[] = $value->id;
                //         }
                //     }
                //     $item['category_discount']= CategoryDiscount::active()->where('category_id', $ids)->first();
                // } else {
                //     $item['category_discount'] = [];
                // }

                foreach (json_decode($item->variations, true) as $var) {
                    $variations[] = [
                        'type' => $var->type,
                        'price' => (float)$var->price,
                        'stock' => isset($var->stock) ? (integer)$var->stock : (integer)0,
                    ];
                }
                $item->variations = $variations;

                array_push($storage, $item);
            }
            $data = $storage;
        } else {
            
            $variations = [];
            
            $data->brand_name = gettype($data->brand_name) == 'array' ? $data->brand_name : json_decode($data->brand_name);
            $data->category_ids = gettype($data->category_ids) == 'array' ? $data->category_ids : json_decode($data->category_ids);
            $data->image =  gettype($data->image) == 'array' ? $data->image : json_decode($data->image);
            $data->attributes = gettype($data->attributes) == 'array' ? $data->attributes : json_decode($data->attributes);
            $data->choice_options = gettype($data->choice_options) == 'array' ? $data->choice_options : json_decode($data->choice_options);

            
            $categories = gettype($data->category_ids) == 'array' ? $data->category_ids : json_decode($data->category_ids, true);

            // if(!is_null($categories) && count($categories) > 0) {
            //     $ids = [];
            //     foreach ($categories as $value) {
            //         $value = (array)$value;
            //         if ($value['position'] == 1) {
            //             $ids[] = $value['id'];
            //         }
            //     }
            //     $data['category_discount']= CategoryDiscount::active()->where('category_id', $ids)->first();
            // } else {
            //     $data['category_discount'] = [];
            // }

            $data->variations = gettype($data->variations) == 'array' ? $data->variations : json_decode($data->variations);
        }

        return $data;
    }
}

if(! function_exists('homesliderbanner_data_formatting')) {
    function homesliderbanner_data_formatting($data, $multi_data = false)
    {
        $storage = [];
        if ($multi_data == true) {
            foreach ($data as $item) {
                $item->item_detail = gettype(json_decode($item->item_detail)) == 'array' ? $item->item_detail : json_decode($item->item_detail);
                
                array_push($storage, $item);
            }
            $data = $storage;
        } else {
            $data->item_detail = gettype(json_decode($data->item_detail)) == 'array' ? $data->item_detail : json_decode($data->item_detail);
            
        }

        return $data;
    }
}

if(! function_exists('display_data_formatting')) {
    function display_data_formatting($data, $multi_data = false)
    {
        $storage = [];
        if ($multi_data == true) {
            foreach ($data as $item) {
                foreach ($item->childes as $child)
                {
                    if($child->item_type == 'product')
                    {
                        $child->item_detail = gettype(json_decode($child->item_detail)) == 'array' ? product_data_formatting($child->item_detail) : product_data_formatting(json_decode($child->item_detail));
                    }else{
                        $child->item_detail = gettype(json_decode($child->item_detail)) == 'array' ? $child->item_detail : json_decode($child->item_detail);
                    }
                }
                array_push($storage, $item);
            }
            $data = $storage;
        } else {
            foreach ($data->childes as $child){
                if($child->item_type == 'product')
                {
                    $child->item_detail = gettype(json_decode($child->item_detail)) == 'array' ? product_data_formatting($child->item_detail) : product_data_formatting(json_decode($child->item_detail));
                }else{
                    $child->item_detail = gettype(json_decode($child->item_detail)) == 'array' ? $child->item_detail : json_decode($child->item_detail);
                }
            }
        }

        return $data;
    }
}
