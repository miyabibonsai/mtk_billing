{{-- resources/views/admin/dashboard.blade.php --}}
@extends('adminlte::page')

@section('title', 'Admin Dashboard')

@section('content_header')
    <h1>Generate Billing</h1>
    @if(session()->has('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session()->has('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif


@stop

@section('content')
     <div class="card">
        <div class="card-body">
            <h5 class="card-title"></h5>
            <form method="POST" action="{{route('generate-billing')}}">
                @csrf
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="row">
                    <div class=" col-md-12 mb-3">
                        <label for="date">Date</label>
                        <input type="date" class="form-control" name="date" id="date">
                    </div>
                    <div class=" col-md-12 mb-3">
                        <label for="simcard_type">Simcard Type</label>
                        <select name="simcard_type" class="form-control" id="simcard_type">
                            <option value="">Default</option>
                            <option value="simcard" >Simcard</option>
                            <option value="simcard_b" >Simcard B</option>
                            <option value="datasim">Datasim</option>
                            <option value="rakuten_call" >Rakuten Callsim</option>
                            <option value="rakuten">Rakuten Datasim</option>
                        </select>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="text">Simcard IDs (Use ',' for multiple ids)</label>
                        <input type="text" class="form-control" name="simcard_ids" id="text">
                    </div>
                </div>
                <div class="d-flex justify-content-center">
                    <button class="btn btn-primary">Generate</button>
                </div>
            </form>
        </div>

    </div>
@stop

@section('css')
    {{-- Add any extra CSS here --}}
@stop

@section('js')
    {{-- Add any extra JS here --}}
@stop
