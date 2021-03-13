<?php

namespace App\DataFixtures;

use App\Entity\Booking;
use App\Entity\Client;
use DateInterval;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class BookingFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $today = new DateTime();
        
        $tomorrow = new DateTime();
        $interval = new DateInterval('P1D');
        $tomorrow->add($interval);

        $clients = [
            Client::create("Manolo", "Federico Fernandez", "659856535"),
            Client::create("Miguel", "Fernandez Rodriguez", "659858535"),
            Client::create("Paula", "González Gómez", "659846535"),
            Client::create("Marta", "Federica Gómez", "659846535"),
            Client::create("Paula", "González Gómez", "659848535"),
            Client::create("Alfonso", "Pérez Rodríguez", "659846235"),
            Client::create("Víctor", "Almendros Pérez", "659846535"),
        ];

        $bookings = [
            Booking::create("Tercera", $today, $today, $today),
            Booking::create("Cuarta", $today, $today, $today),
            Booking::create("Quinta", $today, $today, $today),
            Booking::create("Tercera", $tomorrow, $tomorrow, $tomorrow),
            Booking::create("Cuarta", $tomorrow, $tomorrow, $tomorrow),
            Booking::create("Quinta", $tomorrow, $tomorrow, $tomorrow),
        ];

        /** @var Booking $booking */
        foreach ($bookings as $booking){
            foreach ($clients as $client){
                $booking->addClient($client);
                $manager->persist($client);
            }
            $manager->persist($booking);
        }

        $manager->flush();
    }
}
