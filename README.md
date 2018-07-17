Installation
============

1. `composer install`
2. Configure `config/parameters.yaml` to your database credentials
3. Put following nginx config:
```
server {
    listen 80;

    server_name test-bit.loc;


    access_log /var/log/nginx/test_bit.access.log;
    error_log /var/log/nginx/test_bit.error.log;

    charset utf-8;

    location / {
        root /var/www/bit/test/public;
        rewrite ^ /index.php last;
    }

    location ~ /index.php$ {
        root /var/www/bit/test/public;

        fastcgi_pass www;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root/index.php;
        fastcgi_intercept_errors on;
        fastcgi_read_timeout 600;
        include fastcgi_params;
    }
}
```
4. Add host `test-bit.loc` to your `/etc/hosts` file
5. Create schema and data to database:
```
create table users
(
	id int auto_increment	primary key,
	email varchar(255) not null,
	password varchar(255) not null,
	balance decimal(10,2) null,
	constraint users_email_uindex	unique (email)
);

create table payouts
(
	id int auto_increment	primary key,
	payout_at timestamp default CURRENT_TIMESTAMP not null,
	owner_id int not null,
	sum decimal(10,2) null,
	constraint payouts_owner foreign key (owner_id) references users (id)
);

create index payouts_owner on payouts (owner_id);

create table sessions
(
	sess_id varbinary(128) not null	primary key,
	sess_data blob not null,
	sess_lifetime mediumint not null,
	sess_time int unsigned not null
) collate=utf8_bin;

insert into users (email, password, balance) values ('test@example.com', '$argon2id$v=19$m=65536,t=2,p=1$LNBlbFDhZwzW9X4SfYHNIw$I6oo9u2ErZWflaLS1wLesuHxHiQKdeIkKMYwRzWg9Jw', 1000.00);
```
6. Follow `test-bit.loc/login` in your browser and enter email `test@example.com` and password `test`
