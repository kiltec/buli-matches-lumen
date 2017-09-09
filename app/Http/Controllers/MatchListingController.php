<?php

namespace App\Http\Controllers;

use App\OpenLiga\SeasonDataService;

class MatchListingController extends Controller
{
    /**
     * @var SeasonDataService
     */
    private $seasonDataService;

    public function __construct(SeasonDataService $seasonDataService)
    {
        $this->seasonDataService = $seasonDataService;
    }
    public function index()
    {

        $season = $this->seasonDataService->getAllMatchesForCurrentSeason();

        return view('all-matches.index', ['season' => $season]);
    }
}
