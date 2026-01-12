CREATE TABLE IF NOT EXISTS users (
    telegram_id INTEGER PRIMARY KEY,
    username TEXT,
    first_name TEXT,
    is_subscribed INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS books (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    author TEXT NOT NULL,
    file_id TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS downloads (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    book_id INTEGER NOT NULL,
    downloaded_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(telegram_id),
    FOREIGN KEY (book_id) REFERENCES books(id)
);

CREATE TABLE IF NOT EXISTS admin_states (
    telegram_id INTEGER PRIMARY KEY,
    state TEXT,
    data TEXT
);
```

## Struktura:
```
telegram-book-bot/
├── database/
│   └── schema.sql          ✅
├── src/
│   ├── Core/
│   │   └── Database.php    ✅
│   ├── Admin/
│   │   ├── States/
│   │   │   └── AdminState.php    ✅
│   │   └── Handlers/
│   │       ├── BookUploadHandler.php    ✅
│   │       └── BookListHandler.php      ✅
│   └── Services/
│       └── BookService.php    ✅