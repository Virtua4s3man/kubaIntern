<?php
/**
 * Created by PhpStorm.
 * User: virtua
 * Date: 2019-02-26
 * Time: 11:35
 */

namespace App\FOS\UserBundle\EventListener;

use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

class UserLocaleSubscriber implements EventSubscriberInterface
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function onInteractiveLogin(InteractiveLoginEvent $event)
    {
        $user = $event->getAuthenticationToken()->getUser();
        $locale = $user->getLocale();

        if ($locale) {
            $this->session->set('_locale', $locale);
        }
    }

    public function onProfileEditSuccess(FormEvent $event)
    {
        $locale = $event->getForm()->getData()->getLocale();
        if (null !== $locale) {
            $this->session->set('_locale', $locale);
        }
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $locale = $this->session->get('_locale');
        if (null !== $locale) {
            $event->getRequest()->setLocale($locale);
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            SecurityEvents::INTERACTIVE_LOGIN => 'onInteractiveLogin',
            FOSUserEvents::PROFILE_EDIT_SUCCESS => 'onProfileEditSuccess',
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }
}
