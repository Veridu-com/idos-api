# Tokens

All tokens below have been generated with the following information:

- Username: **usr001**
- Company: **Veridu Ltd**
 - Public Key: **8b5fe9db84e338b424ed6d59da3254a0**
 - Private Key: **4e37dae79456985ae0d27a67639cf335**
- Credential: **My Test Key**
 - Public Key: **4c9184f37cff01bcdc32dc486ec36961**
 - Private Key: **2c17c6393771ee3048ae34d6b380c5ec**
- Handler: **idOS FB Scraper**
 - Public Key: **ef970ffad1f1253a2182a88667233991**
 - Private Key: **213b83392b80ee98c8eb2a9fed9bb84d**

## User Token

```php
echo App\Helper\Token::generateUserToken(
    'usr001',
    '4c9184f37cff01bcdc32dc486ec36961',
    '2c17c6393771ee3048ae34d6b380c5ec'
);
```

`eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiI0YzkxODRmMzdjZmYwMWJjZGMzMmRjNDg2ZWMzNjk2MSIsInN1YiI6InVzcjAwMSJ9.Bliu0m7HqKXUD7wLrvCmvTpTJmzq2ALDwUmErj6430M`

## Identity Token

```php
echo App\Helper\Token::generateIdentityToken(
    '5d41402abc4b2a76b9719d911017c592',
    '7d793037a0760186574b0282f2f435e7'
);
```
eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiI1ZDQxNDAyYWJjNGIyYTc2Yjk3MTlkOTExMDE3YzU5MiJ9.iL4KCEGvoYgTW8NGmP32o9k8UB45ydkhCS7nkEJ2iyg


`eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiI4YjVmZTlkYjg0ZTMzOGI0MjRlZDZkNTlkYTMyNTRhMCJ9.7o7r5gl5tgdkOZALhYZgB1wd_Rn8keWVDSHcyfcquOo`