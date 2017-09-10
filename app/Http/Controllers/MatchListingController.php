<?php

namespace App\Http\Controllers;

use App\OpenLiga\SeasonService;
use Carbon\Carbon;

class MatchListingController extends Controller
{
    const BL_FOUNDING_YEAR = 1963;
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
        if(!is_int($year) || $year < self::BL_FOUNDING_YEAR) {
            $year = Carbon::now()->year;
        }

        $season = $this->seasonDataService->getSeason($year);

        return view('all-matches.index', ['season' => $season]);
    }
}
