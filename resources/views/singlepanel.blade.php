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
        <div class="col-sm">
            <figure class="card">
                <div class="card-body sd-single-panel--image-wrapper">
                    <img class="sd-single-panel--image" src="/panels/{{ $panel["id"] }}/image?token={{ $token }}" alt="{{$panel["title"]}} image">
                </div>
                @if ( !empty($panel["caption"]) )
                <div class="card-body">
                    <p class="card-text">
                        {{ $panel["caption"] }}
                    </p>
                </div>
                @endif
                @if ( !empty($tags) )
                <div class="card-body">
                    <p class="card-text">
                        <div class="row">
                            <div class="col-md">

                                <h4>Structured Tags</h4>
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">Role</th>
                                            <th scope="col">Tags</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if( isset($tags["methods"]) )
                                        <tr>
                                            <td>Methods</td>
                                            <td>
                                                @foreach($tags["methods"] as $item)
                                                <span class="badge badge-info">{{ $item->content }}</span>
                                                @endforeach
                                            </td>
                                        </tr>
                                        @endif
                                        @if( isset($tags["interventions"]) )
                                        <tr>
                                            <td>Interventions</td>
                                            <td>
                                                @foreach($tags["interventions"] as $item)
                                                <span class="badge badge-danger">{{ $item->content }}</span>
                                                @endforeach
                                            </td>
                                        </tr>
                                        @endif
                                        @if( isset($tags["assays"]) )
                                        <tr>
                                            <td>Assays</td>
                                            <td>
                                                @foreach($tags["assays"] as $item)
                                                <span class="badge badge-primary">{{ $item->content }}</span>
                                                @endforeach
                                            </td>
                                        </tr>
                                        @endif
                                        @if( isset($tags["other"]) )
                                        <tr>
                                            <td>Other Tags</td>
                                            <td>
                                                @foreach($tags["other"] as $item)
                                                <span class="badge badge-secondary">{{ $item->content }}</span>
                                                @endforeach
                                            </td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md">
                                <h4>Supplementary Materials</h4>

                            </div>
                        </div>
                    </p>


                </div>
                @endif
                <ul class="sd-single-panel-update-details list-group list-group-flush">
                    <li class="list-group-item list-group-item-secondary"><Strong>Created:</Strong> {{$panel["created_at"]->format("d M Y h:m:s")}} | <strong>Last updated:</strong> {{$panel["updated_at"]->format("d M Y h:m:s")}}</li>
                </ul>
            </figure>
        </div>
    </div>
</div>
@endsection