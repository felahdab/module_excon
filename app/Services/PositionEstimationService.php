<?php
namespace Modules\Excon\Services;

use Carbon\Carbon;
use Exception;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class PositionEstimationService {
    public function extrapolatePositionForTimestamp(Collection $positions, Carbon | null $timestamp = null)
    {
        $timestamp = $timestamp ?? now();

        $closest_before = $positions
            ->filter(function($item, $key) use ($timestamp) {
                return $item->timestamp->lessThanOrEqualTo($timestamp);
            })
            ->sortByDesc(function($value, $key) { return $value->timestamp->getTimestamp();})
            ->first();
        
        $closest_after = $positions
            ->filter(function($item, $key) use ($timestamp) {
                return $item->timestamp->greaterThanOrEqualTo($timestamp);
            })
            ->sortBy(function($value, $key) { return $value->timestamp->getTimestamp();})
            ->first();

        $ret = [];

        if ($closest_after && 
            abs($closest_after->timestamp->diffInSeconds($timestamp)) > config("excon.limite_validite"))
            {
                # On a bien trouvé une position juste après le timestamp demandé, mais l'écart avec le timestamp d'intérêt
                # dépasse la limite de validité. On ignore donc cette position:
                $closest_after = null;
            }

        if ($closest_before && 
            abs($closest_before->timestamp->diffInSeconds($timestamp)) > config("excon.limite_validite"))
            {
                # On a bien trouvé une position juste avant le timestamp demandé, mais l'écart avec le timestamp d'intérêt
                # dépasse la limite de validité. On ignore donc cette position:
                $closest_before = null;
            }
            

        if ($closest_after == null  && 
            $closest_before == null ){
            # Là on a un problème: on n'a aucune position avant le timestamp et aucune position apres le timestamp
            # qui respecte en outre la limite de validité.
            # Ca va être difficile de renvoyer quelque chose d'utile.
            # Pour éviter ce cas: veiller à ne présenter dans les listes de pistes que celles pour lesquelles les 
            # identifiants possèdent au moins une position valide en terme de timestamp.
            throw new Exception("Aucune position en base utilisable pour l'identifiant utilisé.");
            } 
        elseif ($closest_after && 
            $closest_before && 
            abs($closest_after->timestamp->diffInSeconds($closest_before->timestamp)) > config("excon.seuil_extrapolation"))
            {
                # Il parait sage de faire de l'extrapolation
                $diff_total = abs($closest_after->timestamp->diffInSeconds($closest_before->timestamp));
                $diff_current = abs($timestamp->diffInSeconds($closest_before->timestamp));

                $latitude = $closest_before->latitude + ($diff_current/$diff_total)* ($closest_after->latitude - $closest_before->latitude);
                $longitude = $closest_before->longitude + ($diff_current/$diff_total) * ($closest_after->longitude - $closest_before->longitude);
             
                $ret = [$latitude, $longitude];
            }
        else {
            # On n'a qu'une position avant, ou une position apres, ou la difference de temps entre les 2 positions est
            # inférieure au seuil d'extrapolation.

            # On fait simple et on renvoit la position non nulle et par défaut la position juste avant.
            $position = $closest_before ? $closest_before : $closest_after;
            $ret = [$position->latitude, $position->longitude];
        }

        if ($ret) 
        {
            $ret = [ floatval($ret[0]), floatval($ret[1]) ];
        }

        return $ret ?? [0,0];    
    }

}