@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <h1></a></h1>
            <div class="row">
                <div class="col-lg-12">
                    <div class="jumbotron">
                        <h1 class="display-3">Bundesliga Matches</h1>
                        <p class="lead">
                            A small project to explore Lumen and the <a href="https://www.openligadb.de/">OpenLigaDB.de
                                API</a> and to hone my skills.
                        </p>
                        <hr class="my-4">
                        <p>
                            Explore the Bundesliga Matches!
                        </p>
                        <p class="lead">
                            <a class="btn btn-primary btn-lg" href="{{ route('upcoming-matches') }}" role="button">Upcoming</a>
                            <a class="btn btn-primary btn-lg" href="{{ route('all-matches', ['year' => 2017]) }}" role="button">All*</a>
                        </p>
                    </div>
                    <div class="col-lg-6 text-muted pull-right">* Edit the year in the URL to visit the corresponding season</div>
                </div>
            </div>
        </div>
    </div>
@endsection
