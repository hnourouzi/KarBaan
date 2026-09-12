# KarBaan (کاربان)

Web-based employee daily task and time tracking. Employees plan their day as a checklist, close it out with done / not-done / extra tasks, and managers review periodic reports (hours, completion rate, and reasons for incomplete work).

This is a **web-based application**. It was built using **vibe coding** — AI-assisted, prompt-driven development rather than fully manual hand-coding, with human review and direction guiding the process.

The name combines *kar* (work) and *ban* (keeper/guide). It is used consistently in `composer.json`, app config, and the UI.

## Tech Stack

- **Laravel** (PHP) with **Blade** views
- **Tailwind CSS v4** (CSS-first `@theme` tokens in `resources/css/app.css`) and **Alpine.js** for lightweight interactivity (modals, dismissible banners)
- **Vazirmatn** (Vazir) font, full **RTL** layout
- **Laravel Sanctum** for versioned REST API auth (`/api/v1`)
- **morilog/jalali** for Jalali (Persian/Shamsi) display dates; Gregorian dates stay in the database
- **maatwebsite/excel** (Laravel Excel) for periodic report Excel export (RTL worksheet, UTF-8)
- **spatie/browsershot** (default) + **Puppeteer** for periodic report PDF export (headless Chrome, correct Persian/RTL shaping)
- **barryvdh/laravel-dompdf** available as an optional PDF fallback via `KARBAAN_PDF_DRIVER=dompdf` (not recommended for Persian text)

## Features

- **Daily task planning** — employees open a day and create a start-of-day checklist
- **End-of-day closing** — mark planned tasks done or not done (reason required when not done), log extra/unplanned tasks, record hours
- **Time tracking** — start/end timestamps and computed hours per day on `daily_plans`
- **Multiple work sessions per day** — employees can clock out and start again on the same `DailyPlan` (e.g. 09:00–16:00 then 23:00–01:00). Each interval is a `WorkSession`; confirmed hours are the sum of closed sessions. At most one session can be open at a time, with a cap of 10 sessions/day
- **Manager-assigned tasks** — managers/admins can add a task to an employee’s **open** daily plan (from the day-detail modal or plan page). The task is tagged (`assigned_by`) and shown with a “توسط مدیر اضافه شده” badge so it is distinct from employee-planned work
- **Employee self-history** (“سوابق من”) — date-range filtering, summary KPIs, daily table, and shared day-detail modal
- **Manager periodic report (per employee)** — summary KPI cards, daily breakdown table, day-detail modal; team-level report at `/reports`
- **Today's Attendance** (manager/admin dashboard) — real-time view of who has started, is still working, or finished today, with start/end times and task progress; row click opens day-detail modal
- **Jalali (Persian/Shamsi) calendar** throughout the panel, with a Jalali date-picker on report/history filters
- **Excel export** of manager employee periodic reports (`/reports/employee/export/excel`) — RTL-formatted, Persian-safe `.xlsx`
- **PDF export** of the same reports (`/reports/employee/export/pdf`) — RTL-formatted, Persian-safe; rendered via **Browsershot** by default
- **In-panel end-of-day reminder** — configurable time (`KARBAAN_END_OF_DAY_REMINDER_TIME`, default `18:00`) banner for employees who started but have not closed their day (no email/push)
- **Role-based access** — Employee, Manager, Admin (policies; employees only see their own plans)
- **REST API** (`/api/v1`) with Sanctum authentication for future mobile or integrations
- **Modern minimal UI** — Tailwind design system (brand colors, typography, spacing), reusable Blade UI components (`x-ui.*`), stat cards, status badges, polished forms and tables

## Architecture

Request flow:

`Route → thin Controller → Form Request (validation + toDto) → Service (business logic) → Eloquent → API Resource / Blade`

Key folders:

| Layer | Location |
|-------|----------|
| Controllers | `app/Http/Controllers/Web`, `app/Http/Controllers/Api/V1` |
| Form Requests | `app/Http/Requests` |
| Services | `app/Services/Implementations` (+ interfaces in `Contracts`) |
| DTOs | `app/DTOs` |
| Enums | `app/Enums` |
| Policies | `app/Policies` |
| Exports | `app/Exports` |
| UI components | `resources/views/components`, `resources/views/components/ui` |

