@extends("stellar/layout")

@section("head")
<!--	Start Maps Header    -->
<link rel="stylesheet" href="/leaflet/leaflet.css" integrity="sha512-xwE/Az9zrjBIphAcBb3F6JVqxf46+CDLwfLMHloNu6KEQCAWi6HcDUbeOfBIptF7tcCzusKFjFw2yuvEpDL9wQ==" crossorigin="">
<script src="/leaflet/leaflet.js" integrity="sha512-gZwIG9x3wUXg2hdXF6+rVkLF/0Vi9U8D2Ntg4Ga5I5BZpVkVxlJWbSQtXPSiUTtC0TjtGOmxa1AJPuV0CPthew==" crossorigin=""></script>
<style>#mapid {height: 75vh;}</style>
<!--	End Maps Header    -->
@endsection
@section("title")
    The Title
@endsection
@section("header")
<h1>Generic</h1>
<p>Ipsum dolor sit amet nullam</p>
<?php
use SimpleSoftwareIO\QrCode\Facades\QrCode;
?>
{!! QrCode::generate('http://datadeer.net/'); !!}



<x-social_media/>
@endsection
@section("main")
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
@endsection
@section("content")
<span class="image main"><img src="images/pic04.jpg" alt="" /></span>
<h2>Magna feugiat lorem</h2>
<p>Donec eget ex magna. Interdum et malesuada fames ac ante ipsum primis in faucibus. Pellentesque venenatis dolor imperdiet dolor mattis sagittis. Praesent rutrum sem diam, vitae egestas enim auctor sit amet. Pellentesque leo mauris, consectetur id ipsum sit amet, fergiat. Pellentesque in mi eu massa lacinia malesuada et a elit. Donec urna ex, lacinia in purus ac, pretium pulvinar mauris. Curabitur sapien risus, commodo eget turpis at, elementum convallis fames ac ante ipsum primis in faucibus.</p>
<p>Pellentesque venenatis dolor imperdiet dolor mattis sagittis. Praesent rutrum sem diam, vitae egestas enim auctor sit amet. Consequat leo mauris, consectetur id ipsum sit amet, fersapien risus, commodo eget turpis at, elementum convallis elit enim turpis lorem ipsum dolor sit amet feugiat. Phasellus convallis elit id ullamcorper pulvinar. Duis aliquam turpis mauris, eu ultricies erat malesuada quis. Aliquam dapibus, lacus eget hendrerit bibendum, urna est aliquam sem, sit amet est velit quis lorem.</p>
<h2>Tempus veroeros</h2>
<p>Cep risus aliquam gravida cep ut lacus amet. Adipiscing faucibus nunc placerat. Tempus adipiscing turpis non blandit accumsan eget lacinia nunc integer interdum amet aliquam ut orci non col ut ut praesent. Semper amet interdum mi. Phasellus enim laoreet ac ac commodo faucibus faucibus. Curae ante vestibulum ante. Blandit. Ante accumsan nisi eu placerat gravida placerat adipiscing in risus fusce vitae ac mi accumsan nunc in accumsan tempor blandit aliquet aliquet lobortis. Ultricies blandit lobortis praesent turpis. Adipiscing accumsan adipiscing adipiscing ac lacinia cep. Orci blandit a iaculis adipiscing ac. Vivamus ornare laoreet odio vis praesent nunc lorem mi. Erat. Tempus sem faucibus ac id. Vis in blandit. Nascetur ultricies blandit ac. Arcu aliquam. Accumsan mi eget adipiscing nulla. Non vestibulum ac interdum condimentum semper commodo massa arcu.</p>
@endsection
