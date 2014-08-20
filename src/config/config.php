<?php

return [

    // The user ID column name in your database for tables
    'userColumnKeyName' => 'user_id',

    'user'=>array(

        // Function to fetch currently logged in user
        'current'=>'\Auth::user',
        // Function to check if user is logged in
        'check' => '\Auth::check',

    ),

    'repo' => array(
    	//'User'=>'Agandra\Amp\UserRepo' ## Example

    ),

    'salt' => '',



];