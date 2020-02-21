@extends('layouts.global')

@section('content')
<div class="container">
    <div class="row pt-4">
        <div class="col-sm">
            <h1>{{ $title }}</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-sm">
            Created by <strong>{{ $user["firstname"] }} {{ $user["surname"]}}</strong>, {{ $user["department_name"]}}, {{ $user["institution_name"]}}, {{ $user["institution_address"]}}
        </div>
    </div>
    <div class="row py-4">
        <div class="card w-75">
            <div class="card-body">
                <h5 class="card-title">Card title</h5>
                <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
                <a href="#" class="btn btn-primary">Button</a>
            </div>
        </div>

    </div>

</div>
{{ var_dump($user) }}
{{ var_dump($tags) }}
{{ var_dump($files) }}
@endsection