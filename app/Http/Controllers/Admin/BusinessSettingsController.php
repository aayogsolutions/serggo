<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\{Request,JsonResponse,RedirectResponse};
use App\Models\BusinessSetting;
use App\Models\PaymentGateways;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\{Factory,View};
use Illuminate\Routing\Redirector;
use App\Models\Branch;
use Illuminate\Support\Facades\Validator;

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

        if (!$this->businessSettings->where(['key' => 'partial_payment'])->first()) {
            BusinessSetting::updateOrInsert(['key' => 'partial_payment'],
            [
                'value' => 1,
            ]);
        }

        $partial_payment = Helpers_get_business_settings('partial_payment');

        return view('Admin.views.business-settings.index', compact('logo', 'favIcon', 'partial_payment'));
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

        if (!$this->businessSettings->where(['key' => 'refferal_info'])->first()) {
            BusinessSetting::updateOrInsert(['key' => 'refferal_info'], [
                'value' => json_encode([
                    "bonus" => 0,
                    "content" => '',
                ]),
            ]);
        }

        $value = json_decode($this->businessSettings->where(['key' => 'refferal_info'])->first()->value);

        return view('Admin.views.business-settings.refferal_income',compact('value'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function ReferralIncomeSetupUpdate(Request $request): RedirectResponse
    {
        $request->validate([
            'bonus' => 'required',
            'content' => 'required',
        ]);

        BusinessSetting::updateOrInsert(['key' => 'refferal_info'], [
            'value' => json_encode([
                "bonus" => $request->bonus,
                "content" => $request->content,
            ]),
        ]);

        flash()->success(translate('updated_successfully'));
        return back();
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
            $value = [];
            for ($i=0; $i < 14; $i++) { 
                $data = [
                    'minimum' => 0,
                    'maximum' => 0,
                    'charge' => 0
                ];
                array_push($value, $data);
            }
            
            BusinessSetting::updateOrInsert(['key' => 'delivery_management'], [
                'value' => json_encode($value),
            ]);
        }

        $config = json_decode($this->businessSettings->where(['key' => 'delivery_management'])->first()->value);
        
        return view('Admin.views.business-settings.delivery-fee',compact('config'));
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
        
        $request->validate([
            'minimum' => 'required',
            'maximum' => 'required',
            'charge' => 'required',
            'free_delivery_over_amount' => 'required',
        ],
        [
            'minimum.required' => translate('Minimum Kilometer is required while shipping method is active'),
            'maximum.required' => translate('Maximum Kilometer is required while shipping method is active'),
            'charge.required' => translate('Charge per Kilometer is required while shipping method is active'),
        ]);
        
        $value1 = [];
        foreach ($request->minimum as $key => $value) {
            $data = [
                'minimum' => $value,
                'maximum' => $request->maximum[$key],
                'charge' => $request->charge[$key]
            ];
            array_push($value1, $data);
        }

        BusinessSetting::updateOrInsert(['key' => 'delivery_management'], [
            'value' => json_encode($value1),
        ]);
        

        BusinessSetting::updateOrInsert(['key' => 'free_delivery_over_amount'], [
            'value' => $request['free_delivery_over_amount'],
        ]);
        // dd($request->all());
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
    public function firebaseMessageConfigIndex(): Factory|View|Application
    {
        if (!$this->businessSettings->where(['key' => 'firebase_message_config'])->first()) {
            BusinessSetting::updateOrInsert(['key' => 'firebase_message_config'], [
                'value' => json_encode([
                    'apiKey' => '',
                    'authDomain' => '',
                    'projectId' => '',
                    'storageBucket' => '',
                    'messagingSenderId' => '',
                    'appId' => '',
                ]),
            ]);
        }
        $firebasemessageconfig = Helpers_get_business_settings('firebase_message_config');
        return View('Admin.views.business-settings.firebase-config-index',compact('firebasemessageconfig'));
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
    public function CommissionSetup(): Factory|View|Application
    {
        if (!$this->businessSettings->where(['key' => 'vender_commission'])->first()) {
            BusinessSetting::updateOrInsert(['key' => 'vender_commission'], [
                'value' => 0,
            ]);
        }

        if (!$this->businessSettings->where(['key' => 'serviceman_commission'])->first()) {
            BusinessSetting::updateOrInsert(['key' => 'serviceman_commission'], [
                'value' => 0,
            ]);
        }

        $amount = Helpers_get_business_settings('vender_commission');
        $serviceman_commission = Helpers_get_business_settings('serviceman_commission');

        return view('Admin.views.business-settings.commission-setup-index',compact('amount','serviceman_commission'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function CommissionSetupUpdate(Request $request): RedirectResponse
    {
        BusinessSetting::updateOrInsert(['key' => 'vender_commission'], [
            'value' => $request['amount'],
        ]);

        BusinessSetting::updateOrInsert(['key' => 'serviceman_commission'], [
            'value' => $request['serviceman_commission'],
        ]);

        flash()->success(translate('commission_updated_successfully'));
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

        $data_values = PaymentGateways::whereIn('settings_type', ['payment_config'])->get();

        if (!$this->businessSettings->where(['key' => 'cash_on_delivery'])->first()) {
            BusinessSetting::updateOrInsert(['key' => 'cash_on_delivery'], [
                'value' => [
                    'status' => 0
                ],
            ]);
        }

        if (!$this->businessSettings->where(['key' => 'digital_payment'])->first()) {
            BusinessSetting::updateOrInsert(['key' => 'digital_payment'], [
                'value' => [
                    'status' => 1
                ],
            ]);
        }

        $cod = Helpers_get_business_settings('cash_on_delivery');
        $digital_payment = Helpers_get_business_settings('digital_payment');
        
        return view('Admin.views.3rd_party.payment-index', compact('data_values','cod','digital_payment'));
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
        } elseif ($name == 'razor_pay') {
            $payment = $this->businessSettings->where('key', 'razor_pay')->first();
            if (!isset($payment)) {
                BusinessSetting::insert([
                    'key' => 'razor_pay',
                    'value' => json_encode([
                        'status' => 1,
                        'razor_key' => '',
                        'razor_secret' => '',
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                BusinessSetting::where(['key' => 'razor_pay'])->update([
                    'key_name' => 'razor_pay',
                    'value' => json_encode([
                        'status' => $request['status'],
                        'razor_key' => $request['razor_key'],
                        'razor_secret' => $request['razor_secret'],
                    ]),
                    'updated_at' => now(),
                ]);
            }
        }

        flash()->success(translate('payment settings updated!'));
        return back();
    }

    public function paymentConfigUpdate(Request $request)
    {
        $validation = [
            'gateway' => 'required|in:razor_pay,phone_pay',
            'mode' => 'required|in:live,test'
        ];

        $additionalData = [];

        if ($request['gateway'] == 'phone_pay') {
            $additionalData = [
                'merchant_id' => 'required',
                'salt_key' => 'required',
                'salt_index' => 'required',
            ];
        } elseif ($request['gateway'] == 'razor_pay') {
            $additionalData = [
                'api_key' => 'required',
                'api_secret' => 'required',
            ];
        }

        $request->validate(array_merge($validation, $additionalData));
        
        $settings = PaymentGateways::where('key_name', $request['gateway'])->where('settings_type', 'payment_config')->first();
        
        if ($request->has('gateway_image')) {
            $gatewayImage = Helpers_upload('payment_modules/gateway_image/', $request->gateway_image->getClientOriginalExtension() , $request['gateway_image']);

            $payment_additional_data = [
                'gateway_title' => $request['gateway_title'],
                'gateway_image' => $gatewayImage,
            ];
        }
        else
        {
            $payment_additional_data = [
                'gateway_title' => $request['gateway_title'],
                'gateway_image' => json_decode($settings->additional_data)->gateway_image,
            ];
        }

        $data = $request->all();

        unset($data['_token']);
        unset($data['gateway_title']);

        if($settings->mode == 'live')
        {
            $live_value = json_encode(
                $data
            );

            $test_value = $settings->test_values;
        }else{
            $test_value = json_encode(
                $data
            );

            $live_value = $settings->live_values;
        }
        // dd($live_value, $test_value);
        PaymentGateways::where(['key_name' => $request['gateway'], 'settings_type' => 'payment_config'])->update([
            'key_name' => $request['gateway'],
            'live_values' => $live_value,
            'test_values' => $test_value,
            'settings_type' => 'payment_config',
            'mode' => $request['mode'],
            'additional_data' => json_encode($payment_additional_data),
        ]);

        flash()->success(translate('Payment gateway updated successfully'));
        return back();

    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function PaymentStatusUpdate(Request $request): JsonResponse
    {
        $request->validate([
            'gateway' => 'required|in:razor_pay,phone_pay',
        ]);

        try {
            if(PaymentGateways::where('key_name', $request['gateway'])->first()->is_active == 0)
            {
                PaymentGateways::where('key_name', $request['gateway'])->where('settings_type', 'payment_config')->update([
                    'is_active' => 1,
                ]);
            }
            else
            {
                PaymentGateways::where('settings_type', 'payment_config')->update([
                    'is_active' => 1,
                ]);
    
                PaymentGateways::where('key_name', $request['gateway'])->where('settings_type', 'payment_config')->update([
                    'is_active' => 0,
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json(['success' => false]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function PaymentModeUpdate(Request $request): JsonResponse
    {
        $request->validate([
            'gateway' => 'required|in:razor_pay,phone_pay',
            'mode' => 'required|in:live,test',
        ]);

        try {
            PaymentGateways::where('key_name', $request['gateway'])->where('settings_type', 'payment_config')->update([
                'mode' => $request['mode'],
            ]);
            
        } catch (\Throwable $th) {
            return response()->json(['success' => false]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * @return Application|Factory|View
     */
    public function mapApiSetting(): Factory|View|Application
    {
        return view('Admin.views.3rd_party.map-api');
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

    //3rd party

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
     * @return Application|Factory|View
     */
    public function firebaseOTPVerification(): Factory|View|Application
    {
        if (!$this->businessSettings->where(['key' => 'firebase_otp_verification'])->first()) {
            BusinessSetting::updateOrInsert(['key' => 'firebase_otp_verification'], [
                'value' => json_encode([
                    'status'  => 1,
                    'web_api_key' => '',
                ]),
            ]);
        }

        $firebaseOtp = Helpers_get_business_settings('firebase_otp_verification');
       
        return view('Admin.views.3rd_party.firebase-otp-verification',compact('firebaseOtp'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function firebaseOTPVerificationUpdate(Request $request): RedirectResponse
    {
        BusinessSetting::updateOrInsert(['key' => 'firebase_otp_verification'], [
            'value' => json_encode([
                'status'  => $request->has('status') ? 0 : 1,
                'web_api_key' => $request['web_api_key'],
            ]),
        ]);

        if ($request->has('status')){
            foreach (['twilio','nexmo','2factor','msg91', 'signal_wire', 'alphanet_sms','sms_to','akandit_sms','global_sms','releans','paradox','hubtel','viatech','019_sms'] as $gateway) {
                $keep = PaymentGateways::where(['key_name' => $gateway, 'settings_type' => 'sms_config'])->first();
                if (isset($keep)) {
                    $hold = json_decode($keep->live_values);
                    $hold->status = 1;
                    PaymentGateways::where(['key_name' => $gateway, 'settings_type' => 'sms_config'])->update([
                        'live_values' => json_encode($hold),
                        'test_values' => json_encode($hold),
                        'is_active' => 1,
                    ]);
                }
            }
        }

        flash()->success(translate('updated_successfully'));
        return back();
    }


     /**
     * @return Application|Factory|View
     */
    public function fcmIndex(): View|Factory|Application
    {
        if (!$this->businessSettings->where(['key' => 'order_place_message'])->first()) {
            $this->businessSettings->insert([
                'key'   => 'order_place_message',
                'value' => json_encode([
                    'status'  => 1,
                    'message' => '',
                ]),
            ]);
        }
        $order_place_message = Helpers_get_business_settings('order_place_message');

        if (!$this->businessSettings->where(['key' => 'order_approval_message'])->first()) {
            $this->businessSettings->insert([
                'key'   => 'order_approval_message',
                'value' => json_encode([
                    'status'  => 1,
                    'message' => '',
                ]),
            ]);
        }
        $order_approval_message = Helpers_get_business_settings('order_approval_message');

        if (!$this->businessSettings->where(['key' => 'order_rejected_message'])->first()) {
            $this->businessSettings->insert([
                'key'   => 'order_rejected_message',
                'value' => json_encode([
                    'status'  => 1,
                    'message' => '',
                ]),
            ]);
        }
        $order_rejected_message = Helpers_get_business_settings('order_rejected_message');

        if (!$this->businessSettings->where(['key' => 'order_processing_message'])->first()) {
            $this->businessSettings->insert([
                'key'   => 'order_processing_message',
                'value' => json_encode([
                    'status'  => 1,
                    'message' => '',
                ]),
            ]);
        }
        $order_processing_message = Helpers_get_business_settings('order_processing_message');

        if (!$this->businessSettings->where(['key' => 'out_for_delivery_message'])->first()) {
            $this->businessSettings->insert([
                'key'   => 'out_for_delivery_message',
                'value' => json_encode([
                    'status'  => 1,
                    'message' => '',
                ]),
            ]);
        }
        $out_for_delivery_message = Helpers_get_business_settings('out_for_delivery_message');

        if (!$this->businessSettings->where(['key' => 'order_delivered_message'])->first()) {
            $this->businessSettings->insert([
                'key'   => 'order_delivered_message',
                'value' => json_encode([
                    'status'  => 1,
                    'message' => '',
                ]),
            ]);
        }
        $order_delivered_message = Helpers_get_business_settings('order_delivered_message');

        if (!$this->businessSettings->where(['key' => 'delivery_boy_assign_message'])->first()) {
            $this->businessSettings->insert([
                'key'   => 'delivery_boy_assign_message',
                'value' => json_encode([
                    'status'  => 1,
                    'message' => '',
                ]),
            ]);
        }
        $delivery_boy_assign_message = Helpers_get_business_settings('delivery_boy_assign_message');

        if (!$this->businessSettings->where(['key' => 'customer_notify_message'])->first()) {
            $this->businessSettings->insert([
                'key' => 'customer_notify_message',
                'value' => json_encode([
                    'status' => 1,
                    'message' => '',
                ]),
            ]);
        }
        $customer_notify_message = Helpers_get_business_settings('customer_notify_message');

        if (!$this->businessSettings->where(['key' => 'returned_message'])->first()) {
            $this->businessSettings->insert([
                'key' => 'returned_message',
                'value' => json_encode([
                    'status' => 1,
                    'message' => '',
                ]),
            ]);
        }
        $returned_message = Helpers_get_business_settings('returned_message');
        
        if (!$this->businessSettings->where(['key' => 'failed_message'])->first()) {
            $this->businessSettings->insert([
                'key' => 'failed_message',
                'value' => json_encode([
                    'status' => 1,
                    'message' => '',
                ]),
            ]);
        }
        $failed_message = Helpers_get_business_settings('failed_message');
        
        if (!$this->businessSettings->where(['key' => 'canceled_message'])->first()) {
            $this->businessSettings->insert([
                'key' => 'canceled_message',
                'value' => json_encode([
                    'status' => 1,
                    'message' => '',
                ]),
            ]);
        }
        $canceled_message = Helpers_get_business_settings('canceled_message');

        if (!$this->businessSettings->where(['key' => 'deliveryman_order_processing_message'])->first()) {
            $this->businessSettings->insert([
                'key' => 'deliveryman_order_processing_message',
                'value' => json_encode([
                    'status' => 1,
                    'message' => '',
                ]),
            ]);
        }
        $deliveryman_order_processing_message = Helpers_get_business_settings('deliveryman_order_processing_message');

        if (!$this->businessSettings->where(['key' => 'add_fund_wallet_message'])->first()) {
            $this->businessSettings->insert([
                'key' => 'add_fund_wallet_message',
                'value' => json_encode([
                    'status' => 1,
                    'message' => '',
                ]),
            ]);
        }
        $add_fund_wallet_message = Helpers_get_business_settings('add_fund_wallet_message');

        if (!$this->businessSettings->where(['key' => 'add_fund_wallet_bonus_message'])->first()) {
            $this->businessSettings->insert([
                'key' => 'add_fund_wallet_bonus_message',
                'value' => json_encode([
                    'status' => 1,
                    'message' => '',
                ]),
            ]);
        }
        $add_fund_wallet_bonus_message = Helpers_get_business_settings('add_fund_wallet_bonus_message');

        return view('Admin.views.3rd_party.fcm-index',
            compact('order_place_message',
            'order_approval_message',
            'order_rejected_message',
            'order_processing_message',
            'out_for_delivery_message',
            'order_delivered_message',
            'delivery_boy_assign_message',
            'customer_notify_message',
            'returned_message',
            'failed_message',
            'canceled_message',
            'deliveryman_order_processing_message',
            'add_fund_wallet_message',
            'add_fund_wallet_bonus_message'
        ));
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

        return view('Admin.views.3rd_party.fcm-config');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function updateFcmMessages(Request $request): RedirectResponse
    {
        // dd($request->all());
        $this->updateOrInsertMessage('order_place_message', 'place_status','place_message', $request);
        $this->updateOrInsertMessage('order_approval_message', 'approval_status','approval_message', $request);
        $this->updateOrInsertMessage('order_rejected_message', 'rejected_status','rejected_message', $request);
        $this->updateOrInsertMessage('order_processing_message', 'processing_status','processing_message' , $request);
        $this->updateOrInsertMessage('out_for_delivery_message', 'out_for_delivery_status','out_for_delivery_message' , $request);
        $this->updateOrInsertMessage('order_delivered_message', 'delivered_status','delivered_message' , $request);
        $this->updateOrInsertMessage('delivery_boy_assign_message', 'delivery_boy_assign_status','delivery_boy_assign_message' , $request);
        $this->updateOrInsertMessage('customer_notify_message', 'customer_notify_status','customer_notify_message' , $request);
        $this->updateOrInsertMessage('returned_message', 'returned_status','returned_message' , $request);
        $this->updateOrInsertMessage('failed_message', 'failed_status','failed_message' , $request);
        $this->updateOrInsertMessage('canceled_message', 'canceled_status','canceled_message' , $request);
        // $this->updateOrInsertMessage('deliveryman_order_processing_message', 'dm_order_processing_status','dm_order_processing_message' , $request);
        $this->updateOrInsertMessage('add_fund_wallet_message', 'add_fund_status','add_fund_message' , $request);
        $this->updateOrInsertMessage('add_fund_wallet_bonus_message', 'add_fund_bonus_status','add_fund_bonus_message' , $request);

        flash()->success(translate('Message updated!'));
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
    private function updateOrInsertMessage($business_key, $status_key , $default_message_key, $request): void
    {
        
        $status = $request[$status_key] == 'on' ? 0 : 1;
        $message = $request[$default_message_key];

        $this->businessSettings->updateOrInsert(['key' => $business_key], [
            'value' => json_encode([
                'status' => $status,
                'message' => $message,
            ]),
        ]);

        $setting = $this->businessSettings->where('key', $business_key)->first();
      
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
    

    
}
