# API Routes

### `POST` [/api/register](http://localhost/slim-rest-base/api/register)
##### security.auth.controller:register
###### register

### `POST` [/api/login](http://localhost/slim-rest-base/api/login)
##### security.auth.controller:login
###### login

### `POST` [/api/auth/refresh](http://localhost/slim-rest-base/api/auth/refresh)
##### security.auth.controller:refresh
###### jwt.refresh

### `GET` [/api/users/me](http://localhost/slim-rest-base/api/users/me)
##### security.auth.controller:me
###### users.me

### `GET` [/](http://localhost/slim-rest-base/)
##### core.controller:root
###### root

### `GET` [/api/logs/{id:[0-9]+}](http://localhost/slim-rest-base/api/logs/{id:[0-9]+})
##### LogController:getLog
###### get_log

### `GET` [/api/logs](http://localhost/slim-rest-base/api/logs)
##### LogController:getLogs
###### get_logs

### `POST` [/api/logs](http://localhost/slim-rest-base/api/logs)
##### LogController:postLog
###### post_log

### `PUT` [/api/logs/{id:[0-9]+}](http://localhost/slim-rest-base/api/logs/{id:[0-9]+})
##### LogController:putLog
###### put_log

### `DELETE` [/api/logs/{id:[0-9]+}](http://localhost/slim-rest-base/api/logs/{id:[0-9]+})
##### LogController:deleteLog
###### delete_log

### `GET` [/api/logs/{log_id:[0-9]+}/comments/{comment_id:[0-9]+}](http://localhost/slim-rest-base/api/logs/{log_id:[0-9]+}/comments/{comment_id:[0-9]+})
##### LogCommentController:getLogComment
###### get_log_comment

### `GET` [/api/logs/{log_id:[0-9]+}/comments](http://localhost/slim-rest-base/api/logs/{log_id:[0-9]+}/comments)
##### LogCommentController:getLogComments
###### get_log_comments

### `POST` [/api/logs/{log_id:[0-9]+}/comments](http://localhost/slim-rest-base/api/logs/{log_id:[0-9]+}/comments)
##### LogCommentController:postLogComment
###### post_log_comment

### `PUT` [/api/logs/{log_id:[0-9]+}/comments/{comment_id:[0-9]+}](http://localhost/slim-rest-base/api/logs/{log_id:[0-9]+}/comments/{comment_id:[0-9]+})
##### LogCommentController:putLogComment
###### put_log_comment

### `DELETE` [/api/logs/{log_id:[0-9]+}/comments/{comment_id:[0-9]+}](http://localhost/slim-rest-base/api/logs/{log_id:[0-9]+}/comments/{comment_id:[0-9]+})
##### LogCommentController:deleteLogComment
###### delete_log_comment

