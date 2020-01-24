@extends('layouts.global')

@section('content')
    <div class="container pt-5">
        <div class="row">
            <div class="col-sm">
                <h1 class="pb-2">Group Successfully Joined</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-sm">
                <p>You have joined the group <strong>"{{ $group->name }}"</strong>.</p>
                <p>Panels added to this group will now be visible on your <a href="/dashboard">Dashboard</a>.</p>
                <p>Visit the group directly: <a href="/dashboard/group/{{$group->id}}">{{ $group->name }}</a>.</p>
            </div>
        </div>
    </div>
@endsection
