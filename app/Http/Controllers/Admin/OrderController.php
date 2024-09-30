<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\CentralLogics\CustomerLogic;
use App\CentralLogics\Helpers;
use App\Models\Branch;
use App\Models\BusinessSetting;
use App\Models\DeliveryMan;
use App\Models\LoyaltyTransaction;
use App\Models\Order;
use App\Models\Order_details;
use App\Models\Products;
use App\Models\OfflinePayment;
use App\Models\OrderPartialPayment;
use App\Models\Referral_setting;
use App\Traits\HelperTrait;
use App\User;
use Box\Spout\Common\Exception\InvalidArgumentException;
use Box\Spout\Common\Exception\IOException;
use Box\Spout\Common\Exception\UnsupportedTypeException;
use Box\Spout\Writer\Exception\WriterNotOpenedException;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Rap2hpoutre\FastExcel\FastExcel;
use Symfony\Component\HttpFoundation\StreamedResponse;
use function App\CentralLogics\translate;

class OrderController extends Controller
{
    use HelperTrait;
    public function __construct(
        private Branch $branch,
        private BusinessSetting $business_setting,
        private DeliveryMan $delivery_man,
        private Order $order,
        private Order_details $order_detail,
        private Products $product,
        private User $user
    ) {
    }

    /**
     * @param Request $request
     * @param $status
     * @return Factory|View|Application
     */
    public function list(Request $request, $status): View|Factory|Application
    {
        $queryParam = [];
        $search = $request['search'];

        $branches = $this->branch->all();
        $branchId = $request['branch_id'];
        $startDate = $request['start_date'];
        $endDate = $request['end_date'];

        $this->order->where(['checked' => 0])->update(['checked' => 1]);

        $query = $this->order->with(['customer', 'branch'])
            ->when((!is_null($branchId) && $branchId != 'all'), function ($query) use ($branchId) {
                return $query->where('branch_id', $branchId);
            })->when((!is_null($startDate) && !is_null($endDate)), function ($query) use ($startDate, $endDate) {
                return $query->whereDate('created_at', '>=', $startDate)
                    ->whereDate('created_at', '<=', $endDate);
            });

        if ($status != 'all') {
            $query->where(['order_status' => $status]);
        }

        $queryParam = ['branch_id' => $branchId, 'start_date' => $startDate, 'end_date' => $endDate];

        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $query->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('id', 'like', "%{$value}%")
                        ->orWhere('order_status', 'like', "%{$value}%")
                        ->orWhere('payment_status', 'like', "{$value}%");
                }
            });
            $queryParam['search'] = $search;
        }

        $orders = $query->notPos()->orderBy('id', 'desc')->paginate(Helpers_getPagination())->appends($queryParam);

        $countData = [];
        $orderStatuses = ['pending', 'confirmed', 'processing', 'out_for_delivery', 'delivered', 'canceled', 'returned', 'failed'];

        foreach ($orderStatuses as $orderStatus) {
            $countData[$orderStatus] = $this->order->notPos()->where('order_status', $orderStatus)
                ->when(!is_null($branchId) && $branchId != 'all', function ($query) use ($branchId) {
                    return $query->where('branch_id', $branchId);
                })
                ->when(!is_null($startDate) && !is_null($endDate), function ($query) use ($startDate, $endDate) {
                    return $query->whereDate('created_at', '>=', $startDate)
                        ->whereDate('created_at', '<=', $endDate);
                })
                ->count();
        }

        return view('admin-views.order.list', compact('orders', 'status', 'search', 'branches', 'branchId', 'startDate', 'endDate', 'countData'));
    }

    /**
     * @param $id
     * @return View|Factory|RedirectResponse|Application
     */
    public function details($id): Factory|View|Application|RedirectResponse
    {
        $order = $this->order->with(['details', 'offline_payment'])->where(['id' => $id])->first();
        $deliverymanList = $this->delivery_man->where(['is_active' => 1])
            ->where(function ($query) use ($order) {
                $query->where('branch_id', $order->branch_id)
                    ->orWhere('branch_id', 0);
            })->get();

        if (isset($order)) {
            return view('admin-views.order.order-view', compact('order', 'deliverymanList'));
        } else {
            Toastr::info(translate('No more orders!'));
            return back();
        }
    }

    /**
     * @param $id
     * @return View|Factory|RedirectResponse|Application
     */
    public function edit_item($id): Factory|View|Application|RedirectResponse
    {
        $order = $this->order->with(['details', 'offline_payment'])->where(['id' => $id])->first();
        $deliverymanList = $this->delivery_man->where(['is_active' => 1])
            ->where(function ($query) use ($order) {
                $query->where('branch_id', $order->branch_id)
                    ->orWhere('branch_id', 0);
            })->get();

        $all_product = Product::all();
        if (isset($order)) {
            return view('admin-views.order.edit-order', compact('order', 'deliverymanList', 'all_product'));
        } else {
            Toastr::info(translate('No more orders!'));
            return back();
        }
    }

    /**
     * @param $id
     * @return View|Factory|RedirectResponse|Application
     */
    public function edit_item_submit(Request $request, $id): Factory|View|Application|RedirectResponse
    {
        // dd(452 * (56 / 100));
        if (isset($request->direct)) {
            $amount = 0;
            $total_distribute = 0;
            $total_tax = 0;
            $order = $this->order->find($id);
            $status = $order->status;
            if ($order->order_status == 'delivered' || $order->order_status == 'returned' || $order->order_status == 'failed' || $order->order_status == 'canceled') {
                Toastr::warning(translate('Order_can_not_edited_because_of its_status'));
                return back();
            }

            if($order->payment_method != 'cash_on_delivery'){
                Toastr::warning(translate('Order_can_not_edited_directly_when_its_not_cash_on_delivery'));
                return back();
            }

            foreach ($order->details as $key => $value) {

                if ($value->product_id == $request->product[$key]) {

                    if($request->alternate[$key] != 0){

                        $alt_product_detail = Product::find($request->alternate[$key]);

                        $price = $alt_product_detail->price;
                        $tax_on_product = Helpers_tax_calculate($alt_product_detail, $price);

                        $category_id = null;
                        foreach (json_decode($alt_product_detail['category_ids'], true) as $cat) {
                            if ($cat['position'] == 1){
                                $category_id = ($cat['id']);
                            }
                        }

                        $category_discount = Helpers_category_discount_calculate($category_id, $price);
                        $product_discount = Helpers_discount_calculate($alt_product_detail, $price);
                        if ($category_discount >= $price){
                            $discount = $product_discount;
                            $discount_type = 'discount_on_product';
                        }else{
                            $discount = max($category_discount, $product_discount);
                            $discount_type = $product_discount > $category_discount ? 'discount_on_product' : 'discount_on_category';
                        }

                        $distribute_on_product = Helpers_distribute_calculate($alt_product_detail, $price);


                        $value->product_id = $request->alternate[$key];
                        $value->quantity = $request->alternate_qyt[$key];
                        $value->product_details = $alt_product_detail;
                        $value->price = $price;
                        $value->discount_on_product = $discount;
                        $value->discount_type = $discount_type;
                        $value->tax_amount = $tax_on_product;
                        $value->distributed_amount = $distribute_on_product;
                        $value->save();

                        $total_distribute = $total_distribute + $distribute_on_product;
                        $total_tax = $total_tax + $tax_on_product;

                        $amount_price = $price * $request->alternate_qyt[$key];
                        $amount = $amount + $amount_price;
                    }else{

                        $total_distribute = $total_distribute + $value->distributed_amount;
                        $total_tax = $total_tax + $value->tax_amount;

                        $amount_price = $value->price * $value->quantity;
                        $amount = $amount + $amount_price;

                    }
                }
            }


            $order->order_amount = $amount;
            $order->total_distributed_amount = $total_distribute;
            $order->total_tax_amount = $amount;
            $order->save();
            

            try {

                $customerNotifyMessage = 'Your Order Is Edit Because Out of stock Issue! See This new Items';

                $customerLanguageCode = $order->is_guest == 0 ? ($order->customer ? $order->customer->language_code : 'en') : ($order->guest ? $order->guest->language_code : 'en');
                $customerFcmToken = $order->is_guest == 0 ? ($order->customer ? $order->customer->cm_firebase_token : null) : ($order->guest ? $order->guest->fcm_token : null);

                if ($customerLanguageCode != 'en') {
                    $customerNotifyMessage = $this->translate_message($customerLanguageCode, $customerNotifyMessage);
                }
                $value = $this->dynamic_key_replaced_message(message: $customerNotifyMessage, type: 'order', order: $order);

                $data = [
                    'title' => translate('Order Edited'),
                    'description' => $value,
                    'order_id' => $order['id'],
                    'image' => '',
                    'type' => 'order'
                ];

                if ($customerNotifyMessage) {
                    $data['description'] = $value;
                    Helpers_send_push_notif_to_device($customerFcmToken, $data);
                }
            } catch (\Exception $e) {
                Toastr::warning(\App\CentralLogics\translate('Notification failed for Customer!'));
            }

            Toastr::success(\App\CentralLogics\translate('Order Edited Successfully!'));
            return redirect(route('admin.orders.list', 'all'));


        } else {

            // Response

            $order = $this->order->find($id);
            $status = $order->status;
            if ($order->order_status == 'delivered' || $order->order_status == 'returned' || $order->order_status == 'failed' || $order->order_status == 'canceled') {
                Toastr::warning(translate('Order_can_not_edited_because_of its_status'));
                return back();
            }

            $order->editable = 1;
            $order->edit_status = 'pending';

            foreach ($order->details as $key => $value) {
                if ($value->product_id == $request->product[$key]) {
                    $value->alt_product_id = $request->alternate[$key];
                    $value->alt_product_qyt = $request->alternate_qyt[$key];
                    if($request->alternate[$key] != 0){
                        $value->alt_product_status = 'pending';
                    }
                    $product_detail = Product::find($request->alternate[$key]);
                    $value->alt_product_details = $product_detail;
                    $value->save();
                }
            }

            $order->save();

            try {

                $customerNotifyMessage = 'Your Order Is Edit Because Out of stock Issue! See This new Item and Accept it';

                $customerLanguageCode = $order->is_guest == 0 ? ($order->customer ? $order->customer->language_code : 'en') : ($order->guest ? $order->guest->language_code : 'en');
                $customerFcmToken = $order->is_guest == 0 ? ($order->customer ? $order->customer->cm_firebase_token : null) : ($order->guest ? $order->guest->fcm_token : null);

                if ($customerLanguageCode != 'en') {
                    $customerNotifyMessage = $this->translate_message($customerLanguageCode, $customerNotifyMessage);
                }
                $value = $this->dynamic_key_replaced_message(message: $customerNotifyMessage, type: 'order', order: $order);

                $data = [
                    'title' => translate('Order Edited'),
                    'description' => $value,
                    'order_id' => $order['id'],
                    'image' => '',
                    'type' => 'order'
                ];

                if ($customerNotifyMessage) {
                    $data['description'] = $value;
                    Helpers_send_push_notif_to_device($customerFcmToken, $data);
                }
            } catch (\Exception $e) {
                Toastr::warning(\App\CentralLogics\translate('Notification failed for Customer!'));
            }

            Toastr::success(\App\CentralLogics\translate('Order Edited Successfully!'));
            return redirect(route('admin.orders.list', 'all'));
        }
    }

    /**
     * @param $order
     * @param $amount
     * @return void
     */
    private function calculateRefundAmount($order, $amount): void
    {
        $customer = $this->user->find($order['user_id']);
        $wallet = CustomerLogic::create_wallet_transaction($customer->id, $amount, 'refund', $order['id']);
        if ($wallet) {
            $customer->wallet_balance += $amount;
        }
        $customer->save();
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function status(Request $request): \Illuminate\Http\RedirectResponse
    {
        $order = $this->order->find($request->id);

        if (in_array($order->order_status, ['returned', 'delivered', 'failed', 'canceled'])) {
            Toastr::warning(translate('you_can_not_change_the_status_of ' . $order->order_status . ' order'));
            return back();
        }

        if ($request->order_status == 'delivered' && $order['payment_status'] != 'paid') {
            Toastr::warning(translate('you_can_not_delivered_a_order_when_order_status_is_not_paid. please_update_payment_status_first'));
            return back();
        }

        if ($request->order_status == 'delivered' && $order['transaction_reference'] == null && !in_array($order['payment_method'], ['cash_on_delivery', 'wallet_payment', 'offline_payment'])) {
            Toastr::warning(translate('add_your_payment_reference_first'));
            return back();
        }

        if (($request->order_status == 'out_for_delivery' || $request->order_status == 'delivered') && $order['delivery_man_id'] == null && $order['order_type'] != 'self_pickup') {
            Toastr::warning(translate('Please assign delivery man first!'));
            return back();
        }

        //refund amount to wallet
        if (in_array($request['order_status'], ['returned', 'failed', 'canceled']) && $order['is_guest'] == 0 && isset($order->customer) && Helpers_get_business_settings('wallet_status') == 1) {

            if ($order['payment_method'] == 'wallet_payment' && $order->partial_payment->isEmpty()) {
                $this->calculateRefundAmount(order: $order, amount: $order->order_amount);
            }

            if ($order['payment_method'] != 'cash_on_delivery' && $order['payment_method'] != 'wallet_payment' && $order['payment_method'] != 'offline_payment' && $order->partial_payment->isEmpty()) {
                $this->calculateRefundAmount(order: $order, amount: $order->order_amount);
            }

            if ($order['payment_method'] == 'offline_payment' && $order['payment_status'] == 'paid' && $order->partial_payment->isEmpty()) {
                $this->calculateRefundAmount(order: $order, amount: $order['order_amount']);
            }

            if ($order->partial_payment->isNotEmpty()) {
                $partial_payment_total = $order->partial_payment->sum('paid_amount');
                $this->calculateRefundAmount(order: $order, amount: $partial_payment_total);
            }
        }

        //stock adjust
        if ($request->order_status == 'returned' || $request->order_status == 'failed' || $request->order_status == 'canceled') {
            $level_income = LoyaltyTransaction::where('reference', $request->id)->whereNotNull('credited_at')->get();

            if (count($level_income) > 0) {
                for ($i = 0; $i < count($level_income); $i++) {
                    $user_debited_tobe = $level_income[$i]->user_id;

                    $action = LoyaltyTransaction::find($user_debited_tobe);
                    $action->status = 1;
                    $action->save();
                }
            }
            foreach ($order->details as $detail) {
                if (!isset($detail->variant)) {
                    if ($detail['is_stock_decreased'] == 1) {
                        $product = $this->product->find($detail['product_id']);
                        if (!isset($detail->variant)) {
                            dd('ache');
                        }

                        if ($product != null) {
                            $type = json_decode($detail['variation'])[0]->type;
                            $variationStore = [];
                            foreach (json_decode($product['variations'], true) as $var) {
                                if ($type == $var['type']) {
                                    $var['stock'] += $detail['quantity'];
                                }
                                $variationStore[] = $var;
                            }
                            $this->product->where(['id' => $product['id']])->update([
                                'variations' => json_encode($variationStore),
                                'total_stock' => $product['total_stock'] + $detail['quantity'],
                            ]);
                            $this->order_detail->where(['id' => $detail['id']])->update([
                                'is_stock_decreased' => 0,
                            ]);
                        } else {
                            Toastr::warning(translate('Product_deleted'));
                        }
                    }
                }
            }
            die;
        } else {
            foreach ($order->details as $detail) {
                if (!isset($detail->variant)) {
                    if ($detail['is_stock_decreased'] == 0) {

                        $product = $this->product->find($detail['product_id']);
                        if ($product != null) {
                            foreach ($order->details as $c) {
                                $product = $this->product->find($c['product_id']);
                                $type = json_decode($c['variation'])[0]->type;
                                foreach (json_decode($product['variations'], true) as $var) {
                                    if ($type == $var['type'] && $var['stock'] < $c['quantity']) {
                                        Toastr::error(translate('Stock is insufficient!'));
                                        return back();
                                    }
                                }
                            }

                            $type = json_decode($detail['variation'])[0]->type;
                            $variationStore = [];
                            foreach (json_decode($product['variations'], true) as $var) {
                                if ($type == $var['type']) {
                                    $var['stock'] -= $detail['quantity'];
                                }
                                $variationStore[] = $var;
                            }
                            $this->product->where(['id' => $product['id']])->update([
                                'variations' => json_encode($variationStore),
                                'total_stock' => $product['total_stock'] - $detail['quantity'],
                            ]);
                            $this->order_detail->where(['id' => $detail['id']])->update([
                                'is_stock_decreased' => 1,
                            ]);
                        } else {
                            Toastr::warning(translate('Product_deleted'));
                        }
                    }
                }
            }
        }

        if ($request->order_status == 'delivered') {
            if ($order->is_guest == 0) {
                // if($order->user_id) 
                // {
                //     CustomerLogic::create_loyalty_point_transaction($order->user_id, $order->id, $order->order_amount, 'order_place');
                // }

                // $user = $this->user->find($order->user_id);
                // $isFirstOrder = $this->order->where(['user_id' => $user->id, 'order_status' => 'delivered'])->count('id');
                // $referredByUser = $this->user->find($user->referred_by);

                // if ($isFirstOrder < 2 && isset($user->referred_by) && isset($referredByUser))
                // {
                //     if($this->business_setting->where('key','ref_earning_status')->first()->value == 1)
                //     {
                //         CustomerLogic::referral_earning_wallet_transaction($order->user_id, 'referral_order_place', $referredByUser->id);
                //     }
                // }

                if ($order->user_id) {
                    $distributed_amount = $order->total_distributed_amount;
                    $user = User::find($order->user_id);
                    if ($user->referred_by != NULL) {
                        $level_user = $user->referred_by;
                        $level_data = Referral_setting::orderBy('Level', 'ASC')->get();

                        for ($i = 0; $i < count($level_data); $i++) {
                            if ($level_user != NULL) {
                                $single_level_data = $level_data[$i];
                                CustomerLogic::referral_level_earning_loyalty_transaction($level_user, 'referral_level_order_income', $order->id, $single_level_data->percentage, $distributed_amount);

                                $level_user = User::find($level_user)->referred_by;
                            }
                        }
                    }
                }
            }

            if ($order['payment_method'] == 'cash_on_delivery') {
                $partialData = OrderPartialPayment::where(['order_id' => $order->id])->first();
                if ($partialData) {
                    $partial = new OrderPartialPayment;
                    $partial->order_id = $order['id'];
                    $partial->paid_with = 'cash_on_delivery';
                    $partial->paid_amount = $partialData->due_amount;
                    $partial->due_amount = 0;
                    $partial->save();
                }
            }
        }

        $order->order_status = $request->order_status;
        $order->save();

        $message = Helpers_order_status_update_message($request->order_status);
        $languageCode = $order->is_guest == 0 ? ($order->customer ? $order->customer->language_code : 'en') : ($order->guest ? $order->guest->language_code : 'en');
        $customerFcmToken = $order->is_guest == 0 ? ($order->customer ? $order->customer->cm_firebase_token : null) : ($order->guest ? $order->guest->fcm_token : null);

        if ($languageCode != 'en') {
            $message = $this->translate_message($languageCode, $request->order_status);
        }
        $value = $this->dynamic_key_replaced_message(message: $message, type: 'order', order: $order);

        try {
            if ($value) {
                $data = [
                    'title' => translate('Order'),
                    'description' => $value,
                    'order_id' => $order['id'],
                    'image' => '',
                    'type' => 'order'
                ];
                Helpers_send_push_notif_to_device($customerFcmToken, $data);
            }
        } catch (\Exception $e) {
            Toastr::warning(\App\CentralLogics\translate('Push notification failed for Customer!'));
        }

        if ($request->order_status == 'processing' && $order->delivery_man != null) {
            $deliverymanFcmToken = $order->delivery_man->fcm_token;
            $message = Helpers_order_status_update_message('deliveryman_order_processing');
            $deliverymanLanguageCode = $order->delivery_man->language_code ?? 'en';

            if ($deliverymanLanguageCode != 'en') {
                $message = $this->translate_message($deliverymanLanguageCode, 'deliveryman_order_processing');
            }
            $value = $this->dynamic_key_replaced_message(message: $message, type: 'order', order: $order);

            try {
                if ($value) {
                    $data = [
                        'title' => translate('Order'),
                        'description' => $value,
                        'order_id' => $order['id'],
                        'image' => '',
                        'type' => 'order'
                    ];
                    Helpers_send_push_notif_to_device($deliverymanFcmToken, $data);
                }
            } catch (\Exception $e) {
                Toastr::warning(\App\CentralLogics\translate('Push notification failed for DeliveryMan!'));
            }
        }

        Toastr::success(translate('Order status updated!'));
        return back();
    }

    /**
     * @param $order_id
     * @param $delivery_man_id
     * @return JsonResponse
     */
    public function addDeliveryman($order_id, $delivery_man_id): \Illuminate\Http\JsonResponse
    {

        if ($delivery_man_id == 0) {
            return response()->json([], 401);
        }

        $order = $this->order->find($order_id);

        if ($order->order_status == 'pending' || $order->order_status == 'confirmed' || $order->order_status == 'delivered' || $order->order_status == 'returned' || $order->order_status == 'failed' || $order->order_status == 'canceled') {
            return response()->json(['status' => false], 200);
        }

        $order->delivery_man_id = $delivery_man_id;
        $order->save();

        $deliverymanMessage = Helpers_order_status_update_message('del_assign');
        $deliverymanLanguageCode = $order->delivery_man ? $order->delivery_man->language_code : 'en';
        $deliverymanFcmToken = $order->delivery_man ? $order->delivery_man->fcm_token : null;
        if ($deliverymanLanguageCode != 'en') {
            $deliverymanMessage = $this->translate_message($deliverymanLanguageCode, 'del_assign');
        }
        $value = $this->dynamic_key_replaced_message(message: $deliverymanMessage, type: 'order', order: $order);

        try {
            if ($value) {
                $data = [
                    'title' => translate('Order'),
                    'description' => $value,
                    'order_id' => $order['id'],
                    'image' => '',
                    'type' => 'order'
                ];
                Helpers_send_push_notif_to_device($deliverymanFcmToken, $data);

                $customerNotifyMessage = Helpers_order_status_update_message('customer_notify_message');
                $customerLanguageCode = $order->is_guest == 0 ? ($order->customer ? $order->customer->language_code : 'en') : ($order->guest ? $order->guest->language_code : 'en');
                $customerFcmToken = $order->is_guest == 0 ? ($order->customer ? $order->customer->cm_firebase_token : null) : ($order->guest ? $order->guest->fcm_token : null);

                if ($customerLanguageCode != 'en') {
                    $customerNotifyMessage = $this->translate_message($customerLanguageCode, 'customer_notify_message');
                }
                $value = $this->dynamic_key_replaced_message(message: $customerNotifyMessage, type: 'order', order: $order);

                if ($customerNotifyMessage) {
                    $data['description'] = $value;
                    Helpers_send_push_notif_to_device($customerFcmToken, $data);
                }
            }
        } catch (\Exception $e) {
            Toastr::warning(\App\CentralLogics\translate('Push notification failed for DeliveryMan!'));
        }

        Toastr::success('Deliveryman successfully assigned/changed!');
        return response()->json(['status' => true], 200);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function paymentStatus(Request $request): \Illuminate\Http\RedirectResponse
    {
        $order = $this->order->find($request->id);

        if ($order->payment_method == 'offline_payment' && isset($order->offline_payment) && $order->offline_payment?->status != 1) {
            Toastr::warning(translate('please_verify_your_offline_payment_verification'));
            return back();
        }

        if ($request->payment_status == 'paid' && $order['transaction_reference'] == null && $order['payment_method'] != 'cash_on_delivery') {
            Toastr::warning(translate('Add your payment reference code first!'));
            return back();
        }

        if ($request->payment_status == 'paid' && $order['order_status'] == 'pending') {
            $order->order_status = 'confirmed';

            $message = Helpers_order_status_update_message('confirmed');
            $languageCode = $order->is_guest == 0 ? ($order->customer ? $order->customer->language_code : 'en') : ($order->guest ? $order->guest->language_code : 'en');
            $customerFcmToken = $order->is_guest == 0 ? ($order->customer ? $order->customer->cm_firebase_token : null) : ($order->guest ? $order->guest->fcm_token : null);

            if ($languageCode != 'en') {
                $message = $this->translate_message($languageCode, 'confirmed');
            }
            $value = $this->dynamic_key_replaced_message(message: $message, type: 'order', order: $order);

            try {
                if ($value) {
                    $data = [
                        'title' => translate('Order'),
                        'description' => $value,
                        'order_id' => $order['id'],
                        'image' => '',
                        'type' => 'order'
                    ];
                    Helpers_send_push_notif_to_device($customerFcmToken, $data);
                }
            } catch (\Exception $e) {
                //
            }
        }
        $order->payment_status = $request->payment_status;
        $order->save();
        Toastr::success(translate('Payment status updated!'));
        return back();
    }

    /**
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function updateShipping(Request $request, $id): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'contact_person_name' => 'required',
            'address_type' => 'required',
            'contact_person_number' => 'required',
            'address' => 'required',
        ]);

        $address = [
            'contact_person_name' => $request->contact_person_name,
            'contact_person_number' => $request->contact_person_number,
            'address_type' => $request->address_type,
            'address' => $request->address,
            'longitude' => $request->longitude,
            'latitude' => $request->latitude,
            'road' => $request->road,
            'house' => $request->house,
            'floor' => $request->floor,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        DB::table('customer_addresses')->where('id', $id)->update($address);
        Toastr::success(translate('Delivery Information updated!'));
        return back();
    }

    /**
     * @param Request $request
     * @return JsonResponse|void
     */
    public function updateTimeSlot(Request $request)
    {
        if ($request->ajax()) {
            $order = $this->order->find($request->id);
            $order->time_slot_id = $request->timeSlot;
            $order->save();
            $data = $request->timeSlot;

            return response()->json($data);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse|void
     */
    public function updateDeliveryDate(Request $request)
    {
        if ($request->ajax()) {
            $order = $this->order->find($request->id);
            $order->delivery_date = $request->deliveryDate;
            $order->save();
            $data = $request->deliveryDate;
            return response()->json($data);
        }
    }

    /**
     * @param $id
     * @return Factory|View|Application
     */
    public function generateInvoice($id): View|Factory|Application
    {
        $order = $this->order->where('id', $id)->first();
        $footer_text = $this->business_setting->where(['key' => 'footer_text'])->first();
        return view('admin-views.order.invoice', compact('order', 'footer_text'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function addPaymentReferenceCode(Request $request, $id): RedirectResponse
    {
        $this->order->where(['id' => $id])->update([
            'transaction_reference' => $request['transaction_reference'],
        ]);

        Toastr::success(translate('Payment reference code is added!'));
        return back();
    }

    /**
     * @param $id
     * @return RedirectResponse
     */
    public function branchFilter($id): RedirectResponse
    {
        session()->put('branch_filter', $id);
        return back();
    }

    /**
     * @param Request $request
     * @param $status
     * @return string|StreamedResponse
     * @throws IOException
     * @throws InvalidArgumentException
     * @throws UnsupportedTypeException
     * @throws WriterNotOpenedException
     */
    public function exportOrders(Request $request, $status): StreamedResponse|string
    {
        $queryParam = [];
        $search = $request['search'];
        $branchId = $request['branch_id'];
        $startDate = $request['start_date'];
        $endDate = $request['end_date'];

        $query = $this->order->with(['customer', 'branch'])
            ->when((!is_null($branchId) && $branchId != 'all'), function ($query) use ($branchId) {
                return $query->where('branch_id', $branchId);
            })->when((!is_null($startDate) && !is_null($endDate)), function ($query) use ($startDate, $endDate) {
                return $query->whereDate('created_at', '>=', $startDate)
                    ->whereDate('created_at', '<=', $endDate);
            });

        if ($status != 'all') {
            $query->where(['order_status' => $status]);
        }

        $queryParam = ['branch_id' => $branchId, 'start_date' => $startDate, 'end_date' => $endDate];

        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $query->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('id', 'like', "%{$value}%")
                        ->orWhere('order_status', 'like', "%{$value}%")
                        ->orWhere('payment_status', 'like', "{$value}%");
                }
            });
            $queryParam['search'] = $search;
        }

        $orders = $query->notPos()->orderBy('id', 'desc')->get();

        $storage = [];
        foreach ($orders as $order) {
            $branch = $order->branch ? $order->branch->name : '';
            $customer = $order->customer ? $order->customer->f_name . ' ' . $order->customer->l_name : 'Customer Deleted';
            $deliveryman = $order->delivery_man ? $order->delivery_man->f_name . ' ' . $order->delivery_man->l_name : '';
            $timeslot = $order->time_slot ? $order->time_slot->start_time . ' - ' . $order->time_slot->end_time : '';

            $storage[] = [
                'order_id' => $order['id'],
                'customer' => $customer,
                'order_amount' => $order['order_amount'],
                'coupon_discount_amount' => $order['coupon_discount_amount'],
                'payment_status' => $order['payment_status'],
                'order_status' => $order['order_status'],
                'total_tax_amount' => $order['total_tax_amount'],
                'payment_method' => $order['payment_method'],
                'transaction_reference' => $order['transaction_reference'],
                'delivery_man' => $deliveryman,
                'delivery_charge' => $order['delivery_charge'],
                'coupon_code' => $order['coupon_code'],
                'order_type' => $order['order_type'],
                'branch' =>  $branch,
                'time_slot_id' => $timeslot,
                'date' => $order['date'],
                'delivery_date' => $order['delivery_date'],
                'extra_discount' => $order['extra_discount'],
            ];
        }
        return (new FastExcel($storage))->download('orders.xlsx');
    }

    /**
     * @param $order_id
     * @param $status
     * @return JsonResponse
     */
    public function verifyOfflinePayment($order_id, $status): JsonResponse
    {
        $offlineData = OfflinePayment::where(['order_id' => $order_id])->first();

        if (!isset($offlineData)) {
            return response()->json(['status' => false, 'message' => translate('offline data not found')], 200);
        }

        $order = Order::find($order_id);
        if (!isset($order)) {
            return response()->json(['status' => false, 'message' => translate('order not found')], 200);
        }

        if ($status == 1) {
            if ($order->order_status == 'canceled') {
                return response()->json(['status' => false, 'message' => translate('Canceled order can not be verified')], 200);
            }

            $offlineData->status = $status;
            $offlineData->save();

            $order->order_status = 'confirmed';
            $order->payment_status = 'paid';
            $order->save();

            $message = Helpers_order_status_update_message('confirmed');
            $languageCode = $order->is_guest == 0 ? ($order->customer ? $order->customer->language_code : 'en') : ($order->guest ? $order->guest->language_code : 'en');
            $customerFcmToken = $order->is_guest == 0 ? ($order->customer ? $order->customer->cm_firebase_token : null) : ($order->guest ? $order->guest->fcm_token : null);

            if ($languageCode != 'en') {
                $message = $this->translate_message($languageCode, 'confirmed');
            }
            $value = $this->dynamic_key_replaced_message(message: $message, type: 'order', order: $order);

            try {
                if ($value) {
                    $data = [
                        'title' => translate('Order'),
                        'description' => $value,
                        'order_id' => $order['id'],
                        'image' => '',
                        'type' => 'order'
                    ];
                    Helpers_send_push_notif_to_device($customerFcmToken, $data);
                }
            } catch (\Exception $e) {
                //
            }
        } elseif ($status == 2) {
            $offlineData->status = $status;
            $offlineData->save();

            $customerFcmToken = null;
            if ($order->is_guest == 0) {
                $customerFcmToken = $order->customer ? $order->customer->cm_firebase_token : null;
            } elseif ($order->is_guest == 1) {
                $customerFcmToken = $order->guest ? $order->guest->fcm_token : null;
            }
            if ($customerFcmToken != null) {
                try {
                    $data = [
                        'title' => translate('Order'),
                        'description' => translate('Offline payment is not verified'),
                        'order_id' => $order->id,
                        'image' => '',
                        'type' => 'order',
                    ];
                    Helpers_send_push_notif_to_device($customerFcmToken, $data);
                } catch (\Exception $e) {
                }
            }
        }
        return response()->json(['status' => true, 'message' => translate("offline payment verify status changed")], 200);
    }

    public function ProductReplaceAjax(Request $request)
    {
        $request->validate([
            'data' => 'required'
        ]);

        $product = Product::where('id', $request->data)->first();

        $product['fullpath'] = $product->identityImageFullPath[0];

        return $product;
    }

    public function ProductDeleteAjax(Request $request)
    {
        $request->validate([
            'data' => 'required'
        ]);

        $order_detail = OrderDetail::where('id', $request->data)->first();
        $order = Order::find($order_detail->order_id);

        $qyt = $order_detail->quantity;
        $tax = $order_detail->tax_amount * $qyt;
        $price = $order_detail->price * $qyt;

        $order->order_amount = $order->order_amount - $price;
        $order->total_tax_amount = $order->total_tax_amount - $tax;

        $order->save();

        OrderDetail::where('id', $request->data)->first()->delete();

        return response()->json(['success' => true]);
    }
}
