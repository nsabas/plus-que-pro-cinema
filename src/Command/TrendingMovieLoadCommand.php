<?php

namespace App\Command;

use App\Entity\Movie;
use App\Manager\RequestManager;
use App\ServicePath\MovieRequest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:trending:movie:load',
    description: 'Add a short description for your command',
)]
class TrendingMovieLoadCommand extends Command
{
    private RequestManager $requestManager;
    private EntityManagerInterface $entityManager;

    public function __construct(
        RequestManager $requestManager,
        EntityManagerInterface $entityManager
    )
    {
        parent::__construct();
        $this->requestManager = $requestManager;
        $this->entityManager = $entityManager;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $topTrendingMovies = $this->requestManager->getResource(
                MovieRequest::GET_MOVIE_TOP_TRENDING_WEEK,
                [
                    'query' => ['page' => 1],
                ]
            );

            $progressBar = new ProgressBar($output, count($topTrendingMovies->results));

            $results = $topTrendingMovies->results;

            $progressBar->start();

            foreach ($results as $movie) {
                $movieDetails = $this->requestManager->getResource(
                    MovieRequest::GET_MOVIE_DETAILS,
                    [],
                    [
                        '{movie_id}' => $movie->id,
                    ]
                );

                $newMovie = new Movie($movie->title);
                $newMovie->setDescription($movieDetails->overview);
                $newMovie->setPosterUrl($movieDetails->posterPath);

                $this->entityManager->persist($newMovie);
                $progressBar->advance();
            }
            $this->entityManager->flush();
        } catch (\Exception) {
            // log exception
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
