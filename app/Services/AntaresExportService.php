<?php
namespace Modules\Excon\Services;

use Carbon\Carbon;
use Exception;

use Modules\Excon\Models\Unit;

class AntaresExportService 
{

    public static function make()
    {
        return new static;
    }
    
    public function test()
    {
        $start = Carbon::create(2025,4,21, 00, 00);
        $end = Carbon::create(2025,4,29, 23, 59);
        $unit = Unit::find(10);

        return $this->exportAsAntares($unit, $start, $end);
    }

    private function convertLatitudeToAntaresFormat($latitude)
    {
        $lat = abs($latitude);
        $latpart = intval($lat);
        
        $minutes = ($lat - $latpart) * 60;
        $minutespart = intval($minutes);
        
        $remaining = $minutes - $minutespart;
        
        $latpart = str_pad($latpart, 2, "0", STR_PAD_LEFT);
        
        $minutespart = str_pad($minutespart, 2, "0", STR_PAD_LEFT);

        $remainingpart = substr(number_format($remaining, 2, '.', ''),1);

        return  $latpart . $minutespart . $remainingpart . ($latitude < 0 ? "S" : "N") ;
    }

    private function convertLongitudeToAntaresFormat($longitude)
    {
        $long = abs($longitude);
        $longpart = intval($long);
        
        $minutes = ($long - $longpart) * 60;
        $minutespart = intval($minutes);
        
        $remaining = $minutes - $minutespart;
        
        $longpart = str_pad($longpart, 3, "0", STR_PAD_LEFT);
        
        $minutespart = str_pad($minutespart, 2, "0", STR_PAD_LEFT);

        $remainingpart = substr(number_format($remaining, 2, '.', ''),1);

        return  $longpart . $minutespart . $remainingpart . ($longitude < 0 ? "W" : "E") ;
    }

    public function exportAsAntares(Unit $unit, Carbon $start, Carbon $end)
    {
        //$start = Carbon::create(2025,4,23, 21, 57);
        //$end = Carbon::create(2025,4,23, 21, 59);
        
        $firstline = "ANTARES/". $unit->name . "/SURF/-/POLARIS 25/" . ($unit->side->name == "blue" ? "FRNFOR" : "OPFOR") . "//";
        $header = collect($firstline);
        
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
            $line = "TRACK/" . $item->timestamp->format("YmdHi") . "Z/". $this->convertLatitudeToAntaresFormat($item->latitude) . "-" . $this->convertLongitudeToAntaresFormat($item->longitude) . "/000/012/00000//";
            return $line;
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
        ->filter(function($item, $key) use ($end) 
        {
            return $item->is_valid;
        })        
        ->sortBy(function($value, $key) 
        { 
            return $value->timestamp->getTimestamp();
        })
        ->map(function($item, $key){
            $description = $item->description_for_dis();

            return "MISSILE/" . $item->timestamp->format("YmdHi") . "Z/" . 
                            $this->convertLatitudeToAntaresFormat($description["latitude"]) . "-". 
                            $this->convertLongitudeToAntaresFormat($description["longitude"]) . "/" .
                            $item->weapon->name . "/" . 
                            number_format($description["course"], 0, '.', '') ."/" . intval($description["distance"]) . "/" . 
                            "-/-/-/-//";
        });

        $lastLine = "END//";

        $ret = $header
            ->concat($remaining_positions)
            ->concat($engagements)
            ->concat(collect($lastLine));

        return $ret;
    }
}