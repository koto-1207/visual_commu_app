# Render デプロイ前チェックリスト

このチェックリストを使用して、Renderへのデプロイ前に必要な確認を行ってください。

## ✅ ローカル環境での確認（Sail使用）

### 1. マイグレーションの確認

```bash
./vendor/bin/sail artisan migrate:status
```

**確認事項:**
- [ ] すべてのマイグレーションが「Ran」になっている
- [ ] `add_user_id_to_places_table` が実行されている

### 2. ルートの確認

```bash
./vendor/bin/sail artisan route:list --name=profile
```

**確認事項:**
- [ ] `profile` ルートが存在する
- [ ] エラーが表示されない

### 3. アプリケーションのテスト

```bash
# Sailを起動
./vendor/bin/sail up -d

# ブラウザで確認
# http://localhost
```

**確認事項:**
- [ ] ログインできる
- [ ] `/places` ページが表示される
- [ ] 画像のアップロードが動作する
- [ ] 500エラーが発生しない

### 4. 本番環境のキャッシュをテスト

```bash
# キャッシュを生成（本番環境と同じ状態にする）
./vendor/bin/sail artisan config:cache
./vendor/bin/sail artisan route:cache

# アプリケーションをテスト
# http://localhost

# テスト後、キャッシュをクリア
./vendor/bin/sail artisan config:clear
./vendor/bin/sail artisan route:clear
```

**確認事項:**
- [ ] キャッシュ後もアプリケーションが正常に動作する
- [ ] ルートが見つからないエラーが発生しない

## ✅ Gitの準備

### 1. 変更をコミット

```bash
# 変更されたファイルを確認
git status

# すべての変更をステージング
git add .

# コミット
git commit -m "Fix: Resolve Render deployment errors (route and migration issues)"
```

**確認事項:**
- [ ] 重要な変更がすべてコミットされている
- [ ] `.env` ファイルがコミットされていない（gitignoreされている）

### 2. リモートにプッシュ

```bash
git push origin main  # または master
```

**確認事項:**
- [ ] プッシュが成功した
- [ ] GitHubでコミットが確認できる

## ✅ Render環境の準備

### 1. APP_KEYの生成

```bash
# ローカルで実行
./vendor/bin/sail artisan key:generate --show
```

**出力例:**
```
base64:xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

**確認事項:**
- [ ] APP_KEYが生成された
- [ ] 値をコピーした

### 2. Renderの環境変数を設定

Renderダッシュボード → 該当のWeb Service → Environment タブ

**必須の環境変数:**

```bash
# アプリケーション設定
APP_NAME=Visual Commu App
APP_ENV=production
APP_KEY=base64:xxxxxxxxx...  # 上記で生成した値
APP_DEBUG=false
APP_URL=https://your-app-name.onrender.com

# データベース設定（Renderのデータベース情報を使用）
DB_CONNECTION=mysql
DB_HOST=your-database-host.onrender.com
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password

# セッション・キャッシュ設定
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database

# ログ設定
LOG_CHANNEL=stderr
LOG_LEVEL=error
```

**確認事項:**
- [ ] APP_KEYが設定されている
- [ ] APP_URLが正しいRenderのURLになっている
- [ ] データベース接続情報が正しい
- [ ] すべての環境変数が保存された

## ✅ Renderでのデプロイ

### 1. 手動デプロイまたは自動デプロイ

- **自動**: GitHubにプッシュすると自動的にデプロイが開始される
- **手動**: Renderダッシュボードで「Deploy latest commit」をクリック

### 2. デプロイログの確認

Renderダッシュボード → Logs タブ

**確認事項:**
- [ ] `Running composer install...` が成功している
- [ ] `Setting storage permissions...` が実行されている
- [ ] `Running migrations...` が成功している
- [ ] `Migration name` と `Batch / Status` が表示されている
- [ ] `add_user_id_to_places_table` が実行されている
- [ ] `Caching routes...` が成功している
- [ ] `Deployment finished successfully` が表示されている

### 3. デプロイが失敗した場合

**エラーログを確認:**
- Renderの「Logs」タブで詳細なエラーメッセージを確認

**よくあるエラー:**

#### APP_KEY is not set
```bash
# 環境変数 APP_KEY が設定されていない
# → Renderの Environment タブで APP_KEY を設定
```

#### Route [profile] not defined
```bash
# ルートキャッシュの問題
# → Renderのシェルで実行:
php artisan route:clear
php artisan route:cache
```

#### Column 'user_id' not found
```bash
# マイグレーションが実行されていない
# → Renderのシェルで実行:
php artisan migrate --force
```

## ✅ デプロイ後の確認

### 1. アプリケーションにアクセス

```
https://your-app-name.onrender.com
```

**確認事項:**
- [ ] サイトが表示される
- [ ] 500エラーが表示されない
- [ ] ログインページが表示される

### 2. 機能テスト

**確認事項:**
- [ ] 新規登録ができる
- [ ] ログインができる
- [ ] `/places` ページが表示される
- [ ] 場所の登録ができる
- [ ] 画像のアップロードが動作する
- [ ] スケジュールページが表示される
- [ ] 選択ページが表示される

### 3. エラーが発生した場合

#### デバッグモードを一時的に有効化

1. Renderの環境変数を変更:
   ```bash
   APP_DEBUG=true
   LOG_LEVEL=debug
   ```

2. 再デプロイまたは再起動

3. エラーの詳細を確認

4. **問題解決後、必ず戻す:**
   ```bash
   APP_DEBUG=false
   LOG_LEVEL=error
   ```

#### Renderのシェルで修復スクリプトを実行

```bash
# Renderダッシュボード → Shell タブ
bash scripts/fix-render-errors.sh
```

このスクリプトは以下を実行します:
- キャッシュのクリア
- マイグレーションの実行
- ストレージリンクの作成
- 設定の再キャッシュ

#### デバッグ情報を取得

```bash
bash scripts/render-debug.sh
```

## 📝 トラブルシューティング

詳細なトラブルシューティング方法は以下のドキュメントを参照:

- [RENDER_SETUP.md](./RENDER_SETUP.md) - Renderデプロイの詳細ガイド
- [SAIL_COMMANDS.md](./SAIL_COMMANDS.md) - Sailコマンド一覧

## 🎉 デプロイ成功！

すべてのチェック項目が完了し、アプリケーションが正常に動作していれば、デプロイ成功です！

### 次のステップ

1. **モニタリング**: Renderのメトリクスタブでパフォーマンスを確認
2. **バックアップ**: データベースの定期バックアップを設定
3. **カスタムドメイン**: 独自ドメインを設定（必要に応じて）

---

**問題が解決しない場合は、以下の情報を含めて質問してください:**
- Renderのログに表示されているエラーメッセージ
- `bash scripts/render-debug.sh` の出力結果
- 実行した手順

