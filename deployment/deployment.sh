#!/bin/bash

# Define variables
BASE_DIR="/var/www/html"
ZIP_FILE="$BASE_DIR/deployment/delivery.zip"
DB_BACKUP_DIR="db_backup"
SQL_FILE="$DB_BACKUP_DIR/database.sql"
DB_USER="postgres"       # Hardcoded database username
DB_PASSWORD="DwtwN6J=fc?*" # Hardcoded database password

# Prompt for the folder name
read -p "Enter the folder name for deployment: " FOLDER_NAME
TARGET_DIR="$BASE_DIR/$FOLDER_NAME"

# Prompt for the database name
read -p "Enter the PostgreSQL database name: " DB_NAME

# Prompt for Data Receiving Port and Configuration Port
read -p "Enter the Data Receiving Port: " DATA_PORT
read -p "Enter the Configuration Port: " CONFIG_PORT

# Prompt for Application Title
read -p "Enter the Application Title: " APP_TITLE

# Create the deployment folder
echo "Creating deployment folder at $TARGET_DIR..."
sudo mkdir -p "$TARGET_DIR"

# Unzip the application files into the folder
echo "Unzipping deployment.zip into $TARGET_DIR..."
sudo unzip "$ZIP_FILE" -d "$TARGET_DIR"

# Set permissions
echo "Setting permissions for $TARGET_DIR..."
sudo chown -R www-data:www-data "$TARGET_DIR"
sudo chmod -R 755 "$TARGET_DIR"

# Create PostgreSQL database and add PostGIS extension
echo "Creating PostgreSQL database $DB_NAME with PostGIS extension..."
sudo -u postgres psql -c "CREATE DATABASE $DB_NAME;"
sudo -u postgres psql -c "GRANT ALL PRIVILEGES ON DATABASE $DB_NAME TO $DB_USER;"
sudo -u postgres psql -d "$DB_NAME" -c "CREATE EXTENSION postgis;"

# Import the .sql file
if [ -f "$TARGET_DIR/$SQL_FILE" ]; then
  echo "Importing SQL file into the database..."
  sudo -u postgres psql -d "$DB_NAME" -f "$TARGET_DIR/$SQL_FILE"
else
  echo "SQL file not found in $TARGET_DIR/$SQL_FILE. Please check the file path and try again."
fi

# Create .env file with updated variables
echo "Creating .env file with updated configurations..."
cat > "$TARGET_DIR/.env" <<EOL
#--------------------------------------------------------------------
# ENVIRONMENT
#--------------------------------------------------------------------

CI_ENVIRONMENT = production

#--------------------------------------------------------------------
# APP
#--------------------------------------------------------------------

app.baseURL = 'http://$FOLDER_NAME.9trax.com/'
app.appName = '$APP_TITLE'
app.
#--------------------------------------------------------------------
# DATABASE
#--------------------------------------------------------------------

database.default.hostname = localhost
database.default.database = $DB_NAME
database.default.schema   = public
database.default.username = postgres
database.default.password = $DB_PASSWORD
database.default.DBDriver = PostgreSQL
database.default.DBPrefix =

#--------------------------------------------------------------------
# SESSION
#--------------------------------------------------------------------

#session.driver = 'CodeIgniter\Session\Handlers\FileHandler'
#session.savePath = null

#--------------------------------------------------------------------
# LOGGER
#--------------------------------------------------------------------

logger.threshold = 4

#--------------------------------------------------------------------
# CONFIGURATION
#--------------------------------------------------------------------
configuration_port = $CONFIG_PORT
data_port = $DATA_PORT

EOL

# Configure Apache2
echo "Configuring Apache2..."
sudo bash -c "cat > /etc/apache2/sites-available/$FOLDER_NAME.conf << EOL
<VirtualHost *:80>
    ServerAdmin webmaster@localhost
    ServerName $FOLDER_NAME.9trax.com
    DocumentRoot $TARGET_DIR
    <Directory $TARGET_DIR>
        AllowOverride All
        Require all granted
    </Directory>
    ErrorLog \${APACHE_LOG_DIR}/error.log
    CustomLog \${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
EOL"

# Enable the site and rewrite module
sudo a2ensite "$FOLDER_NAME.conf"
sudo a2enmod rewrite

# Restart Apache2 to apply changes
echo "Restarting Apache2..."
sudo systemctl restart apache2

# Final message
echo "Deployment complete! Your CodeIgniter application is now set up with the PostgreSQL database $DB_NAME and PostGIS extension."
echo "Data Receiving Port: $DATA_PORT, Configuration Port: $CONFIG_PORT"
echo "Application Title: $APP_TITLE"
