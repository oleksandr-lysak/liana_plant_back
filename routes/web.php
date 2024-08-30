<?php

use App\Models\Master;
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

Route::get('/fill-place-id', [\App\Http\Controllers\MasterController::class,'fillPlaceId']);
Route::get('/',
    function () {
        return json_encode([
            'status' => 'success',
        ]);
    }
);



Route::get('/master-from-api', function () {
    $url = 'https://app.mybeauty.ua/api/v1/salon/nearest?perPage=50&page=1';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.15) Gecko/20080623 Firefox/2.0.0.15"));
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $result = curl_exec($ch);
    curl_close($ch);
    $info = json_decode($result, true);
    $i = 0;
    $masters = [];
    foreach ($info as $key => $value) {
        try {
            if ($value['isMaster']){
                $masters[]=$value;
            }
        } catch (\Throwable $th) {
            //throw $th;
        }

        $i++;
        // $master = new Master();
        // $master->name =
    }
    dd($masters[0]);
     // print all data
});
