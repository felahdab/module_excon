@php
use Illuminate\Support\Arr;
use Illuminate\Support\Number;
use Illuminate\Support\Facades\Storage;

$description = [];
if ($engagement->is_valid){
    $description = $engagement->description_for_dis();
}

$timestamp = $engagement->timestamp;
$filepart = $timestamp->format('YmdHi');

$routes = [];
foreach(["BLEU", "RED", "BALISE"] as $datasource){
    $filepath = "excon/nvgs/{$filepart}_{$datasource}_sitac.nvg";
    $routes[$datasource] = route('skeletor.public_file_download', ['skeletor_download_filename' => $filepath]);
}

$distance = Arr::get($description, "distance", "No distance available.");
if ($distance != "No distance available")
{
    $distancestr = Number::format(floatval($distance)) . " meters ";
}
else 
{
    $distancestr = "No distance available.";
}

@endphp

<x-markdown>
An attack was reported into the Polaris Online platform.

The user reporting this attack is: {{ $user->display_name }}

The attack is coming from: {{ $engagement->unit->name }}

Its intented target is: {{ Arr::get($engagement->data, "assessed_target", "")}}

The timestamp of the attack is: {{ $engagement->timestamp }}

The attacking unit used {{ $engagement->amount }} missiles of type {{ $engagement->weapon->name }}.

The distance between the attacking unit and its target was: {{  $distancestr }}

Here are the snapshot of the tactical datalinks pictures and gps trackers. You can download them and open them in any ICC workstation (Map, activate Drawing tools, open drawing layer):

@foreach(["BLEU", "RED", "BALISE"] as $datasource)
- [{{$datasource}} situation]({{ $routes[$datasource] }})
@endforeach
</x-markdown>