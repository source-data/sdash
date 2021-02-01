@extends('layouts.global')

@section('header-scripts')
<script src="{{ mix('js/main.js') }}" defer></script>
@endsection

@section('content')
<div class="sd-homepage">
    <div class="sd-title-container">
        <h1 class="sd-main-title">SDash</h1>
        <h2 class="sd-subtitle">The SmartFigures Dashboard</h2>
    </div>
    <div class="sd-image-credit">
        <strong>Image credit:</strong> Max C Richter et al. (2018) <em>Distinct in vivo roles of secreted APP ectodomain variants APPsα and APPsβ in regulation of spine density, synaptic plasticity, and cognition.</em> EMBOJ 37:e98335 <a style="color:#fff; text-decoration:underline;" target="_blank" href="https://doi.org/10.15252/embj.201798335">https://doi.org/10.15252/embj.201798335</a>
        <div>
        </div>
        @endsection