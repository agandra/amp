<?php

return [

    // The user ID column name in your database for tables
    'userColumnKeyName' => 'user_id',

    'user'=>array(

        // Function to fetch currently logged in user
        'current'=>'\Auth::user',

    ),



];