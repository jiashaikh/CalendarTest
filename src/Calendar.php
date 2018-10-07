<?php

namespace Calendar;

use DateTimeInterface;

require_once('CalendarInterface.php');

class Calendar implements CalendarInterface
{
    private $datetime;

    /**
     * @param DateTimeInterface $datetime
     */
    public function __construct(DateTimeInterface $datetime)
    {
	$this->datetime = $datetime;
    }

    /**
     * Get the day
     *
     * @return int
     */
    public function getDay() 
    {
	return (int)$this->datetime->format('j');
    }

    /**
     * Get the weekday (1-7, 1 = Monday)
     *
     * @return int
     */
    public function getWeekDay()
    {
        return (int)$this->datetime->format('N');
    }

    /**
     * Get the first weekday of this month (1-7, 1 = Monday)
     *
     * @return int
     */
    public function getFirstWeekDay()
    {
	return (int)date('N', mktime(0, 0, 0, $this->datetime->format('m'), 1, $this->datetime->format('Y')));
    }

    /**
     * Get the first week of this month (18th March => 9 because March starts on week 9)
     *
     * @return int
     */
    public function getFirstWeek() 
    {
        return (int)date('W', mktime(0, 0, 0, $this->datetime->format('m'), 1, $this->datetime->format('Y')));
    }

    /**
     * Get the number of days in this month
     *
     * @return int
     */
    public function getNumberOfDaysInThisMonth()
    {
        return (int)$this->datetime->format('t');
    }
	
    /**
     * Get the number of days in the previous month
     *
     * @return int
     */
    public function getNumberOfDaysInPreviousMonth()
    {
	return (int)date('t', mktime(0, 0, 0, $this->datetime->format('m')-1, 1, $this->datetime->format('Y')));
    }
	
    /**
     * Get the calendar array
     *
     * @return array
     */
    public function getCalendar()
    {
	$firstWeekday    = $this->getFirstWeekDay();
	$daysInMonth	 = $this->getNumberOfDaysInThisMonth();
	$daysInPrevMonth = $this->getNumberOfDaysInPreviousMonth();
	$week	         = $this->getFirstWeek();
	$currentWeek     = (int)$this->datetime->format('W');
	
	// To get the last Monday of last month if the first weekday of the month is not Monday
	$firstMonday = $firstWeekday === 1 ? $firstWeekday : $daysInPrevMonth - $firstWeekday + 2;

	// To highlight the previous week
	$highlight = ((int) $week === (int) ($currentWeek - 1) || ((int)$week !== (int)$currentWeek && (int)$currentWeek === 1)) ? 1 : 0;
	
	$count        = 0; // To count the day of the week
	$calendarData = [];
	if ($firstMonday !== 1) {
	    for ($i = $firstMonday; $i <= $daysInPrevMonth; $i++) {
		$calendarData[$week][$i] = (bool)$highlight; 		    	
		$count++;
	    }	
	}

	for ($j = 1; $j <= $daysInMonth; $j++) {
	    $calendarData[$week][$j] = (bool)$highlight;

	    $count++;

	    if ($count === 7) {
                $count = 0;
                $week = ((int)$week === 53) ? 1 : $week+1;
                $highlight = ((int) $week === (int) ($currentWeek - 1)) ? 1 : 0;
            }
	}

	// To add the remaining days of the week from next month
	if ($count > 0) {
	    for ($k = 1; $k <= 7-$count; $k++) {
	   	$calendarData[$week][$k] = (bool)$highlight;
	    }	
	} 

	return $calendarData;
    }
}
