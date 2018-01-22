# Goal
My goal with this project was to create a simple, self-hosted to-do app that does not rely on any dependencies or frameworks.  
I also wanted to make an API that was easy to use so that user can build their own clients.

# Stack
* A web server (preferably nginx)
* PHP 7.0 (latest release should work)
* MySQL (MariaDB should also work)

Make sure to enable MySQLi in your php.ini config file!

# API
Right now, you may only access the API if you're logged in. This will change.  
In the future, you will be able to add/remove/update notes without being logged in by using your username and an API-key.  
I recomend [Postman](https://www.getpostman.com/) if you'd like to test the API.


## Get notes
`GET api/api/.php?action=list` responds like this when there is data:
```json
{
	"notes": [
		{
			"id": 74,
			"title": "Improve the API",
			"body": "Let users access the API with an API-key.",
			"due": "2018-01-30",
			"created": "2018-01-20",
			"importance": 10
		},
		{
			"id": 75,
			"title": "Rewrite the JavaScript",
			"body": "The JavaScript file is a mess, rewrite and comment it!",
			"due": "2018-02-15",
			"created": "2018-01-15",
			"importance": 5
		}
	]
}
```
And if there is none, this is what you'll get;
```json
{
	"notes": []
}
```

## Add a note
To add a note to your to-do-list:
`POST  api/api.php`  
Body:
```http
new-note&  
new-note-title=<note-title>&  
new-note-body=<note-body-text>&  
new-note-due-date=<note-due-date>&  
new-note-importance=<note-importance>&  
new-note-create-date=<note-creation-date>
```
Response on success:
```json
{
    "error": false,
    "code": 200,
    "message": "Success",
    "method": "POST"
}
```

## Delete a note
To delete a note, send a DELETE request:
`DELETE api/api.php`
Body:
```http
note-id=<note-id>
```
Response on success:
```json
{
    "error": false,
    "code": 200,
    "message": "Success. Removed note from database.",
    "method": "DELETE"
}
```

## Errors
If a request is correct but something else fails, you'll get a response like this:
```json
{
	"error": true,
	"code": 200,
	"message": "<error-message>",
	"method": "<method>"
}
```
If you make a request that does not fulfill the requirements you'll get a response like this:
```json
{
	"error": true,
	"code": 400,
	"message": "Bad request",
	"method": "<method>"
}
```
And if you're not logged in you'll get this:

```json
{
	"error": true,
	"code": 401,
	"message": "Unauthorized",
	"method": "<method>"
}
```

# Create database
The following SQL snippet will create 3 tables, one for the users, for the notes and one for the site settigs.
```SQL
--
-- Table structure for table `notes`
--

CREATE TABLE `notes` (
  `id` int(11) NOT NULL,
  `user` varchar(25) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `title` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `body` varchar(1024) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `due` date NOT NULL,
  `created` date NOT NULL,
  `importance` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `notes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `user` (`user`);

ALTER TABLE `notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;


--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(25) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `password` varchar(512) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `api_key` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `isAdmin` tinyint(1) NOT NULL DEFAULT '0',
  `reg_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `id` (`id`),
  ADD UNIQUE KEY `api_key` (`api_key`);

ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;


--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `setting` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `value` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

```