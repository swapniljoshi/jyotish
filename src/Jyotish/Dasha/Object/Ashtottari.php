<?php
/*
 * @link      http://github.com/kunjara/jyotish for the canonical source repository
 * @license   GNU General Public License version 2 or later
 */

namespace Jyotish\Dasha\Object;

use Jyotish\Base\Utils;
use Jyotish\Graha\Graha;
use Jyotish\Dasha\Dasha;
use Jyotish\Tattva\Kala\Samvatsara;
use Jyotish\Panchanga\Nakshatra\Nakshatra;

/**
 * Class of Ashtottari Dasha
 *
 * @author Kunjara Lila das <vladya108@gmail.com>
 * @see Maharishi Parashara. Brihat Parashara Hora Shastra. Chapter 46, Verse 17-23.
 */
class Ashtottari extends AbstractDasha {
    /**
     * Dasha key
     * 
     * @var string
     */
    protected $dashaType = Dasha::TYPE_ASHTOTTARI;

    /**
     * Duration of dasha.
     * 
     * @var int
     */
    protected $durationTotal = 108;

    /**
     * Duration of dasha by subperiods.
     * 
     * @var array
     */
    protected $durationGraha = array(
        Graha::KEY_SY => 6,
        Graha::KEY_CH => 15,
        Graha::KEY_MA => 8,
        Graha::KEY_BU => 17,
        Graha::KEY_SA => 10,
        Graha::KEY_GU => 19,
        Graha::KEY_RA => 12,
        Graha::KEY_SK => 21,
    );

    /**
     * Constructor
     * 
     * @param null|array $options Options to set
     */
    public function __construct($options = null)
    {
        parent::__construct($options);
        
        $nakshatras = Nakshatra::nakshatraList(true);
        $this->orderNakshatra = Utils::shiftArray($nakshatras, 6, true);
    }

    /**
     * Get start period.
     * 
     * @return array
     */
    public function getStartPeriod()
    {
        $nakshatra = $this->panchangaObject->getNakshatra(true, true);
        $keysNakshatra = array_keys($this->orderNakshatra);
        $indexNum      = array_search($nakshatra['key'], $keysNakshatra) + 1;
        
        $partSum = 0;
        foreach ($this->durationGraha as $key => $value){
            $G = Graha::getInstance($key);
            if($G->grahaCharacter == Graha::CHARACTER_PAPA){
                $part = 4;
            }else{
                $part = 3;
            }

            $partSum += $part;
            if($partSum >= $indexNum)
                break;
        }

        $num = $part - ($partSum - $indexNum);

        $result['graha'] = $key;
        $result['total'] = $this->durationTotal * Samvatsara::DUR_GREGORIAN * 86400;

        $durationNakshatra = round($this->durationGraha[$key] * Samvatsara::DUR_GREGORIAN * 86400 / $part);
        $result['start']   = $durationNakshatra * ($num - 1) + round($durationNakshatra * (100 - $nakshatra['left']) / 100);

        return $result;
    }
}