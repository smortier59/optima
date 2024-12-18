<?php
/**
 * PHPUnit
 *
 * Copyright (c) 2002-2010, Sebastian Bergmann <sb@sebastian-bergmann.de>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Sebastian Bergmann nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   Testing
 * @package    PHPUnit
 * @author     Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @copyright  2002-2010 Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link       http://www.phpunit.de/
 * @since      File available since Release 3.0.0
 */

//require_once 'PHPUnit/Util/Filter.php';

//PHPUnit_Util_Filter::addFileToFilter(__FILE__, 'PHPUNIT');

/**
 * Utility class for timing.
 *
 * @category   Testing
 * @package    PHPUnit
 * @author     Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @copyright  2002-2010 Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: 3.4.9
 * @link       http://www.phpunit.de/
 * @since      Class available since Release 3.0.0
 */
class PHPUnit_Util_Timer
{
    protected static $startTimes = array();

    /**
     * Starts the timer.
     *
     */
    public static function start()
    {
        array_push(self::$startTimes, microtime(TRUE));
    }

    /**
     * Returns the currently elapsed time.
     *
     * @return float
     */
    public static function current()
    {
        return microtime(TRUE) - self::$startTimes[count(self::$startTimes)-1];
    }

    /**
     * Stops the timer and returns the elapsed time.
     *
     */
    public static function stop()
    {
        return microtime(TRUE) - array_pop(self::$startTimes);
    }

    /**
     * Formats elapsed time (in seconds) to a string.
     *
     * @param  float $time
     * @return float
     */
    public static function secondsToTimeString($time)
    {
        $buffer = '';

        $hours   = sprintf('%02d', ($time >= 3600) ? floor($time / 3600) : 0);
        $minutes = sprintf(
                     '%02d',
                     ($time >= 60)   ? floor($time /   60) - 60 * $hours : 0
                   );
        $seconds = sprintf('%02d', $time - 60 * 60 * $hours - 60 * $minutes);

        if ($hours == 0 && $minutes == 0) {
            $seconds = sprintf('%1d', $seconds);

            $buffer .= $seconds . ' second';

            if ($seconds != '1') {
                $buffer .= 's';
            }
        } else {
            if ($hours > 0) {
                $buffer = $hours . ':';
            }

            $buffer .= $minutes . ':' . $seconds;
        }

        return $buffer;
    }
}
?>
