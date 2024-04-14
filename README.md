## Configuration project

### Update .env file
```
GOOGLE_SHEET_APPLICATION_NAME=
GOOGLE_SHEET_DEVELOPER_KEY=
```

### Run database migrate
```
php artisan migrate
```

## Run parsing

```
php artisan parse:google-sheet {googlesheet_id} {sheet_name} 
```
