# AWS Lightsail Deployment Guide

This guide explains how to deploy the Laravel Candidate Assessment application to AWS Lightsail using Docker.

## Prerequisites

1. **AWS Lightsail Instance**: Create a Lightsail instance with at least 1GB RAM
2. **Docker & Docker Compose**: Installed on your Lightsail instance
3. **GitHub Container Registry**: Access to pull the Docker image

## Deployment Process

### 1. GitHub Actions Build

The application is automatically built and pushed to GitHub Container Registry when you push to the `main` branch.

- **Registry**: `ghcr.io/mandaltuhin/candidate-assessment-aruvaa:latest`
- **Workflow**: `.github/workflows/build-image.yaml`

### 2. AWS Lightsail Setup

#### Install Docker on Lightsail (Ubuntu/Debian)

```bash
# Update package index
sudo apt update

# Install Docker
sudo apt install -y docker.io docker-compose

# Add your user to docker group
sudo usermod -aG docker $USER

# Log out and log back in for group changes to take effect
```

#### Clone Repository Files

```bash
# Clone only the necessary files
git clone https://github.com/mandaltuhin/candidate-assessment-aruvaa.git
cd candidate-assessment-aruvaa

# Or manually create the files:
# - docker-compose.yml
# - deploy-lightsail.sh
# - .env.production (template will be created by script)
```

### 3. Deploy the Application

```bash
# Make the deployment script executable
chmod +x deploy-lightsail.sh

# Run the deployment
./deploy-lightsail.sh
```

The script will:
1. Create a `.env.production` template if it doesn't exist
2. Pull the latest Docker image from GitHub Container Registry
3. Stop any existing containers
4. Start the new container with production configuration
5. Verify the deployment

### 4. Configuration

Edit `.env.production` with your specific settings:

```env
APP_NAME="Candidate Assessment"
APP_ENV=production
APP_KEY=base64:your-generated-key
APP_DEBUG=false
APP_URL=http://your-lightsail-ip:8080

DB_CONNECTION=sqlite
DB_DATABASE=/var/www/html/database/database.sqlite

SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database

LOG_CHANNEL=stack
LOG_LEVEL=info

MAIL_MAILER=log
```

### 5. Networking

Configure your Lightsail instance networking:

1. **Firewall Rules**: Allow HTTP traffic on port 8080
2. **Static IP**: Assign a static IP to your instance (recommended)
3. **Domain**: Point your domain to the static IP (optional)

## Maintenance Commands

### View Application Logs
```bash
docker-compose --env-file=.env.production logs -f
```

### Restart Application
```bash
docker-compose --env-file=.env.production restart
```

### Update Application
```bash
./deploy-lightsail.sh
```

### Access Application Container
```bash
docker-compose --env-file=.env.production exec app sh
```

### Backup Database (SQLite)
```bash
docker-compose --env-file=.env.production exec app cp /var/www/html/database/database.sqlite /tmp/
docker cp $(docker-compose --env-file=.env.production ps -q app):/tmp/database.sqlite ./backup-$(date +%Y%m%d).sqlite
```

## Troubleshooting

### Application Won't Start
1. Check logs: `docker-compose --env-file=.env.production logs`
2. Verify environment variables in `.env.production`
3. Ensure port 8080 is not in use: `sudo netstat -tlnp | grep 8080`

### Database Issues
1. Check SQLite file permissions
2. Verify database volume mounts
3. Run migrations manually: `docker-compose --env-file=.env.production exec app php artisan migrate`

### Memory Issues
1. Monitor resource usage: `docker stats`
2. Consider upgrading Lightsail instance size
3. Optimize Docker image if needed

## Security Considerations

1. **Environment Variables**: Never commit `.env.production` to version control
2. **Firewall**: Only open necessary ports (8080 for HTTP, 22 for SSH)
3. **Updates**: Regularly update the Docker image and Lightsail instance
4. **Backups**: Implement regular database backups
5. **SSL/TLS**: Consider adding a reverse proxy with SSL (nginx, Caddy, or CloudFlare)

## Monitoring

The application includes a health check endpoint at `/health` that returns:
- Application status
- Database connectivity
- Timestamp

Use this endpoint for monitoring and load balancer health checks.