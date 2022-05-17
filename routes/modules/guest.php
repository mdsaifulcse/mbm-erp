<?php


Route::group(['prefix' => 'pms','namespace' => 'Pms','as'=>'pms.'], function(){


    Route::prefix('/rfp')->as('rfp.')->group(function (){

        Route::get('online-quotations/{proposalId}/{supplierId}', 'OnlineQuotationController@showOnlineQuotationForm')->name('online-quotations');
        Route::post('online-quotations', 'OnlineQuotationController@store')->name('online.quotations.store');

    });

});


	

