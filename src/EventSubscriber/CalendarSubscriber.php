<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\EventSubscriber;

use App\Repository\ProgrammeRepository;
use CalendarBundle\CalendarEvents;
use CalendarBundle\Entity\Event;
use CalendarBundle\Event\CalendarEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CalendarSubscriber implements EventSubscriberInterface
{
    private $programmeRepository;
    private $router;

    public function __construct(
        ProgrammeRepository $programmeRepository,
        UrlGeneratorInterface $router
    ) {
        $this->programmeRepository = $programmeRepository;
        $this->router = $router;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CalendarEvents::SET_DATA => 'onCalendarSetData',
        ];
    }

    public function onCalendarSetData(CalendarEvent $calendar)
    {
        $start = $calendar->getStart();
        $end = $calendar->getEnd();
        $filters = $calendar->getFilters();

        // Modify the query to fit to your entity and needs
        // Change programme.beginAt by your start date property
        $programmes = $this->programmeRepository
            ->createQueryBuilder('programme')
            ->where('programme.beginAt BETWEEN :start and :end OR programme.endAt BETWEEN :start and :end')
            ->setParameter('start', $start->format('d-m-Y H:i:s'))
            ->setParameter('end', $end->format('d-m-Y H:i:s'))
            ->getQuery()
            ->getResult()
        ;

        foreach ($programmes as $programme) {
            // this create the events with your data (here programme data) to fill calendar
            $programmeEvent = new Event(
                $programme->getTitle(),
                $programme->getBeginAt(),
                $programme->getEndAt() // If the end date is null or not defined, a all day event is created.
            );

            /*
             * Add custom options to events
             *
             * For more information see: https://fullcalendar.io/docs/event-object
             * and: https://github.com/fullcalendar/fullcalendar/blob/master/src/core/options.ts
             */

            $programmeEvent->setOptions([
                'backgroundColor' => 'red',
                'borderColor' => 'red',
            ]);
            $programmeEvent->addOption(
                'url',
                $this->router->generate('programme', [
                    'id' => $programme->getId(),
                ])
            );

            // finally, add the event to the CalendarEvent to fill the calendar
            $calendar->addEvent($programmeEvent);
        }
    }
}