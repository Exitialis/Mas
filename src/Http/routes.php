<?php

Route::group([ "prefix" => config("mas.route_prefix"), "namespace" => "Exitialis\Mas\Http\Controllers"], function(){
    Route::get("/", ["as" => "mas.index", "uses" => "MasClientController@index"]);

    Route::post("/join", ["as" => "mas.join", "uses" => "MasClientController@join"]);
    Route::get("/hasJoined", ["as" => "mas.hasJoined", "uses" => "MasClientController@hasJoined"]);
    Route::get("/profile/{user}", ["as" => "mas.profile", "uses" => "MasClientController@profile"]);

    Route::get("/skins/{username}", ["as" => "mas.skin", "uses" => "MasTexturesController@getSkin"]);
    Route::get("/cloaks/{username}", ["as" => "mas.cloaks", "uses" => "MasTexturesController@getCloak"]);
    Route::get("/textures/{username}", ["as" => "mas.textures", "uses" => "MasTexturesController@getTextures"]);

    Route::get("/server", ["as" => "mas.server", "uses" => "MasClientController@server"]);

    Route::get("/auth/{login}/{password}", ["as" => "mas.auth", "uses" => "MasLoginController@auth"]);
    Route::get("/refresh", ["as" => "mas.refresh", "uses" => "MasLoginController@refresh"]);
    Route::get("/validate", ["as" => "mas.validate", "uses" => "MasLoginController@MasValidate"]);
    Route::get("/invalidate", ["as" => "mas.invalidate", "uses" => "MasLoginController@invalidate"]);


});