# Academic Leveling API

RESTful API backend powering the Academic Leveling mobile app — a gamified learning platform where students take quizzes, track study sessions, earn experience and coins, complete daily and weekly quests, unlock achievements, and purchase items from an in-app shop.

## Features

- **Quiz Management** — Create, edit, delete, and share quizzes (multiple choice, true/false, identification, mixed)
- **Quiz Attempts** — Start, answer, and submit attempts with automated scoring and answer snapshots
- **Study Sessions** — Log study time with auto-calculated rewards
- **Gamification** — EXP, levels, coins, daily/weekly quests, achievements, and an item shop
- **Public Sharing** — Share quizzes via unique 8-character codes
- **API Auth** — Token-based authentication via Laravel Sanctum
- **Event-Driven Rewards** — Experience, coins, quests, and achievements handled through events and listeners

## Tech Stack

- **PHP** 8.2+ / **Laravel** 12 / **Sanctum**
- **Database** — PostgreSQL (hosted on Supabase)
- **Deployment** — Render

## API Endpoints

### Auth
| Method | URI | Auth | Description |
|--------|-----|------|-------------|
| POST | `/api/register` | — | Register a new account |
| POST | `/api/login` | — | Login with email/username |
| POST | `/api/logout` | Yes | Revoke current token |
| POST | `/api/change-password` | Yes | Change password |
| POST | `/api/forgot-password` | — | Send password reset link |
| POST | `/api/reset-password` | — | Reset password with token |

### User
| Method | URI | Auth | Description |
|--------|-----|------|-------------|
| GET | `/api/user` | Yes | Get profile |
| PUT | `/api/user` | Yes | Update username/email |
| GET | `/api/user/stats` | Yes | Study statistics |

### Quizzes
| Method | URI | Auth | Description |
|--------|-----|------|-------------|
| GET | `/api/quizzes` | Yes | List quizzes (public + own) |
| POST | `/api/quizzes` | Yes | Create quiz with questions |
| GET | `/api/quizzes/{id}` | Yes | Get quiz details |
| PUT | `/api/quizzes/{id}` | Yes | Update quiz |
| DELETE | `/api/quizzes/{id}` | Yes | Soft-delete quiz |
| GET | `/api/quizzes/mine` | Yes | List own quizzes |

### Attempts
| Method | URI | Auth | Description |
|--------|-----|------|-------------|
| POST | `/api/quizzes/{id}/attempts` | Yes | Start a quiz attempt |
| POST | `/api/attempts/{id}/answers` | Yes | Save an answer |
| POST | `/api/attempts/{id}/submit` | Yes | Submit attempt |
| POST | `/api/attempts/{id}/submit-all` | Yes | Submit all answers at once |
| GET | `/api/attempts` | Yes | Attempt history |
| GET | `/api/attempts/{id}` | Yes | Attempt details |

### Study Sessions
| Method | URI | Auth | Description |
|--------|-----|------|-------------|
| GET | `/api/study-sessions` | Yes | List study sessions |
| POST | `/api/study-sessions` | Yes | Log a study session |

### Quests & Achievements
| Method | URI | Auth | Description |
|--------|-----|------|-------------|
| GET | `/api/quests` | Yes | List quests with progress |
| POST | `/api/quests/{id}/claim` | Yes | Claim quest reward |
| GET | `/api/achievements` | Yes | List achievements with progress |
| POST | `/api/achievements/{id}/claim` | Yes | Claim achievement reward |

### Shop
| Method | URI | Auth | Description |
|--------|-----|------|-------------|
| GET | `/api/shop/items` | Yes | Browse shop items |
| POST | `/api/shop/buy/{id}` | Yes | Purchase item with coins |
| GET | `/api/user/inventory` | Yes | View purchased items |
| POST | `/api/user/inventory/use/{id}` | Yes | Use/consume an item |

## Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

## Deployment

- **Docker** — See `Dockerfile`
- **Vercel** — See `vercel.json` and `api/lambda.php`

## License

[MIT](LICENSE)
