<?php

namespace App\Http\Controllers;

use App\Models\mobile\WaitingBillingGenerateSim;
use Illuminate\Http\Request;

class WaitingBillingController extends Controller
{
    public function index() {
        $waitings = WaitingBillingGenerateSim::when(request('status'), function($query) {
            $query->where('status', request('status'));
        })->when(request('simcard_type') && array_key_exists(request('simcard_type'), config('billings.types')), function($query) {
            $query->where('simcard_type', config('billings.types')[request('simcard_type')]);
        })->when(request('month'), function($query) {
            $query->whereMonth('date', explode('-', request('month'))[1])->whereYear('date', explode('-', request('month'))[0]);
        })->latest()->paginate(10);
        return view('waiting-billings.index', [
            'waitings' => $waitings
        ]);
    }


}
