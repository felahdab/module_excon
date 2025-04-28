<?php
namespace Modules\Excon\Services;

use Carbon\Carbon;
use Exception;

use Modules\Excon\Models\Unit;

class AntaresExportService {
    public function exportAsAntares(Unit $unit, Carbon $start, Carbon $end)
    {
        //$start = Carbon::create(2025,4,23, 21, 57);
        //$end = Carbon::create(2025,4,23, 21, 59);
        
        /**
         * On commence par les positions
         */
        $positions = $unit->positions()->get();
        $previous_timestamp = null;

        $remaining_positions = $positions
        ->filter(function($item, $key) use ($start) 
        {
            return $item->timestamp->greaterThanOrEqualTo($start);
        })
        ->filter(function($item, $key) use ($end) 
        {
            return $item->timestamp->lessThanOrEqualTo($end);
        })
        ->sortBy(function($value, $key) 
        { 
            return $value->timestamp->getTimestamp();
        })
        ->filter(function ($item, $key) use (&$previous_timestamp)
        {
            if ($previous_timestamp == null)
            {
                $previous_timestamp = $item->timestamp;
                return true;

            }

            $ret = $item->timestamp->greaterThanOrEqualTo($previous_timestamp->clone()->addMinutes(1));

            if ($ret)
            {
                $previous_timestamp = $item->timestamp;
                return true;
            }
            return false;
        })
        ->map(function($item, $key){
            return "TRACK/" . $item->timestamp->format("YmdHis") . "/". $item->latitude . "/" . $item->longitude;
        });

        /**
         * On poursuit avec les engagements
         */
        $engagements = $unit->engagements()->get();
        $previous_timestamp = null;

        $engagements = $engagements
        ->filter(function($item, $key) use ($start) 
        {
            return $item->timestamp->greaterThanOrEqualTo($start);
        })
        ->filter(function($item, $key) use ($end) 
        {
            return $item->timestamp->lessThanOrEqualTo($end);
        })
        ->sortBy(function($value, $key) 
        { 
            return $value->timestamp->getTimestamp();
        })
        ->filter(function ($item, $key) use (&$previous_timestamp)
        {
            if ($previous_timestamp == null)
            {
                $previous_timestamp = $item->timestamp;
                return true;

            }

            $ret = $item->timestamp->greaterThanOrEqualTo($previous_timestamp->clone()->addMinutes(1));

            if ($ret)
            {
                $previous_timestamp = $item->timestamp;
                return true;
            }
            return false;
        })
        ->map(function($item, $key){
            return "MISSILE/" . $item->timestamp->format("YmdHis") . "/";
        });

        return $engagements->concat($remaining_positions);
    }
}