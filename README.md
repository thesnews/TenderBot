# TenderBot

* **Copyright:** 2012 State News, Inc.
* **License:** MIT

TenderBot is a dirt simple one-way integration between [Tender](http://tenderapp.com) and [Basecamp](http://basecamp.com). It simply takes a new discussion from Tender and creates a new TODO in basecamp. It works great for us, but your mileage may vary.

## Requirements

* PHP5 with cURL lib
* OPTIONALLY a dedicated TenderBot account on Basecamp

## Install

1. Drop the file somewhere web readable, you should try to obfuscate the URL somewhat since Tender webhooks are open to all.
2. Set the config vars in the file
3. Add the webhook for a New Discussion in Tender
4. ...
5. Profit?
