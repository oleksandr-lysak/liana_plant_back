#!/bin/bash

cd /home/lianaplant || exit

echo "📦 Pulling latest changes..."
git fetch origin develop
git reset --hard origin/develop

echo "🐳 Rebuilding Docker containers..."

sudo chmod -R 777 /home/lianaplant/package-lock.json
docker-compose down
docker-compose up 

echo "✅ Done"
