<?php

namespace App\EventSubscriber;

use CalendarBundle\Entity\Event;
use CalendarBundle\CalendarEvents;
use App\Repository\OccasionRepository;
use CalendarBundle\Event\CalendarEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


class CalendarSubscriber implements EventSubscriberInterface
{
    private $occasionRepository;
    private $router;

    public function __construct(
        OccasionRepository $occasionRepository,
        UrlGeneratorInterface $router
    ) {
        $this->occasionRepository = $occasionRepository;
        $this->router = $router;
    }

    public static function getSubscribedEvents()
    {
        return [
            CalendarEvents::SET_DATA => 'onCalendarSetData',
        ];
    }

    public function onCalendarSetData(CalendarEvent $calendar)
    {
        $start = $calendar->getStart();
        $end = $calendar->getEnd();

        $occasions = $this->occasionRepository
            ->createQueryBuilder('occasion')
            ->where('occasion.startDate BETWEEN :start and :end OR occasion.endDate BETWEEN :start and :end')
            ->setParameter('start', $start->format('Y-m-d H:i'))
            ->setParameter('end', $end->format('Y-m-d H:i'))
            ->getQuery()
            ->getResult()
        ;

               
        foreach ($occasions as $occasion) {
            // On crée un nouvel évènement a partir de nos données
            $occasionEvent = new Event(
                $occasion->getTitle(),
                $occasion->getStartDate(),
                $occasion->getEndDate(),
            );

            /*
             * Add custom options to events
             *
             * For more information see: https://fullcalendar.io/docs/event-object
             * and: https://github.com/fullcalendar/fullcalendar/blob/master/src/core/options.ts
             */

            $occasionEvent->setOptions([
                'backgroundColor' => $occasion->getCategory()->getBgColor(),
                'borderColor' => $occasion->getCategory()->getBdColor(),
                'textColor' => $occasion->getCategory()->getTextColor(),
            ]);
            $occasionEvent->addOption(
                'url',
                $this->router->generate('showEvent', [
                    'id' => $occasion->getId(),
                ])
            );

            // On envoie l'évènement dans le calendrier
            $calendar->addEvent($occasionEvent);
        }
    }
}