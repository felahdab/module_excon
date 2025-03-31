<?php

namespace Modules\Excon\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AckEngagementRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            /**
            * Identifiant de l'engagement à acquitter
            * @example "3"
            */
            "engagement" => "required",
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
