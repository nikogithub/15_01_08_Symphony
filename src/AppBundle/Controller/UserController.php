<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;

use AppBundle\Entity\User;
use AppBundle\Form\RegistrationType;          // pour le formulaire enregistrement
use Symfony\Component\Security\Core\Security; // pour le formulaire de logIn

class UserController extends Controller
{   
     /**
     * @Route("/login", name="login_route")
     */
    public function loginAction(Request $request)
    {
        $session = $request->getSession();

        // get the login error if there is one
        if ($request->attributes->has(Security::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(
                Security::AUTHENTICATION_ERROR
            );
        } elseif (null !== $session && $session->has(Security::AUTHENTICATION_ERROR)) {
            $error = $session->get(Security::AUTHENTICATION_ERROR);
            $session->remove(Security::AUTHENTICATION_ERROR);
        } else {
            $error = '';
        }

        // last username entered by the user
        $lastUsername = (null === $session) ? '' : $session->get(Security::LAST_USERNAME);

        return $this->render(
            'user/login.html.twig',
            array(
                // last username entered by the user
                'last_username' => $lastUsername,
                'error'         => $error,
            )
        );
    }

    /**
     * @Route("/login_check", name="login_check")
     */
    public function loginCheckAction(){}
    
    /**
    * @Route("/logout", name="logout")
    */
    public function loginOutAction(){}
   
    /**
     * @Route("/register", name="register")
     */
    public function registreAction(Request $request)
    {   //on cree un utilisateur vide 
        $user = new User();
        
        // en recupere une instance de notre form
        //ce form est associe à l'utilisateur vide
        $registrationForm = $this->createForm(new RegistrationType(), $user);
        
        //traiter le form
        $registrationForm->handleRequest( $request);
        
        if ($registrationForm->isValid()){
            //hydrate le autres propriete de notre User

                // generer le salt de facon aleatoire 
                $salt = md5(uniqid());
                $user->setSalt($salt);
                
                // generer un token
                $token = md5(uniqid());
                $user->setToken($token);
                
                // hacher le mot de passe avec hachage "sha512" , 5000 fois
//                dump($user);
//                die();
                $encoder = $this->container->get('security.password_encoder');
                $encoded = $encoder->encodePassword($user, $user->getPassword());
                $user->setPassword($encoded);
                
//               die($encoded);
            // date d'inscription et date demodification
                $user->setDateRegistered(new \DateTime() );
                $user->setDateModified(new \DateTime() );
                
            // assigne toujours le role aux utilisateurs du front-office
                $user->setRoles( array("ROLE_USER"));
            
            // sauvegarder le User en BDD
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();
            dump($user);
        }
        
        // on shoot le formulaire à twig (on n'oublie pas le createView)
       $params = array (
                "registrationForm"=>$registrationForm->createView()
        );
        return $this->render('user/register.html.twig', $params);
        
    }
}
