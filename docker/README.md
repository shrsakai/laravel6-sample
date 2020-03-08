## docker コマンド

基本コマンド

```bash
cd ./docker

# env を作成
cp env-example .env

# docker 開始
docker-compose build && docker-compose up -d

# docker 停止
docker-compose down
```

build→停止→開始 を一括操作

```bash
docker-compose build && docker-compose down && docker-compose up -d
```

基本URL (Mac)

- Top画面 … http://127.0.0.1/
- phpMyAdmin … http://127.0.0.1:8080/

基本URL(Windows)

- Top画面 … http://192.168.99.100/
- phpMyAdmin … http://192.168.99.100:8080/
