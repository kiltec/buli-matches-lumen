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
    private $seasonService;

    public function __construct(SeasonService $seasonDataService)
    {
        $this->seasonService = $seasonDataService;
    }

    public function index($year)
    {
        if($year < self::BL_FOUNDING_YEAR) {
            $year = Carbon::now()->year;
        }

        $season = $this->seasonService->getSeason($year);

        return view('matches.index', ['season' => $season]);
    }
}
