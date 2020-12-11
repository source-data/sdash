@extends('layouts.global')

@section('header-scripts')
<script src="{{ mix('js/main.js') }}" defer></script>
@endsection

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Waiting to verify your email address</div>

                <div class="card-body">
                    <div class="alert alert-warning" role="alert">
                        We've sent you an email with a link to click. Please follow the link to verify your email address.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection