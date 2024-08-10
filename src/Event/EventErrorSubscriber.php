<?php

namespace App\Event;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\EventDispatcher\EventSubscriberInterface;

class EventErrorSubscriber implements EventSubscriberInterface
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LoginErrorEvent::NAME => 'onLoginError',
        ];
    }

    public function onLoginError(LoginErrorEvent $event)
    {
        $this->logger->info('Login error for user: ' . $event->getUser()->getUserIdentifier());
    }
}