Conventions:

- Controllers only orchestrate; they do not contain query builders or business rules.
- Each service is bound behind an interface in `DomainServiceProvider`.
- Data moves between layers via `readonly` DTOs.
- Fixed values use PHP enums (`UserRole`, `TaskStatus`, `NotDoneReason`, `ReportPeriod`, `DailyPlanStatus`, `DayHistoryStatus`, `AttendanceStatus`, …).
- Policies enforce access: employees edit only their own daily plan; managers/admins view team and employee reports; only admins CRUD employees.
- Closing a day runs in a database transaction (task statuses + attendance hours).
- Reports are computed on demand by `ReportService` (not stored); screen, Excel, and PDF share the same DTO output.

### Design decisions

- There is no separate `employees` table; `User` is the employee, with a role on the same model.
- Attendance lives on `daily_plans` (`started_at`, `closed_at`, `hours_worked`) so one working day has a single source of truth. Individual clock-in/out intervals live on `work_sessions`; `hours_worked` is the sum of **closed** sessions. `closed_at` still means the employee explicitly closed the day, not merely ended a session.
- Manager-assigned tasks are restricted to **open** plans. A closed day has already been checked out; adding work there would leave the employee unable to mark it done without reopening the day.
- Jalali conversion for filter inputs happens in Form Requests (`prepareForValidation`); services always receive Gregorian `Carbon` dates.

### Possible later extensions

- Email or push notifications (in-panel reminder exists today)
- Caching for heavy periodic reports
- Calendar or payroll integrations

## Installation

```bash
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate --seed
npm install
npm run build
php artisan serve
```

`npm install` pulls **Puppeteer** (used by Browsershot for PDF export). `npm run build` needs Node 20.12+ or 22 (Vite 8). On an older Node version, the UI falls back to the Tailwind CDN and Bunny Fonts so the app is still viewable without a frontend build.

Optional `.env` settings:

```env
KARBAAN_END_OF_DAY_REMINDER_TIME=18:00
KARBAAN_PDF_DRIVER=browsershot   # or dompdf (fallback)
```

### Demo accounts

Password for all accounts: `password`

| Role     | Email                |
|----------|----------------------|
| Admin    | admin@karbaan.test   |
| Manager  | manager@karbaan.test |
| Employee | ali@karbaan.test     |
| Employee | sara@karbaan.test    |
| Employee | reza@karbaan.test    |

## API v1

Base path: `/api/v1`  
Auth: `Authorization: Bearer {token}` (Sanctum)

Success envelope:

```json
{ "success": true, "message": "...", "data": {} }
```

Error envelope:

```json
{ "success": false, "message": "...", "errors": {} }
```

### Login

`POST /api/v1/auth/login`

```json
{
  "email": "ali@karbaan.test",
  "password": "password",
  "device_name": "mobile"
}
```

Other routes:

- `POST /api/v1/auth/logout`
- `GET /api/v1/auth/me`
- `GET|POST /api/v1/daily-plans`
- `GET /api/v1/daily-plans/{id}`
- `POST /api/v1/daily-plans/{id}/close`
- `POST /api/v1/daily-plans/{id}/tasks`
- `POST /api/v1/daily-plans/{id}/manager-tasks`
- `POST /api/v1/work-sessions/start`
- `POST /api/v1/work-sessions/{id}/end`
- `PATCH /api/v1/plan-tasks/{id}`
- `PATCH /api/v1/plan-tasks/{id}/status`
- `GET /api/v1/reports?period=weekly&user_id=`
- `GET /api/v1/reports/day/{dailyPlan}`
- `GET|POST|PUT|DELETE /api/v1/employees`

Open a daily plan:

```json
{
  "titles": ["Review inbox", "Team standup"],
  "notes": "Focus on phase 1 delivery"
}
```

Close a day:

```json
{
  "tasks": {
    "12": { "status": "done" },
    "13": { "status": "not_done", "not_done_reason": "time_shortage" }
  },
  "extras": [
    { "title": "Urgent support call" }
  ]
}
```

`period` may be `daily`, `weekly`, `monthly`, or `custom`. For `custom`, `from` and `to` are required (Jalali `Y/m/d` or Gregorian `Y-m-d`).

## License

MIT
