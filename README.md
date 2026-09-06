# KarBaan (کاربان)

Web-based employee daily task and time tracking. Employees plan their day as a checklist, close it out with done / not-done / extra tasks, and managers review periodic reports (hours, completion rate, and reasons for incomplete work).

This is a **web-based application**. It was built using **vibe coding** — AI-assisted, prompt-driven development rather than fully manual hand-coding, with human review and direction guiding the process.

The name combines *kar* (work) and *ban* (keeper/guide). It is used consistently in `composer.json`, app config, and the UI.

## Tech Stack

- **Laravel** (PHP) with Blade views
- **Tailwind CSS** and the Vazirmatn (Vazir) font, RTL-ready
- **Laravel Sanctum** for versioned REST API auth (`/api/v1`)
- **morilog/jalali** for Jalali (Persian/Shamsi) display dates; Gregorian dates stay in the database

## Features

- Daily work planning: employees open a day and add a checklist of intended tasks
- Checklist close-out: mark tasks done or not done (reason required when not done) and log extra/unplanned work
- Time tracking: start/end timestamps and computed hours per day
- Employee history (“سوابق من”): previous days, Jalali date filter, and day-detail modal
- Manager reports: team summary and per-employee periodic reports (today / this week / this month / custom Jalali range)
- Role-based access: Employee, Manager, Admin (policies; employees only see their own plans)
- Jalali calendar in the panel: Persian day/month names, Jalali date-picker on range filters
- REST API alongside the Blade UI for future mobile or integrations

## Architecture

Request flow:

`Route → thin Controller → Form Request (validation + toDto) → Service (business logic) → Eloquent → API Resource / Blade`

- Controllers only orchestrate; they do not contain query builders or business rules.
- Each service is bound behind an interface in `DomainServiceProvider`.
- Data moves between layers via `readonly` DTOs.
- Fixed values use PHP enums (`UserRole`, `TaskStatus`, `NotDoneReason`, `ReportPeriod`, `DailyPlanStatus`, `DayHistoryStatus`).
- Policies enforce access: employees edit only their own daily plan; managers/admins view team and employee reports; only admins CRUD employees.
- Closing a day runs in a database transaction (task statuses + attendance hours).

### Design decisions

- There is no separate `employees` table; `User` is the employee, with a role on the same model.
- Attendance lives on `daily_plans` (`started_at`, `closed_at`, `hours_worked`) so one working day has a single source of truth.
- Reports are not stored; `ReportService` computes them on demand.
- Jalali conversion for filter inputs happens in Form Requests (`prepareForValidation`); services always receive Gregorian `Carbon` dates.

### Possible later extensions

- Notifications (remind employees to close the day, or alert managers on low completion)
- Excel/PDF export for reports
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

`npm run build` needs Node 20.12+ or 22 (Vite 8). On an older Node version, the UI falls back to the Tailwind CDN and Bunny Fonts so the app is still viewable without a frontend build.

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
