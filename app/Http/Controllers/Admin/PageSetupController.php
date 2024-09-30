<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\{Request,JsonResponse,RedirectResponse};
use App\Models\BusinessSetting;
use App\Models\SocialMedias;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\{Factory,View};
use Illuminate\Routing\Redirector;

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
        $termsAndConditions = $this->businessSettings->where(['key' => 'terms_and_conditions'])->first();
        if (!$termsAndConditions) {
            $this->businessSettings->insert([
                'key'   => 'terms_and_conditions',
                'value' => '',
            ]);
        }
        return view('Admin.views.pages_&_media.terms-and-conditions', compact('termsAndConditions'));
    }

    public function termsAndConditionsUpdate(Request $request): \Illuminate\Http\RedirectResponse
    {
        $this->businessSettings->where(['key' => 'terms_and_conditions'])->update([
            'value' => $request->tnc,
        ]);
        flash()->success(translate('Terms and Conditions updated!'));
        return back();
    }

    /**
     * @return Application|Factory|View
     */
    public function privacyPolicy(): Factory|View|Application
    {
        $data = $this->businessSettings->where(['key' => 'privacy_policy'])->first();
        if (!$data) {
            $data = [
                'key' => 'privacy_policy',
                'value' => '',
            ];
            $this->businessSettings->insert($data);
        }
        return view('Admin.views.pages_&_media.privacy-policy', compact('data'));
    }

    public function privacyPolicyUpdate(Request $request): \Illuminate\Http\RedirectResponse
    {
        $this->businessSettings->where(['key' => 'privacy_policy'])->update([
            'value' => $request->privacy_policy,
        ]);

        flash()->success(translate('Privacy policy updated!'));
        return back();
    }

    /**
     * @return Application|Factory|View
     */
    public function aboutUs(): Factory|View|Application
    {
        $data = $this->businessSettings->where(['key' => 'about_us'])->first();
        if (!$data) {
            $data = [
                'key' => 'about_us',
                'value' => '',
            ];
            $this->businessSettings->insert($data);
        }
        return view('Admin.views.pages_&_media.about-us', compact('data'));
    }

    public function aboutUsUpdate(Request $request): \Illuminate\Http\RedirectResponse
    {
        $this->businessSettings->where(['key' => 'about_us'])->update([
            'value' => $request->about_us,
        ]);

        flash()->success(translate('About us updated!'));
        return back();
    }

    /**
     * @return Application|Factory|View
     */
    public function faq(): Factory|View|Application
    {
        $data = $this->businessSettings->where(['key' => 'faq'])->first();
        if (!$data) {
            $data = [
                'key' => 'faq',
                'value' => '',
            ];
            $this->businessSettings->insert($data);
        }
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
        $data = $this->businessSettings->where(['key' => 'cancellation_policy'])->first();
        $status = $this->businessSettings->where(['key' => 'cancellation_policy_status'])->first();
        if (!$data) {
            $data = [
                'key' => 'cancellation_policy',
                'value' => '',
            ];
            $this->businessSettings->insert($data);
        }
        if (!$status) {
            $status = [
                'key' => 'cancellation_policy_status',
                'value' => 0,
            ];
            $this->businessSettings->insert($status);
        }
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
        $data = $this->businessSettings->where(['key' => 'refund_policy'])->first();
        $status = $this->businessSettings->where(['key' => 'refund_policy_status'])->first();
        if (!$data) {
            $data = [
                'key' => 'refund_policy',
                'value' => '',
            ];
            $this->businessSettings->insert($data);
        }
        if (!$status) {
            $status = [
                'key' => 'refund_policy_status',
                'value' => 0,
            ];
            $this->businessSettings->insert($status);
        }
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
        $data = $this->businessSettings->where(['key' => 'return_policy'])->first();
        $status = $this->businessSettings->where(['key' => 'return_policy_status'])->first();
        if (!$data) {
            $data = [
                'key' => 'return_policy',
                'value' => '',
            ];
            $this->businessSettings->insert($data);
        }

        if (!$status) {
            $status = [
                'key' => 'return_policy_status',
                'value' => 0,
            ];
            $this->businessSettings->insert($status);
        }
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

}
