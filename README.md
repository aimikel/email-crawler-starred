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

#### The workflow of the creating a NEW Job
1. The user submits a URL address to be parsed using the Interface. Alternatively, a POST API request to `DOMAIN/api/crawler/addJob` with POST parameter `url` can be used.
2. The application evaluates if the URL is valid and prompts the user with a suitable message.
3. The application saves a new Job in DB with status `in_progress`.


#### Quick Explanation of a NEW Job
The URL validation process is performed using filter_var() with FILTER_VALIDATE_URL parameter. If POST API request is used to create a new Job, the application will return a `JSON` object with the created Job information.

---


#### The workflow of VIEW ALL Jobs request
1. The user can view all Jobs information using the interface. Alternatively, a GET request to `DOMAIN/api/crawler/getJobs`.
2. The application retrieves all Jobs data from db and returns them in `JSON` format.
3. In order to retrieve Jobs information in `XML`, a parameter `?format=XML` is required.


#### Quick Explanation of GET ALL Jobs
For assignment purposes, there is no validation of the user. The `getJobs` request returns all the available Jobs from db.

---

## The workflow of GET a single Job request
1. The user can GET a specific Job's information using the parameter `uuid` in the GET request.
2. The application retrieves the Job (if available by uuid) and returns the specified Job's information in `JSON` format.
3. In order to retrieve the Job in `XML`, a parameter `?format=XML` is also required.

#### Quick Explanation of GET SINGLE Job
After performing the GET ALL Jobs request, a uuid is returned for each Job. The user must use the parameter `?uuid=THE_UUID_VALUE` to retrieve the information concerning the specified Job.

---

## The workflow of the Crawler
1. The application retrieves all the `in_progress` Jobs.
2. For each `in_progress` Job the application retrieves the initial Url provided by the user.
3. A `cURL` is performed to get the URL response.
4. Emails are then retrieved through the use of regular expressions match.
5. Following URLs in `<a href>` tags are retrieved using DOMXpath.
6. The application categorizes the initial URL as "Parsed" and registers its following URLs as "toParse".
7. The difference between the "Parsed" and "toParse" arrays is calculated so that the application knows which URLs need to be further parsed.
8. The same process is applied to all URLs until the "toParse" array is empty.

#### Quick explanation of the Crawler
The Crawler was designed in a way that could handle the parsing of multiple URLs. 

Instead of parsing each URL address found in real-time-process (a processes that needs time to be executed), the application saves in db the records with the Job details (uuid, status and created date) and creates a record in the Urls table with the initial URL that is connected with the Job's UUID.
Each Job is thus saved in database and has a status=0 when inserted, that means the Job is `in_progress`. 

In the application (not the API call) there is another functionality where, it retrieves the `in_progress` Jobs and finds the initial URL that is connected to each Job. From that point on, the application parses all emails and follows all the URLs that are contained in the initial URL given. For the email filtering and recognition, regular expressions matching has been used.
For the filtering of the URLs, DOMXpath has been used, assuming that the application will match with all `<a href>` tags.


When all URLS are parsed and followed, the application updates the Job to status 1, that means `completed`. According to the project's requirements only the URLs hosted on the same domain are followed.

In that way, the application can handle millions of Jobs and the only handling and parsing limitations are imposed by the server's recourses.

A daemon/worker/queue/cron can be used to run the above `Run Process` functionality. Because i have own a simple shared web hosting account, i could not test any of them. 
Instead of the above, the `execute jobs` functionality is emulated by clicking the button `Run Process`.

Until the `Run Process` functionality is completed, the Jobs stored in DB can still be retrieved but with showing the status `in_progress`. 

---

#### Sample POST response of addJob
```php
[
{
   "response": "success",
   "uuid": "1854659d024dd77e1f960923732",
   "status": "in_progress"
}
]
```
---

#### Sample GET response of getJobs/ getJob

```php
[
{
   "response": "success",
   "jobs": [
       {
           "uuid": "2495759d01809a06ab471379864",
           "url_address": "http://www.aimilia-kelaidi.gr/test.html",
           "job_created_date": "2017-10-01 01:17:45",
           "job_status": "completed",
           "urls": [
               {
                   "url_address": "http://www.aimilia-kelaidi.gr/test.html",
                   "emails": [
                       {
                           "email_address": "aim.kel@gmail.com"
                       },
                       {
                           "email_address": "gg@ipdigital.er"
                       }
                   ]
               },
               {
                   "url_address": "http://www.aimilia-kelaidi.gr/test2.html",
                   "emails": [
                       {
                           "email_address": "asds@dsadsa.gr"
                       },
                       {
                           "email_address": "mail1@mail.gr"
                       }
                   ]
               },
               {
                   "url_address": "http://www.aimilia-kelaidi.gr/test3.html",
                   "emails": [
                       {
                           "email_address": "asds@dsadsa.gr"
                       },
                       {
                           "email_address": "mail1@mail.gr"
                       }
                   ]
               },
               {
                   "url_address": "http://www.aimilia-kelaidi.gr/test1.html"
               }
           ]
       },
       {
           "uuid": "1854659d024dd77e1f960923732",
           "url_address": "http://www.example.com",
           "job_created_date": "2017-10-01 02:12:29",
           "job_status": "in_progress",
           "urls": [
               {
                   "url_address": "http://www.example.com"
               }
           ]
       }
   ]
}
]

```

---

### Installation and Configuration

1. Download or clone repository into your webserver. The domain should point to folder `public` where `index.php` will route into `Codeigniter`.
2. In the folder where the application was installed perform the following: Run `cmd - command line` the command: composer create-project 
2. Create a database in your server.
3. Replace `base_url` in `applications/config/config.php` with your domain name.
4. Change `hostname`, `username`, `password` and `database` in `applications/config/database.php`.
5. Run migration file to create the database schema. The migration URL is `YOUR_DOMAIN/migrate`.

---

### Code Testing

For code testing purposes, built-in unit_test library of CodeIgniter has been used. 6 tests were performed in total, 3 functions were tested twice with correct and failed data as parameters.
The first 3 of these tests should value as Passed while the rest 3 of them should value as Failed.

---

### How to Use and Test
* The project can be used by providing with a valid URL through the form field at http://aimilia-kelaidi.gr/starred/public/
* After successfully entering the above URL, you need to manually press the 'Run Process' button, otherwise all Jobs will remain in status `in_progress`.
* The API is uploaded on my shared hosting account with limited recourses.
* The API URL to POST a new Job is http://aimilia-kelaidi.gr/starred/public/api/crawler/addJob with POST parameter `url`.
* The API URL to GET ALL Jobs information is http://aimilia-kelaidi.gr/starred/public/api/crawler/getJobs 
* The API URL to GET a single Job information is http://aimilia-kelaidi.gr/starred/public/api/crawler/getJob?uuid=XXX , where the uuid parameter is dynamically used for already saved Jobs.
* The Testing URL Report is http://aimilia-kelaidi.gr/starred/public/testing

---

### Notes

* In order to check if the URL submitted by the user is a valid URL a check with filter_var() is performed.
* Every Job is created with a UUID as it was considered a necessity for each and every Job to be unique. 
* URLs creation is based on auto-increment ID because its uniqueness per Job is not necessary.

---

### FUTURE EXTENSIONS
There are some things that would need to be concidered for the application's extension. Some of those could include the following:

* Create API KEYS for each user to use the API functionality.
* Run the `Run Process` functionality as a worker or as a separated thread at least, immediately after the `Create new Job` button is pressed, based on server load.
* ORM could be used ideally for quering the database.