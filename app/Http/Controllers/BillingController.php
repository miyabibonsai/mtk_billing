<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

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
}
