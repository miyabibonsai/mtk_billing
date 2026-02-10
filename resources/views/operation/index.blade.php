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
    @foreach($types as $type => $config)
                <div class="card mb-3">
                    <div class="card-header fw-bold">{{ ucfirst($type) }}</div>

                    <div class="card-body">
                    {{-- @foreach($config['statuses'] as $status) --}}
                        <form class="billing-form mb-2"
                            data-type="{{ $type }}">

                        <div class="row align-items-center">
                            {{-- <div class="col-md-3">
                            <span class="badge bg-secondary">{{ $status }}</span>
                            </div> --}}

                            <div class="col-md-3">
                            <input type="hidden" name="normal" value="1">
                            <span class="text-success">Normal</span>
                            </div>

                            <div class="col-md-3">
                            <button type="button"
                                    class="btn btn-sm btn-outline-warning advanced-btn">
                                Advanced
                            </button>
                            </div>

                            <div class="col-md-3">
                            <button type="submit" class="btn btn-sm btn-primary">
                                Generate
                            </button>
                            </div>
                        </div>

                        {{-- Advanced section (hidden) --}}
                        <div class="advanced-section mt-3 d-none">
                            <input type="hidden" name="normal" value="0">

                            <div class="mb-2">
                            <label>Status</label>
                            <select name="override_status" class="form-select form-control">
                                @foreach($config['statuses'] as $s)
                                <option value="{{ $s }}">{{ $s }}</option>
                                @endforeach
                            </select>
                            </div>

                            <div class="dynamic-columns"></div>

                            <button type="button"
                                    class="btn btn-sm btn-outline-secondary add-column">
                            + Add Column
                            </button>
                        </div>

                        </form>
                    {{-- @endforeach --}}
                    </div>
                </div>
                @endforeach


@stop

@section('css')
    {{-- Add any extra CSS here --}}
@stop

@section('js')
    <script>
    $(document).on('click', '.advanced-btn', function () {
        const form = $(this).closest('form');
        form.find('.advanced-section').toggleClass('d-none');
    });

    $(document).on('click', '.add-column', function () {
    const container = $(this).siblings('.dynamic-columns');

    const index = container.children().length;

    const html = `
      <div class="row g-3 mb-2 column-row">
        <div class="col-md-3">
          <input type="text"
                 name="columns[${index}][key]"
                 class="form-control"
                 placeholder="column name">
        </div>
        <div class="col-md-1">
            <select class="form-control">
                <option> = </option>
                <option> > </option>
                <option> < </option>
            </select>
        </div>
        <div class="col-md-3">
          <input type="text"
                 name="columns[${index}][value]"
                 class="form-control"
                 placeholder="value">
        </div>
        <div class="col-md-2">
          <button type="button"
                  class="btn btn-danger btn-sm remove-column">×</button>
        </div>
      </div>
    `;

    container.append(html);
});

$(document).on('click', '.remove-column', function () {
    $(this).closest('.column-row').remove();
});

</script>

@stop
