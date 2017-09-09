<?php
namespace App\OpenLiga\Entities;

class Season
{
    public $name;
    public $rounds;

    public function __construct(array $seasonData)
    {
        $this->name = $seasonData['name'];
        $this->rounds = $seasonData['rounds'];
    }
}
