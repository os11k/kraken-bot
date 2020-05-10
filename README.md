kraken-bot
============

Simple trading bot for Kraken, what sends notification via Telegram messenger

Please update following variables inside file:

$buyprice=7001; -- At this price or less  we are buying BTC from USD

$sellprice=9999; -- At this price or more we are selling BTC to USD

$apiToken = "1123456789:zzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzz"; -- Telegram API token

$chatID = "123456789"; -- Telegram chat ID

$key = 'zzzzzzzzzzzzzzzzzzzzzzzzzzzz'; -- Kraken key

$secret = 'oooooooooooooooooooooooooooo'; -- Kraken secret

### To install:
```
apt-get update
apt-get install php
apt-get install php-curl
cd /usr/src
git clone https://github.com/payward/kraken-api-client.git
cd ./kraken-api-client/php
git clone https://github.com/os11k/kraken-bot
cp ./kraken-bot/kra.php ./
```

then you need to edit crontab:

```crontab -e```

I have following setup, what triggers bot every 10 seconds:
```
* * * * * /usr/src/kraken-api-client/php/kra.php >> /var/log/alternatives.log
* * * * * ( sleep 10 ; /usr/src/kraken-api-client/php/kra.php >> /var/log/alternatives.log )
* * * * * ( sleep 20 ; /usr/src/kraken-api-client/php/kra.php >> /var/log/alternatives.log )
* * * * * ( sleep 30 ; /usr/src/kraken-api-client/php/kra.php >> /var/log/alternatives.log )
* * * * * ( sleep 40 ; /usr/src/kraken-api-client/php/kra.php >> /var/log/alternatives.log )
* * * * * ( sleep 50 ; /usr/src/kraken-api-client/php/kra.php >> /var/log/alternatives.log )
```
