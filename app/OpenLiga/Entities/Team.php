<?php
namespace App\OpenLiga\Entities;

class Team
{
    public $name;

    public function __construct(array $teamData)
    {
        $this->name = $teamData['name'];
    }
}
