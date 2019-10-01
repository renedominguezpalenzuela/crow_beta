<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{

    /**
     * @Route("/", name="login")
     */
    public function index(Request $request, AuthenticationUtils $utils)
    {
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('dashboard');
        }
        $error = $utils->getLastAuthenticationError();
        $lastUsername = $utils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'error' => $error,
            'last_username' => $lastUsername,
        ]);
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout()
    {

    }

    /**
     * @Route("/admin/check_user_custom", name="check_user_custom")
     */
    public function customCheckUser()
    {
        if (!$this->getUser()->getActive()) {
            $this->addFlash('error', 'This user ' . $this->getUser() . 'is unabled!');

            return $this->redirectToRoute('logout');
        } else {
            $this->addFlash('success', 'Welcome ' . $this->getUser());
            if ($this->isGranted('ROLE_ADMIN')) {
                return $this->redirectToRoute('dashboard');
            }
            return $this->redirectToRoute('homepage');
        }
    }

    /**
     *
     * @param User $user
     * @Route("/reset-confirm/{security}", name="user_reset_password_confirm")
     */
    public function resetPasswordConfirm(\App\Entity\User $user)
    {
        $password = substr(sha1(md5(uniqid())), 0, 8);
        $user->setPassword($this->get('security.password_encoder')->encodePassword($user, $password));
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        $message = \Swift_Message::newInstance()
            ->setSubject('Reset Password')
            ->setFrom($this->container->get('mailer_user'))
            ->setTo($user->getEmail())
            ->setBody(
                $this->renderView(
                    'emails/reset_password_confirm.html.twig',
                    array('user' => $user, 'passwd' => $password)
                ),
                'text/html'
            );
        $this->get('mailer')->send($message);

        $this->addFlash('success', 'You reset your password, please looking for your email!');

        return $this->redirectToRoute('login');
    }

}
