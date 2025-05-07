<?php

namespace Modules\Excon\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PositionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            /**
            * La source à l'origine de cette position
            * @example "COT"
            */
            "source" => "required",
            /**
            * La latitude
            * @example "43.2"
            */
            "latitude" => "required|decimal:0,12",
            /**
            * La longitude
            * @example "5.123"
            */
            "longitude" => "required|decimal:0,12",
            /**
            * La route
            * @example "230"
            */
            "course" => "decimal:0,4",
            /**
            * La vitesse
            * @example "5.123"
            */
            "speed" => "decimal:0,4",
            /**
            * L'identifiant de l'unité dont la position est reportée. Le couple source, identifier doit correspondre 
            * à une entrée dans la table des identifiers.'
            * @example "COT_1"
            */
            "identifier" => "required",
            /**
            * La date/heure pour laquelle la position est reportée
            * @example "2024-01-12 12:03:45"
            */
            "timestamp" => "date_format:Y-m-d H:i:s"
            # Y: A full numeric representation of a year, at least 4 digits, with - for years BCE.
            # m: Numeric representation of a month, with leading zeros
            # d: Day of the month, 2 digits with leading zeros
            # H: 24-hour format of an hour with leading zeros
            # i: Minutes with leading zeros
            # s: Seconds with leading zeros
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
