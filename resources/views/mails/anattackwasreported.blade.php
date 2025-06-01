@php
use Illuminate\Support\Arr;

$description = [];
if ($engagement->is_valid){
    $description = $engagement->description_for_dis();
}
@endphp

<x-markdown>
An attack was reported into the Polaris Online platform.

The user reporting this attack is: {{ $user->display_name }}

The attack is coming from: {{ $engagement->unit->name }}

Its intented target is: {{ Arr::get($engagement->data, "assessed_target", "")}}

The timestamp of the attack is: {{ $engagement->timestamp }}

The attacking unit used {{ $engagement->amount }} missiles of type {{ $engagement->weapon->name }}.

The distance between the attacking unit and its target was: {{ Arr::get($description, "distance") }}
</x-markdown>