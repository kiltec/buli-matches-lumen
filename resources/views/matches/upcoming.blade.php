@extends('layouts.app')
@section('content')
            <div class="row">
                <div class="col-lg-12">
                    <h1>Upcoming Matches</h1>

                    <h2>{{ $matchList->infoText }}</h2>

                    <ul id="upcoming-matches" class="list-group">
                        @foreach($matchList->matches as $match)
                            <li class="list-group-item">
                                {{ $match->dateTime }}
                                {{ $match->team1->name }} vs. {{ $match->team2->name }}
                                -:-
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
@endsection
