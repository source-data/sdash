@extends('layouts.global')

@section('content')
<div class="container">
    <div class="row pt-4">
        <div class="col-sm">
            <h1>{{ $panel["title"] }}</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-sm">
            Created by <strong>{{ $panel["user"]["firstname"] }} {{ $panel["user"]["surname"]}}</strong>, {{ $panel["user"]["department_name"]}}, {{ $panel["user"]["institution_name"]}}, {{ $panel["user"]["institution_address"]}}
        </div>
    </div>
    <div class="row py-4">
        <div class="card w-75">
            <div class="card-body">
                <img src="/panels/{{ $panel["id"] }}/image?token={{ $token }}" alt="{{$panel["title"]}} image">
            </div>
        </div>

    </div>

</div>
{{ var_dump( $panel["user"]) }}
{{ var_dump( $panel["tags"]) }}
{{ var_dump( $panel["files"]) }}
@endsection