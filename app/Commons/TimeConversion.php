<?php

class TimeConversion
{
    private static $defatulTimeZone = 'UTC';
    private static $defaultFormat = 'Y-m-d H:i:s';

    public static function convertToUtc($dateTime, $timeZone)
    {
        $dateTimeObject = new DateTime($dateTime, new DateTimeZone($timeZone));
        $dateTimeObject->setTimeZone(new DateTimeZone(self::$defatulTimeZone));

        return $dateTimeObject->format(self::$defaultFormat);
    }

    public static function convertFromUtc($dateTime, $timeZone)
    {
        $dateTimeObject = new DateTime($dateTime, new DateTimeZone(self::$defatulTimeZone));
        $dateTimeObject->setTimeZone(new DateTimeZone($timeZone));

        return $dateTimeObject->format(self::$defaultFormat);
    }

    public static function timeInterval($queryDate, $periodType = 'days', $timeZone = null)
    {
        $result = false;
        $timeZone = empty($timeZone) ? self::$defatulTimeZone : $timeZone;

        $now = new DateTime();
        $now->setTimeZone(new DateTimeZone($timeZone));
        $queryDate = new DateTime($queryDate);
        $interval = $now->diff($queryDate);

        if ($periodType == 'days') { // in days
            $result = $interval->format('%R') . (($interval->y * 365) + ($interval->m * 30) + $interval->d);
        } elseif ($periodType == 'months') { // in months
            $result = $interval->format('%R') . (($interval->y * 12) + $interval->m);
        }

        return $result;
    }

    public static function createStartEndDateTime($year, $month)
    {
        $dateTimeObject = new DateTime($year . '-' . $month . '-01');
        $startDateTime = $dateTimeObject->format('Y-m-d H:i:s');
        $dateTimeObject->modify('last day of this month');
        $endDate = $dateTimeObject->format('Y-m-d');
        $dateTimeObject->modify('last hour');
        $lastHour = $dateTimeObject->format('H');
        $dateTimeObject->modify('last minute');
        $lastMinute = $dateTimeObject->format('i');
        $dateTimeObject->modify('last second');
        $lastSecond = $dateTimeObject->format('s');
        $endDateTime = $endDate . ' ' . $lastHour . ':' . $lastMinute . ':' . $lastSecond;

        return array(
            'start' => $startDateTime,
            'end' => $endDateTime
        );
    }

    public static function isFutureDate($date, $timeZone = null, $skipHourMinute = false)
    {
        $timeZone = empty($timeZone) ? self::$defatulTimeZone : $timeZone;

        $now = new DateTime();
        $now->setTimeZone(new DateTimezone($timeZone));
        $unixTimeNow = ($skipHourMinute == true) ? strtotime($now->format('Y-m-d')) : strtotime($now->format('Y-m-d H:i:s'));
        
        $dateTimeToCheck = new DateTime($date, new DateTimeZone($timeZone));
        $unixTimeToCheck = ($skipHourMinute == true) ? strtotime($dateTimeToCheck->format('Y-m-d')) : strtotime($now->format('Y-m-d H:i:s'));

        return ($unixTimeToCheck > $unixTimeNow) ? true : false;
    }

    public static function isSameDate($date1, $date2)
    {
        $date1 = new DateTime($date1);
        $date2 = new DateTime($date2);

        return ($date1 == $date2) ? true : false;
    }

    public static function isGreaterDate($referenceDate, $compareWith)
    {
        $referenceDate = new DateTime($referenceDate);
        $compareWith = new DateTime($compareWith);

        return ($compareWith > $referenceDate) ? true : false;
    }

    public static function getYear($date)
    {
        $dateTimeObject = new DateTime($date);
        return $dateTimeObject->format('Y');
    }

    public static function getMonth($date, $representation = 'm')
    {
        $dateTimeObject = new DateTime($date);
        return $dateTimeObject->format($representation);
    }

    public static function getDay($date, $representation = 'd')
    {
        $dateTimeObject = new DateTime($date);
        return $dateTimeObject->format($representation);
    }

    public static function setDefaultTimeZone($timeZone)
    {
        self::$defatulTimeZone = $timeZone;
    }

    public static function setDefaultFormat($format)
    {
        self::$defaultFormat = $format;
    }
}

?>