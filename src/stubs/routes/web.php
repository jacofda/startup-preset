<?php

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

use Areaseb\Core\Http\Middleware\SaveTracking;
use Areaseb\Core\Http\Controllers\{LoginController, PagesController, ReportController};

Route::get('login', [LoginController::class, 'login'])->name('login');
Route::post('login', [LoginController::class, 'loginPost'])->name('loginPost');

//newsletter tracking
Route::get('tracker', [ReportController::class, 'tracker']);
Route::get('unsubscribe', [ReportController::class, 'unsubscribe']);
Route::get('track', [ReportController::class, 'track']);

use Areaseb\Core\Models\{Client, Contact, NewsletterList, Setting};
Route::get('register-lead', function(){

    $url = 'https://www.'.Setting::base()->sitoweb;

    $ref = request()->headers->get('referer');
    if(strpos($ref, $url) !== false)
    {
        if(!Contact::where('email', request('email'))->exists())
        {
            $contact = new Contact;
                $contact->email = request('email');
                $contact->nome = request('nome');
                $contact->cognome = request('cognome');
                $contact->subscribed = intval(request('newsletter'));
                $contact->origin = "Sito";
            $contact->save();
            $contact->clients()->attach(Client::Lead());

            if(intval(request('newsletter')))
            {
                $list = NewsletterList::firstOrCreate(['nome' => 'Contatti da sito']);
                $contact->lists()->attach($list->id);
            }
        }
    }
    return redirect($url.'/grazie');
});
