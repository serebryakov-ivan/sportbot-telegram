# 🏃‍♂️ Sport Event Telegram Bot Skeleton

A skeleton for Telegram bots for organizing sports events with multi-language support.

## 🚀 Features

- User registration
- Event browsing
- Competition registration
- Participant management
- Simple navigation
- **Multi-language support (English/Russian)**

## 📋 Installation

1. Create a MySQL database
2. Run SQL migrations from `database/migrations/`
3. Configure `config/config.php`
4. Upload files to a server with HTTPS
5. Open `webhooks/webhook.php` to set the webhook

## 🛠️ Configuration

1. Get a token from @BotFather on Telegram
2. Set the token in `config/config.php`
3. Configure database parameters

## 🌍 Multi-language Support

The bot supports two languages:
- English (default) 🇬🇧
- Russian 🇷🇺

Users can change language through the menu. All interface elements are translatable.

## 📁 Structure

- `/config` - Configuration files
- `/database` - Migrations and database class
- `/lang` - Language translation files
- `/src` - Main bot code
- `/public` - Entry point

## 👥 For Community Developers

You can extend the functionality:
- Add notification system
- Implement admin panel
- Add rating system
- Integrate payment for participation

## 📄 License

MIT License - free to use and modify!