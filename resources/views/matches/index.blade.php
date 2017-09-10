@extends('layouts.app')
@section('content')
        <div class="row">
            <div class="col-lg-12">
                <h1>{{ $season->name }}</h1>
                <ul id="match-days" class="list-group">
                    @foreach($season->rounds as $round)
                    <li class="list-group-item">
                        <h2>{{ $round->name }}</h2>
                        <ul id="match-day">
                            @foreach($round->matches as $match)
                            <li>
                                {{ $match->dateTime }}
                                {{ $match->team1->name }} vs. {{ $match->team2->name }}
                                @if($match->finished)
                                    {{ $match->results->finalScore->pointsTeam1 }}:{{ $match->results->finalScore->pointsTeam2 }}
                                @else
                                    -:-
                                @endif
                            </li>
                            @endforeach
                        </ul>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
@endsection
