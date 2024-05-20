<?php

namespace App\Controller\Admin;

use App\Entity\Movie;
use App\Repository\MovieRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private MovieRepository $movieRepository
    )
    {
    }

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $topTrendingMovies = $this->movieRepository->findAll();

         return $this->render(
             'admin/dashboard/dashboard.html.twig',
             [
                 'movies' => $topTrendingMovies
             ]
         );
    }

    #[Route('/admin/movie/{id}', name: 'movie.details')]
    public function movieDetails(Movie $movie): Response
    {
        return $this->render(
            'admin/dashboard/movies/details.html.twig',
            [
                'movie' => $movie
            ]
        );
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Plus Que Pro Cinema');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Movies', 'fas fa-list', Movie::class);
    }
}
