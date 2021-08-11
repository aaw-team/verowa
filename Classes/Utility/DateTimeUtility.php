<?php
declare(strict_types=1);
namespace AawTeam\Verowa\Utility;
/*
 * Copyright by Agentur am Wasser | Maeder & Partner AG
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

/**
 * DateTimeUtility
 */
class DateTimeUtility
{
    /**
     * @param \DateTimeInterface $start
     * @param \DateTimeInterface $end
     * @param string $separator
     * @throws \LogicException
     * @return string
     */
    public static function renderFromToDate(\DateTimeInterface $start, \DateTimeInterface $end, string $separator = ' - '): string
    {
        if ($end < $start) {
            throw new \LogicException('$start must not be greater than $end');
        }
        if ($start->format('Ymd') == $end->format('Ymd')) {
            return strftime('%d. %B %Y', $start->getTimestamp());
        }

        $showYear = $start->format('Y') != $end->format('Y');
        $showMonth = $showYear || $start->format('m') != $end->format('m');

        $startDateFormat = '%d.'
            . ($showMonth ? ' %B' : '')
            . ($showYear ? ' %Y' : '');

        return strftime($startDateFormat, $start->getTimestamp()) . $separator . strftime('%d. %B %Y', $end->getTimestamp());
    }

    /**
     * @param \DateTimeInterface $start
     * @param \DateTimeInterface $end
     * @param string $timeSeparator
     * @param string $partsSeparator
     * @throws \LogicException
     * @return string
     */
    public static function renderFromToDateAndTime(\DateTimeInterface $start, \DateTimeInterface $end, string $timeSeparator = ' | ', string $partsSeparator = ' - '): string
    {
        if ($end < $start) {
            throw new \LogicException('$start must not be greater than $end');
        }
        if ($start == $end) {
            return strftime('%d. %B %Y', $start->getTimestamp()) . $timeSeparator . $start->format('H:i');
        }
        if ($start->format('Ymd') == $end->format('Ymd')) {
            return strftime('%d. %B %Y', $start->getTimestamp()) . $timeSeparator . $start->format('H:i') . $partsSeparator . $end->format('H:i');
        }
        return strftime('%d. %B %Y', $start->getTimestamp()) . $timeSeparator . $start->format('H:i') . $partsSeparator . strftime('%d. %B %Y', $end->getTimestamp()) . $timeSeparator . $end->format('H:i');
    }
}
