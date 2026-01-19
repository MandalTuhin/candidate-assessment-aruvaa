#!/bin/bash

# AWS Lightsail Deployment Script
# This script should be run on your AWS Lightsail instance

set -e

echo "🚀 Starting AWS Lightsail deployment..."

# Configuration
GITHUB_REGISTRY="ghcr.io"
IMAGE_NAME="mandaltuhin/candidate-assessment-aruvaa:latest"
COMPOSE_FILE="docker-compose.yml"
ENV_FILE=".env.production"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if Docker is installed
if ! command -v docker &> /dev/null; then
    print_error "Docker is not installed. Please install Docker first."
    exit 1
fi

# Check if Docker Compose is installed
if ! command -v docker-compose &> /dev/null; then
    print_error "Docker Compose is not installed. Please install Docker Compose first."
    exit 1
fi

# Create .env.production file if it doesn't exist
if [ ! -f "$ENV_FILE" ]; then
    print_warning ".env.production file not found. Creating template..."
    cat > "$ENV_FILE" << EOF
APP_NAME="Candidate Assessment"
APP_ENV=production
APP_KEY=base64:$(openssl rand -base64 32)
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
EOF
    print_warning "Please edit $ENV_FILE with your actual configuration before continuing."
    print_warning "Press Enter to continue after editing the file..."
    read
fi

# Pull the latest image
print_status "Pulling latest Docker image..."
docker pull "$GITHUB_REGISTRY/$IMAGE_NAME"

# Stop existing containers
print_status "Stopping existing containers..."
docker-compose --env-file="$ENV_FILE" down || true

# Remove old containers and images
print_status "Cleaning up old containers and images..."
docker system prune -f

# Start the application
print_status "Starting the application..."
docker-compose --env-file="$ENV_FILE" up -d

# Wait for the application to start
print_status "Waiting for application to start..."
sleep 30

# Check if the application is running
if docker-compose --env-file="$ENV_FILE" ps | grep -q "Up"; then
    print_status "✅ Application is running successfully!"
    
    # Show container status
    print_status "Container status:"
    docker-compose --env-file="$ENV_FILE" ps
    
    # Show logs
    print_status "Recent logs:"
    docker-compose --env-file="$ENV_FILE" logs --tail=20
    
    print_status "🎉 Deployment completed successfully!"
    print_status "Your application should be available at: http://$(curl -s http://checkip.amazonaws.com):8080"
else
    print_error "❌ Application failed to start. Check the logs:"
    docker-compose --env-file="$ENV_FILE" logs
    exit 1
fi