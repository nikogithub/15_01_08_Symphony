<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;

use AppBundle\Entity\User;
use AppBundle\Form\RegistrationType;          // pour le formulaire enregistrement
use Symfony\Component\Security\Core\Security; // pour le formulaire de logIn

class MoviesController extends Controller
{   
     /**
     * @Route("/movie", name="showAllMovies")
     * Le paramètre "page" est optionnel. Sa valeur, s'il n'est pas présent, est de 1
     */
    public function MoviesAction(Request $request)
    {
        //pour faire un SELECT, on utilise le repository de notre entité
        $moviesRepository = $this->getDoctrine()->getRepository("AppBundle:Movie");

        $movies = $moviesRepository->findBy(array(), array("year" => "DESC"),50,0);
        
        $moviesNumber = $moviesRepository->countAll();
        
        $params = array(
            "movies" => $movies, 
            "moviesNumber" => $moviesNumber
        );
        //on n'oublie pas de passer le tableau en 2e paramètre
        return $this->render("movie/list_movies.html.twig", $params);
    }

  /**
     * @Route("/movie/{id}", name="movieDetails")
     */
    public function detailsAction($id)
    {
        $movieRepo = $this->getDoctrine()->getRepository("AppBundle:Movie");
        $movie = $movieRepo->find($id);
         $params = array(
            "movie" => $movie, 
        );
         return $this->render("movie/movie.html.twig", $params);
    }
}
