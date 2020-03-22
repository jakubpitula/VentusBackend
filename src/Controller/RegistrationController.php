<?php

namespace App\Controller;

use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Form\Factory\FactoryInterface;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\UserBundle\Controller\RegistrationController as BaseController;
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Intervention\Image\ImageManagerStatic as Image;
use Symfony\Component\Form\FormInterface;

/**
 * Controller managing the registration.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 * @author Christophe Coevoet <stof@notk.org>
 */
class RegistrationController extends BaseController
{
    private $eventDispatcher;
    private $formFactory;
    private $userManager;
    private $tokenStorage;
    private $encoder;
    private $em;

    public function __construct (EntityManagerInterface $em, UserPasswordEncoderInterface $encoder, EventDispatcherInterface $eventDispatcher, FactoryInterface $formFactory, UserManagerInterface $userManager, TokenStorageInterface $tokenStorage)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->formFactory = $formFactory;
        $this->userManager = $userManager;
        $this->tokenStorage = $tokenStorage;
        $this->encoder = $encoder;
        $this->em = $em;
    }

    private function processForm(Request $request, FormInterface $form)
    {
        $data = json_decode($request->getContent(), true);
        $clearMissing = $request->getMethod() != 'PATCH';

        $form->submit($data, $clearMissing);
    }

    public function registerAction(Request $request)
    {
        $user = new User();
        $user->setEnabled(true);

        $event = new GetResponseUserEvent($user, $request);
        $this->eventDispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }
        $this->processForm($request, $form);

        if(!$form->isValid()){
            dd((string)$form->getErrors(true,false));
        }

        $data = $request->request->all();
        $files = $request->files->all();
        
        $isEmailUnique = isset($data['email']) ? $this->userManager->findUserByEmail($data['email']) : null;

        if(!isset($data['password'])) return new JsonResponse(['error' => 'Password not set'], 400);
        if(!isset($data['email'])) return new JsonResponse(['error' => 'Email not set'], 400);
        if(!isset($data['gender'])) return new JsonResponse(['error' => 'Gender not set'], 400);
        if(!isset($data['location'])) return new JsonResponse(['error' => 'Location not set'], 400);
        if(!isset($data['first_name'])) return new JsonResponse(['error' => 'First_name not set'], 400);
        if(!isset($data['birthday'])) return new JsonResponse(['error' => 'Birthday not set'], 400);
        if(!isset($data['messenger'])) return new JsonResponse(['error' => 'Messenger not set'], 400);

        if(strlen($data['password']) < 8) return new JsonResponse(['error' => 'Password too short'], 400); 
        if(!filter_var(trim($data['messenger']), FILTER_VALIDATE_URL)) return new JsonResponse(['error' => 'Invalid link'], 400);
        if(!filter_var(trim($data['email']), FILTER_VALIDATE_EMAIL)) return new JsonResponse(['error' => 'Invalid email'], 400);

        if($isEmailUnique!==null) return new JsonResponse(['error' => 'Email already exists'], 400);

        $user->setFirstName($data['first_name']);
        $user->setEmail($data['email']);
        $user->setGender($data['gender']);
        $user->setLocation($data['location']);
        $user->setPassword($this->encoder->encodePassword($user, $data['password']));
        $user->setBirthday($data['birthday']);
        $user->setMessenger($data['messenger']);
        if(isset($files['picture'])){
            // $image = Image::make($files['picture']);
            // $image->resize(300,300);
            // $image->save();
            
            $user->setPictureFile($files['picture']);
        } 
        
        $this->em->persist($user);
        $this->em->flush();

        if (null === $response = $event->getResponse()) {
            $response = new JsonResponse([], 200);
        }

        $this->eventDispatcher->dispatch(FOSUserEvents::REGISTRATION_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

        return $response;
    }

    /**
     * Tell the user to check their email provider.
     */
    public function checkEmailAction(Request $request)
    {
        $email = $request->getSession()->get('fos_user_send_confirmation_email/email');

        if (empty($email)) {
            return new RedirectResponse($this->generateUrl('fos_user_registration_register'));
        }

        $request->getSession()->remove('fos_user_send_confirmation_email/email');
        $user = $this->userManager->findUserByEmail($email);

        if (null === $user) {
            return new RedirectResponse($this->container->get('router')->generate('fos_user_security_login'));
        }

        return $this->render('@FOSUser/Registration/check_email.html.twig', array(
            'user' => $user,
        ));
    }

    /**
     * Receive the confirmation token from user email provider, login the user.
     *
     * @param Request $request
     * @param string  $token
     *
     * @return Response
     */
    public function confirmAction(Request $request, $token)
    {
        $userManager = $this->userManager;

        $user = $userManager->findUserByConfirmationToken($token);

        if (null === $user) {
            throw new NotFoundHttpException(sprintf('The user with confirmation token "%s" does not exist', $token));
        }

        $user->setConfirmationToken(null);
        $user->setEnabled(true);

        $event = new GetResponseUserEvent($user, $request);
        $this->eventDispatcher->dispatch(FOSUserEvents::REGISTRATION_CONFIRM, $event);

        $userManager->updateUser($user);

        if (null === $response = $event->getResponse()) {
            $url = $this->generateUrl('fos_user_registration_confirmed');
            $response = new RedirectResponse($url);
        }

        $this->eventDispatcher->dispatch(FOSUserEvents::REGISTRATION_CONFIRMED, new FilterUserResponseEvent($user, $request, $response));

        return $response;
    }

    /**
     * Tell the user his account is now confirmed.
     */
    public function confirmedAction(Request $request)
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        return $this->render('@FOSUser/Registration/confirmed.html.twig', array(
            'user' => $user,
            'targetUrl' => $this->getTargetUrlFromSession($request->getSession()),
        ));
    }

    /**
     * @return string|null
     */
    private function getTargetUrlFromSession(SessionInterface $session)
    {
        $key = sprintf('_security.%s.target_path', $this->tokenStorage->getToken()->getProviderKey());

        if ($session->has($key)) {
            return $session->get($key);
        }

        return null;
    }
}
