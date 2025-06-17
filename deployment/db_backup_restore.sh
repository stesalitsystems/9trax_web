#!/bin/bash

# Configuration variables
SOURCE_DB_HOST="120.138.8.188"
SOURCE_DB_NAME="ecrddu"
SOURCE_DB_USER="postgres"
SOURCE_DB_PASS="DwtwN6J=fc?*"
SOURCE_DB_PORT="5432"  # Adjust if not default

DEST_DB_HOST="localhost"
DEST_DB_NAME="ecrddu"
DEST_DB_USER="postgres"
DEST_DB_PASS="DwtwN6J=fc?*"
DEST_DB_PORT="5432"  # Adjust if not default

BACKUP_FILE="/tmp/db_backup.sql"

# Set environment variable for pg_dump and psql authentication
export PGPASSWORD=$SOURCE_DB_PASS

# Step 1: Get a list of tables to include in the backup (all except 'traker_positionaldata_*' tables)
INCLUDED_TABLES=$(psql -h $SOURCE_DB_HOST -U $SOURCE_DB_USER -d $SOURCE_DB_NAME -t -c \
"SELECT table_name FROM information_schema.tables WHERE table_schema='public' AND table_name NOT LIKE 'traker_positionaldata_%';")

# Step 2: Generate the backup for the included tables only
echo "Taking backup of all tables except those starting with 'traker_positionaldata_'..."

# Initialize the pg_dump command
DUMP_CMD="pg_dump -h $SOURCE_DB_HOST -U $SOURCE_DB_USER -d $SOURCE_DB_NAME -p $SOURCE_DB_PORT"

# Append each table with --table option
for table in $INCLUDED_TABLES; do
    DUMP_CMD+=" --table=$table"
done

# Execute the pg_dump command and redirect output to the backup file
eval "$DUMP_CMD > $BACKUP_FILE"

if [ $? -eq 0 ]; then
    echo "Backup completed successfully and saved to $BACKUP_FILE."
else
    echo "Backup failed."
    exit 1
fi

# Step 3: Restore the backup to the destination database
export PGPASSWORD=$DEST_DB_PASS
echo "Inserting backup into the destination database..."

psql -h $DEST_DB_HOST -U $DEST_DB_USER -d $DEST_DB_NAME -p $DEST_DB_PORT < $BACKUP_FILE

if [ $? -eq 0 ]; then
    echo "Backup successfully restored to the destination database."
else
    echo "Restore failed."
    exit 1
fi

# Cleanup
rm -f $BACKUP_FILE
echo "Temporary backup file removed."
