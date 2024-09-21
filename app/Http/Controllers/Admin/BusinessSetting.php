<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessSetting as ModelsBusinessSetting;
use Illuminate\Http\Request;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\{Factory,View};

class BusinessSetting extends Controller
{
    /**
     * @return Application|Factory|View
     */
    public function termsAndConditions(): View|Factory|Application
    {
        $termsAndConditions = ModelsBusinessSetting::where(['key' => 'terms_and_conditions'])->first();
        if (!$termsAndConditions) {
            ModelsBusinessSetting::insert([
                'key'   => 'terms_and_conditions',
                'value' => '',
            ]);
        }
        return view('admin-views.business-settings.terms-and-conditions', compact('termsAndConditions'));
    }

    public function termsAndConditionsUpdate(Request $request): \Illuminate\Http\RedirectResponse
    {
        // $this->businessSettings->where(['key' => 'terms_and_conditions'])->update([
        //     'value' => $request->tnc,
        // ]);
        Flash()->success(translate('Terms and Conditions updated!'));
        return back();
    }

    /**
     * @return Application|Factory|View
     */
    public function privacyPolicy(): Factory|View|Application
    {
        // $data = $this->businessSettings->where(['key' => 'privacy_policy'])->first();
        // if (!$data) {
        //     $data = [
        //         'key' => 'privacy_policy',
        //         'value' => '',
        //     ];
        //     $this->businessSettings->insert($data);
        // }
        return view('admin-views.business-settings.privacy-policy', compact('data'));
    }

    public function privacyPolicyUpdate(Request $request): \Illuminate\Http\RedirectResponse
    {
        // $this->businessSettings->where(['key' => 'privacy_policy'])->update([
        //     'value' => $request->privacy_policy,
        // ]);

        Flash()->success(translate('Privacy policy updated!'));
        return back();
    }
}
