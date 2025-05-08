FROM tiangolo/nginx-rtmp

RUN apt-get update && \
    apt-get install -y ffmpeg php-fpm php-cli && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

# Kopiere nginx config
COPY nginx.conf /etc/nginx/nginx.conf

# Kopiere Passwortdatei
COPY .htpasswd /etc/nginx/.htpasswd

# Erstelle Aufnahmeordner
RUN mkdir -p /tmp/recordings && chmod 777 /tmp/recordings

CMD ["nginx", "-g", "daemon off;"]
