<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\{Request,JsonResponse,RedirectResponse};
use App\Models\BusinessSetting;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\{Factory,View};
use Illuminate\Routing\Redirector;
use App\Models\Branch;

class BusinessSettingsController extends Controller
{
    public function __construct(
        private BusinessSetting $businessSettings
    ){}

    /**
     * @return Application|Factory|View
     */
    public function businessSettingsIndex(): View|Factory|Application
    {
        $logo = Helpers_get_business_settings('logo');

        $favIcon = Helpers_get_business_settings('fav_icon');

        return view('Admin.views.business-settings.index', compact('logo', 'favIcon'));
    }

    /**
     * @param $status
     * @return JsonResponse
     */
    public function maintenanceMode(): \Illuminate\Http\JsonResponse
    {
        $mode = Helpers_get_business_settings('maintenance_mode');
        $mode = BusinessSetting::where('key' , 'maintenance_mode')->first();
        
        if ($mode->value == null || $mode->value == 1){
            $mode->value = 0;
            $mode->save();
            flash()->success('Maintenance Mode is on.');
            return response()->json(['message' => translate('done')]);
        }
        $mode->value = 1;
        $mode->save();
        flash()->success('Maintenance Mode is Off.');
        return response()->json(['message' => translate('done')]);
    }

    /**
     * @param $status
     * @return JsonResponse
     */
    public function partialPaymentStatus($status): JsonResponse
    {
        BusinessSetting::updateOrInsert(['key' => 'partial_payment'], [
            'value' => $status
        ]);
        return response()->json(['message' => translate('partial payment status updated') ]);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function businessSetup(Request $request): \Illuminate\Http\RedirectResponse
    {
        BusinessSetting::updateOrInsert(['key' => 'footer_text'], [
            'value' => $request['footer_text'],
        ]);

        BusinessSetting::updateOrInsert(['key' => 'app_name'], [
            'value' => $request->app_name
        ]);

        BusinessSetting::updateOrInsert(['key' => 'phone'], [
            'value' => $request['phone'],
        ]);

        BusinessSetting::updateOrInsert(['key' => 'email_address'], [
            'value' => $request['email'],
        ]);

        BusinessSetting::updateOrInsert(['key' => 'address'], [
            'value' => $request['address'],
        ]);

        BusinessSetting::updateOrInsert(['key' => 'pagination_limit'], [
            'value' => $request['pagination_limit'],
        ]);

        $currentLogo = $this->businessSettings->where(['key' => 'logo'])->first();
        if ($request->has('logo')) {
            $NewLogo = $request->file('logo');
            $imageName = Helpers_update('Images/Business/', $currentLogo->value, $NewLogo->getClientOriginalExtension(), $NewLogo);
        } else {
            $imageName = $currentLogo['value'];
        }
        BusinessSetting::updateOrInsert(['key' => 'logo'], [
            'value' => $imageName,
        ]);

        $currentFavIcon = $this->businessSettings->where(['key' => 'fav_icon'])->first();
        if ($request->has('fav_icon')) {
            $newFavIcon = $request->file('fav_icon');
            $FavName = Helpers_update('Images/Business/', $currentFavIcon->value, $newFavIcon->getClientOriginalExtension(), $newFavIcon);
        } else {
            $FavName = $currentFavIcon['value'];
        }
        BusinessSetting::updateOrInsert(['key' => 'fav_icon'], [
            'value' => $FavName
        ]);

        flash()->success(translate('Settings updated!'));
        return back();
    }

    /**
     * @return Application|Factory|View
     */
    public function ReferralIncomeSetup(): View|Factory|Application
    {
        return view('Admin.views.business-settings.refferal_income');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function ReferralIncomeSetupUpdate(Request $request): RedirectResponse
    {
        $request->validate([
            'refferal_bonus' => 'required'
        ]);

        BusinessSetting::updateOrInsert(['key' => 'refferal_bonus'], [
            'value' => $request->refferal_bonus,
        ]);

        flash()->success(translate('updated_successfully'));
        return back();
    }

    /**
     * @return Application|Factory|View
     */
    public function mainBranchSetup(): View|Factory|Application
    {
        $main_branch = Branch::where(['id' => 1])->first();
        return view('Admin.views.business-settings.main-branch-setup', compact('main_branch'));
    }

    /**
     * @return Application|Factory|View
     */
    public function deliveryIndex(): Factory|View|Application
    {
        if (!$this->businessSettings->where(['key' => 'minimum_order_value'])->first()) {
            BusinessSetting::updateOrInsert(['key' => 'minimum_order_value'], [
                'value' => 1,
            ]);
        }

        if (!$this->businessSettings->where(['key' => 'delivery_management'])->first()) {
            BusinessSetting::updateOrInsert(['key' => 'delivery_management'], [
                'value' => json_encode([
                    "status" => 1,
                    "min_shipping_charge" => 0,
                    "shipping_per_km" => 0
                ]),
            ]);
        }

        $config = json_decode($this->businessSettings->where(['key' => 'delivery_management'])->first()->value);

        return view('admin.views.business-settings.delivery-fee',compact('config'));
    }

    /**
     * @param $status
     * @return JsonResponse
     */
    public function freeDeliveryStatus($status): \Illuminate\Http\JsonResponse
    {
        BusinessSetting::updateOrInsert(['key' => 'free_delivery_over_amount_status'], [
            'value' => $status
        ]);

        return response()->json([
            'status' => 1,
            'message' => translate('status updated')
        ]);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function deliverySetupUpdate(Request $request): \Illuminate\Http\RedirectResponse
    {
        if ($request['shipping_status'] == 0) {
            $request->validate([
                'min_shipping_charge' => 'required',
                'shipping_per_km' => 'required',
            ],
            [
                'min_shipping_charge.required' => translate('Minimum shipping charge is required while shipping method is active'),
                'shipping_per_km.required' => translate('Shipping charge per Kilometer is required while shipping method is active'),
            ]);
        }else{
            if($request->delivery_charge == null) {
                $request['delivery_charge'] = $this->businessSettings->where(['key' => 'default_delivery_charge'])->first()->value;
            }
    
            BusinessSetting::updateOrInsert(['key' => 'default_delivery_charge'], [
                'value' => $request->delivery_charge,
            ]);
        }

        $delivery_management = Helpers_get_business_settings('delivery_management');
        // dd($delivery_management);
        if($request['min_shipping_charge'] == null) {
            $request['min_shipping_charge'] = $delivery_management['min_shipping_charge'];
        }
        if($request['shipping_per_km'] == null) {
            $request['shipping_per_km'] = $delivery_management['shipping_per_km'];
        }

        BusinessSetting::updateOrInsert(['key' => 'delivery_management'], [
            'value' => json_encode([
                'status'  => $request['shipping_status'],
                'min_shipping_charge' => $request['min_shipping_charge'],
                'shipping_per_km' => $request['shipping_per_km'],
            ]),
        ]);
        

        BusinessSetting::updateOrInsert(['key' => 'free_delivery_over_amount'], [
            'value' => $request['free_delivery_over_amount'],
        ]);

        flash()->success(translate('Settings Updated'));
        return back();
    }

    /**
     * @return Application|Factory|View
     */
    public function productSetup(): Factory|View|Application
    {
        return view('Admin.views.business-settings.product-setup-index');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function productSetupUpdate(Request $request): \Illuminate\Http\RedirectResponse
    {
        BusinessSetting::updateOrInsert(['key' => 'product_gst_tax_status'], [
            'value' => $request['product_vat_tax_status'],
        ]);

        flash()->success(translate('Settings updated!'));
        return back();
    }

    /**
     * @return Application|Factory|View
     */
    public function cookiesSetup(): Factory|View|Application
    {
        if (!$this->businessSettings->where(['key' => 'cookies'])->first()) {
            BusinessSetting::updateOrInsert(['key' => 'cookies'],
            [
                'value' => json_encode([
                    "status" => 1,
                    "text" => ''
                ]),
            ]);
        }

        $cookies = Helpers_get_business_settings('cookies');

        return view('Admin.views.business-settings.cookies-setup-index',compact('cookies'));
    }

    /**
     * @param $status
     * @return JsonResponse
     */
    public function cookiesStatus($status): \Illuminate\Http\JsonResponse
    {
        $cookies = Helpers_get_business_settings('cookies');

        BusinessSetting::updateOrInsert(['key' => 'cookies'], 
        [
            'value' => json_encode([
                'status' => $status,
                'text' => $cookies['text'],
            ])
        ]);

        return response()->json([
            'status' => 1,
            'message' => translate('status updated')
        ]);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function cookiesSetupUpdate(Request $request): \Illuminate\Http\RedirectResponse
    {
        $cookies = Helpers_get_business_settings('cookies');

        BusinessSetting::updateOrInsert(['key' => 'cookies'], [
            'value' => json_encode([
                'status' => $cookies['status'],
                'text' => $request['text'],
            ])
        ]);

        flash()->success(translate('Settings updated!'));
        return back();
    }

/**
     * @return Application|Factory|View
     */
    public function OTPSetup(): Factory|View|Application
    {
        if (!$this->businessSettings->where(['key' => 'otp_resend_time'])->first()) {
            BusinessSetting::updateOrInsert(['key' => 'otp_resend_time'],
            [
                'value' => 60,
            ]);
        }

        $timer = Helpers_get_business_settings('otp_resend_time');

        return view('Admin.views.business-settings.otp-setup', compact('timer'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function OTPSetupUpdate(Request $request): RedirectResponse
    {
        BusinessSetting::updateOrInsert(['key' => 'otp_resend_time'], [
            'value' => $request['otp_resend_time'],
        ]);

        flash()->success(translate('Settings updated!'));
        return back();
    }

    /**
     * @return Application|Factory|View
     */
    public function orderSetup(): Factory|View|Application
    {
        if (!$this->businessSettings->where(['key' => 'maximum_amount_for_cod_order_status'])->first()) {
            BusinessSetting::updateOrInsert(['key' => 'maximum_amount_for_cod_order_status'], [
                'value' => 1,
            ]);
        }

        if (!$this->businessSettings->where(['key' => 'maximum_amount_for_cod_order'])->first()) {
            BusinessSetting::updateOrInsert(['key' => 'maximum_amount_for_cod_order'], [
                'value' => 0,
            ]);
        }

        $status = Helpers_get_business_settings('maximum_amount_for_cod_order_status');
        $amount = Helpers_get_business_settings('maximum_amount_for_cod_order');

        return view('Admin.views.business-settings.order-setup-index',compact('status','amount'));
    }

    /**
     * @param $status
     * @return JsonResponse
     */
    public function orderStatus($status): \Illuminate\Http\JsonResponse
    {
        BusinessSetting::updateOrInsert(['key' => 'maximum_amount_for_cod_order_status'], 
        [
            'value' => $status
        ]);

        return response()->json([
            'status' => 1,
            'message' => translate('status updated')
        ]);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function orderSetupUpdate(Request $request): RedirectResponse
    {
        BusinessSetting::updateOrInsert(['key' => 'maximum_amount_for_cod_order'], [
            'value' => $request['amount'],
        ]);

        flash()->success(translate('order_settings_updated_successfully'));
        return back();
    }

    /**
     * @return Application|Factory|View
     */
    public function customerSetup(): Factory|View|Application
    {
        $data = $this->businessSettings->where('key','like','wallet_%')
            ->orWhere('key','like','loyalty_%')
            ->orWhere('key','like','ref_earning_%')
            ->orWhere('key','like','add_fund_to_wallet%')
            ->orWhere('key','like','ref_earning_%')->get();
        $data = array_column($data->toArray(), 'value','key');

        return view('Admin.views.business-settings.customer-setup', compact('data'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function customerSetupUpdate(Request $request): RedirectResponse
    {

        $this->businessSettings->updateOrInsert(['key' => 'wallet_status'], [
            'value' => $request['customer_wallet']??0
        ]);

        $this->businessSettings->updateOrInsert(['key' => 'add_fund_to_wallet'], [
            'value' => $request['add_fund_to_wallet']??0
        ]);

        flash()->success(translate('customer_settings_updated_successfully'));
        return back();
    }



    /**
     * @return Application|Factory|View
     */
    public function paymentIndex(): Factory|View|Application
    {

        $data_values = AddonSetting::whereIn('settings_type', ['payment_config'])
            ->whereIn('key_name', ['ssl_commerz','paypal','stripe','razor_pay','senang_pay','paystack','paymob_accept','flutterwave','bkash','mercadopago'])
            ->get();

        return view('Admin.views.3rd_party.payment-index', compact('data_values'));
    }

    public function paymentUpdate(Request $request, $name): \Illuminate\Http\RedirectResponse
    {

        if ($name == 'cash_on_delivery') {
            $payment = $this->businessSettings->where('key', 'cash_on_delivery')->first();
            if (!isset($payment)) {
                BusinessSetting::insert([
                    'key'        => 'cash_on_delivery',
                    'value'      => json_encode([
                        'status' => $request['status'],
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                BusinessSetting::where(['key' => 'cash_on_delivery'])->update([
                    'key'        => 'cash_on_delivery',
                    'value'      => json_encode([
                        'status' => $request['status'],
                    ]),
                    'updated_at' => now(),
                ]);
            }
        } elseif ($name == 'digital_payment') {
            $payment = $this->businessSettings->where('key', 'digital_payment')->first();
            if (!isset($payment)) {
                BusinessSetting::insert([
                    'key'        => 'digital_payment',
                    'value'      => json_encode([
                        'status' => $request['status'],
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                BusinessSetting::where(['key' => 'digital_payment'])->update([
                    'key'        => 'digital_payment',
                    'value'      => json_encode([
                        'status' => $request['status'],
                    ]),
                    'updated_at' => now(),
                ]);
            }
        } elseif ($name == 'offline_payment') {
            $payment = $this->businessSettings->where('key', 'offline_payment')->first();
            if (!isset($payment)) {
                BusinessSetting::insert([
                    'key'        => 'offline_payment',
                    'value'      => json_encode([
                        'status' => $request['status'],
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                BusinessSetting::where(['key' => 'offline_payment'])->update([
                    'key'        => 'offline_payment',
                    'value'      => json_encode([
                        'status' => $request['status'],
                    ]),
                    'updated_at' => now(),
                ]);
            }
        }elseif ($name == 'ssl_commerz_payment') {
            $payment = $this->businessSettings->where('key', 'ssl_commerz_payment')->first();
            if (!isset($payment)) {
                BusinessSetting::insert([
                    'key'        => 'ssl_commerz_payment',
                    'value'      => json_encode([
                        'status'         => 1,
                        'store_id'       => '',
                        'store_password' => '',
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                BusinessSetting::where(['key' => 'ssl_commerz_payment'])->update([
                    'key'        => 'ssl_commerz_payment',
                    'value'      => json_encode([
                        'status'         => $request['status'],
                        'store_id'       => $request['store_id'],
                        'store_password' => $request['store_password'],
                    ]),
                    'updated_at' => now(),
                ]);
            }
        } elseif ($name == 'razor_pay') {
            $payment = $this->businessSettings->where('key', 'razor_pay')->first();
            if (!isset($payment)) {
                BusinessSetting::insert([
                    'key'        => 'razor_pay',
                    'value'      => json_encode([
                        'status'       => 1,
                        'razor_key'    => '',
                        'razor_secret' => '',
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                BusinessSetting::where(['key' => 'razor_pay'])->update([
                    'key'        => 'razor_pay',
                    'value'      => json_encode([
                        'status'       => $request['status'],
                        'razor_key'    => $request['razor_key'],
                        'razor_secret' => $request['razor_secret'],
                    ]),
                    'updated_at' => now(),
                ]);
            }
        } elseif ($name == 'paypal') {
            $payment = $this->businessSettings->where('key', 'paypal')->first();
            if (!isset($payment)) {
                BusinessSetting::insert([
                    'key'        => 'paypal',
                    'value'      => json_encode([
                        'status'           => 1,
                        'paypal_client_id' => '',
                        'paypal_secret'    => '',
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                BusinessSetting::where(['key' => 'paypal'])->update([
                    'key'        => 'paypal',
                    'value'      => json_encode([
                        'status'           => $request['status'],
                        'paypal_client_id' => $request['paypal_client_id'],
                        'paypal_secret'    => $request['paypal_secret'],
                    ]),
                    'updated_at' => now(),
                ]);
            }
        } elseif ($name == 'stripe') {
            $payment = $this->businessSettings->where('key', 'stripe')->first();
            if (!isset($payment)) {
                BusinessSetting::insert([
                    'key'        => 'stripe',
                    'value'      => json_encode([
                        'status'        => 1,
                        'api_key'       => '',
                        'published_key' => '',
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                BusinessSetting::where(['key' => 'stripe'])->update([
                    'key'        => 'stripe',
                    'value'      => json_encode([
                        'status'        => $request['status'],
                        'api_key'       => $request['api_key'],
                        'published_key' => $request['published_key'],
                    ]),
                    'updated_at' => now(),
                ]);
            }
        } elseif ($name == 'senang_pay') {
            $payment = $this->businessSettings->where('key', 'senang_pay')->first();
            if (!isset($payment)) {
                BusinessSetting::insert([
                    'key'        => 'senang_pay',
                    'value'      => json_encode([
                        'status'      => 1,
                        'secret_key'  => '',
                        'merchant_id' => '',
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                BusinessSetting::where(['key' => 'senang_pay'])->update([
                    'key'        => 'senang_pay',
                    'value'      => json_encode([
                        'status'      => $request['status'],
                        'secret_key'  => $request['secret_key'],
                        'merchant_id' => $request['merchant_id'],
                    ]),
                    'updated_at' => now(),
                ]);
            }
        }elseif ($name == 'paystack') {
            $payment = $this->businessSettings->where('key', 'paystack')->first();
            if (!isset($payment)) {
                BusinessSetting::insert([
                    'key' => 'paystack',
                    'value' => json_encode([
                        'status' => 1,
                        'publicKey' => '',
                        'secretKey' => '',
                        'paymentUrl' => '',
                        'merchantEmail' => '',
                    ]),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            } else {
                BusinessSetting::where(['key' => 'paystack'])->update([
                    'key' => 'paystack',
                    'value' => json_encode([
                        'status' => $request['status'],
                        'publicKey' => $request['publicKey'],
                        'secretKey' => $request['secretKey'],
                        'paymentUrl' => $request['paymentUrl'],
                        'merchantEmail' => $request['merchantEmail'],
                    ]),
                    'updated_at' => now()
                ]);
            }
        } else if ($name == 'bkash') {
            BusinessSetting::updateOrInsert(['key' => 'bkash'], [
                'value' => json_encode([
                    'status' => $request['status'],
                    'api_key' => $request['api_key'],
                    'api_secret' => $request['api_secret'],
                    'username' => $request['username'],
                    'password' => $request['password'],
                ])
            ]);
        } else if ($name == 'paymob') {
            BusinessSetting::updateOrInsert(['key' => 'paymob'], [
                'value' => json_encode([
                    'status' => $request['status'],
                    'api_key' => $request['api_key'],
                    'iframe_id' => $request['iframe_id'],
                    'integration_id' => $request['integration_id'],
                    'hmac' => $request['hmac']
                ])
            ]);
        } else if ($name == 'flutterwave') {
            BusinessSetting::updateOrInsert(['key' => 'flutterwave'], [
                'value' => json_encode([
                    'status' => $request['status'],
                    'public_key' => $request['public_key'],
                    'secret_key' => $request['secret_key'],
                    'hash' => $request['hash']
                ])
            ]);
        } else if ($name == 'mercadopago') {
            BusinessSetting::updateOrInsert(['key' => 'mercadopago'], [
                'value' => json_encode([
                    'status' => $request['status'],
                    'public_key' => $request['public_key'],
                    'access_token' => $request['access_token']
                ])
            ]);
        }else if ($name == '6cash') {
            BusinessSetting::updateOrInsert(['key' => '6cash'], [
                'value' => json_encode([
                    'status' => $request['status'],
                    'public_key' => $request['public_key'],
                    'secret_key' => $request['secret_key'],
                    'merchant_number' => $request['merchant_number']
                ])
            ]);
        }

        flash()->success(translate('payment settings updated!'));
        return back();
    }

    public function paymentConfigUpdate(Request $request)
    {
        $validation = [
            'gateway' => 'required|in:ssl_commerz,paypal,stripe,razor_pay,senang_pay,paystack,paymob_accept,flutterwave,bkash,mercadopago',
            'mode' => 'required|in:live,test'
        ];

        $request['status'] = $request->has('status') ? 1 : 0;

        $additionalData = [];

        if ($request['gateway'] == 'ssl_commerz') {
            $additionalData = [
                'status' => 'required|in:1,0',
                'store_id' => 'required_if:status,1',
                'store_password' => 'required_if:status,1'
            ];
        } elseif ($request['gateway'] == 'paypal') {
            $additionalData = [
                'status' => 'required|in:1,0',
                'client_id' => 'required_if:status,1',
                'client_secret' => 'required_if:status,1'
            ];
        } elseif ($request['gateway'] == 'stripe') {
            $additionalData = [
                'status' => 'required|in:1,0',
                'api_key' => 'required_if:status,1',
                'published_key' => 'required_if:status,1',
            ];
        } elseif ($request['gateway'] == 'razor_pay') {
            $additionalData = [
                'status' => 'required|in:1,0',
                'api_key' => 'required_if:status,1',
                'api_secret' => 'required_if:status,1'
            ];
        } elseif ($request['gateway'] == 'senang_pay') {
            $additionalData = [
                'status' => 'required|in:1,0',
                'callback_url' => 'required_if:status,1',
                'secret_key' => 'required_if:status,1',
                'merchant_id' => 'required_if:status,1'
            ];
        }elseif ($request['gateway'] == 'paystack') {
            $additionalData = [
                'status' => 'required|in:1,0',
                'public_key' => 'required_if:status,1',
                'secret_key' => 'required_if:status,1',
                'merchant_email' => 'required_if:status,1'
            ];
        } elseif ($request['gateway'] == 'paymob_accept') {
            $additionalData = [
                'status' => 'required|in:1,0',
                'callback_url' => 'required_if:status,1',
                'api_key' => 'required_if:status,1',
                'iframe_id' => 'required_if:status,1',
                'integration_id' => 'required_if:status,1',
                'hmac' => 'required_if:status,1'
            ];
        } elseif ($request['gateway'] == 'mercadopago') {
            $additionalData = [
                'status' => 'required|in:1,0',
                'access_token' => 'required_if:status,1',
                'public_key' => 'required_if:status,1'
            ];
        } elseif ($request['gateway'] == 'flutterwave') {
            $additionalData = [
                'status' => 'required|in:1,0',
                'secret_key' => 'required_if:status,1',
                'public_key' => 'required_if:status,1',
                'hash' => 'required_if:status,1'
            ];
        }  elseif ($request['gateway'] == 'bkash') {
            $additionalData = [
                'status' => 'required|in:1,0',
                'app_key' => 'required_if:status,1',
                'app_secret' => 'required_if:status,1',
                'username' => 'required_if:status,1',
                'password' => 'required_if:status,1',
            ];
        }

        $request->validate(array_merge($validation, $additionalData));

        $settings = AddonSetting::where('key_name', $request['gateway'])->where('settings_type', 'payment_config')->first();

        $additionalDataImage = $settings['additional_data'] != null ? json_decode($settings['additional_data']) : null;

        if ($request->has('gateway_image')) {
            $gatewayImage = Helpers_upload('payment_modules/gateway_image/', 'png', $request['gateway_image']);
        } else {
            $gatewayImage = $additionalDataImage != null ? $additionalDataImage->gateway_image : '';
        }

        $payment_additional_data = [
            'gateway_title' => $request['gateway_title'],
            'gateway_image' => $gatewayImage,
        ];

        $validator = Validator::make($request->all(), array_merge($validation, $additionalData));

        AddonSetting::updateOrCreate(['key_name' => $request['gateway'], 'settings_type' => 'payment_config'], [
            'key_name' => $request['gateway'],
            'live_values' => $validator->validate(),
            'test_values' => $validator->validate(),
            'settings_type' => 'payment_config',
            'mode' => $request['mode'],
            'is_active' => $request->status,
            'additional_data' => json_encode($payment_additional_data),
        ]);

        flash()->success(GATEWAYS_DEFAULT_UPDATE_200['message']);
        return back();

    }






































    

    

    public function maximumAmountStatus($status): \Illuminate\Http\JsonResponse
    {
        BusinessSetting::updateOrInsert(['key' => 'maximum_amount_for_cod_order_status'], [
            'value' => $status
        ]);

        return response()->json([
            'status' => 1,
            'message' => translate('status updated')
        ]);
    }







    

    

    public function currencySymbolPosition($side): \Illuminate\Http\JsonResponse
    {
        BusinessSetting::updateOrInsert(['key' => 'currency_symbol_position'], [
            'value' => $side
        ]);
        return response()->json(['message' => 'Symbol position is ' . $side]);
    }

    public function phoneVerificationStatus($status): \Illuminate\Http\JsonResponse
    {
        $emailStatus = BusinessSetting::where('key','email_verification')->first()->value;

        if ($emailStatus == 1){
            return response()->json([
                'status' => 0,
                'message' => 'Both email and phone verification can not be active at a time!'
            ]);
        }

        BusinessSetting::updateOrInsert(['key' => 'phone_verification'], [
            'value' => $status
        ]);

        return response()->json([
            'status' => 1,
            'message' => translate('Phone verification status updated')
        ]);
    }

    public function emailVerificationStatus($status): \Illuminate\Http\JsonResponse
    {
        $phoneStatus = BusinessSetting::where('key','phone_verification')->first()->value;

        if ($phoneStatus == 1){
            return response()->json([
                'status' => 0,
                'message' => 'Both email and phone verification can not be active at a time!'
            ]);
        }

        BusinessSetting::updateOrInsert(['key' => 'email_verification'], [
            'value' => $status
        ]);

        return response()->json([
            'status' => 1,
            'message' => 'Email verification status updated'
        ]);
    }

    public function selfPickupStatus($status): \Illuminate\Http\JsonResponse
    {
        BusinessSetting::updateOrInsert(['key' => 'self_pickup'], [
            'value' => $status
        ]);
        return response()->json(['message' => translate('Pickup status updated')]);
    }

    public function deliverymanSelfRegistrationStatus($status): \Illuminate\Http\JsonResponse
    {
        BusinessSetting::updateOrInsert(['key' => 'dm_self_registration'], [
            'value' => $status
        ]);
        return response()->json(['message' => translate('Delivery man self registration status updated')]);
    }

    /**
     * @param $status
     * @return JsonResponse
     */
    public function guestCheckoutStatus($status): JsonResponse
    {
        BusinessSetting::updateOrInsert(['key' => 'guest_checkout'], [
            'value' => $status
        ]);
        return response()->json(['message' => translate('guest checkout status updated')]);
    }

    

    

    

    /**
     * @return Application|Factory|View
     */
    public function mailIndex(): Factory|View|Application
    {
        return view('Admin.views.business-settings.mail-index');
    }

    public function mailSend(Request $request): \Illuminate\Http\JsonResponse
    {
        $responseFlag = 0;
        try {
            $emailServices = Helpers_get_business_settings('mail_config');

            if (isset($emailServices['status']) && $emailServices['status'] == 1) {
                Mail::to($request->email)->send(new \App\Mail\TestEmailSender());
                $responseFlag = 1;
            }
        } catch (\Exception $exception) {
            $responseFlag = 2;
        }

        return response()->json(['success' => $responseFlag]);
    }

    public function mailConfig(Request $request): \Illuminate\Http\RedirectResponse
    {
        $data = Helpers_get_business_settings('mail_config');

        $this->businessSettings->where(['key' => 'mail_config'])->update([
            'value' => json_encode([
                "status" => $data['status'],
                "name"       => $request['name'],
                "host"       => $request['host'],
                "driver"     => $request['driver'],
                "port"       => $request['port'],
                "username"   => $request['username'],
                "email_id"   => $request['email'],
                "encryption" => $request['encryption'],
                "password"   => $request['password'],
            ]),
        ]);
        flash()->success(translate('Configuration updated successfully!'));

        return back();
    }

    public function mailConfigStatus($status): \Illuminate\Http\JsonResponse
    {
        $data = Helpers_get_business_settings('mail_config');
        $data['status'] = $status == '1' ? 1 : 0;

        $this->businessSettings->where(['key' => 'mail_config'])->update([
            'value' => $data,
        ]);
        return response()->json(['message' => 'Mail config status updated']);
    }

    

    /**
     * @return Application|Factory|View
     */
    public function currencyIndex(): Factory|View|Application
    {
        return view('Admin.views.business-settings.currency-index');
    }

    public function currencyStore(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'currency_code' => 'required|unique:currencies',
        ]);

        Currency::create([
            "country"         => $request['country'],
            "currency_code"   => $request['currency_code'],
            "currency_symbol" => $request['symbol'],
            "exchange_rate"   => $request['exchange_rate'],
        ]);
        flash()->success(translate('Currency added successfully!'));
        return back();
    }


    /**
     * @param $id
     * @return Application|Factory|View
     */
    public function currencyEdit($id): Factory|View|Application
    {
        $currency = Currency::find($id);
        return view('Admin.views.business-settings.currency-update', compact('currency'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return Application|RedirectResponse|Redirector
     */
    public function currencyUpdate(Request $request, $id): Redirector|Application|RedirectResponse
    {
        Currency::where(['id' => $id])->update([
            "country"         => $request['country'],
            "currency_code"   => $request['currency_code'],
            "currency_symbol" => $request['symbol'],
            "exchange_rate"   => $request['exchange_rate'],
        ]);
        flash()->success(translate('Currency updated successfully!'));
        return redirect('admin/business-settings/currency-add');
    }

    public function currencyDelete($id): \Illuminate\Http\RedirectResponse
    {
        Currency::where(['id' => $id])->delete();
        flash()->success(translate('Currency removed successfully!'));
        return back();
    }

 

    /**
     * @return Application|Factory|View
     */
    public function fcmIndex(): View|Factory|Application
    {
        if (!$this->businessSettings->where(['key' => 'order_pending_message'])->first()) {
            $this->businessSettings->insert([
                'key'   => 'order_pending_message',
                'value' => json_encode([
                    'status'  => 0,
                    'message' => '',
                ]),
            ]);
        }

        if (!$this->businessSettings->where(['key' => 'order_confirmation_msg'])->first()) {
            $this->businessSettings->insert([
                'key'   => 'order_confirmation_msg',
                'value' => json_encode([
                    'status'  => 0,
                    'message' => '',
                ]),
            ]);
        }

        if (!$this->businessSettings->where(['key' => 'order_processing_message'])->first()) {
            $this->businessSettings->insert([
                'key'   => 'order_processing_message',
                'value' => json_encode([
                    'status'  => 0,
                    'message' => '',
                ]),
            ]);
        }

        if (!$this->businessSettings->where(['key' => 'out_for_delivery_message'])->first()) {
            $this->businessSettings->insert([
                'key'   => 'out_for_delivery_message',
                'value' => json_encode([
                    'status'  => 0,
                    'message' => '',
                ]),
            ]);
        }

        if (!$this->businessSettings->where(['key' => 'order_delivered_message'])->first()) {
            $this->businessSettings->insert([
                'key'   => 'order_delivered_message',
                'value' => json_encode([
                    'status'  => 0,
                    'message' => '',
                ]),
            ]);
        }

        if (!$this->businessSettings->where(['key' => 'delivery_boy_assign_message'])->first()) {
            $this->businessSettings->insert([
                'key'   => 'delivery_boy_assign_message',
                'value' => json_encode([
                    'status'  => 0,
                    'message' => '',
                ]),
            ]);
        }

        if (!$this->businessSettings->where(['key' => 'delivery_boy_start_message'])->first()) {
            $this->businessSettings->insert([
                'key'   => 'delivery_boy_start_message',
                'value' => json_encode([
                    'status'  => 0,
                    'message' => '',
                ]),
            ]);
        }

        if (!$this->businessSettings->where(['key' => 'delivery_boy_delivered_message'])->first()) {
            $this->businessSettings->insert([
                'key'   => 'delivery_boy_delivered_message',
                'value' => json_encode([
                    'status'  => 0,
                    'message' => '',
                ]),
            ]);
        }

        if (!$this->businessSettings->where(['key' => 'customer_notify_message'])->first()) {
            $this->businessSettings->insert([
                'key' => 'customer_notify_message',
                'value' => json_encode([
                    'status' => 0,
                    'message' => '',
                ]),
            ]);
        }
        if (!$this->businessSettings->where(['key' => 'returned_message'])->first()) {
            $this->businessSettings->insert([
                'key' => 'returned_message',
                'value' => json_encode([
                    'status' => 0,
                    'message' => '',
                ]),
            ]);
        }if (!$this->businessSettings->where(['key' => 'failed_message'])->first()) {
            $this->businessSettings->insert([
                'key' => 'failed_message',
                'value' => json_encode([
                    'status' => 0,
                    'message' => '',
                ]),
            ]);
        }if (!$this->businessSettings->where(['key' => 'canceled_message'])->first()) {
            $this->businessSettings->insert([
                'key' => 'canceled_message',
                'value' => json_encode([
                    'status' => 0,
                    'message' => '',
                ]),
            ]);
        }

        return view('Admin.views.business-settings.fcm-index');
    }

    /**
     * @return Application|Factory|View
     */
    public function fcmConfig(): View|Factory|Application
    {
        if (!$this->businessSettings->where(['key' => 'fcm_topic'])->first()) {
            $this->businessSettings->insert([
                'key' => 'fcm_topic',
                'value' => '',
            ]);
        }
        if (!$this->businessSettings->where(['key' => 'fcm_project_id'])->first()) {
            $this->businessSettings->insert([
                'key' => 'fcm_project_id',
                'value' => '',
            ]);
        }
        if (!$this->businessSettings->where(['key' => 'push_notification_key'])->first()) {
            $this->businessSettings->insert([
                'key' => 'push_notification_key',
                'value' => '',
            ]);
        }

        return view('Admin.views.business-settings.fcm-config');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function updateFcm(Request $request): RedirectResponse
    {
        BusinessSetting::updateOrInsert(['key' => 'fcm_project_id'], [
            'value' => $request['fcm_project_id'],
        ]);

        BusinessSetting::updateOrInsert(['key' => 'push_notification_key'], [
            'value' => $request['push_notification_key'],
        ]);

        flash()->success(translate('Settings updated!'));
        return back();
    }

    /**
     * @param $business_key
     * @param $status_key
     * @param $default_message_key
     * @param $multi_lang_message_key
     * @param $request
     * @return void
     */
    private function updateOrInsertMessage($business_key, $status_key , $default_message_key, $multi_lang_message_key, $request): void
    {
        $status = $request[$status_key] == 1 ? 1 : 0;
        $message = $request[$default_message_key];

        $this->businessSettings->updateOrInsert(['key' => $business_key], [
            'value' => json_encode([
                'status' => $status,
                'message' => $message,
            ]),
        ]);

        $setting = $this->businessSettings->where('key', $business_key)->first();

        foreach ($request->lang as $index => $lang) {
            if ($lang === 'default') {
                continue;
            }
            $messageValue = $request[$multi_lang_message_key][$index - 1] ?? null;
            if ($messageValue !== null) {
                Translation::updateOrInsert(
                    [
                        'translationable_type' => 'App\Model\BusinessSetting',
                        'translationable_id' => $setting->id,
                        'locale' => $lang,
                        'key' => $multi_lang_message_key,
                    ],
                    ['value' => $messageValue]
                );
            }
        }
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function updateFcmMessages(Request $request): RedirectResponse
    {
        $this->updateOrInsertMessage('order_pending_message', 'pending_status','pending_message' ,'order_pending_message', $request);
        $this->updateOrInsertMessage('order_confirmation_msg', 'confirm_status','confirm_message' ,'order_confirmation_message', $request);
        $this->updateOrInsertMessage('order_processing_message', 'processing_status','processing_message' ,'order_processing_message', $request);
        $this->updateOrInsertMessage('out_for_delivery_message', 'out_for_delivery_status','out_for_delivery_message' ,'order_out_for_delivery_message', $request);
        $this->updateOrInsertMessage('order_delivered_message', 'delivered_status','delivered_message' ,'order_delivered_message', $request);
        $this->updateOrInsertMessage('delivery_boy_assign_message', 'delivery_boy_assign_status','delivery_boy_assign_message' ,'assign_deliveryman_message', $request);
        $this->updateOrInsertMessage('delivery_boy_start_message', 'delivery_boy_start_status','delivery_boy_start_message' ,'deliveryman_start_message', $request);
        $this->updateOrInsertMessage('delivery_boy_delivered_message', 'delivery_boy_delivered_status','delivery_boy_delivered_message' ,'deliveryman_delivered_message', $request);
        $this->updateOrInsertMessage('customer_notify_message', 'customer_notify_status','customer_notify_message' ,'customer_notification_message', $request);
        $this->updateOrInsertMessage('returned_message', 'returned_status','returned_message' ,'return_order_message', $request);
        $this->updateOrInsertMessage('failed_message', 'failed_status','failed_message' ,'failed_order_message', $request);
        $this->updateOrInsertMessage('canceled_message', 'canceled_status','canceled_message' ,'canceled_order_message', $request);
        $this->updateOrInsertMessage('deliveryman_order_processing_message', 'dm_order_processing_status','dm_order_processing_message' ,'deliveryman_order_processing_message', $request);
        $this->updateOrInsertMessage('add_fund_wallet_message', 'add_fund_status','add_fund_message' ,'add_fund_wallet_message', $request);
        $this->updateOrInsertMessage('add_fund_wallet_bonus_message', 'add_fund_bonus_status','add_fund_bonus_message' ,'add_fund_wallet_bonus_message', $request);

        flash()->success(translate('Message updated!'));
        return back();
    }

    /**
     * @return Application|Factory|View
     */
    public function mapApiSetting(): Factory|View|Application
    {
        return view('Admin.views.business-settings.map-api');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function mapApiStore(Request $request): \Illuminate\Http\RedirectResponse
    {
        BusinessSetting::updateOrInsert(['key' => 'map_api_server_key'], [
            'value' => $request['map_api_server_key'],
        ]);
        BusinessSetting::updateOrInsert(['key' => 'map_api_client_key'], [
            'value' => $request['map_api_client_key'],
        ]);
        flash()->success(translate('Map API updated successfully'));
        return back();
    }

    /**
     * @param Request $request
     * @return Application|Factory|View
     */
    public function recaptchaIndex(Request $request): Factory|View|Application
    {
        return view('Admin.views.business-settings.recaptcha-index');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function recaptchaUpdate(Request $request): \Illuminate\Http\RedirectResponse
    {
        BusinessSetting::updateOrInsert(['key' => 'recaptcha'], [
            'key' => 'recaptcha',
            'value' => json_encode([
                'status' => $request['status'],
                'site_key' => $request['site_key'],
                'secret_key' => $request['secret_key']
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        flash()->success(translate('Updated Successfully'));
        return back();
    }

    /**
     * @return Application|Factory|View
     */
    public function appSettingIndex(): Factory|View|Application
    {
        return View('Admin.views.business-settings.app-setting-index');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function appSettingUpdate(Request $request): RedirectResponse
    {
        if($request->platform == 'android')
        {
            BusinessSetting::updateOrInsert(['key' => 'play_store_config'], [
                'value' => json_encode([
                    'status' => $request['play_store_status'],
                    'link' => $request['play_store_link'],
                    'min_version' => $request['android_min_version'],

                ]),
            ]);

            flash()->success(translate('Updated Successfully for Android'));
            return back();
        }

        if($request->platform == 'ios')
        {
            BusinessSetting::updateOrInsert(['key' => 'app_store_config'], [
                'value' => json_encode([
                    'status' => $request['app_store_status'],
                    'link' => $request['app_store_link'],
                    'min_version' => $request['ios_min_version'],
                ]),
            ]);

            flash()->success(translate('Updated Successfully for IOS'));
            return back();
        }

        flash()->error(translate('Updated failed'));
        return back();
    }

    /**
     * @return Application|Factory|View
     */
    public function firebaseMessageConfigIndex(): Factory|View|Application
    {
        return View('Admin.views.business-settings.firebase-config-index');
    }

    public function firebaseMessageConfig(Request $request): \Illuminate\Http\RedirectResponse
    {
        BusinessSetting::updateOrInsert(['key' => 'firebase_message_config'], [
            'key' => 'firebase_message_config',
            'value' => json_encode([
                'apiKey' => $request['apiKey'],
                'authDomain' => $request['authDomain'],
                'projectId' => $request['projectId'],
                'storageBucket' => $request['storageBucket'],
                'messagingSenderId' => $request['messagingSenderId'],
                'appId' => $request['appId'],
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        self::firebaseMessageConfigFileGen();

        flash()->success(translate('Config Updated Successfully'));
        return back();
    }

    /**
     * @return void
     */
    function firebaseMessageConfigFileGen(): void
    {
        $config = Helpers_get_business_settings('firebase_message_config');
        $apiKey = $config['apiKey'] ?? '';
        $authDomain = $config['authDomain'] ?? '';
        $projectId = $config['projectId'] ?? '';
        $storageBucket = $config['storageBucket'] ?? '';
        $messagingSenderId = $config['messagingSenderId'] ?? '';
        $appId = $config['appId'] ?? '';

        try {
            $old_file = fopen("firebase-messaging-sw.js", "w") or die("Unable to open file!");

            $new_text = "importScripts('https://www.gstatic.com/firebasejs/8.10.0/firebase-app.js');\n";
            $new_text .= "importScripts('https://www.gstatic.com/firebasejs/8.10.0/firebase-messaging.js');\n";
            $new_text .= 'firebase.initializeApp({apiKey: "' . $apiKey . '",authDomain: "' . $authDomain . '",projectId: "' . $projectId . '",storageBucket: "' . $storageBucket . '", messagingSenderId: "' . $messagingSenderId . '", appId: "' . $appId . '"});';
            $new_text .= "\nconst messaging = firebase.messaging();\n";
            $new_text .= "messaging.setBackgroundMessageHandler(function (payload) { return self.registration.showNotification(payload.data.title, { body: payload.data.body ? payload.data.body : '', icon: payload.data.icon ? payload.data.icon : '' }); });";
            $new_text .= "\n";

            fwrite($old_file, $new_text);
            fclose($old_file);

        }catch (\Exception $exception) {}

    }

    /**
     * @return Application|Factory|View
     */
    public function socialMediaLogin(): Factory|View|Application
    {
        $apple = BusinessSetting::where('key', 'apple_login')->first();
        if (!$apple) {
            BusinessSetting::updateOrInsert(['key' => 'apple_login'], [
                'value' => '{"login_medium":"apple","client_id":"","client_secret":"","team_id":"","key_id":"","service_file":"","redirect_url":"","status":""}',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $apple = BusinessSetting::where('key', 'apple_login')->first();
        }
        $appleLoginService = json_decode($apple->value, true);

        return view('Admin.views.business-settings.social-media-login', compact('appleLoginService'));
    }

    /**
     * @param $status
     * @return JsonResponse
     */
    public function googleSocialLogin($status): \Illuminate\Http\JsonResponse
    {
        BusinessSetting::updateOrInsert(['key' => 'google_social_login'], [
            'value' => $status
        ]);
        return response()->json(['message' => 'Status updated']);
    }

    /**
     * @param $status
     * @return JsonResponse
     */
    public function facebookSocialLogin($status): \Illuminate\Http\JsonResponse
    {
        BusinessSetting::updateOrInsert(['key' => 'facebook_social_login'], [
            'value' => $status
        ]);
        return response()->json(['message' => 'Status updated']);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function updateAppleLogin(Request $request): RedirectResponse
    {
        $appleLogin = Helpers_get_business_settings('apple_login');

        if ($request->hasFile('service_file')) {
            $fileName = Helpers_upload('apple-login/', 'p8', $request->file('service_file'));
        }

        $data = [
            'value' => json_encode([
                'login_medium' => 'apple',
                'client_id' => $request['client_id'],
                'client_secret' => '',
                'team_id' => $request['team_id'],
                'key_id' => $request['key_id'],
                'service_file' => $fileName ?? $appleLogin['service_file'],
                'redirect_url' => '',
                'status' => $request->has('status') ? 1 : 0,
            ]),
        ];

        $this->businessSettings->updateOrInsert(['key' => 'apple_login'], $data);

        flash()->success(translate('settings updated!'));
        return back();
    }

    /**
     * @return Application|Factory|View
     */
    public function chatIndex(): Factory|View|Application
    {
        if (!$this->businessSettings->where(['key' => 'whatsapp'])->first()) {
            $this->businessSettings->insert([
                'key'   => 'whatsapp',
                'value' => json_encode([
                    'status'  => 0,
                    'number' => '',
                ]),
            ]);
        }

        if (!$this->businessSettings->where(['key' => 'telegram'])->first()) {
            $this->businessSettings->insert([
                'key'   => 'telegram',
                'value' => json_encode([
                    'status'  => 0,
                    'user_name' => '',
                ]),
            ]);
        }

        if (!$this->businessSettings->where(['key' => 'messenger'])->first()) {
            $this->businessSettings->insert([
                'key'   => 'messenger',
                'value' => json_encode([
                    'status'  => 0,
                    'user_name' => '',
                ]),
            ]);
        }

        return view('Admin.views.business-settings.chat-index');
    }


    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function updateChat(Request $request): \Illuminate\Http\RedirectResponse
    {
        BusinessSetting::updateOrInsert(['key' => 'whatsapp'], [
            'value' => json_encode([
                'status'  => $request['whatsapp_status'] == 1 ? 1 : 0,
                'number' => $request['whatsapp_number'],
            ]),
        ]);

        BusinessSetting::updateOrInsert(['key' => 'telegram'], [
            'value' => json_encode([
                'status'  => $request['telegram_status'] == 1 ? 1 : 0,
                'user_name' => $request['telegram_user_name'],
            ]),
        ]);

        BusinessSetting::updateOrInsert(['key' => 'messenger'], [
            'value' => json_encode([
                'status'  => $request['messenger_status'] == 1 ? 1 : 0,
                'user_name' => $request['messenger_user_name'],
            ]),
        ]);

        flash()->success(translate('Settings updated!'));
        return back();
    }
    

    /**
     * @return Application|Factory|View
     */
    public function firebaseOTPVerification(): Factory|View|Application
    {
        return view('Admin.views.business-settings.firebase-otp-verification');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function firebaseOTPVerificationUpdate(Request $request): RedirectResponse
    {
        BusinessSetting::updateOrInsert(['key' => 'firebase_otp_verification'], [
            'value' => json_encode([
                'status'  => $request->has('status') ? 1 : 0,
                'web_api_key' => $request['web_api_key'],
            ]),
        ]);

        if ($request->has('status')){
            foreach (['twilio','nexmo','2factor','msg91', 'signal_wire', 'alphanet_sms'] as $gateway) {
                $keep = AddonSetting::where(['key_name' => $gateway, 'settings_type' => 'sms_config'])->first();
                if (isset($keep)) {
                    $hold = $keep->live_values;
                    $hold['status'] = 0;
                    AddonSetting::where(['key_name' => $gateway, 'settings_type' => 'sms_config'])->update([
                        'live_values' => $hold,
                        'test_values' => $hold,
                        'is_active' => 0,
                    ]);
                }
            }
        }

        flash()->success(translate('updated_successfully'));
        return back();
    }
}
