# ProposalCRM

ProposalCRM is a Laravel-based CRM for freelancers who are tired of pretending a pile of browser tabs and a heroic memory count as a sales system.

This project helps you track jobs, employers, proposals, portfolio proof, follow-ups, AI scoring, and team settings in one place. In other words: it takes the whole "I swear I sent that proposal somewhere" workflow and upgrades it into something that behaves like an actual product.

## What This Project Actually Is

ProposalCRM is a focused operating system for freelance outbound work.

It is built for people who:

- hunt for client work consistently
- send tailored proposals instead of spray-and-pray nonsense
- want a clean history of who was contacted, when, and why
- need visibility into connects, replies, follow-ups, and job quality
- like AI assistance, but prefer it attached to useful workflow instead of vaporware

The app gives you a structured pipeline for:

1. capturing jobs
2. qualifying employers
3. drafting and tracking proposals
4. attaching notes and Loom activity
5. scheduling follow-ups
6. matching portfolio items
7. managing team access and operational settings

It is not trying to be a generic CRM for every industry on earth. It knows what it is. That alone already puts it ahead of half the SaaS market.

## Highlights

- Proposal pipeline with statuses like sent, viewed, replied, won, lost, and withdrawn
- Job tracking with scoring hooks for prioritization
- Employer records so contacts stop disappearing into the void
- Portfolio library for showcasing relevant proof of work
- Follow-up scheduling and completion flow
- AI tools page with OpenAI configuration awareness
- Admin settings for team and operational controls
- Auth, profile management, password flows, and policy-protected surfaces
- Blade-first UI with a custom dual-tone design system
- Pest test coverage for auth, CRM pages, workflow behavior, seeders, and settings

## Product Areas

### Dashboard

The dashboard is the command center. It surfaces high-level stats, proposal momentum, due follow-ups, recent proposals, and top scored jobs.

### Proposals

Track proposal lifecycle, connect spend, notes, Loom views, filters, and status changes without turning your brain into a RAM leak.

### Jobs

Store opportunities, score them, and decide what is worth your time before you write a masterpiece for a client offering exposure and $17.

### Employers

Keep employer profiles, so your outreach history has memory even when you do not.

### Portfolio

Maintain reusable proof of work and match it to opportunities with a little more dignity than digging through old folders.

### Follow-Ups

Schedule follow-ups, mark them complete, and stop relying on the ancient productivity technique known as "I will definitely remember that later."

### AI Tools

The AI surface is wired for OpenAI-backed settings and related tooling. If the key is not configured, the UI tells you plainly instead of performing interpretive dance.

### Settings

Admin-only settings cover operational preferences and team management. Because eventually every useful tool needs a place where someone can be in charge.

## Tech Stack

- PHP 8.3+
- Laravel 13
- Laravel Breeze
- Laravel Sanctum
- Pest 4
- SQLite by default for local development
- Blade views
- Tailwind CSS, Alpine.js, Chart.js, and Tabler Icons
- Vite available in the project toolchain

## Domain Model

Core models in the app:

- `User`
- `Employer`
- `Job`
- `Proposal`
- `ProposalNote`
- `FollowUp`
- `Portfolio`
- `Setting`

These are not decorative. They are the backbone of the product workflow.

## Project Structure

Useful places to know before you start poking around:

- `app/Http/Controllers` - request handling for the CRM surfaces
- `app/Models` - domain entities
- `app/Services` - app logic like proposal and scoring services
- `app/Policies` - authorization rules
- `database/seeders` - demo data and baseline settings
- `resources/views` - Blade screens and shared UI components
- `routes/web.php` - route map for the main CRM experience
- `tests/Feature` - behavior and page coverage
- `tests/Unit` - service and enum coverage

## Local Requirements

Make sure you have these installed:

- PHP 8.3 or newer
- Composer
- Node.js and npm
- SQLite

If you do not have those, the app is not the problem.

## Quick Start

### One-command-ish setup

If you want the shortest path from zero to running app:

```bash
composer run setup
php artisan migrate:fresh --seed
php artisan serve
```

Then open:

```text
http://127.0.0.1:8000
```

### Demo login

Seeded admin credentials:

```text
Email: admin@crm.local
Password: password
```

Yes, that password is embarrassingly obvious. It is for local development, not for impressing a security auditor.

## Manual Setup

