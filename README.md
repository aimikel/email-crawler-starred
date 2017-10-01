# Starred Interview Assignment
---

by Aimilia Kelaidi (aim.kelaidi@gmail.com)

## Getting started

### Synopsis
This small email parser was implemented for the purposes of the Junior PHP Developer position assignment that was given to me on 21/09/2017 by Starred and
it was built in PHP using `Codeigniter v.3.1` through `composer` from https://github.com/kenjis/codeigniter-composer-installer,  `Restserver` through `composer` from https://github.com/chriskacerguis/codeigniter-restserver, JQuery CDN, Bootstrap CDN and Jquery DataTables plugin.

The project's purpose is to parse a valid URL entered by the user and provide with the URLs and all the email addresses that are included in those. Moreover, an API Layer was implemented in order to interact with the application. The API supports HTTP requests using method GET and POST. 

For assignment purposes, no `API KEY` is required, neither any other Authentication/Authorization service to use or install the application.

*The API was tested using LAMP Stack on Ubuntu 16.10 and WAMP64 on Windows 10. Also Postman was used to simulate requests.*

---

### Introduction
For this project a user interface was created. In this, the user :
1. Needs to fill in a valid URL address and create a new Job.
2. Needs to press `Run Process` button so that the system can start performng the parsing functionality. ` Check -Quick explanation of the Crawler- below for more information `.
3. View all saved Jobs and their related data.

As per the API Layer functionality, it allows the following operations:
1. Allow the user to `POST` a new Job.
2. Allow the user to `GET` information on all Jobs.
3. Allow the user to `GET` information on a specific Job.

---