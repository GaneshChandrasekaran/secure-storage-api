[![Build status](https://badge.buildkite.com/93a54ac419c1a6e17ca5b3118a7e9c168ff1aee26aa42af91a.svg?branch=main)](https://buildkite.com/gchub/secure-storage-api/builds?branch=main)

# Secure Information Storage REST API

### Project setup

* Add `secure-storage.localhost` to your `/etc/hosts`: `127.0.0.1 secure-storage.localhost`

* Navigate to `docker/nginx/` directory

* Run `brew install mkcert`

* Run `mkcert -install`

* Run `mkcert secure-storage.localhost`

* Run `make init` to initialize project

* Open in the browser: https://secure-storage.localhost/item Should get `Full authentication is required to access this resource.` error, because first you need to make `login` call (see `postman_collection.json` or `SecurityController` for more info).

### Run tests

make tests

### API credentials

* User: john
* Password: maxsecure

### Postman requests collection

You can import all available API calls to Postman using `postman_collection.json` file

### API Documentation

Open the following link in the browser to view API capabilities:
https://secure-storage.localhost/api/doc 
