@extends('layouts.vertical', ['title' => 'Notification Blast'])

@section('content')
<div class="container-fluid">
    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">Notification Blast</h4>
        </div>

        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="{{ route('root') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Notification Blast</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Welcome to the Dashboard</h4>
                    <p class="card-text">This is your dashboard where you can manage your application.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script-bottom')
@endsection