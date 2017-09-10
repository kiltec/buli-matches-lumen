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
    public function index()
    {

        $season = $this->seasonDataService->getSeason();

        return view('all-matches.index', ['season' => $season]);
    }
}
