@extends('layouts.app')
@section('content')
        <div class="row">
            <div class="col-lg-12">
                <h1>{{ $teamRatioList->name }}</h1>
                @if($teamRatioList->teamRatios->isNotEmpty())
                <table id="#win-loss-ratios" class="table col-lg-12">
                    <thead>
                    <tr>
                        <th>Team</th>
                        <th>Win %</th>
                        <th>Wins</th>
                        <th>Losses</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($teamRatioList->teamRatios as $teamRatio)
                    <tr>
                        <td>{{ $teamRatio->team->name }}</td>
                        <td>{{ $teamRatio->ratio }}</td>
                        <td>{{ $teamRatio->wins }}</td>
                        <td>{{ $teamRatio->losses }}</td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
                @endif
            </div>
        </div>
@endsection
