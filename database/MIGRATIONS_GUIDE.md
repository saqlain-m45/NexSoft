# Database Migration System

Simple, safe way to manage database changes over time.

## How It Works

✅ **Track Changes** - All migrations are recorded in `migrations` table  
✅ **Never Duplicate** - Each migration runs exactly once, no matter how many times you execute  
✅ **Version Control** - Easy to see what changed and when  
✅ **Batch Management** - Migrations grouped by batch number

## Quick Start

### 1. Run All Pending Migrations

**Option A: Via Browser**
```
http://localhost/NexSoft/database/run-migrations.php
```

**Option B: Via Command Line**
```bash
cd c:\xampp\htdocs\NexSoft\database
php run-migrations.php
```

### 2. Add New Migration

Create a new SQL file in `/database/migrations/`:

```
003_your_feature_name.sql
004_another_feature.sql
005_next_update.sql
```

**Naming Convention:**
- `NNN_feature_description.sql` (NNN = 001, 002, 003...)
- Keep it sequential
- Use lowercase with underscores

### 3. Write Migration SQL

```sql
-- Migration 003: Add New Feature
-- Created: 2026-03-25
-- Description: What this migration does

USE `nexsoft_hub`;

-- Your SQL changes here
ALTER TABLE `users` ADD COLUMN `new_field` VARCHAR(255);
CREATE TABLE `new_table` (...);

-- Mark as applied
INSERT IGNORE INTO `migrations` (`migration_name`, `batch`) VALUES
('003_your_feature_name', 1);
```

### 4. Apply Migration

- Just visit `run-migrations.php` again
- Or run `php run-migrations.php`
- It will:
  - Find `003_your_feature_name.sql`
  - Execute it
  - Record it in migrations table
  - Show success/error status

## Examples

### Example 1: Adding a Column

**File:** `003_add_user_phone.sql`
```sql
USE `nexsoft_hub`;

ALTER TABLE `users` ADD COLUMN `phone` VARCHAR(20);

INSERT IGNORE INTO `migrations` (`migration_name`, `batch`) VALUES
('003_add_user_phone', 1);
```

### Example 2: Creating a Table

**File:** `004_create_notifications.sql`
```sql
USE `nexsoft_hub`;

CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `read_at` timestamp NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO `migrations` (`migration_name`, `batch`) VALUES
('004_create_notifications', 1);
```

### Example 3: Data Updates

**File:** `005_update_default_values.sql`
```sql
USE `nexsoft_hub`;

UPDATE `users` SET `role` = 'viewer' WHERE `role` IS NULL;

INSERT IGNORE INTO `migrations` (`migration_name`, `batch`) VALUES
('005_update_default_values', 1);
```

## Features

✓ **Idempotent** - Safe to run multiple times  
✓ **Auto-tracks** - Automatically records applied migrations  
✓ **Batch Grouping** - See which migrations ran together  
✓ **Error Handling** - Shows clear error messages  
✓ **Both Interfaces** - Browser & CLI support  

## Viewing Applied Migrations

Check in phpMyAdmin:

1. Open `nexsoft_hub` database
2. Click `migrations` table
3. See all applied migrations with timestamps

```sql
SELECT * FROM migrations ORDER BY batch, executed_at;
```

## Best Practices

1. **One feature per migration** - Don't mix multiple features
2. **Always test locally first** - Before running on production
3. **Keep migrations small** - Easier to debug
4. **Use descriptive names** - Name tells you what changed
5. **Add comments** - Document why, not just what
6. **Never edit old migrations** - Always create new ones

## Troubleshooting

**Migration not running?**
- Check file name format: `NNN_name.sql`
- Ensure file is in `/database/migrations/` folder
- Check file is not empty

**"Already applied" message?**
- Migration already exists in `migrations` table
- To re-run: Delete from table first (be careful!)

**SQL Error?**
- Check SQL syntax is valid
- Test in phpMyAdmin first
- Ensure all referenced tables exist

## Migration Runner Interface

**Browser Output Shows:**
- ✅ Successful migrations (green)
- ❌ Failed migrations (red)
- ⏭️ Skipped (already applied)
- ⏳ In progress

**Command Line Output Shows:**
- Color-coded status
- Total count of migrations applied
- Current batch number
