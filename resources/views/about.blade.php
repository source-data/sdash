@extends('layouts.global')

@section('content')
<div class="container">
    <div class="row" style="margin: 50px auto;">
        <div class="col-md-8">
            <h2 class="pb-3 mb-4 border-bottom">
                About
            </h2>
            <div>
                <h3>Aims</h3>
                <blockquote>
                    <p><strong>SDash is an online open source platform for sharing of scientific results among collaborators.</strong></p>
                </blockquote>
                <hr/>
                <p>
                    SDash enables scientists to generate and share <em>SmartFigures</em> that link a scientific figure to the underlying source data
                    and structured machine-readable metadata. Users can manage their SmartFigures to share them with groups of colleagues or
                    make them public to share with the whole scientific community. Users can comment and discuss initiating
                    an early scientific dissemination of results.
                </p>
                <p>SDash provides:</p>
                <ul>
                    <li>an <strong>intuitive</strong> way of sharing scientific results</li>
                    <li><strong>direct</strong> access to underlying data for in-depth analysis</li>
                    <li>a <strong>rapid</strong> mechanism to disseminate scientific results</li>
                    <li>a <strong>painless</strong> route for organizing results and data</li>
                </ul>
            </div>
            <div>
                <h3>How does it work?</h3>
                <p>
                    Scientists have their own personal workspace where they upload their result figures by a simple drag &amp; drop.
                    Research results and underlying data are easily organized by linking them with local or remote data files,
                    computer scripts, and protocols. By creating or participating in groups of collaborating peers, researchers
                    can control the visibility of their content. Online commenting opens the interdisciplinary dialogue to exchange ideas,
                    promote critical debates and provides a way for early dissemination of findings ultimately prepare your figures for publication.
                </p>
                <p><em>Learn more: <a href="https://elifesciences.org/labs/9d062a9b/smartfigures-dashboard-and-editor-tools-for-quick-and-easy-research-communication">
                    SmartFigures Dashboard and Editor: tools for quick and easy research communication</a></em></p>
            </div>
        </div>

        <aside class="col-md-4">
            <div class="p-3">
                <p class="font-italic">SDash is developed by <a href="https://sourcedata.embo.org/">SourceData</a></p>
            </div>
            <div class="card" style="margin-bottom: 1rem;">
                <div class="card-body">
                    <h5 class="card-title">Do you have any questions?</h5>
                    <h6 class="card-subtitle mb-2 text-muted">Contact</h6>
                    <p class="card-text">
                        <strong>Hannah Sonntag</strong><br/>
                        SDash and SourceData Coordinator<br/>
                        <a href="mailto:hannah.sonntag@embo.org">hannah.sonntag@embo.org</a>
                    </p>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title" style="margin: 0;">Our partners</h5>
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><a href="https://www.biologie.hu-berlin.de/en/gruppenseiten-en/sfb1315">SFB 1315 - Mechanisms and disturbances in memory consolidation</a></li>
                    <li class="list-group-item"><a href="https://www.sib.swiss/">SIB Swiss Institute of Bioinformatics</a></li>
                    <li class="list-group-item"><a href="#">Substance</a></li>
                </ul>
            </div>
        </aside>
    </div>
</div>
@endsection