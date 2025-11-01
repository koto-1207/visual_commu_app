# Render デプロイ設定ガイド

このアプリケーションをRenderにデプロイするための設定ガイドです。

## 必要な環境変数

Renderのダッシュボードで以下の環境変数を設定してください。

### 必須の環境変数

```bash
# アプリケーション基本設定
APP_NAME="Visual Commu App"
APP_ENV=production
APP_KEY=base64:xxxxx...  # 下記の方法で生成してください
APP_DEBUG=false
APP_URL=https://your-app-name.onrender.com

# データベース設定（Renderで作成したデータベースの情報を使用）
DB_CONNECTION=mysql
DB_HOST=your-database-host.onrender.com
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password

# セッション設定
SESSION_DRIVER=database
SESSION_LIFETIME=120

# ログ設定
LOG_CHANNEL=stderr
LOG_LEVEL=error

# キャッシュ設定
CACHE_STORE=database

# キュー設定
QUEUE_CONNECTION=database
```

## APP_KEYの生成方法

ローカル環境で以下のコマンドを実行:

```bash
php artisan key:generate --show
```

出力された値（例: `base64:xxxxxxxxxxxxxx...`）をRenderの環境変数`APP_KEY`に設定してください。

## Renderでのデプロイ手順

### 1. 新しいWeb Serviceを作成

- Renderダッシュボードで「New +」→「Web Service」を選択
- GitHubリポジトリを接続

### 2. ビルド設定

- **Name**: アプリケーション名を入力
- **Environment**: Docker
- **Region**: 任意（日本に近いリージョン推奨）
- **Branch**: main または master

### 3. データベースの作成

- 「New +」→「PostgreSQL」または「MySQL」を選択
- データベース作成後、接続情報を環境変数に設定

### 4. 環境変数の設定

- Web Serviceの「Environment」タブで上記の環境変数を全て設定

### 5. デプロイ

- 「Create Web Service」をクリック
- デプロイが完了するまで待つ（初回は数分かかります）

## トラブルシューティング

### よくあるエラーと解決方法

#### 1. 500エラーが発生する場合

**原因チェックリスト:**

- [ ] **APP_KEYが設定されていない**
  ```bash
  # ローカルで生成
  php artisan key:generate --show
  # 出力された値をRenderの環境変数APP_KEYに設定
  ```

- [ ] **データベース接続エラー**
  - Renderの環境変数でDB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORDが正しく設定されているか確認
  - データベースが起動しているか確認

- [ ] **マイグレーションが実行されていない**
  - Renderのログで「Running migrations...」が成功しているか確認
  - 手動で実行: Renderのシェルから`php artisan migrate --force`

#### 2. Route [profile] not defined エラー

このエラーは**ルートキャッシュの問題**です。

**解決方法:**
```bash
# Renderのシェルまたはデプロイスクリプトで実行
php artisan route:clear
php artisan route:cache
```

または、環境変数を追加:
```bash
ROUTE_CACHE=false
```

#### 3. Column 'user_id' not found エラー

**マイグレーションが実行されていない**ことが原因です。

**解決方法:**
1. Renderのシェルにアクセス
2. 以下を実行:
```bash
php artisan migrate:status
php artisan migrate --force
```

3. マイグレーションの状態を確認:
```bash
php artisan migrate:status
```

#### 4. デバッグモードを一時的に有効化

問題を特定するために、一時的にデバッグモードを有効化:

```bash
APP_DEBUG=true
LOG_LEVEL=debug
```

**⚠️ 重要:** 問題を特定したら必ず`APP_DEBUG=false`に戻してください！

#### 5. デバッグ情報の取得

Renderのシェルから以下のスクリプトを実行:

```bash
bash scripts/render-debug.sh
```

このスクリプトは以下の情報を表示します:
- PHP/Laravelのバージョン
- 環境変数の設定状況
- マイグレーションの状態
- ルートの一覧
- ストレージリンクの状態

### ストレージの問題

画像がアップロードできない場合:
- デプロイスクリプトで`storage:link`が実行されているか確認
- ストレージディレクトリの権限が正しいか確認
- Renderのログで権限エラーがないか確認

### キャッシュの問題

ルートやビューが更新されない場合:

```bash
# すべてのキャッシュをクリア
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 再キャッシュ
php artisan config:cache
php artisan route:cache
```

### パフォーマンスの問題

- キャッシュが有効になっているか確認（`php artisan config:cache`が実行されているか）
- データベースインデックスが適切に設定されているか確認
- `php artisan optimize`が実行されているか確認

## 参考情報

- [Render公式ドキュメント](https://render.com/docs)
- [Laravel公式デプロイガイド](https://laravel.com/docs/deployment)

