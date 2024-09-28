<?php
use App\Models\Products;
use App\Models\CategoryDiscount;
use App\Models\ProductReview;

if(! function_exists('product_data_formatting')) {
    function product_data_formatting($data, $multi_data = false, $reviews = false)
    {
        $storage = [];
        if ($multi_data == true) {
            foreach ($data as $item) {

                $variations = [];
                $item->brand_name = json_decode($item->brand_name);
                $item->image = json_decode($item->image);
                $item->attributes = json_decode($item->attributes);
                $item->choice_options = json_decode($item->choice_options);
                $item->tags = json_decode($item->tags);
                if(isset($item->category_id))
                {
                    $item->category_discount = CategoryDiscount::Active()->where('category_id', $item->category_id)->first();
                }

                foreach (json_decode($item->variations, true) as $var) {
                    $variations[] = [
                        'type' => $var->type,
                        'price' => (float)$var->price,
                        'stock' => isset($var->stock) ? (integer)$var->stock : (integer)0,
                    ];
                }

                if($reviews)
                {
                    $views = ProductReview::StatusStatic()->where('product_id',$data->id)->get();

                    $item->reviewsCount = $views->count();
                    $item->reviewsAverage = $views->avg('rating');

                    foreach ($views as $key => $value) {
                        $value->attachment = gettype($value->attachment) == 'array' ? $value->attachment : json_decode($value->attachment);
                    }

                    $item->reviews = $views;
                }else{
                    $views = ProductReview::StatusStatic()->where('product_id',$data->id)->get();

                    $item->reviewsCount = $views->count();
                    $item->reviewsAverage = $views->avg('rating');
                }

                $item->variations = $variations;

                array_push($storage, $item);
            }
            $data = $storage;
        } else {

            $variations = [];
            
            $data->brand_name = gettype($data->brand_name) == 'array' ? $data->brand_name : json_decode($data->brand_name);
            $data->image =  gettype($data->image) == 'array' ? $data->image : json_decode($data->image);
            $data->attributes = gettype($data->attributes) == 'array' ? $data->attributes : json_decode($data->attributes);
            $data->choice_options = gettype($data->choice_options) == 'array' ? $data->choice_options : json_decode($data->choice_options);
            $data->tags = gettype($data->tags) == 'array' ? $data->tags : json_decode($data->tags);

            if(isset($data->category_id))
            {
                $data['category_discount'] = CategoryDiscount::Active()->where('category_id', $data->category_id)->first();
            }

            if($reviews)
            {
                $views = ProductReview::StatusStatic()->where('product_id',$data->id)->get();

                $data->reviewsCount = $views->count();
                $data->reviewsAverage = $views->avg('rating');

                foreach ($views as $key => $value) {
                    $value->attachment = gettype($value->attachment) == 'array' ? $value->attachment : json_decode($value->attachment);
                }

                $data->reviews = $views;
            }else{
                $views = ProductReview::StatusStatic()->where('product_id',$data->id)->get();

                $data->reviewsCount = $views->count();
                $data->reviewsAverage = $views->avg('rating');
            }

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
                if($item->item_type == 'product')
                    {
                        $item->item_detail = gettype(json_decode($item->item_detail)) == 'array' ? product_data_formatting($item->item_detail) : product_data_formatting(json_decode($item->item_detail));
                    }else{
                        $item->item_detail = gettype(json_decode($item->item_detail)) == 'array' ? $item->item_detail : json_decode($item->item_detail);
                    }
                // $item->item_detail = gettype(json_decode($item->item_detail)) == 'array' ? $item->item_detail : product_data_formatting(json_decode($item->item_detail));
                
                array_push($storage, $item);
            }
            $data = $storage;
        } else {
            if($data->item_type == 'product')
                {
                    $data->item_detail = gettype(json_decode($data->item_detail)) == 'array' ? product_data_formatting($data->item_detail) : product_data_formatting(json_decode($data->item_detail));
                }else{
                    $data->item_detail = gettype(json_decode($data->item_detail)) == 'array' ? $data->item_detail : json_decode($data->item_detail);
                }
            // $data->item_detail = gettype(json_decode($data->item_detail)) == 'array' ? $data->item_detail : json_decode($data->item_detail);
            
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
