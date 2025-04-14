#!/bin/bash

cd /home/lianaplant || exit

echo "📦 Pulling latest changes..."
git fetch origin develop
git reset --hard origin/develop

echo "🐳 Rebuilding Docker containers..."
docker-compose down
docker-compose up

echo "✅ Done"
