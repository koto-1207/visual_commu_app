# Sail コマンド一覧

このプロジェクトはLaravel Sailを使用しています。以下は便利なコマンド一覧です。

## 基本コマンド

### Sailの起動・停止

```bash
# コンテナを起動
./vendor/bin/sail up -d

# コンテナを停止
./vendor/bin/sail down

# ログを表示
./vendor/bin/sail logs -f
```

## Render デプロイ前のチェック

### 1. ルートの確認

```bash
# ルートキャッシュをクリア
./vendor/bin/sail artisan route:clear

# profileルートが存在するか確認
./vendor/bin/sail artisan route:list --name=profile

# すべてのルート一覧
./vendor/bin/sail artisan route:list
```

### 2. マイグレーションの確認

```bash
# マイグレーション状態を確認
./vendor/bin/sail artisan migrate:status

# マイグレーションを実行
./vendor/bin/sail artisan migrate

# マイグレーションをリセット（注意：データが消えます）
./vendor/bin/sail artisan migrate:fresh
```

### 3. キャッシュ管理

```bash
# すべてのキャッシュをクリア
./vendor/bin/sail artisan cache:clear
./vendor/bin/sail artisan config:clear
./vendor/bin/sail artisan route:clear
./vendor/bin/sail artisan view:clear

# キャッシュを再生成
./vendor/bin/sail artisan config:cache
./vendor/bin/sail artisan route:cache
./vendor/bin/sail artisan view:cache
```

### 4. データベース操作

```bash
# データベースに接続
./vendor/bin/sail mariadb

# 新しいマイグレーションを作成
./vendor/bin/sail artisan make:migration create_example_table

# シーダーを実行
./vendor/bin/sail artisan db:seed
```

## Renderエラーのローカル再現

Renderで発生したエラーをローカルで再現してテストする方法：

### Route [profile] エラーを再現

```bash
# ルートをキャッシュ（本番環境と同じ状態にする）
./vendor/bin/sail artisan route:cache

# アプリケーションにアクセスしてエラーを確認
# http://localhost

# 修正後、キャッシュをクリア
./vendor/bin/sail artisan route:clear
```

### マイグレーションエラーを再現

```bash
# データベースをリセット
./vendor/bin/sail artisan migrate:fresh

# 特定のマイグレーションだけロールバック
./vendor/bin/sail artisan migrate:rollback --step=1

# 再度マイグレーション
./vendor/bin/sail artisan migrate
```

## デバッグ

### ログの確認

```bash
# Laravelのログを表示
./vendor/bin/sail artisan pail

# または直接ファイルを確認
tail -f storage/logs/laravel.log
```

### アプリケーション情報

```bash
# Laravelバージョン
./vendor/bin/sail artisan --version

# 環境情報
./vendor/bin/sail artisan about

# 設定値の確認
./vendor/bin/sail artisan config:show database
```

## Composer / NPM

```bash
# Composer パッケージをインストール
./vendor/bin/sail composer install

# NPMパッケージをインストール
./vendor/bin/sail npm install

# フロントエンドをビルド
./vendor/bin/sail npm run build

# 開発サーバー（Vite）を起動
./vendor/bin/sail npm run dev
```

## テスト

```bash
# すべてのテストを実行
./vendor/bin/sail artisan test

# 特定のテストを実行
./vendor/bin/sail artisan test --filter=ExampleTest

# Pest テスト
./vendor/bin/sail pest
```

## エイリアスの設定（オプション）

毎回 `./vendor/bin/sail` と入力するのが面倒な場合、エイリアスを設定できます：

```bash
# ~/.bashrc または ~/.zshrc に追加
alias sail='./vendor/bin/sail'

# 再読み込み
source ~/.bashrc  # または source ~/.zshrc
```

設定後は以下のように短縮できます：

```bash
sail up -d
sail artisan migrate
sail npm run dev
```

## Render デプロイ前の最終チェックリスト

デプロイ前に以下を確認してください：

```bash
# 1. すべてのマイグレーションが実行されているか
./vendor/bin/sail artisan migrate:status

# 2. ルートが正しく定義されているか
./vendor/bin/sail artisan route:list --compact

# 3. テストが通るか
./vendor/bin/sail artisan test

# 4. キャッシュをクリア
./vendor/bin/sail artisan optimize:clear

# 5. 本番環境用にキャッシュを生成（テスト用）
./vendor/bin/sail artisan config:cache
./vendor/bin/sail artisan route:cache

# 6. アプリケーションが正常に動作するか確認
# http://localhost でテスト

# 7. キャッシュをクリア（開発に戻す）
./vendor/bin/sail artisan optimize:clear
```

## トラブルシューティング

### コンテナが起動しない

```bash
# コンテナを完全に削除して再作成
./vendor/bin/sail down -v
./vendor/bin/sail up -d --build
```

### データベース接続エラー

```bash
# データベースコンテナの状態を確認
./vendor/bin/sail ps

# データベースコンテナのログを確認
docker logs visual_commu_app-mariadb-1
```

### パッケージの問題

```bash
# Composerのキャッシュをクリア
./vendor/bin/sail composer clear-cache
./vendor/bin/sail composer install

# NPMのキャッシュをクリア
./vendor/bin/sail npm cache clean --force
./vendor/bin/sail npm install
```

## 参考リンク

- [Laravel Sail 公式ドキュメント](https://laravel.com/docs/sail)
- [Docker 公式ドキュメント](https://docs.docker.com/)

