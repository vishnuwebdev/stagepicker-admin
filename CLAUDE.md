# CLAUDE.md

This file provides guidance to Claude when working with code in this repository.

## Project Overview

**StagePicker / Acthound Casting** backend — a Laravel 8 (PHP 7.3/7.4) REST API + Blade admin panel. Serves the Flutter mobile app in the sibling `platform/` folder. Connects **Producers** (post auditions, photography events, external castings, production crew listings) with **Auditioners** (browse/apply). Not a git repository — there is no commit history to consult; treat the working tree as the only source of truth.

## Stack

- Laravel 8 (`laravel/framework: ^8.12`), PHP `^7.3|^7.4` — **both are end-of-life** (Laravel 8 security support ended Jan 2023, PHP 7.4 EOL Nov 2022). Factor this in before recommending new packages — many modern packages require PHP 8+.
- `firebase/php-jwt` — used only for signing FCM v1 OAuth requests, **not** for user auth.
- `laravel/ui` — classic Blade auth scaffolding for the admin panel.
- No Sanctum, no Passport, no Nova/Voyager/Backpack.

## Architecture

### Routes
- `routes/api.php` — almost every endpoint uses `Route::any` with **no middleware**. The only guarded route is an unused `GET /user` behind `auth:api`.
- `routes/web.php` — entire Blade admin panel wrapped in `Route::group(['middleware' => 'auth'])`, prefix `admin/*`, plus public pages (`terms-condition`, `privacy-policy`, `firestore-test`) and two unauthenticated cron endpoints (`checkexpireaudition`, `notifybeforeexpire`).
- `routes/channels.php` — stock default, unused (no real broadcasting).

Major API endpoint groups (see `app/Http/Controllers/api/`):
- **Auth/onboarding** (`UserController`): signup+OTP, login, forgot/reset password, social login.
- **Profile/portfolio**: profile CRUD, portfolio CRUD, delete-account, roles.
- **Verification**: standard + company verification flows.
- **Producer auditions/casting** (`PostauditionController`, the largest controller, ~41 methods): audition CRUD, photography, production crew, external casting, dashboard, analytics, reports.
- **Auditioner discovery/apply**: search, feed, apply, favourites, participant selection.
- **Marketplace/classes/webinars/merchandise/credits/compcards** (`ClassController`).
- **Billing**: `/subscription`, `/addstripeammount` (Stripe).
- **Notifications**: legacy `/notification` (UserController) plus a newer, cleaner `api\NotificationController` (`/notifications`, `markAsRead`), and an **unauthenticated debug route** `GET /run-notifications` that manually fires the daily notification cron via `Artisan::call`.

### Auth — critical gap
**There is no real per-request API authentication.** Business endpoints trust a client-supplied `user_id` in the request body/params; nothing verifies the caller owns that ID. `Login` validates credentials and returns the `User` model directly — **no token is issued**. `firebase/php-jwt` is unrelated to user auth (see below). Any endpoint work should treat this as the existing (fragile) pattern unless the user explicitly asks to add real auth — don't silently assume a Bearer-token pattern exists.

### Models & DB
- Models live flat in `app/Models/*.php` (~33 files, no subfolders). Central ones: `User`, `Postaudition` (table `post_auditions`), `PostauditionPhotography`, `Auditionparticipant`, `ProductionCrew`, `CrewRole`, `Role`, `RoleType`, `Skill`, `Career`, `Category`, `Classes`, `Merchandise`(+`Images`/`Dates`), `Subscription`, `Transaction`, `Credit`, `Compcard`, `Favourite`, `AuditionView`, `ExtCasting`, `Slider`, `ContactRequest`, `Report`, `RecentLogin`, `Notification`, `NotificationCycle` (+dead duplicate `NotificationCycle_nw`), `UserVerification`, `CompanyVerification`, `Portfolio`, `Pages`, `Mail` (custom mail sender, not a real Eloquent model).
- `User` has almost no relationships defined despite being the central entity — roles/portfolio/verification/credits are joined via raw `DB::table()` in controllers instead of Eloquent relations. `Postaudition` does define proper `hasMany` relations.
- **Migrations do not reflect the real schema**: `database/migrations/` has only 4 files (stock Laravel tables + one real migration for `notification_cycles`). The ~30 other tables were built outside Laravel's migration system — you cannot rebuild the DB from migrations alone. When adding a column/table, check the live DB schema, don't assume a migration exists to reference.

### Controllers
- Root: `Controller.php`, `HomeController.php`, `AdminController.php`, `ApiController.php`, `AuditionExpireCheckController.php`.
- `Auth/`: stock `laravel/ui` scaffolding.
- `admin/`: 14 controllers mirroring admin web routes.
- `api/`: `UserController` (~31 methods), `PostauditionController` (~41 methods, largest/most central), `ClassController` (~14 methods), `NotificationController` (small, newer, cleanest).
- **Dated backup files are committed alongside live code** — e.g. `PostauditionController.php(1-06-2021)`, `CategoryController.php(2-08-2021)`, `sidebar.blade.php_2-08-2021`. These are dead files, not alternate versions to edit — ignore them, but don't delete without asking (may be intentional manual backups given there's no git history).

