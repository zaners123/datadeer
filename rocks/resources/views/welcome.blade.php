<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!--	Maps    -->
        <link rel="stylesheet" href="/css/css.css" crossorigin="">
        <link rel="stylesheet" href="/leaflet/leaflet.css" integrity="sha512-xwE/Az9zrjBIphAcBb3F6JVqxf46+CDLwfLMHloNu6KEQCAWi6HcDUbeOfBIptF7tcCzusKFjFw2yuvEpDL9wQ==" crossorigin="">
        <script src="/leaflet/leaflet.js" integrity="sha512-gZwIG9x3wUXg2hdXF6+rVkLF/0Vi9U8D2Ntg4Ga5I5BZpVkVxlJWbSQtXPSiUTtC0TjtGOmxa1AJPuV0CPthew==" crossorigin=""></script>
        <style>
            #mapid {
                height: 75vh;
            }
        </style>
        <title></title>
    </head>
    <body>
    <div id="mapid">  </div>
    <script>
        var map = L.map('mapid').setView([48, -110], 4);
        L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}', {
            attribution: '',
            maxZoom: 10,
            minZoom: 3,
            id: 'mapbox/streets-v11',
            tileSize: 512,
            zoomOffset: -1,
            accessToken: "{{env("MAPBOX_KEY")}}"
        }).addTo(map);
        map.setMaxBounds(L.latLngBounds(L.latLng(-185, -185),L.latLng(185,185)))
        let markers = [];
        @foreach (App\Code::all() as $code)
            {{--            @dd($code)--}}
            @if ($id->longitude != null && $id->latitude != null)
                {!! "markers.push(L.marker([".$id->longitude.",".$id->latitude."],{title:''}).addTo(map));"!!}
            @endif
        @endforeach
    </script>
    </body>
</html>
