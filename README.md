# Fifa-15-Autobuyer
Project designed to automate functions through EA's Fifa 15 Web App

###### Please Note: This project has been abandoned.
In January 2015, EA took down the web application due to the rise in automated bots causing damage to the marketplace. Since this tool relies on the web application to function, I have since abandoned development. I have decided however to public for educational and portfolio purposes.


### Introduction
EA launched a web application to interact with the Ultimate Team marketplace across consoles. By interacting with this web application, it is possible to replicate HTTP requests to send and receive data through the web application.

### How it works

#### Login
To log in using this autobuyer, you need to have 2-step verification activated on your Origin account. This means when you first log in, you then need to type in a code sent to your email address. To bypass this restriction, you need to use your Origin Backup Codes (Account Settings -> Privacy). There are 6 of them, just copy and paste them into the field on the autobuyer and click connect.

Improvements: Originally I intended to write a PHP script which automatically logs into your mail, searches for the Origin email and extracts the code. This will remove the reconnect limit of 6.

#### Connection
The autobuyer connects to EA's web app using Guzzle to send the HTTP requests. The requests are build with [Fifa-request-forge](https://github.com/ebess/Fifa-15-Ultimate-Team-WebApp-Api) designed by ebess. Login request contains the backup code, email address, password and secret answer (see php/ui.php).
The cookie is then stored in a Cookie jar for future requests.

#### Functions
Basic functions can be found in /php/functions.php and include retrieving coins, searching for players, automatic bidding (with min and max bids), automatic buy (min & max BuyItNow). Also included in functions is TradePile and WishList management.

### Contact
Any questions feel free to contact me on mike.shiner@hotmail.com
All code here is written by me and is free to use if you want to apply it to EA’s new Fifa 16 web app.
