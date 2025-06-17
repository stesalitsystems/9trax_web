#!/bin/bash

# Enable strict error handling
set -euo pipefail

# Configuration variables
SOURCE_DB_HOST="120.138.8.188"
SOURCE_DB_PORT="5432"  # Adjust if not default
SOURCE_DB_NAME="erhwh"
SOURCE_DB_USER="postgres"
SOURCE_DB_PASS="DwtwN6J=fc?*"
SOURCE_DB_PORT="5432"  # Adjust if not default

DEST_DB_HOST="localhost"
DEST_DB_PORT="5432"  # Adjust if not default
DEST_DB_NAME="erhwhnew"
DEST_DB_USER="postgres"
DEST_DB_PASS="DwtwN6J=fc?*"
DEST_DB_PORT="5432"  # Adjust if not default

BACKUP_FILE="/tmp/db_backup.sql"
LOG_FILE="/tmp/db_backup_restore.log"

# Start logging
exec > >(tee -i "$LOG_FILE")
exec 2>&1

echo "Starting database backup and restore process..."
echo "Timestamp: $(date)"

# Check if SOURCE_DB_PASS and DEST_DB_PASS are set
if [[ -z "${SOURCE_DB_PASS:-}" ]]; then
    echo "Error: SOURCE_DB_PASS environment variable is not set."
    exit 1
fi

if [[ -z "${DEST_DB_PASS:-}" ]]; then
    echo "Error: DEST_DB_PASS environment variable is not set."
    exit 1
fi

# Step 1: Take the backup excluding tables starting with 'traker_positionaldata_' from all schemas
echo "Taking backup of all tables except those starting with 'traker_positionaldata_'..."

PGPASSWORD="$SOURCE_DB_PASS" pg_dump -h "$SOURCE_DB_HOST" -U "$SOURCE_DB_USER" -d "$SOURCE_DB_NAME" -p "$SOURCE_DB_PORT" \
    --exclude-table='*.traker_positionaldata_*' > "$BACKUP_FILE"

if [ $? -eq 0 ]; then
    echo "Backup completed successfully and saved to $BACKUP_FILE."
else
    echo "Backup failed."
    exit 1
fi

# Step 2: Create the destination database if it doesn't exist
echo "Checking if the destination database '$DEST_DB_NAME' exists..."

DB_EXISTS=$(PGPASSWORD="$DEST_DB_PASS" psql -h "$DEST_DB_HOST" -U "$DEST_DB_USER" -p "$DEST_DB_PORT" -tAc \
"SELECT 1 FROM pg_database WHERE datname='$DEST_DB_NAME'")

if [ "$DB_EXISTS" = "1" ]; then
    echo "Database '$DEST_DB_NAME' already exists on destination server."
else
    echo "Database '$DEST_DB_NAME' does not exist. Creating database..."
    PGPASSWORD="$DEST_DB_PASS" createdb -h "$DEST_DB_HOST" -U "$DEST_DB_USER" -p "$DEST_DB_PORT" "$DEST_DB_NAME"
    if [ $? -eq 0 ]; then
        echo "Database '$DEST_DB_NAME' created successfully."
    else
        echo "Failed to create database '$DEST_DB_NAME'."
        exit 1
    fi
fi

# Step 3: Restore the backup to the destination database
echo "Restoring backup into the destination database..."

PGPASSWORD="$DEST_DB_PASS" psql -h "$DEST_DB_HOST" -U "$DEST_DB_USER" -d "$DEST_DB_NAME" -p "$DEST_DB_PORT" < "$BACKUP_FILE"

if [ $? -eq 0 ]; then
    echo "Backup successfully restored to the destination database."
else
    echo "Restore failed."
    exit 1
fi

# Cleanup
rm -f "$BACKUP_FILE"
echo "Temporary backup file removed."

echo "Process completed successfully."
echo "Timestamp: $(date)"
