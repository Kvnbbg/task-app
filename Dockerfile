# Create Dockerfile
echo 'FROM php:8.2-cli
COPY . /app
WORKDIR /app
EXPOSE 8000
CMD ["php", "-S", "0.0.0.0:${PORT:-8000}", "index.php"]' > Dockerfile
