<?php

return [

    'user'=>array(
        // The user ID column name in your database for tables
        'columnName' => 'user_id',
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