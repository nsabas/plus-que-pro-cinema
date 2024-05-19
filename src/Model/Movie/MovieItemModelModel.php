<?php

namespace App\Model\Movie;

class MovieItemModelModel
{
    public ?int $id;
    public ?string $posterPath;
    public ?string $overview;
    public string $title;
    public float $popularity;
    public int $voteCount;
    public $voteAverage;

}
