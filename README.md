# GUILD

## 📌 概要
GUILDは、フリーランサーと企業をつなぐクラウドソーシングプラットフォームです。
フリーランサーが仕事を見つけやすく、企業が適切な人材を効率的に採用できる仕組みを提供します。（3/28までにデプロイ予定）

## 🎯 主な機能

### フリーランサー向け
- ✅ ユーザー登録・ログイン（メール認証）
- ✅ プロフィール編集・ToDo管理
- ✅ 案件一覧表示・検索・絞り込み
- ✅ メッセージ機能
- ✅ Google SMTPを使用した管理者への問い合わせ
- ✅ 案件いいね機能
- ✅ ファイル提出・ダウンロード機能

### 企業向け
- ✅ 案件作成・編集・削除
- ✅ 案件一覧表示
- ✅ おすすめフリーランサーの表示
- ✅ メッセージ機能
- ✅ Google SMTPを使用した管理者への問い合わせ
- ✅ 提出されたファイルのダウンロード
- ✅ プロフィール編集
- ✅ 報酬支払い機能（PayPal対応）

### 管理者向け
- ✅ フリーランサー・企業・プロジェクトの一覧表示・非表示
- ✅ 支払い履歴一覧

## 🛠 使用技術
- **フロントエンド:** HTML / CSS / JavaScript / Bootstrap
- **バックエンド:** PHP / Laravel
- **データベース:** MySQL
- **その他:** Font Awesome

## 📥 インストール方法
```bash
# リポジトリをクローン
git clone https://github.com/Kenta2360/guild-app.git
cd guild-app

# 1. 依存関係をインストール (Laravelのパッケージ)
composer install

# 2. 環境設定ファイルを作成
cp .env.example .env

# 3. アプリケーションキーを生成
php artisan key:generate

# 4. 設定をクリアして反映
php artisan config:clear
php artisan cache:clear

# 5. データベースを作成し、マイグレーション + Seeder 実行
php artisan migrate --seed

# 6. npm 依存関係をインストール (フロントエンド)
npm install && npm run dev

# 7. (必要なら) 本番環境用のビルド
npm run build

# 8. 開発サーバーを起動
php artisan serve
```

## 📊 ERD（データベース設計）
このプロジェクトのデータベース構造は以下の通りです。

![15513C32-4890-48CB-A5B1-94F4F9B928E6](https://github.com/user-attachments/assets/5a2bea49-d674-4ff6-a3b8-69eeb1618742)


## ⚙️ 環境設定 (.env)
以下の環境変数を `.env` に設定してください。
```
APP_NAME=GUILD
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=guild_project
DB_USERNAME=your_database_username
DB_PASSWORD=your_database_password

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=tls

PAYPAL_MODE=sandbox
PAYPAL_SANDBOX_CLIENT_ID=your_paypal_client_id
PAYPAL_SANDBOX_CLIENT_SECRET=your_paypal_client_secret
PAYPAL_CURRENCY=USD
```

## 💡 使用方法

### デモアカウント
- **管理者**
  - メール: `admin@example.com`
  - パスワード: `adminpassword`
- **企業**
  - メール: `info@cloudsolutions.com`
  - パスワード: `password`
- **フリーランサー**
  - メール: `emily@freelance.com`
  - パスワード: `password`

### 基本的な利用フロー
#### 企業
1. ユーザー登録
2. プロフィール編集
3. プロジェクト作成
4. リクエスト受け取り
5. 支払い
6. ファイル受け取り
7. 案件完了

#### フリーランサー
1. ユーザー登録
2. プロフィール編集
3. 案件リクエスト
4. 承認後、開始
5. ファイル提出
6. 完了後、報酬受け取り
7. 評価・レビュー機能

## 🔧 今後の改善点
- [ ] **通知機能** - メッセージや案件進行のリアルタイム通知
- [ ] **多言語対応** - 英語、日本語、その他の言語対応
- [ ] **モバイル最適化** - スマホ・タブレット向けのUI改善
- [ ] **チーム開発** -ユーザー同士のコミュニティ機能



