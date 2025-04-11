# sign-jwt-rsa

**Setup**
- Build the docker image
  - `docker-compose build`
- Run containers
  - `docker-compose up -d`
- Change `INTEGRATION_KEY` & `PRIVATE_KEY` constants in `sign.php` to your own
- Call `http://localhost:88/sign.php` with cURL or Postman

**Postman**

You may want to save the signed token in a envar by using `Tests` or `Post-Request Script`:

```
var response = pm.response.json();
pm.environment.set("local_signed_token", response.signedToken);
```

**Todo**

- Could pass IntegrationKey and PrivateKey in the Payload, but we'd need to base64encode them and for now it's just hardcoded.