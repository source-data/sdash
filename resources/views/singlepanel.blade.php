@extends('layouts.global')

@section('header-scripts')
<script src="{{ mix('js/main.js') }}" defer></script>
@endsection

@section('content')
<div class="container">
    <div class="row pt-4">
        <div class="col-sm">
            <h1>{{ $panel["title"] }}</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-sm">
            <ul class="sd-single-panel--authors list-unstyled list-inline mb-1">
                @foreach($authors as $author)
                @if($author["role"]==='corresponding')
                <li class="sd-single-panel--author list-inline-item">
                    <strong>{{ $author["firstname"] }} {{ $author["surname"] }}
                        @if($author["department_name"] && $author["institution_name"]) ({{$author["department_name"]}}, {{$author["institution_name"]}})@endif</strong><sup>*</sup>@if(!$loop->last),
                    @endif
                </li>
                @else
                <li class="sd-single-panel--author list-inline-item">
                    {{ $author["firstname"] }} {{ $author["surname"] }}@if($author["department_name"] && $author["institution_name"]) ({{$author["department_name"]}}, {{$author["institution_name"]}})@endif @if(!$loop->last),
                    @endif
                </li>
                @endif
                @endforeach
            </ul>
            <div><strong><sup>*</sup> Corresponding author</strong></div>
        </div>
    </div>
    <div class="row py-4">
        <div class="col-sm">
            <figure class="card">
                <div class="card-body sd-single-panel--image-wrapper">
                    <img class="sd-single-panel--image" @if($token)src="/panels/{{ $panel["id"] }}/image?token={{ $token }}" @else src="/panels/{{ $panel["id"] }}/image" @endif alt="{{$panel["title"]}} image">
                </div>
                @if ( !empty($panel["caption"]) )
                <div class="card-body">
                    <p class="card-text">
                        {{ $panel["caption"] }}
                    </p>
                </div>
                @endif
                <div class="card-body">
                    <p class="card-text">
                    <div class="row">
                        @if(!empty($panel["files"]->toArray()))
                        <div class="col-md">

                            <h4>Sources</h4>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th scope="col">Category</th>
                                        <th scope="col">Filename / URL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Attached Files</td>
                                        <td>
                                            <ul class="list-unstyled">
                                                @foreach($panel["files"] as $file)
                                                @if($file["type"]==="file")
                                                <li class="sd-file-list"><a @if($token)href="/files/{{$file["id"]}}?token={{$token}}" @else href="/files/{{$file["id"]}}" @endif>{{ $file["original_filename"] }}</a>@if($file["description"]): {{ $file["description"]}} @endif</li>
                                                @endif
                                                @endforeach
                                            </ul>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>External Resources</td>
                                        <td>
                                            <ul class="list-unstyled">
                                                @foreach($panel["files"] as $file)
                                                @if($file["type"]==="url")
                                                <li><a href="{{$file["url"]}}">{{ $file["url"] }}</a>@if($file["description"]) - {{ $file["description"]}} @endif</li>
                                                @endif
                                                @endforeach
                                            </ul>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        @endif

                        <div class="col-md">
                            @if ( !empty($tags) )
                            <h4>Keywords</h4>
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
                                        <td>Instruments / Methods</td>
                                        <td>
                                            @foreach($tags["methods"] as $item)
                                            <span class="badge badge-info">{{ $item->content }}</span>
                                            @endforeach
                                        </td>
                                    </tr>
                                    @endif
                                    @if( isset($tags["interventions"]) )
                                    <tr>
                                        <td>Controlled Variables</td>
                                        <td>
                                            @foreach($tags["interventions"] as $item)
                                            <span class="badge badge-danger">{{ $item->content }}</span>
                                            @endforeach
                                        </td>
                                    </tr>
                                    @endif
                                    @if( isset($tags["assays"]) )
                                    <tr>
                                        <td>Measured Variables</td>
                                        <td>
                                            @foreach($tags["assays"] as $item)
                                            <span class="badge badge-primary">{{ $item->content }}</span>
                                            @endforeach
                                        </td>
                                    </tr>
                                    @endif
                                    @if( isset($tags["other"]) )
                                    <tr>
                                        <td>General Keywords</td>
                                        <td>
                                            @foreach($tags["other"] as $item)
                                            <span class="badge badge-secondary">{{ $item->content }}</span>
                                            @endforeach
                                        </td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                            @endif
                        </div>
                    </div>
                    </p>
                    <p class="card-text">
                    <h4>Download Panel</h4>
                    <ul>
                        <li><a target="_blank" @if($token) href="/panels/{{$panel["id"]}}/zip?token={{$token}}" @else href="/panels/{{$panel["id"]}}/zip" @endif>Archive containing all files (.zip)</a></li>
                        <li><a target="_blank" @if($token) href="/panels/{{$panel["id"]}}/dar?token={{$token}}" @else href="/panels/{{$panel["id"]}}/dar" @endif>SmartFigure Editor document (.smartfigure)</a></li>
                        <li><a target="_blank" @if($token) href="/panels/{{$panel["id"]}}/pdf?token={{$token}}" @else href="/panels/{{$panel["id"]}}/pdf" @endif>Adobe Acrobat Reader file (.pdf)</a></li>
                        <li><a target="_blank" @if($token) href="/panels/{{$panel["id"]}}/powerpoint?token={{$token}}" @else href="/panels/{{$panel["id"]}}/powerpoint" @endif>Microsoft Powerpoint slide (.pptx)</a></li>
                        @if($panel["image"])
                        <li><a target="_blank" @if($token) href="/panels/{{$panel["id"]}}?token={{$token}}" @else href="/panels/{{$panel["id"]}}" @endif>Original image file (MIME type: {{$panel["image"]["mime_type"]}})</a></li>
                        @endif
                    </ul>

                    </p>

                </div>
                @if($panel["is_public"])
                <div class="card-body">
                    <p class="card-text">&copy; 2021 The Authors. This figure is licensed under the terms of the
                        <a href="https://creativecommons.org/licenses/by/4.0/">Creative Commons Attribution License</a>,
                        which permits use, distribution and reproduction in any medium, provided the original work is properly cited.
                    </p>
                </div>
                @endif
                <ul class="sd-single-panel-update-details list-group list-group-flush">
                    <li class="list-group-item list-group-item-secondary"><Strong>Created:</Strong> {{$panel["created_at"]->format("d M Y h:m e")}} | <strong>Last updated:</strong> {{$panel["updated_at"]->format("d M Y h:m e")}}</li>
                </ul>
            </figure>
        </div>
    </div>
</div>
@endsection