### Third-party services
- **Firebase / FCM push**: `app/Services/FCMService.php` hand-rolls the FCM HTTP v1 API — signs a JWT with `firebase/php-jwt`, exchanges it for a Google OAuth2 token, POSTs to `fcm.googleapis.com/v1/projects/stage-picker/messages:send`. Reads the service account key from `storage_path('app/service-account.json')`. A **legacy FCM server key is hardcoded** in the same file (`sendNotificationOld`, dead code path). Firebase web API key is duplicated in `config/services.php`, `PostauditionController.php` (~line 2451), and `HomeController.php` (~line 44) — same value each place, `project_id: stage-picker`.
- `app/FireStore/FireStoreDocument.php` + `FireStoreApiClient.php` — raw Firestore REST client for chat, matching the Flutter side's Firestore chat implementation. `firestore-test` route in `web.php` for manual testing.
- **Stripe**: custom wrapper `app/Lib/StripePayment.php` (no `stripe/stripe-php` SDK — raw HTTP calls). Keys in `.env` (`STRIPE_SECRET`/`STRIPE_PUBLISH`/`STRIPE_CLIENT_ID`).
- **Mail**: SMTP via Mandrill (`smtp.mandrillapp.com`), sent through `App\Models\Mail::sendMail()`, used for OTP/verification/notification emails. No SMS gateway anywhere — OTP is email-only.
- **AWS S3**: configured in `config/filesystems.php` but env keys are blank and no controller actually calls `Storage::disk('s3')` — uploads go to the local/public disk instead. Treat S3 as unused, not wired up.
- **Pusher/broadcasting**: env vars exist, `BROADCAST_DRIVER=log`, nothing actually broadcasts — vestigial, don't assume it works.

### Admin panel
Custom Blade dashboard (Bootstrap admin template, not Nova/Voyager/Backpack). Real CRUD screens under `resources/views/admin/**` for users, roles, role-types, skills, categories, classes, webinars, subscriptions, sliders, career, merchandise, crew-roles, verification review, push-notification composer, reports. A large `resources/views/pages/**` tree is unused template demo boilerplate — ignore it when searching for real admin views.

### Queues/Jobs/Events
No `app/Jobs`, `app/Events`, or `app/Listeners`. `QUEUE_CONNECTION=sync` — nothing is actually queued. `app/Console/Commands/SendDailyNotifications.php` is the real, current scheduled command (`notifications:send-daily`) driving a 14-day notification cycle via `NotificationCycle`; `SendDailyNotifications_nw.php` is a dead duplicate.

### File storage
`config/filesystems.php` defines `local`/`public`/`s3`. In practice uploads (portfolio, verification docs, compcards, photos) are stored on the local `public` disk; S3 config is present but unexercised.

### Testing
No real test coverage — `tests/Feature/ExampleTest.php` and `tests/Unit/ExampleTest.php` are untouched Laravel defaults.

## Known tech debt / risks (surface these when relevant, don't silently "fix" without asking)

1. **No server-side API authorization** — any endpoint trusts client-supplied `user_id`.
2. **Secrets committed to the repo**: working `.env` has live-looking Mandrill SMTP password, Stripe test keys, real `APP_KEY`; Firebase web API key and a legacy FCM server key are hardcoded directly in PHP source in multiple places.
3. **Migrations don't reflect real schema** — don't assume `php artisan migrate:fresh` reproduces the DB.
4. **God classes**: `api\PostauditionController` (41 methods) and `api\UserController` (31 methods) mix many concerns.
5. **Unauthenticated debug route** `GET /api/run-notifications` fires the daily notification job from a plain browser request.
6. **Dead/duplicate files**: `NotificationCycle_nw.php`, `SendDailyNotifications_nw.php`, dated controller/view backups.
7. **EOL stack**: Laravel 8 + PHP 7.3/7.4 — factor this into any package upgrade suggestions.

## Cross-repo integration notes (see also `platform/CLAUDE.md`)

- Flutter app's base API URL: `http://63.142.251.228/stagepicker/api/` (hardcoded in `lib/api/network_utils.dart`, no dev/prod flavor split).
- Chat is peer-to-peer via Firestore directly from the Flutter app (not proxied through this Laravel API) — this backend's Firestore client (`app/FireStore/`) is used for server-triggered writes (e.g. FCM token bookkeeping), not the chat message flow itself.
- Push notifications: Laravel (`FCMService`) → FCM v1 API → Flutter app's `FirebaseMessegingServices.dart` handles the client side.
