# Render 環境変数設定ガイド

このガイドに従って、Renderダッシュボードで環境変数を設定してください。

## 📋 設定する環境変数の一覧

以下の環境変数を **Render Dashboard → Environment** タブで設定してください。

### ✅ 現在設定済みの環境変数

以下はすでに設定されているはずです（確認してください）:

| Key | Value |
|-----|-------|
| `APP_KEY` | `base64:xQVbsCgM/yfeptlAD1cFDHoN+eoyCRXN4yRVoqCwt1Q=` |
| `APP_URL` | `https://visual-commu-app.onrender.com` |
| `APP_FAKER_LOCALE` | `ja_JP` |
| `APP_FALLBACK_LOCALE` | `ja` |
| `APP_LOCALE` | `ja` |
| `APP_TIMEZONE` | `Asia/Tokyo` |
| `DB_CONNECTION` | `pgsql` |
| `DB_DATABASE` | `visual_commu_app_db` |
| `DB_HOST` | `dpg-d42lihv5r7bs73b3r1b0-a` |
| `DB_PASSWORD` | `h2a1unSzFkF8uH5VtFnvFPfJ6nX8Ssf9` |
| `DB_PORT` | `5432` |
| `DB_USERNAME` | `db_user` |

### ⚠️ 追加が必要な環境変数

以下の環境変数を **今すぐ追加** してください：

| Key | Value | 説明 |
|-----|-------|------|
| `APP_NAME` | `Visual Commu App` | アプリケーション名 |
| `APP_ENV` | `production` | 環境設定（本番環境） |
| `APP_DEBUG` | `false` | デバッグモード（本番ではfalse） |
| `SESSION_DRIVER` | `database` | セッションの保存先 |
| `CACHE_STORE` | `database` | キャッシュの保存先 |
| `QUEUE_CONNECTION` | `database` | キューの保存先 |
| `LOG_CHANNEL` | `stderr` | ログ出力先 |
| `LOG_LEVEL` | `error` | ログレベル |

## 🔧 設定手順

### ステップ1: Renderダッシュボードにアクセス

1. [Render Dashboard](https://dashboard.render.com/) にログイン
2. `visual-commu-app` サービスを選択
3. 左メニューから **Environment** タブをクリック

### ステップ2: 環境変数を追加

「**Add Environment Variable**」ボタンをクリックして、以下を1つずつ追加:

#### 1. APP_NAME
- **Key:** `APP_NAME`
- **Value:** `Visual Commu App`
- 「Add」をクリック

#### 2. APP_ENV
- **Key:** `APP_ENV`
- **Value:** `production`
- 「Add」をクリック

#### 3. APP_DEBUG
- **Key:** `APP_DEBUG`
- **Value:** `false`
- 「Add」をクリック

#### 4. SESSION_DRIVER
- **Key:** `SESSION_DRIVER`
- **Value:** `database`
- 「Add」をクリック

#### 5. CACHE_STORE
- **Key:** `CACHE_STORE`
- **Value:** `database`
- 「Add」をクリック

#### 6. QUEUE_CONNECTION
- **Key:** `QUEUE_CONNECTION`
- **Value:** `database`
- 「Add」をクリック

#### 7. LOG_CHANNEL
- **Key:** `LOG_CHANNEL`
- **Value:** `stderr`
- 「Add」をクリック

#### 8. LOG_LEVEL
- **Key:** `LOG_LEVEL`
- **Value:** `error`
- 「Add」をクリック

### ステップ3: 変更を保存

すべての環境変数を追加したら、ページ下部の「**Save Changes**」ボタンをクリックしてください。

**⚠️ 重要:** 保存すると自動的にサービスが再起動されます（1-2分かかります）。

## 🔍 データベース接続の確認

現在の `DB_HOST` の値が短すぎる可能性があります:
```
DB_HOST=dpg-d42lihv5r7bs73b3r1b0-a
```

### 正しいホスト名の確認方法

1. Renderダッシュボードで **Database** を選択
2. **Connections** セクションを確認
3. **Internal Database URL** または **External Database URL** をコピー

通常、ホスト名は以下のような形式です:
```
dpg-xxxxxxxxxxxxx-a.oregon-postgres.render.com
```

もし現在の `DB_HOST` が短い場合は、正しい完全なホスト名に更新してください。

## ✅ 確認チェックリスト

環境変数の設定が完了したら、以下を確認してください:

- [ ] 8つの新しい環境変数を追加した
- [ ] 「Save Changes」をクリックした
- [ ] サービスが再起動された（Logsタブで確認）
- [ ] DB_HOSTが正しいホスト名になっている

## 🚀 次のステップ

環境変数の設定が完了したら:

1. **デプロイを待つ** (自動的に開始されます)
2. **デプロイログを確認**
   - Render Dashboard → Logs タブ
   - 以下のメッセージが表示されるはずです:
     ```
     Building frontend assets...
     Running migrations...
     ===== Deployment finished successfully =====
     ```
3. **アプリケーションにアクセス**
   ```
   https://visual-commu-app.onrender.com
   ```

## 🐛 まだエラーが出る場合

もしまだ500エラーが出る場合は、Render Shellで以下を実行してください:

```bash
# デバッグ情報を取得
bash scripts/render-debug.sh

# 手動で修復を実行
bash scripts/fix-render-errors.sh
```

## 📝 コピー用（まとめ）

以下をそのままコピー&ペーストできます:

```
APP_NAME=Visual Commu App
APP_ENV=production
APP_DEBUG=false
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
LOG_CHANNEL=stderr
LOG_LEVEL=error
```

---

**質問や問題が発生した場合は、Renderのログ（Logsタブ）を確認してください。**

