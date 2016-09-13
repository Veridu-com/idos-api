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

## Company Token

### With User

```php
echo App\Helper\Token::generateCompanyToken(
    '4c9184f37cff01bcdc32dc486ec36961:usr001',
    '8b5fe9db84e338b424ed6d59da3254a0',
    '4e37dae79456985ae0d27a67639cf335'
);
```

`eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiI4YjVmZTlkYjg0ZTMzOGI0MjRlZDZkNTlkYTMyNTRhMCIsInN1YiI6IjRjOTE4NGYzN2NmZjAxYmNkYzMyZGM0ODZlYzM2OTYxOnVzcjAwMSJ9.Dxh-qrVXkS3PHNHkkh50IMxtKQoS3W3HHClOs6h0gnM`

### Without User

```php
echo App\Helper\Token::generateCompanyToken(
    '',
    '8b5fe9db84e338b424ed6d59da3254a0',
    '4e37dae79456985ae0d27a67639cf335'
);
```

`eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiI4YjVmZTlkYjg0ZTMzOGI0MjRlZDZkNTlkYTMyNTRhMCJ9.7o7r5gl5tgdkOZALhYZgB1wd_Rn8keWVDSHcyfcquOo`

## Credential Token

```php
echo App\Helper\Token::generateCredentialToken(
    '4c9184f37cff01bcdc32dc486ec36961',
    'ef970ffad1f1253a2182a88667233991',
    '213b83392b80ee98c8eb2a9fed9bb84d'
);
```

`eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJlZjk3MGZmYWQxZjEyNTNhMjE4MmE4ODY2NzIzMzk5MSIsInN1YiI6IjRjOTE4NGYzN2NmZjAxYmNkYzMyZGM0ODZlYzM2OTYxIn0.oeiD9R7FlnMBiDW3UClRO39nvbMM-TTZkyedYaSysCc`
