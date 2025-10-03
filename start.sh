#!/bin/bash

# Quick PHP development server starter
# Usage: ./start.sh [port] [host]

PORT=${1:-8000}
HOST=${2:-localhost}

echo "Starting PHP development server..."
echo "Server will be available at: http://$HOST:$PORT"
echo "Press Ctrl+C to stop the server"
echo "----------------------------------------"

php -S $HOST:$PORT