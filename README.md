# BanIPs-CF

## About

EDIT: As of a while ago, this script does not function!


This PHP Script uses [Cloudflare's API](https://www.cloudflare.com/docs/client-api.html) to easily Ban/Unban IPs.
This script can also parse nginx logs and ban malicious entities.

## Usage

1. Download and extract anywhere on your webserver
2. Edit CF-API.php and add your own email/API link
3. Visit /banips-cf.php and ban or unban any IP you'd like.

### How to use the NGINX Logs as a way to ban IPs
* Grab some malicious entities in your logs
* Example: `51.15.61.192 - - [22/Jul/2017:00:08:56 -0400]  "POST /CGI/Execute HTTP/1.1" 404 162 "-" "curl/7.35.0"`
* Enter it into the Input
* Click Go! and then make sure there are no errors and click confirm
