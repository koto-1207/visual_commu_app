<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

# Visual Communication App

視覚的コミュニケーションを支援するLaravelアプリケーションです。

## 機能

- 場所の登録・管理（画像付き）
- スケジュール管理
- ドラッグ&ドロップでの並び替え
- Livewire/Voltによるリアクティブなインターフェース

## ローカル開発環境

このプロジェクトは **Laravel Sail** を使用しています。

### 必要なもの

- Docker Desktop
- Git

### セットアップ

```bash
# リポジトリをクローン
git clone <repository-url>
cd visual_commu_app

# Sailコンテナを起動
./vendor/bin/sail up -d

# マイグレーションを実行
./vendor/bin/sail artisan migrate

# フロントエンドをビルド
./vendor/bin/sail npm install
./vendor/bin/sail npm run build
```

ブラウザで `http://localhost` にアクセスしてください。

### よく使うコマンド

```bash
# コンテナを起動
./vendor/bin/sail up -d

# コンテナを停止
./vendor/bin/sail down

# Artisanコマンドを実行
./vendor/bin/sail artisan <command>

# Composerコマンドを実行
./vendor/bin/sail composer <command>

# NPMコマンドを実行
./vendor/bin/sail npm <command>
```

詳細は [SAIL_COMMANDS.md](SAIL_COMMANDS.md) を参照してください。

## Renderへのデプロイ

### クイックスタート

デプロイ前のチェックリスト: [DEPLOY_CHECKLIST.md](DEPLOY_CHECKLIST.md)  
詳細なデプロイ手順: [RENDER_SETUP.md](RENDER_SETUP.md)

### 簡単な手順

1. **APP_KEYを生成**
   ```bash
   ./vendor/bin/sail artisan key:generate --show
   ```

2. **Renderで新しいWeb Serviceを作成**（Docker環境）

3. **環境変数を設定**（必須）
   - `APP_KEY` - 上記で生成した値
   - `APP_URL` - RenderのURL
   - データベース接続情報

4. **デプロイ**
   ```bash
   git add .
   git commit -m "Deploy to Render"
   git push
   ```

## トラブルシューティング

500エラーが発生する場合は、以下を確認してください：

1. 環境変数`APP_KEY`が設定されているか
2. データベース接続情報が正しいか
3. ストレージディレクトリの権限が適切か
4. Renderのログで詳細なエラーを確認

詳細は [RENDER_SETUP.md](RENDER_SETUP.md) の「トラブルシューティング」セクションを参照してください。

## ライセンス

MIT License

---

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
