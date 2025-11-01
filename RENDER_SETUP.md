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

### 500エラーが発生する場合

1. **APP_KEYの確認**
   - 環境変数`APP_KEY`が正しく設定されているか確認

2. **データベース接続の確認**
   - データベース接続情報が正しいか確認
   - マイグレーションが正常に実行されているかログで確認

3. **ログの確認**
   - Renderの「Logs」タブで詳細なエラーメッセージを確認

4. **デバッグモードを一時的に有効化**
   ```bash
   APP_DEBUG=true
   LOG_LEVEL=debug
   ```
   問題を特定したら必ず`APP_DEBUG=false`に戻してください

### ストレージの問題

画像がアップロードできない場合:
- デプロイスクリプトで`storage:link`が実行されているか確認
- ストレージディレクトリの権限が正しいか確認

### パフォーマンスの問題

- キャッシュが有効になっているか確認（`php artisan config:cache`が実行されているか）
- データベースインデックスが適切に設定されているか確認

## 参考情報

- [Render公式ドキュメント](https://render.com/docs)
- [Laravel公式デプロイガイド](https://laravel.com/docs/deployment)

