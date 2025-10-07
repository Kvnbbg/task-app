php -v  # Ensure PHP >=8.0
docker --version  # For later containerization

touch .env  # Minimal: APP_PORT=8000 (fallback)
php -S localhost:8000 index.php  # Or -t public/ if dir exists
curl http://localhost:8000/  # Check homepage
curl -X POST http://localhost:8000/log-bp -d "systolic=120&diastolic=80"  # Smoke test BP form (adapt to actual endpoints)

# Build and run locally
docker build -t task-app:latest .
docker run -d -p 8000:8000 --env PORT=8000 task-app:latest

php -S localhost:8000 index.php
curl http://localhost:8000/
