<?php
namespace Tests\Feature;

use App\OpenLiga\Entities\Match;
use App\OpenLiga\Entities\EmptyMatchList;
use App\OpenLiga\Entities\Team;
use App\OpenLiga\SeasonService;
use Illuminate\Support\Collection;
use Mockery;
use Tests\TestCase;

/**
 * @group tdd
 */
class ViewUpcomingMatchesTest extends TestCase
{
    /**
     * @var Mockery\Mock
     */
    private $seasonService;

    public function setUp()
    {
        parent::setUp();

        $this->seasonService = Mockery::mock(SeasonService::class);
        $this->app->instance(SeasonService::class, $this->seasonService);
    }

    /**
     * @test
     */
    public function user_views_list_when_no_upcoming_matches_available()
    {
        $this->seasonService
            ->shouldReceive('getUpcomingMatches')
            ->andReturn(
                new EmptyMatchList()
            )->once();

        $response = $this->get('/upcoming-matches');

        $response->assertResponseOk();
        $response->assertSee('Upcoming Matches');
        $response->assertSee('No matches available.');
        $response->assertDontSee('#upcoming-matches');
        $response->assertDontSee('-:-');
    }
}