If you prefer seeing every moving part:

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
npm install
php artisan migrate:fresh --seed
php artisan serve
```

If you want the Vite dev server running too:

```bash
npm run dev
```

Or use the combined dev script:

```bash
composer run dev
```

That script starts:

- the Laravel server
- the queue listener
- the log stream
- the Vite dev server

It is a nice "make the app feel alive" button.

## Environment Notes

The project ships with a local-friendly default setup:

- `DB_CONNECTION=sqlite`
- `QUEUE_CONNECTION=database`
- `SESSION_DRIVER=database`
- `CACHE_STORE=database`

Important queue note:

- the queue table is `queued_jobs`, not `jobs`

That naming choice avoids colliding with the app's actual domain `jobs` table, which is exactly the kind of silly problem that wastes an afternoon if nobody writes it down.

## AI Configuration

OpenAI-related configuration is supported through:

- `OPENAI_API_KEY`
- `OPENAI_MODEL`

Default model:

```text
gpt-4o
```

There is also seeded settings support for:

- `openai_api_key`
- `openai_model`

So you can manage AI configuration through the app settings layer too.

## How To Run The App Day-To-Day

### Start the app

```bash
php artisan serve
```

### Run the full dev stack

```bash
composer run dev
```

### Rebuild the database with demo data

```bash
php artisan migrate:fresh --seed
```

### Run the test suite

```bash
php artisan test
```

### Run a focused test file

```bash
php artisan test tests/Feature/CrmFrontendPagesTest.php
```

### Format PHP code

```bash
./vendor/bin/pint
```

## Testing

The project uses Pest and currently has passing coverage across:

- authentication flows
- CRM page rendering
- proposal workflow behavior
- settings management
- seed smoke coverage
- service-level scoring and matching logic
- enum sanity checks

Verified passing baseline:

```bash
php artisan test
```

At the time of writing, that passes with 45 tests and 282 assertions.

## Main Routes

Important surfaces in the app:

- `/` - dashboard
- `/login` - login screen
- `/proposals` - proposal management
- `/jobs` - jobs list and scoring actions
- `/employers` - employer management
- `/portfolio` - portfolio library
- `/follow-ups` - follow-up scheduling
- `/ai-tools` - AI tools and config awareness
- `/settings` - admin settings
- `/profile` - user profile management

## Workflow Philosophy

The app is opinionated in a useful way.

The intended operating loop looks like this:

1. capture a job
2. evaluate whether the employer is worth your time
3. score the opportunity
4. prepare a proposal
5. attach notes, proof, and Loom tracking if needed
6. monitor reply and status movement
7. schedule follow-ups
8. learn from the pipeline instead of freehanding the same chaos tomorrow

Basically: less guessing, less duplicate effort, fewer "where did that lead go" moments.

## UI Notes

The frontend is Blade-first and currently leans on shared reusable components plus CDN-delivered UI libraries for styling and interactivity.

That includes:

- Tailwind CSS
- Alpine.js
- Chart.js
- Tabler Icons

The visual system has already been customized beyond stock Laravel, because the default starter aesthetic was not exactly winning awards.

## Seeded Data

After seeding, you get:

- an admin user
- intern users
- portfolio entries
- employers
- jobs
- proposals
- baseline settings

This means you can boot the app and click through real surfaces immediately instead of staring at empty tables like a museum exhibit on unfinished software.

## Common Commands

```bash
# install dependencies and build assets
composer run setup

# start everything in dev mode
composer run dev

# run only the Laravel server
php artisan serve

# reset and reseed local data
php artisan migrate:fresh --seed

# run tests
php artisan test

# run code formatter
./vendor/bin/pint
```

## Troubleshooting

### The app boots but the database explodes

Check that `database/database.sqlite` exists and that your `.env` still points to SQLite.

### Jobs and queues are acting weird

Make sure you did not rename the queue table back to `jobs`. The app intentionally uses `queued_jobs`.

### The AI page looks empty or unhelpful

Set `OPENAI_API_KEY` in `.env` or configure the key through the settings layer.

### Login works but the app feels empty

Run:

```bash
php artisan migrate:fresh --seed
```

That is usually the difference between "working CRM" and "very tasteful void."

## Why This README Exists

Because the stock Laravel README was technically accurate and practically useless for this project.

This file is here so the next person opening the repo can answer the important questions quickly:

- what is this thing
- how do I run it
- how do I log in
- what does it do
- where is the logic
- how do I test it

Radical concept, I know.

## License

This project is open-sourced under the MIT license.

Use it wisely. Or at least use it competently.
