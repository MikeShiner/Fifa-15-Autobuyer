# Fifa-15-Autobuyer
Project designed to automate functions through EA's Fifa 15 Web App

###### Please Note: This project has been abandoned.
In January 2015, EA took down the web application due to the rise in automated bots causing damage to the marketplace. Since this tool relies on the web application to function, I have since abandoned development. I have decided however to public for educational and portfolio purposes.


### Introduction
EA launched a web application to interact with the Ultimate Team marketplace across consoles. By interacting with this web application, it is possible to replicate HTTP requests to send and receive data through the web application.

### How it works

#### Login
To log in using this autobuyer, you need to have 2-step verification activated on your Origin account. This means when you first log in, you then need to type in a code sent to your email address. To bypass this restriction, you need to use your Origin Backup Codes (Account Settings -> Privacy). There are 6 of them, just copy and past them into the field on the autobuyer and click connect.

Improvements: Originally I intended to write a PHP script which automatically logs into your mail, searches for the Origin email and extracts the code. This will extend the usages from 6 reconnects to infinity.

#### Connection
The autobuyer connects to EA's web app using Guzzle to send the HTTP requests. The requests are build with with the email address, password and secret answer (editable in php/ui.php).
T
