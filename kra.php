#!/usr/bin/php
<?php
require_once 'KrakenAPIClient.php';

$buyprice=7001;
$sellprice=9999;

// Telega credentials
$apiToken = "1123456789:zzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzz";
$chatID = "123456789";
// Kraken api credentials
$key = 'zzzzzzzzzzzzzzzzzzzzzzzzzzzz';
$secret = 'oooooooooooooooooooooooooooo';

##################################################
function send2telega($text) {

global $apiToken, $chatID;

$data = [
    'chat_id' => $chatID,
    'text' => $text
];

$response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
}
##################################################

// set which platform to use (beta or standard)
$beta = false;
$url = $beta ? 'https://api.beta.kraken.com' : 'https://api.kraken.com';
$sslverify = $beta ? false : true;
$version = 0;

$kraken = new \Payward\KrakenAPI($key, $secret, $url, $version, $sslverify);

// Query public ticker info for BTC/USD pair:
$res = $kraken->QueryPublic('Ticker', array('pair' => 'XXBTZUSD'));
if (!empty($res['error'])) {
        print "error getting price\n";
        print_r($res);
        send2telega("error getting price");
        send2telega(json_encode($res));
        exit("unable to get price");
}
$price = $res['result']['XXBTZUSD']['c']['0'];

// Query private asset balances
$res = $kraken->QueryPrivate('Balance');
if (!empty($res['error'])) {
        print "error getting balance\n";
        send2telega("error getting balance");
        send2telega(json_encode($res));
        print_r($res);
        exit("unable to get balance");
}
$usdbalance = $res['result']['ZUSD'];
$btcbalance = $res['result']['XXBT'];

$res = $kraken->QueryPrivate('OpenOrders', array('trades' => true));
if (!empty($res['error'])) {
        print "error getting open orders\n";
        send2telega("error getting orders");
        send2telega(json_encode($res));
        print_r($res);
        exit("unable to get open orders");
}
$openorder = $res['result']['open'];

##################################################
function printPriceAndETC() {
        global $price, $usdbalance, $btcbalance;
        print "XXBTZUSD $price \n";
        print "USD balance $usdbalance \n";
        print "BTC balance $btcbalance \n";
}
##################################################

##################################################
function sent2telegaPriceAndETC() {
        global $price, $usdbalance, $btcbalance;
        send2telega("XXBTZUSD $price\nUSD balance $usdbalance\nBTC balance $btcbalance");
}
##################################################


printPriceAndETC();
#sent2telegaPriceAndETC();

if (empty($openorder)) {

        print "No open orders \n";

        if ($btcbalance > 0 and $price > $sellprice) {
                printPriceAndETC();
                sent2telegaPriceAndETC();
                print "selling BTC $btcbalance to USD \n";
                $res = $kraken->QueryPrivate('AddOrder', array(
                        'pair' => 'XXBTZUSD',
                        'type' => 'sell',
                        'ordertype' => 'market',
                        'volume' => $btcbalance
                ));
                print_r($res);
                send2telega(json_encode($res));
        }

        if ($usdbalance > 0 and $price < $buyprice) {
                printPriceAndETC();
                sent2telegaPriceAndETC();
                print "buying BTC with USD $usdbalance \n";
                $res = $kraken->QueryPrivate('AddOrder', array(
                        'pair' => 'XXBTZUSD',
                        'type' => 'buy',
                        'ordertype' => 'market',
                        'volume' => $usdbalance/$price
                ));
                print_r($res);
                send2telega(json_encode($res));
        }
}

