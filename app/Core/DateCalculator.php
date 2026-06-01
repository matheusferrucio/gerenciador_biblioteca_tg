<?php

/**
 * DateCalculator — Loan date computation utility
 * Calculates due dates considering business days, weekends, and Brazilian holidays.
 */
class DateCalculator
{
    /**
     * Calculate the due date by adding N business days from a start date.
     * Optionally skips weekends and Brazilian national holidays.
     *
     * @param string $startDate   Date in Y-m-d format
     * @param int    $days        Number of business days to add (default 14)
     * @param bool   $skipWeekends Skip Saturdays and Sundays
     * @param bool   $skipHolidays Skip Brazilian national holidays
     * @return string Due date in Y-m-d format
     */
    public static function calculateDueDate(
        string $startDate,
        int $days = 14,
        bool $skipWeekends = true,
        bool $skipHolidays = true
    ): string {
        $date = new DateTime($startDate);
        $holidays = $skipHolidays ? self::getBrazilianHolidays((int)$date->format('Y')) : [];
        $added = 0;

        while ($added < $days) {
            $date->modify('+1 day');

            // Refresh holiday list if we crossed into a new year
            $currentYear = (int)$date->format('Y');
            if ($skipHolidays && !isset($holidays[$currentYear])) {
                $holidays = array_merge($holidays, self::getBrazilianHolidays($currentYear));
            }

            if (!self::isBusinessDay($date, $skipWeekends, $skipHolidays, $holidays)) {
                continue;
            }

            $added++;
        }

        return $date->format('Y-m-d');
    }

    /**
     * Check if a date is a business day
     */
    public static function isBusinessDay(
        DateTime $date,
        bool $checkWeekends = true,
        bool $checkHolidays = true,
        array $holidays = []
    ): bool {
        $dayOfWeek = (int)$date->format('N'); // 1=Mon, 7=Sun

        // Skip weekends
        if ($checkWeekends && $dayOfWeek >= 6) {
            return false;
        }

        // Skip holidays
        if ($checkHolidays && in_array($date->format('Y-m-d'), $holidays)) {
            return false;
        }

        return true;
    }

    /**
     * Get Brazilian national holidays for a given year.
     * Includes fixed holidays and moveable holidays based on Easter.
     *
     * @param int $year
     * @return array List of date strings in Y-m-d format
     */
    public static function getBrazilianHolidays(int $year): array
    {
        // Fixed holidays
        $holidays = [
            "$year-01-01", // Confraternização Universal
            "$year-04-21", // Tiradentes
            "$year-05-01", // Dia do Trabalhador
            "$year-09-07", // Independência do Brasil
            "$year-10-12", // Nossa Senhora Aparecida
            "$year-11-02", // Finados
            "$year-11-15", // Proclamação da República
            "$year-12-25", // Natal
        ];

        // Easter-based moveable holidays
        $easter = new DateTime();
        $easter->setDate($year, 3, 21);
        $easterDays = easter_days($year);
        $easter->modify("+{$easterDays} days");

        $easterStr = $easter->format('Y-m-d');

        // Carnival: 47 days before Easter (Monday and Tuesday)
        $carnivalTue = clone $easter;
        $carnivalTue->modify('-47 days');
        $carnivalMon = clone $carnivalTue;
        $carnivalMon->modify('-1 day');

        // Good Friday: 2 days before Easter
        $goodFriday = clone $easter;
        $goodFriday->modify('-2 days');

        // Corpus Christi: 60 days after Easter
        $corpusChristi = clone $easter;
        $corpusChristi->modify('+60 days');

        $holidays[] = $carnivalMon->format('Y-m-d');
        $holidays[] = $carnivalTue->format('Y-m-d');
        $holidays[] = $goodFriday->format('Y-m-d');
        $holidays[] = $easterStr;
        $holidays[] = $corpusChristi->format('Y-m-d');

        sort($holidays);

        return $holidays;
    }

    /**
     * Get a formatted list of holidays for display
     *
     * @param int $year
     * @return array Associative array [date => name]
     */
    public static function getHolidayNames(int $year): array
    {
        $easter = new DateTime();
        $easter->setDate($year, 3, 21);
        $easterDays = easter_days($year);
        $easter->modify("+{$easterDays} days");

        $carnivalTue = clone $easter;
        $carnivalTue->modify('-47 days');
        $carnivalMon = clone $carnivalTue;
        $carnivalMon->modify('-1 day');
        $goodFriday = clone $easter;
        $goodFriday->modify('-2 days');
        $corpusChristi = clone $easter;
        $corpusChristi->modify('+60 days');

        return [
            "$year-01-01" => 'Confraternização Universal',
            $carnivalMon->format('Y-m-d') => 'Carnaval (segunda)',
            $carnivalTue->format('Y-m-d') => 'Carnaval (terça)',
            $goodFriday->format('Y-m-d') => 'Sexta-feira Santa',
            $easter->format('Y-m-d') => 'Páscoa',
            "$year-04-21" => 'Tiradentes',
            "$year-05-01" => 'Dia do Trabalhador',
            $corpusChristi->format('Y-m-d') => 'Corpus Christi',
            "$year-09-07" => 'Independência do Brasil',
            "$year-10-12" => 'Nossa Sra. Aparecida',
            "$year-11-02" => 'Finados',
            "$year-11-15" => 'Proclamação da República',
            "$year-12-25" => 'Natal',
        ];
    }
}
