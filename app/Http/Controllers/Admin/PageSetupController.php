<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\{Request,JsonResponse,RedirectResponse};
use App\Models\BusinessSetting;
use App\Models\SocialMedias;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\{Factory,View};
use Illuminate\Routing\Redirector;

use function Pest\Laravel\json;

class PageSetupController extends Controller
{
    public function __construct(
        private BusinessSetting $businessSettings
    ){}

    /**
     * @return Application|Factory|View
     */
    public function termsAndConditions(): View|Factory|Application
    {
        if (!$this->businessSettings->where(['key' => 'terms_and_conditions'])->first()) {
            BusinessSetting::updateOrInsert(['key' => 'terms_and_conditions'], [
                'value' => 'No data available',
            ]);
        }

        if (!$this->businessSettings->where(['key' => 'terms_and_conditions_partner'])->first()) {
            BusinessSetting::updateOrInsert(['key' => 'terms_and_conditions_partner'], [
                'value' => 'No data available',
            ]);
        }

        if (!$this->businessSettings->where(['key' => 'terms_and_conditions_vendor'])->first()) {
            BusinessSetting::updateOrInsert(['key' => 'terms_and_conditions_vendor'], [
                'value' => 'No data available',
            ]);
        }

        $termsAndConditions = $this->businessSettings->where(['key' => 'terms_and_conditions'])->first();
        $termsAndConditionspartner = $this->businessSettings->where(['key' => 'terms_and_conditions_partner'])->first();
        $termsAndConditionsVendor = $this->businessSettings->where(['key' => 'terms_and_conditions_vendor'])->first();

        return view('Admin.views.pages_&_media.terms-and-conditions', compact('termsAndConditions', 'termsAndConditionspartner', 'termsAndConditionsVendor'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function termsAndConditionsUpdate(Request $request): \Illuminate\Http\RedirectResponse
    {
        $this->businessSettings->where(['key' => 'terms_and_conditions'])->update([
            'value' => $request->tnc,
        ]);
        flash()->success(translate('Terms and Conditions updated!'));
        return back();
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function termsAndConditionsPartnerUpdate(Request $request): \Illuminate\Http\RedirectResponse
    {
        $this->businessSettings->where(['key' => 'terms_and_conditions_partner'])->update([
            'value' => $request->tnc,
        ]);
        flash()->success(translate('Terms and Conditions of Partner updated!'));
        return back();
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function termsAndConditionsVendorUpdate(Request $request): \Illuminate\Http\RedirectResponse
    {
        $this->businessSettings->where(['key' => 'terms_and_conditions_vendor'])->update([
            'value' => $request->tnc,
        ]);
        flash()->success(translate('Terms and Conditions of Vendor updated!'));
        return back();
    }

    /**
     * @return Application|Factory|View
     */
    public function privacyPolicy(): Factory|View|Application
    {
        if (!$this->businessSettings->where(['key' => 'privacy_policy'])->first()) {
            BusinessSetting::updateOrInsert(['key' => 'privacy_policy'], [
                'value' => '',
            ]);
        }

        if (!$this->businessSettings->where(['key' => 'privacy_policy_partner'])->first()) {
            BusinessSetting::updateOrInsert(['key' => 'privacy_policy_partner'], [
                'value' => '',
            ]);
        }

        if (!$this->businessSettings->where(['key' => 'privacy_policy_vendor'])->first()) {
            BusinessSetting::updateOrInsert(['key' => 'privacy_policy_vendor'], [
                'value' => '',
            ]);
        }

        $data = json_decode($this->businessSettings->where(['key' => 'privacy_policy'])->first());
        $vendor_data = json_decode($this->businessSettings->where(['key' => 'privacy_policy_vendor'])->first());
        $partner_data = json_decode($this->businessSettings->where(['key' => 'privacy_policy_partner'])->first());
        
        return view('Admin.views.pages_&_media.privacy-policy', compact('data', 'vendor_data', 'partner_data'));
    }

    public function privacyPolicyUpdate(Request $request): \Illuminate\Http\RedirectResponse
    {
        $this->businessSettings->where(['key' => 'privacy_policy'])->update([
            'value' => $request->privacy_policy,
        ]);

        flash()->success(translate('Privacy policy updated!'));
        return back();
    }

    public function privacyPolicyVendorUpdate(Request $request): \Illuminate\Http\RedirectResponse
    {
        $this->businessSettings->where(['key' => 'privacy_policy_vendor'])->update([
            'value' => $request->privacy_policy,
        ]);

        flash()->success(translate('Vendor Privacy policy updated!'));
        return back();
    }

    public function privacyPolicyPartnerUpdate(Request $request): \Illuminate\Http\RedirectResponse
    {
        $this->businessSettings->where(['key' => 'privacy_policy_partner'])->update([
            'value' => $request->privacy_policy,
        ]);

        flash()->success(translate('Partner Privacy policy updated!'));
        return back();
    }

    /**
     * @return Application|Factory|View
     */
    public function aboutUs(): Factory|View|Application
    {
        if (!$this->businessSettings->where(['key' => 'about_us'])->first()) {
            BusinessSetting::updateOrInsert(['key' => 'about_us'], [
                'value' => '',
            ]);
        }

        if (!$this->businessSettings->where(['key' => 'about_us_partner'])->first()) {
            BusinessSetting::updateOrInsert(['key' => 'about_us_partner'], [
                'value' => '',
            ]);
        }

        if (!$this->businessSettings->where(['key' => 'about_us_vendor'])->first()) {
            BusinessSetting::updateOrInsert(['key' => 'about_us_vendor'], [
                'value' => '',
            ]);
        }
        
        $data = json_decode($this->businessSettings->where(['key' => 'about_us'])->first());
        $vendor_data = json_decode($this->businessSettings->where(['key' => 'about_us_vendor'])->first());
        $partner_data = json_decode($this->businessSettings->where(['key' => 'about_us_partner'])->first());
        
        return view('Admin.views.pages_&_media.about-us', compact('data', 'vendor_data', 'partner_data'));
    }

    public function aboutUsUpdate(Request $request): \Illuminate\Http\RedirectResponse
    {
        $this->businessSettings->where(['key' => 'about_us'])->update([
            'value' => $request->about_us,
        ]);

        flash()->success(translate('About us updated!'));
        return back();
    }

    public function aboutUsPartnerUpdate(Request $request): \Illuminate\Http\RedirectResponse
    {
        $this->businessSettings->where(['key' => 'about_us_partner'])->update([
            'value' => $request->about_us,
        ]);

        flash()->success(translate('Partner About us updated!'));
        return back();
    }

    public function aboutUsVendorUpdate(Request $request): \Illuminate\Http\RedirectResponse
    {
        $this->businessSettings->where(['key' => 'about_us_vendor'])->update([
            'value' => $request->about_us,
        ]);

        flash()->success(translate('Vendor About us updated!'));
        return back();
    }

    /**
     * @return Application|Factory|View
     */
    public function faq(): Factory|View|Application
    {
        if (!$this->businessSettings->where(['key' => 'faq'])->first()) {
            BusinessSetting::updateOrInsert(['key' => 'faq'], [
                'value' => '',
            ]);
        }
        $data = $this->businessSettings->where(['key' => 'faq'])->first();
        
        return view('Admin.views.pages_&_media.faq', compact('data'));
    }

    public function faqUpdate(Request $request): \Illuminate\Http\RedirectResponse
    {
        $this->businessSettings->where(['key' => 'faq'])->update([
            'value' => $request->faq,
        ]);

        flash()->success(translate('FAQ updated!'));
        return back();
    }

    /**
     * @return Application|Factory|View
     */
    public function cancellationPolicy(): Factory|View|Application
    {
        if (!$this->businessSettings->where(['key' => 'cancellation_policy'])->first()) {
            BusinessSetting::updateOrInsert(['key' => 'cancellation_policy'], [
                'value' => '',
            ]);
        }

        if (!$this->businessSettings->where(['key' => 'cancellation_policy_status'])->first()) {
            BusinessSetting::updateOrInsert(['key' => 'cancellation_policy_status'], [
                'value' => 1,
            ]);
        }
        $data = $this->businessSettings->where(['key' => 'cancellation_policy'])->first();
        $status = $this->businessSettings->where(['key' => 'cancellation_policy_status'])->first();
        
        return view('Admin.views.pages_&_media.cancellation-policy', compact('data', 'status'));
    }

    public function cancellationPolicyUpdate(Request $request): \Illuminate\Http\RedirectResponse
    {
        $this->businessSettings->where(['key' => 'cancellation_policy'])->update([
            'value' => $request->cancellation_policy,
        ]);

        flash()->success(translate('Cancellation Policy updated!'));
        return back();
    }

    public function cancellationPolicyStatus(Request $request): \Illuminate\Http\RedirectResponse
    {
        $this->businessSettings->where(['key' => 'cancellation_policy_status'])->update([
            'value' => $request->status,
        ]);
        flash()->success(translate('Status updated!'));
        return back();
    }

    /**
     * @return Application|Factory|View
     */
    public function refundPolicy(): Factory|View|Application
    {
        if (!$this->businessSettings->where(['key' => 'refund_policy'])->first()) {
            BusinessSetting::updateOrInsert(['key' => 'refund_policy'], [
                'value' => '',
            ]);
        }

        if (!$this->businessSettings->where(['key' => 'refund_policy_status'])->first()) {
            BusinessSetting::updateOrInsert(['key' => 'refund_policy_status'], [
                'value' => 1,
            ]);
        }
        $data = $this->businessSettings->where(['key' => 'refund_policy'])->first();
        $status = $this->businessSettings->where(['key' => 'refund_policy_status'])->first();
        
        return view('Admin.views.pages_&_media.refund-policy', compact('data', 'status'));
    }

    public function refundPolicyUpdate(Request $request): \Illuminate\Http\RedirectResponse
    {
        $this->businessSettings->where(['key' => 'refund_policy'])->update([
            'value' => $request->refund_policy,
        ]);

        flash()->success(translate('Refund Policy updated!'));
        return back();
    }

    public function refundPolicyStatus(Request $request): \Illuminate\Http\RedirectResponse
    {
        $this->businessSettings->where(['key' => 'refund_policy_status'])->update([
            'value' => $request->status,
        ]);
        flash()->success(translate('Status updated!'));
        return back();
    }

    /**
     * @return Application|Factory|View
     */
    public function returnPolicy(): Factory|View|Application
    {

        if (!$this->businessSettings->where(['key' => 'return_policy'])->first()) {
            BusinessSetting::updateOrInsert(['key' => 'return_policy'], [
                'value' => '',
            ]);
        }

        if (!$this->businessSettings->where(['key' => 'return_policy_status'])->first()) {
            BusinessSetting::updateOrInsert(['key' => 'return_policy_status'], [
                'value' => 1,
            ]);
        }
        $data = $this->businessSettings->where(['key' => 'return_policy'])->first();
        $status = $this->businessSettings->where(['key' => 'return_policy_status'])->first();
        
        return view('Admin.views.pages_&_media.return-policy', compact('data', 'status'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function returnPolicyUpdate(Request $request): \Illuminate\Http\RedirectResponse
    {
        $this->businessSettings->where(['key' => 'return_policy'])->update([
            'value' => $request->return_policy,
        ]);

        flash()->success(translate('Return Policy updated!'));
        return back();
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function returnPolicyStatus(Request $request): \Illuminate\Http\RedirectResponse
    {
        $this->businessSettings->where(['key' => 'return_policy_status'])->update([
            'value' => $request->status,
        ]);
        flash()->success(translate('Status updated!'));
        return back();
    }

    //start social media

     /**
     * @return Application|Factory|View
     */
    public function socialMedia(): Factory|View|Application
    {
        return view('Admin.views.pages_&_media.social-media');
    }

     
    /**
     * @param Request $request
     * @return JsonResponse|void
     */
    public function fetch(Request $request)
    {
        if ($request->ajax()) {
            $data = SocialMedias::orderBy('id', 'desc')->get();
            return response()->json($data);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function socialMediaStore(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            SocialMedias::updateOrInsert([
                'name' => $request->get('name'),
            ], [
                'name' => $request->get('name'),
                'link' => $request->get('link'),
            ]);

            return response()->json([
                'success' => 1,
            ]);

        } catch (\Exception $exception) {
            return response()->json([
                'error' => 1,
            ]);
        }

    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function socialMediaEdit(Request $request): \Illuminate\Http\JsonResponse
    {
        $data = SocialMedias::where('id', $request->id)->first();
        return response()->json($data);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function socialMediaUpdate(Request $request): \Illuminate\Http\JsonResponse
    {
        $socialMedia = SocialMedias::find($request->id);
        $socialMedia->name = $request->name;
        $socialMedia->link = $request->link;
        $socialMedia->save();
        return response()->json();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function socialMediaDelete(Request $request): \Illuminate\Http\JsonResponse
    {
        $socialMedia = SocialMedias::find($request->id);
        $socialMedia->delete();
        return response()->json();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function socialMediaStatusUpdate(Request $request): \Illuminate\Http\JsonResponse
    {
        SocialMedias::where(['id' => $request['id']])->update([
            'status' => $request['status'],
        ]);
        return response()->json([
            'success' => 1,
        ], 200);
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

        $google = BusinessSetting::where('key', 'google_social_login')->first();
        if (!$google) {
            BusinessSetting::updateOrInsert(['key' => 'google_social_login'], [
                'value' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $google = BusinessSetting::where('key', 'apple_login')->first();
        }
        $googleLoginService = json_decode($google->value, true);

        $facebook = BusinessSetting::where('key', 'facebook_social_login')->first();
        if (!$facebook) {
            BusinessSetting::updateOrInsert(['key' => 'facebook_social_login'], [
                'value' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $facebook = BusinessSetting::where('key', 'facebook_social_login')->first();
        }
        $facebookLoginService = json_decode($facebook->value, true);

        return view('Admin.views.3rd_party.social-media-login', compact('appleLoginService','googleLoginService','facebookLoginService'));
    }

}
