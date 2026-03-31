<?php

namespace App\Services;

class SalesPeriodCalculator
{
    /**
     * @return \DateTimeImmutable[]
     */
    public function getWinterSales(int $year): array
    {
        // Trouve le mercredi le plus récent à la date actuelle ou avant.
        $date = new \DateTimeImmutable("$year-01-01");
        $wednesdays = [];
        while ($date->format('m') === '01') {
            if ($date->format('N') == 3) $wednesdays[] = $date; // N = 1 (Monday) to 7 (Sunday)
            $date = $date->modify('+1 day');
        }

        $secondWednesday = $wednesdays[1];
        $start = $secondWednesday->format('d') > 12 ? $wednesdays[0] : $secondWednesday;
        $end = $start->modify('+4 weeks');

        return [$start->setTime(8, 0), $end->setTime(23, 59)];
    }

     /**
     * @return \DateTimeImmutable[]
     */
    public function getSummerSales(int $year): array
    {
        $date = new \DateTimeImmutable("$year-06-30");
        while ($date->format('N') != 3) $date = $date->modify('-1 day');

        $lastWednesday = $date;
        $start = $lastWednesday->format('d') > 28 ? $lastWednesday->modify('-1 week') : $lastWednesday;
        $end = $start->modify('+4 weeks');

        return [$start->setTime(8, 0), $end->setTime(23, 59)];
    }
}