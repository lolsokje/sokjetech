<?php

namespace App\Console\Commands;

use App\Models\Circuit;
use App\Models\CircuitVariation;
use App\Models\EngineSeason;
use App\Models\Entrant;
use App\Models\Season;
use App\Models\Series;
use App\Models\Team;
use App\Models\Universe;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use stdClass;

class SeedSeasonCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'season:seed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clears the database and seeds an entire season including calendar, teams and drivers';


    protected ?User $user;
    protected array $stints;
    protected Universe $universe;
    protected Series $series;
    protected Season $season;

    private array $developmentEnvironments = [
        'local',
        'testing',
    ];

    private function setup(): void
    {
        $avatar = 'https://cdn.discordapp.com/avatars/136818922006511616/a_6ed1262b87fa88d80a8ffc991a1aaf36.gif';
        $this->user = User::firstOrCreate(
            ['discord_id' => '136818922006511616'],
            [
                'username' => 'lolsokje',
                'avatar' => $avatar,
                'is_admin' => true,
            ],
        );

        $this->stints = $this->getStintsData()->toArray();
        $this->universe = Universe::factory()->for($this->user)->create(['name' => 'SokjeVerse']);
        $this->series = Series::factory()->for($this->universe)->create(['name' => 'Formula One']);
        $this->season = Season::factory()->for($this->series)->create(['year' => 2022]);
    }

    public function handle(): int
    {
        if (! in_array(config('app.env'), $this->developmentEnvironments) && ! config('app.debug')) {
            $this->error('Not in a local development environment, aborting');

            return 1;
        }

        if (! $this->confirm('This will clear the entire database, are you sure you want to continue?')) {
            $this->info('Seeding aborted');

            return 1;
        }

        $this->call('migrate:fresh');

        $this->setup();
        $this->setupEngines();
        $this->setupCircuits();
        $this->setupTeams();
        $this->setupDrivers();

        $this->info('Database seeded');

        return 0;
    }

    private function setupEngines(): void
    {
        $this->getEngineData()->each(function (stdClass $engine) {
            $createdEngine = $this->series->engines()->create([
                'name' => $engine->name,
            ]);

            $this->season->engines()->create([
                'base_engine_id' => $createdEngine->id,
                'rebadge' => false,
                'name' => $engine->name,
            ]);
        });
    }

    private function setupCircuits(): void
    {
        $this->getCircuitData()->each(function (stdClass $circuit, int $key) {
            $createdCircuit = Circuit::create([
                'user_id' => $this->user->id,
                'name' => $circuit->circuit_name,
                'country' => $circuit->country,
            ]);

            $variation = CircuitVariation::factory()->for($createdCircuit)->create([
                'length' => $circuit->length,
                'base_laptime' => $circuit->base_laptime,
            ]);

            $createdRace = $this->season->races()->create([
                'circuit_id' => $createdCircuit->id,
                'circuit_variation_id' => $variation->id,
                'name' => $circuit->race_name,
                'order' => $key + 1,
            ]);

            foreach ($this->stints as $stint) {
                $stint['race_id'] = $createdRace->id;

                $createdRace->stints()->create($stint);
            }
        });
    }

    private function setupTeams(): void
    {
        $this->getTeamData()->each(function (stdClass $team) {
            $createdTeam = $this->createTeam($team);

            $engine = EngineSeason::where('name', $team->engine)->first();

            $this->createEntrant($createdTeam, $engine, $team);
        });
    }

    private function createTeam(stdClass $team): Team
    {
        return $this->universe->teams()->create([
            'full_name' => $team->full_name,
            'short_name' => $team->short_name,
            'team_principal' => $team->team_principal,
            'primary_colour' => $team->primary_colour,
            'secondary_colour' => $team->secondary_colour,
            'accent_colour' => $team->accent_colour,
            'country' => $team->country,
        ]);
    }

    private function createEntrant(Team $createdTeam, EngineSeason $engine, stdClass $team): void
    {
        $this->season->entrants()->create([
            'team_id' => $createdTeam->id,
            'engine_id' => $engine->id,
            'full_name' => $team->full_name,
            'short_name' => $team->short_name,
            'team_principal' => $team->team_principal,
            'primary_colour' => $team->primary_colour,
            'secondary_colour' => $team->secondary_colour,
            'accent_colour' => $team->accent_colour,
            'country' => $team->country,
        ]);
    }

    private function setupDrivers(): void
    {
        $this->getDriverData()->each(function (stdClass $driver) {
            $createdDriver = $this->universe->drivers()->create([
                'first_name' => $driver->first_name,
                'last_name' => $driver->last_name,
                'dob' => $driver->dob,
                'country' => $driver->country,
            ]);

            $entrant = Entrant::where('short_name', $driver->team)->first();
            $entrant->activeRacers()->create([
                'season_id' => $entrant->season->id,
                'driver_id' => $createdDriver->id,
                'number' => $driver->number,
                'active' => true,
            ]);
        });
    }

    private function getStintsData(): Collection
    {
        return $this->getDataFromFile('StintData', true);
    }

    private function getCircuitData(): Collection
    {
        return $this->getDataFromFile('CircuitData');
    }

    private function getDriverData(): Collection
    {
        return $this->getDataFromFile('DriverData');
    }

    private function getTeamData(): Collection
    {
        return $this->getDataFromFile('TeamData');
    }

    private function getEngineData(): Collection
    {
        return $this->getDataFromFile('EngineData');
    }

    private function getDataFromFile(string $fileName, ?bool $associative = false): Collection
    {
        $data = json_decode(file_get_contents(resource_path("json/$fileName.json")), $associative);

        return collect($data);
    }
}
