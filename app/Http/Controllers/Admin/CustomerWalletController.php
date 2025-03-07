<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\CentralLogics\CustomerLogic;
use App\CentralLogics\Helpers;
use App\Models\BusinessSetting;
use App\Models\WalletTranscation;
use App\Traits\HelperTrait;
use App\Models\User;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CustomerWalletController extends Controller
{
    use HelperTrait;
    public function __construct(
        private User $user,
        private BusinessSetting $businessSetting,
        private WalletTranscation $walletTransaction
    ){}


    /**
     * @return Application|Factory|View|RedirectResponse
     */
    public function addFundView(): View|Factory|RedirectResponse|Application
    {
        $all_user = $this->user->status()->get();

        return view('Admin.views.customer.wallet.add-fund',compact('all_user'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function addFund(Request $request): RedirectResponse
    {
        $request->validate([
            'customer_id' => 'required',
            'amount' => 'required|numeric|min:.01',
            'referance' => 'nullable',
        ]);

        $customer = User::find($request->customer_id);
        $customer->wallet_balance += $request->amount;
        $customer->save();

        $transactions = new WalletTranscation();
        $transactions->user_id = $request->customer_id;
        $transactions->transactions_id = Helpers_generate_transction_id();
        $transactions->credit = $request->amount;
        $transactions->debit = 0;
        $transactions->transactions_type = "Add_fund_by_admin";
        $transactions->reference = $request->referance ?? 'Add_fund_by_admin';
        $transactions->balance = $customer->wallet_balance;
        $transactions->save();

        flash()->success(translate('Add Fund successfully!'));
        return back();
    }

    /**
     * @return Application|Factory|View|RedirectResponse
     */
    public function DeductFundView(): View|Factory|RedirectResponse|Application
    {
        $all_user = $this->user->status()->get();

        return view('Admin.views.customer.wallet.deduct-fund',compact('all_user'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function DeductFund(Request $request): RedirectResponse
    {
        $request->validate([
            'customer_id' => 'required',
            'amount' => 'required|numeric|min:.01',
            'referance' => 'nullable',
        ]);

        $customer = User::find($request->customer_id);
        $customer->wallet_balance -= $request->amount;
        $customer->save();

        $transactions = new WalletTranscation();
        $transactions->user_id = $request->customer_id;
        $transactions->transactions_id = Helpers_generate_transction_id();
        $transactions->credit = 0;
        $transactions->debit = $request->amount;
        $transactions->transactions_type = "Deduct_fund_by_admin";
        $transactions->reference = $request->referance ?? 'Deduct_fund_by_admin';
        $transactions->balance = $customer->wallet_balance;
        $transactions->save();

        flash()->success(translate('Deduct Fund successfully!'));
        return back();
    }

    /**
     * @param Request $request
     * @return Factory|View|Application
     */
    public function report(Request $request): View|Factory|Application
    {
        $data = $this->walletTransaction
            ->selectRaw('sum(credit) as total_credit, sum(debit) as total_debit')
            ->when(($request->from && $request->to),function($query)use($request){
                $query->whereBetween('created_at', [$request->from.' 00:00:00', $request->to.' 23:59:59']);
            })
            ->when($request->transaction_type, function($query)use($request){
                $query->where('transaction_type',$request->transaction_type);
            })
            ->when($request->customer_id, function($query)use($request){
                $query->where('user_id',$request->customer_id);
            })
            ->get();

        $transactions = $this->walletTransaction->with('transaction')
            ->when(($request->from && $request->to),function($query)use($request){
                $query->whereBetween('created_at', [$request->from.' 00:00:00', $request->to.' 23:59:59']);
            })
            ->when($request->transaction_type, function($query)use($request){
                $query->where('transaction_type',$request->transaction_type);
            })
            ->when($request->customer_id, function($query)use($request){
                $query->where('user_id',$request->customer_id);
            })
            ->latest()
            ->paginate(Helpers_getPagination());
       
        $all_user = $this->user->status()->get();
        return view('Admin.views.customer.wallet.report', compact('data','transactions','all_user'));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    // public function getCustomers(Request $request): JsonResponse
    // {
    //     $key = explode(' ', $request['q']);
    //     $data = $this->user->where(function ($q) use ($key) {
    //             foreach ($key as $value) {
    //                 $q->orWhere('name', 'like', "%{$value}%")
    //                     ->orWhere('number', 'like', "%{$value}%");
    //             }
    //         })
    //         ->limit(8)
    //         ->get([DB::raw('id, CONCAT(name, " (", number ,")") as text')]);
        
    //     if($request->all) $data[]=(object)['id'=>false, 'text'=>translate('all')];

    //     return response()->json($data);
    // }
}
