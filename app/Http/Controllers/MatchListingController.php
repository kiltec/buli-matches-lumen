<?php

namespace App\Http\Controllers;

use App\OpenLiga\SeasonService;

class MatchListingController extends Controller
{
    /**
     * @var SeasonService
     */
    private $seasonDataService;

    public function __construct(SeasonService $seasonDataService)
    {
        $this->seasonDataService = $seasonDataService;
    }
    public function index($year)
    {
        $season = $this->seasonDataService->getSeason($year);

        return view('all-matches.index', ['season' => $season]);
    }
}
