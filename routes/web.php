<?php



Route::fallback(function () {
    return view('errors.404');
});



Route::get('clear', function () {
    \Artisan::call('view:clear');
    \Artisan::call('config:clear');
    \Artisan::call('route:clear');
    \Artisan::call('cache:clear');
});



Route::post('ipn/g101', 'Gateway\g101\ProcessController@ipn')->name('ipn.g101'); // paypal
Route::post('ipn/g102', 'Gateway\g102\ProcessController@ipn')->name('ipn.g102'); // Perfect Money
Route::post('ipn/g103', 'Gateway\g103\ProcessController@ipn')->name('ipn.g103'); // Stripe
Route::post('ipn/g104', 'Gateway\g104\ProcessController@ipn')->name('ipn.g104'); // Skrill
Route::post('ipn/g105', 'Gateway\g105\ProcessController@ipn')->name('ipn.g105'); // PayTMbuy.shares_trx
Route::post('ipn/g106', 'Gateway\g106\ProcessController@ipn')->name('ipn.g106'); // Payeer
Route::post('ipn/g107', 'Gateway\g107\ProcessController@ipn')->name('ipn.g107'); // PayStack
Route::post('ipn/g108', 'Gateway\g108\ProcessController@ipn')->name('ipn.g108'); // VoguePay
Route::get('ipn/g109/{trx}/{type}', 'Gateway\g109\ProcessController@ipn')->name('ipn.g109'); //flutterwave
Route::post('ipn/g110', 'Gateway\g110\ProcessController@ipn')->name('ipn.g110'); // RozarPay
Route::post('ipn/g111', 'Gateway\g111\ProcessController@ipn')->name('ipn.g111'); // stripeJs
Route::post('ipn/g112', 'Gateway\g112\ProcessController@ipn')->name('ipn.g112'); //instamojo
Route::get('ipn/g501', 'Gateway\g501\ProcessController@ipn')->name('ipn.g501'); // Blockchain
Route::get('ipn/g502', 'Gateway\g502\ProcessController@ipn')->name('ipn.g502'); // Block.io
Route::post('ipn/g503', 'Gateway\g503\ProcessController@ipn')->name('ipn.g503'); // CoinPayment
Route::post('ipn/g504', 'Gateway\g504\ProcessController@ipn')->name('ipn.g504'); // CoinPayment ALL
Route::post('ipn/g505', 'Gateway\g505\ProcessController@ipn')->name('ipn.g505'); // Coingate
Route::post('ipn/g506', 'Gateway\g506\ProcessController@ipn')->name('ipn.g506'); // Coinbase commerce
Route::get('/receiver/all/{id}', 'rpcController@insert_deposit')->name('wallet_user_receiver');


Route::get('/with/gdenetwork/','confirmW@validar_matic')->name('withaw.method.confirm');


Route::get('/', function(){
     
    return redirect()->route('admin.dashboard');
});

Route::namespace('Admin')->prefix('admin')->name('admin.')->group(function () {

    Route::namespace('Auth')->group(function () {

        Route::get('/', 'LoginController@showLoginForm')->name('login');

        Route::post('/', 'LoginController@login')->name('login');

        Route::get('logout', 'LoginController@logout')->name('logout');

        // Admin Password Reset

        Route::get('password/reset', 'ForgotPasswordController@showLinkRequestForm')->name('password.reset');
        Route::post('password/reset', 'ForgotPasswordController@sendResetLinkEmail');
        Route::post('password/verify-code', 'ForgotPasswordController@verifyCode')->name('password.verify-code');
        Route::get('password/reset/{token}', 'ResetPasswordController@showResetForm')->name('password.change-link');
        Route::post('password/reset/change', 'ResetPasswordController@reset')->name('password.change');
    });



Route::middleware(['admin', 'CheckAdminStatus'])->group(function () {

            Route::middleware(['CheckAdminAccess'])->group(function () {
            Route::get('dashboard', 'AdminController@dashboard')->name('dashboard');



            
             //Depósitos
            Route::get('deposit', 'DepositController@index')->name('depositos');
            Route::get('deposit/buscar', 'DepositController@index')->name('deposit.buscar');
            Route::get('deposit/detail/{id}', 'DepositController@confirmar')->name('deposit.detail');
            Route::post('deposit/save', 'DepositController@save')->name('deposit.save');
            Route::get('deposit/confirma/{id}/{stado}', 'DepositController@confirmar_linear')->name('deposit.confirma');
             
             
             //membresias
               Route::get('membresias', 'MembresiaController@index')->name('membresias');
               Route::get('membresias/buscar', 'MembresiaController@index')->name('membresias.buscar');
               Route::post('membresias/new', 'MembresiaController@buy')->name('membresias.buy');

          
             //paerticipaciones
             
                  Route::get('participaciones', 'ParticipacionController@index')->name('participar');
            Route::get('participaciones/buscar', 'ParticipacionController@index')->name('participar.buscar');

            // General Setting
                  Route::get('Config', 'DepositController@index')->name('config.general');
                  
                  
             //retiros
                Route::get('retiros', 'WithdrawalController@index')->name('retiros');
                Route::post('retiros/pay', 'WithdrawalController@pay' )->name('retiros.pay');


                       //envío masivo
            Route::get('send_masivo', 'ComunicateController@index')->name('masivo');
            Route::post('send_masivo/send', 'ComunicateController@send' )->name('masivo.send');
            
            
            //confirmacion de reriros
            Route::get('send/transaction'   , 'WithdrawalController@pendientes')->name('with.pend');
            Route::get('confirm/transaction', 'WithdrawalController@confirmadas')->name('with.confi');
            
            
            // nfts
                Route::get('nfts/compras', 'nftsController@buy_pendientes')->name('nfts');
            Route::get('nfts/detail/{id}', 'nftsController@details')->name('nfts.details');
            Route::get('nfts/complete', 'nftsController@purchase')->name('nfts.complete');

            Route::post('nfts/compras', 'nftsController@editar_buy')->name('nfts.confirma');
            
            
          
            
            //Configuracion
            
            Route::get('recarga_smart', 'WithdrawalController@recargar_balance')->name('recargar.smart');
            Route::post('recarga_smart', 'WithdrawalController@smart_carga')->name('smart.recarga');
            
                Route::get('mis_nfts', 'nftsController@index')->name('my.nfts');
            Route::get('mis_nfts/{id}', 'nftsController@edit')->name('my.nfts.edit');
            Route::Post('mis_nfts', 'nftsController@save')->name('nfts.update');


        });






          // Frontend

        Route::middleware(['CheckAdminAccess'])->name('frontend.')->prefix('frontend')->group(function () {
            Route::post('store', 'FrontendController@store')->name('store');
          

        });



    });

});

