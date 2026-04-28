<div align="center">
  <img src="docs/screenshots/logo.png" alt="PulseBid Logo" width="220" />

  # PulseBid

  **The Future of Real-Time Digital Auctions**

  [![Laravel 12](https://img.shields.io/badge/Laravel-12.0-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
  [![Vue 3](https://img.shields.io/badge/Vue.js-3.4-4FC08D?style=for-the-badge&logo=vuedotjs&logoColor=white)](https://vuejs.org/)
  [![Inertia.js](https://img.shields.io/badge/Inertia.js-2.0-9553E9?style=for-the-badge&logo=inertia&logoColor=white)](https://inertiajs.com/)
  [![Tailwind CSS](https://img.shields.io/badge/Tailwind-3.2-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com/)
  [![Laravel Reverb](https://img.shields.io/badge/Reverb-Real--Time-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com/docs/reverb)

  <p align="center">
    PulseBid is a high-performance, real-time auction ecosystem built on the edge of modern web technology. Experience zero-latency bidding, anti-sniping protection, and a seamless buyer-seller communication layer wrapped in a premium, glassmorphism-inspired interface.
  </p>

  [Explore Features](#-features) • [Tech Stack](#-tech-stack) • [How it Works](#-how-it-works) • [Installation](#-getting-started)
</div>

---

## ✨ Features

- ⚡ **Real-Time Bidding Engine**: Powered by **Laravel Reverb**, bids update instantly across all clients via WebSockets. No polling, no refreshes.
- 🛡️ **Anti-Sniping Protection**: A sophisticated "10-second extension" algorithm automatically extends the auction clock if a bid is placed at the buzzer, ensuring fair competition.
- 💬 **Integrated Messaging**: Real-time transaction-based chat system for buyers and sellers to finalize delivery and payment details.
- 🚦 **Intelligent Rate Limiting**: Built-in bidding cooldowns (5-second intervals) to maintain platform integrity and prevent bot manipulation.
- 📊 **Pro Dashboard Suite**:
  - **User Hub**: Comprehensive tracking of active bids, winning history, and listing performance.
  - **Admin Command Center**: Real-time system health monitoring, platform-wide analytics, and user management.
- 🔐 **Enterprise Security**: Role-based access control (RBAC), multi-layer validation, and secure transaction locking.

---

## 📸 Project Showcase

> **Placeholder:** Add your screenshots in `docs/screenshots/` to see them here!

<table width="100%">
  <tr>
    <td width="50%">
      <p align="center"><b>Live Auction Discovery</b></p>
      <img src="docs/screenshots/discovery.png" alt="Discovery" />
    </td>
    <td width="50%">
      <p align="center"><b>Bidding Interface</b></p>
      <img src="docs/screenshots/bidding.png" alt="Bidding" />
    </td>
  </tr>
  <tr>
    <td width="50%">
      <p align="center"><b>Real-Time Chat</b></p>
      <img src="docs/screenshots/chat.png" alt="Chat" />
    </td>
    <td width="50%">
      <p align="center"><b>Admin Statistics</b></p>
      <img src="docs/screenshots/admin.png" alt="Admin" />
    </td>
  </tr>
</table>

---

## 🛠️ Tech Stack

| Layer | Technology |
| :--- | :--- |
| **Backend** | Laravel 12 (PHP 8.2), Laravel Reverb (WebSockets), MySQL |
| **Frontend** | Vue 3 (Composition API), Inertia.js 2.0 (Bridge) |
| **Styling** | Tailwind CSS 3.2 (Glassmorphism design system) |
| **State/Real-time** | Laravel Echo, Pusher-JS |
| **Architecture** | Service-Repository Pattern, API Resources, Event-Driven |

---

## 📖 How it Works

PulseBid streamlines the entire auction lifecycle in six simple steps:

1.  **Discover**: Explore the curated marketplace of live auctions with advanced filtering.
2.  **Bid Live**: Enter the arena and place bids. Watch the top bid change in real-time.
3.  **Stay in Game**: Monitor the dynamic countdown. React fast to last-minute extensions.
4.  **Win**: The platform automatically closes the auction and identifies the winner.
5.  **Connect**: A private chat channel opens instantly between the winner and the seller.
6.  **Complete**: Finalize the transaction and delivery details directly.

---

## 🏛️ Architectural Excellence

PulseBid isn't just about the UI; it's built with a focus on maintainability and scale:

- **Service Layer Pattern**: Heavy business logic (like bidding rules and sniping deterrence) is decoupled from controllers into the `AuctionService`.
- **Database Integrity**: Uses `lockForUpdate()` during critical bidding transactions to prevent race conditions.
- **Event-Driven**: Dispatches `BidPlaced` events that propagate through Reverb to update frontends globally.
- **SEO Ready**: Dynamic slug generation and semantic HTML structure.

---

## 🚀 Getting Started

### Prerequisites
- PHP 8.2+
- Node.js 20+
- Composer & NPM

### Installation Steps

1. **Clone & Install**
   ```bash
   git clone https://github.com/yourusername/pulsebid.git
   cd pulsebid
   composer install
   npm install
   ```

2. **Configure Environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Database & Migrations**
   ```bash
   # Create a sqlite database or configure mysql in .env
   touch database/database.sqlite
   php artisan migrate --seed
   ```

4. **Launch Everything**
   PulseBid uses a unified dev script to start the server, vite, the queue, and Reverb:
   ```bash
   composer run dev
   ```

---

## 📁 Key Directories

- `app/Services/` - Core business logic (Bidding, Auctions).
- `app/Events/` - Real-time WebSocket events.
- `resources/js/Pages/` - Vue components for each application view.
- `app/Http/Resources/` - Data transformation layer for the frontend.

---

## 🤝 Contributing

We welcome contributions! Please fork the repo and submit a PR for any features or bug fixes.

---

<div align="center">
  <sub>Built with passion using the Laravel + Vue ecosystem</sub><br/>
  <b>PulseBid © 2026</b>
</div>
