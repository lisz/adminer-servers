# adminer-servers
adminer 快捷登录


> 同级目录下添加文件 `.adminer-servers.json`

```json
{
  "adminerUrl": "http://127.0.0.1/adminer/adminer-zh.php",
  "servers": [
    {
      "driver": "server",
      "name": "本地 MySQL",
      "host": "127.0.0.1:3306",
      "username": "root",
      "password": "123456",
      "database": "",
      "group": "本地"
    },
    {
      "driver": "server",
      "name": "本地 PostgreSQL",
      "host": "127.0.0.1:5432",
      "username": "postgres",
      "password": "123456",
      "database": "",
      "group": "本地"
    }
  ]
}
```

- driver

| 数据库类型 | driver |
|:---|:---|
| MySQL / MariaDB | server |
| SQLite | sqlite |
| PostgreSQL | pgsql |
| Oracle (beta) | oracle |
| MS SQL | mssql |

