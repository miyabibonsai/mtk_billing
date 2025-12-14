{{-- resources/views/admin/dashboard.blade.php --}}
@extends('adminlte::page')

@section('title', 'Admin Dashboard')

@section('content_header')
    <h1>Dashboard</h1>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title"></h5>
            <form>
                <div class="row">
                    <div class=" col-md-3">
                        <label for="month">Month</label>
                        <input type="month" class="form-control" name="month" id="month" value="{{ request('month') }}">
                    </div>

                    <div class=" col-md-3">
                        <label for="status">Status</label>
                        <select name="status" class="form-control" id="status">
                            <option value="">Default</option>
                            <option value="waiting" {{ request('status') == 'waiting' ? 'selected' : '' }}>Waiting</option>
                            <option value="done" {{ request('status') == 'done' ? 'selected' : '' }}>Done</option>
                            <option value="error" {{ request('status') == 'error' ? 'selected' : '' }}>Error</option>
                        </select>
                    </div>

                    <div class=" col-md-3">
                        <label for="simcard_type">Simcard Type</label>
                        <select name="simcard_type" class="form-control" id="simcard_type">
                            <option value="">Default</option>
                            <option value="simcard" {{ request('simcard_type') == 'simcard' ? 'selected' : '' }}>Simcard</option>
                            <option value="simcard_b" {{ request('simcard_type') == 'simcard_b' ? 'selected' : '' }}>Simcard B</option>
                            <option value="datasim" {{ request('simcard_type') == 'datasim' ? 'selected' : '' }}>Datasim</option>
                            <option value="rakuten_call" {{ request('simcard_type') == 'rakuten_call' ? 'selected' : '' }}>Rakuten Callsim</option>
                            <option value="rakuten" {{ request('simcard_type') == 'rakuten' ? 'selected' : '' }}>Rakuten Datasim</option>
                        </select>
                    </div>
                </div>
                <div class="d-flex justify-content-end">
                    <button class="btn btn-primary">Search</button>
                </div>
            </form>
        </div>

    </div>
@stop

@section('content')
    <table class="table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Status</th>
                <th>Simcard Type</th>
                <th>Tel No</th>
                <th>Plan</th>
                <th>Call Plan</th>
                <th>Previous Plan</th>
                <th>Previous Call Plan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($waitings as $waiting)
                <tr>
                    <td>{{$waiting->date}}</td>
                    <td>{{$waiting->status}}</td>
                    <td>{{$waiting->simcard_type}}</td>
                    <td>{{$waiting->tel_no}}</td>
                    <td>{{$waiting->plan}}</td>
                    <td>{{$waiting->callplan}}</td>
                    <td>{{$waiting->previous_plan}}</td>
                    <td>{{$waiting->previous_callplan}}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="d-flex justify-content-center">
        {{ $waitings->links() }}
    </div>
@stop

@section('css')
    {{-- Add any extra CSS here --}}
@stop

@section('js')
    {{-- Add any extra JS here --}}
@stop
