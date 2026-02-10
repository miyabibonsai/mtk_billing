<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class BillingController extends Controller
{
    public function showGenerateBilling()
    {
        return view('billings.generate');
    }

    public function generateBilling(Request $request)
    {
        $request->validate([
            'simcard_type' => 'required',
            'simcard_ids' => 'required',
            'date' => 'required|date'
        ]);
        $model = config('billings.types')[$request->simcard_type];
        if(!$model) abort( 412, "Invalid Simcard Type");
        $ids = explode(',', $request->simcard_ids);
        if(count($ids) > 10) abort(412 , "Exceeds maxiumn limits");
        $sims = app($model)->find($ids);
        if($sims->count() === 0) abort(404, "No Sim");
        foreach ($sims as $sim) {
            $sim->generateBilling(new Carbon($request->date));
        }
        return redirect()->back()->with('success', "Succcessfully generated for ". implode(', ',$sims->pluck('tel_no')->toArray()));
    }

    public function showOperation() {
        $types = [
            "simcard" => [
                'statuses' => ['OtaWait','MnpWait','unactive','active','pfd','deactivate']
            ],
            "datasim" => [
                'statuses' => ['otaWait','active','instock','deactivate']
            ],
            "rakuten" => [
                'statuses' => ['otaWait','activeWait','active','stop','deactivate','deWait','instock']
            ],
            "rakuten_call" => [
                'statuses' => ['OtaWait','MnpWait','unactive','active','pfd','deactivate','processing']
            ],
            "simcard_b" => [
                'statuses' => ['OtaWait','MnpWait','unactive','active','pfd','deactivate','processing','inactive']
            ]
        ];
        return view('operation.index',[
            'types' => $types
        ]);
    }

    public function generateMultipleBillings(Request $request)
    {
        $request->validate([
            'type' => ['required', 'in:simcard,simcard_b,rakuten,datasim,rakuten_call'],
            'normal' => ['required', 'boolean'],
        ]);
        if($request->normal) {
            Artisan::call("app:add-waiting $request->type");
            return redirect()->back()->with('success', "Sucessfully generated for $request->type");
        }
    }
}
