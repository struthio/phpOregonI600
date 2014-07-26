PHP Implementation of Weather OS Server

Background

Few years ago I bought Oregon Scientific I600 weather station. Main reason for this decision was possibility to display 4 day forecast right on my desk without need to turn on my PC just to check weather. Meanwhile I moved and didn’t had static internet access (I was connecting by GSM network) and sometimes Weather was synched sometimes not but I didn't took it as a big issue (and mainly thought what this was some issues with my ISP). On April I decided to take a look why my Weather is not synching anymore and ended up on Oregon Scientific Page (http://uk.oregonscientific.com/tips/OS-Weather-Server-Discontinuation-Notice.html) which said "However, I300 & I600 will be totally out of function after the weather server is shut down." (and they mean totally ! Even simple Clock was not working without server) - in other words I was left out with piece of junk that will never work again. 
Since I really liked this station I decided to search for solution, I searched through net and only stumbled upon other people question 'Why my I600 do not work anymore?'. I was also a bit of angry on myself that I didn't found this 'Discontinuation notice' sooner. On June I wanted to replay post on some local forum, and while searching Google for link I found another page titled 'Oregon Scientific I600 - Working Again' (http://users.skynet.be/luc.pauwels/luc/weatheros/) that contained documentation on Server <->Weather OS communication. Since I had everything I needed to create my own Server I started to write this code...

Requirements

This script was created on Ubuntu Server xx.xx, with following software installed:

Apache (with url rewrite module)
PHP 5.5.9 (with modules: )

This script must be accessible by following link (http://<your_host>/mds). Oregon request sub-pages to get information for Weather OS app.

Known Bugs:
Incorrect City Information - I cannot find way to correctly query Yahoo API to get proper City (for particular country) list - obtained list I either very big (but YQL have limit of 5000 rows per SELECT) or very small (almost no Cities at all).

Additional Information

Master branch of this repository should be also available on my page (http://weather.superstar.one.pl/mds) - just change in ....ini line:

Server=www.os-weather.com

to 

Server=weather.superstar.one.pl


Problems

A way to much requests to cache (ex. we query Yahoo cache even when we will not use this data, because we have cached XML).