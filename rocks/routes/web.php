<?php

use App\Code;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {return view('stellar/frontpage');});

Route::get('/qr/{code:handout_code}', function (Code $code) {
    if ($code->isPlacedAtAll()) return response('',404);
    return view('stellar/add_pin',
        //todo what should happen if someone scans a QR code that is already added?
        ['code'=>$code]
    );
});//->where('qr','^[a-zA-Z0-9]+$');

Route::get('/adminsignin', function () {

    return view('stellar/admin/signin');
});
Route::get('/admincheck', function () {

    return view('stellar/frontpage');
});

//Route::get('/elements', function () {return view('stellar/elements');});
//Route::get('/index', function () {return view('stellar/index');});

//Route::get('/user/{id}', function ($id) {
//    return 'User '.$id;
//});


