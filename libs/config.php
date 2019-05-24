<?php

        // where is your .navcoin4/.cookie?
        $navcookie = ".cookie";


        // get user/pass from .navcoin4/.cookie:
        $user =  explode(":",file_get_contents($navcookie))[0];
        $pass =  urlencode(explode(":",file_get_contents($navcookie))[1]);

        $wallets = array();
        $wallets['NavCoin'] = array(
                "user" => $user,
                "pass" => $pass,
                "host" => "localhost",
                "port" => 44444,
                "protocol" => "http",
                "ticker" => "NAV"
        );
?>

