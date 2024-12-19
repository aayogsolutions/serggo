<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use App\Models\PaymentGateways;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class SMSModuleController extends Controller
{
     /**
     * @return Application|Factory|View
     */
    public function smsIndex(): View|Factory|Application
    {
        $dataValues = PaymentGateways::where('settings_type','sms_config')->get();
        return view('Admin.views.3rd_party.sms-index', compact('dataValues'));
    }

    /**
     * @param Request $request
     * @param $module
     * @return RedirectResponse
     */
    public function smsUpdate(Request $request, $module): RedirectResponse
    {
        if ($module == 'twilio') {
            $additionalData = [
                'status' => $request['status'],
                'sid' => $request['sid'],
                'messaging_service_sid' => $request['messaging_service_sid'],
                'token' => $request['token'],
                'from' => $request['from'],
                'otp_template' => $request['otp_template'],
            ];

        } elseif ($module == 'nexmo') {
            $additionalData = [
                'status' =>$request['status'],
                'api_key' => $request['api_key'],
                'api_secret' => $request['api_secret'],
                'token' =>null,
                'from' => $request['from'],
                'otp_template' => $request['otp_template'],
            ];

        } elseif ($module == '2factor') {
            $additionalData = [
                'status' => $request['status'],
                'api_key' => $request['api_key'],
            ];
        } elseif ($module == 'msg91') {
            $additionalData = [
                'status' => $request['status'],
                'template_id' => $request['template_id'],
                'auth_key' => $request['auth_key'],
            ];
        } elseif ($module == 'signal_wire') {
            $additionalData = [
                'status' => $request['status'],
                'project_id' => $request['project_id'],
                'token' => $request['token'],
                'space_url' => $request['space_url'],
                'from' => $request['from'],
                'otp_template' => $request['otp_template'],
            ];
        }elseif ($request['gateway'] == 'alphanet_sms') {
            $additionalData = [
                'status' => $request['status'],
                'api_key' => $request['api_key'],
                'otp_template' => $request['otp_template'],
            ];
        }

        $data= [
            'gateway' => $module ,
            'mode' =>  isset($request['status']) == 1  ?  'live': 'test'
        ];

        $credentials= json_encode(array_merge($data, $additionalData));

        DB::table('addon_settings')->updateOrInsert(['key_name' => $module, 'settings_type' => 'sms_config'], [
            'key_name' => $module,
            'live_values' => $credentials,
            'test_values' => $credentials,
            'settings_type' => 'sms_config',
            'mode' => isset($request['status']) == 1  ?  'live': 'test',
            'is_active' => isset($request['status']) == 1  ?  1: 0 ,
        ]);

        if ($request['status'] == 1) {
            foreach (['twilio','nexmo','2factor','msg91', 'signal_wire', 'alphanet_sms'] as $gateway) {
                if ($module != $gateway) {
                    // $keep = AddonSetting::where(['key_name' => $gateway, 'settings_type' => 'sms_config'])->first();
                    if (isset($keep)) {
                        $hold = $keep->live_values;
                        $hold['status'] = 0;
                        // AddonSetting::where(['key_name' => $gateway, 'settings_type' => 'sms_config'])->update([
                        //     'live_values' => $hold,
                        //     'test_values' => $hold,
                        //     'is_active' => 0,
                        // ]);
                    }
                }
            }

            // $firebaseOTP = Helpers::get_business_settings('firebase_otp_verification');

            // DB::table('business_settings')->updateOrInsert(['key' => 'firebase_otp_verification'], [
            //     'value' => json_encode([
            //         'status'  => 0,
            //         'web_api_key' => $firebaseOTP['web_api_key'],
            //     ]),
            // ]);
        }
        return back();
    }
}
