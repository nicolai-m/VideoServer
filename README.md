# VideoServer

Docker-basierter Video-Server mit Nginx RTMP, PHP-FPM und einer einfachen Weboberflaeche zur Anzeige gespeicherter MP4-Aufnahmen.

## Dienste

- `nginx`: Webserver und RTMP-Server
- `php-fpm`: PHP 8.3 Runtime fuer die Weboberflaeche
- `recordings`: Docker-Volume fuer gespeicherte Videos

## Voraussetzungen

- Docker
- Docker Compose

## Starten

Im Projektordner ausfuehren:

```bash
docker compose up -d --build
```

Status pruefen:

```bash
docker compose ps
```

Logs anzeigen:

```bash
docker compose logs -f
```

## URLs

| Zweck | URL |
| --- | --- |
| Weboberflaeche | `http://localhost:8080/` |
| Gespeicherte Videos / Autoindex | `http://localhost:8080/videos/` |
| RTMP-Server | `rtmp://localhost/live` |
| RTMP-Stream-URL mit Stream-Key | `rtmp://localhost/live/<stream-key>` |

Beispiel mit Stream-Key `test`:

```text
rtmp://localhost/live/test
```

Nach dem Beenden eines Streams wird die Aufnahme im Docker-Volume `recordings` unter `/tmp/recordings` gespeichert. Nginx schreibt zuerst eine `.flv`-Aufnahme, danach wandelt FFmpeg die fertige Datei automatisch in eine abspielbare `.mp4`-Datei um. Die MP4-Datei wird ueber die Weboberflaeche angezeigt.

## Streaming mit OBS

In OBS unter `Einstellungen > Stream`:

- Dienst: `Benutzerdefiniert`
- Server: `rtmp://localhost/live`
- Stream-Key: frei waehlbar, z. B. `test`

Die resultierende Aufnahme ist danach unter folgenden URLs erreichbar:

```text
http://localhost:8080/
http://localhost:8080/videos/
```

Hinweis: Durch `record_unique on` erzeugt Nginx eindeutige Dateinamen. Der genaue MP4-Dateiname ist in der Weboberflaeche oder unter `http://localhost:8080/videos/` sichtbar. Direkt nach Stream-Ende kann die Datei je nach Laenge des Streams noch einige Sekunden konvertieren.

Wenn noch alte, defekte MP4-Dateien aus vorherigen Tests vorhanden sind, koennen sie aus dem Volume entfernt werden:

```bash
docker compose exec nginx find /tmp/recordings -name '*.mp4' -delete
```

## Lokale Dateien

- Webroot: `./html`
- Nginx-Konfiguration: `./nginx/nginx.conf`
- PHP-FPM-Konfiguration: `./php-fpm/docker-php-ext-xdebug.ini`
- Aufnahmeordner im Container: `/tmp/recordings`

## Container stoppen

```bash
docker compose down
```

Container stoppen und gespeicherte Aufnahmen im Docker-Volume loeschen:

```bash
docker compose down -v
```

## Debugging

Xdebug ist fuer PHP-FPM aktiviert:

- Port: `9003`
- IDE-Key: `PHPSTORM`
- Servername: `tradeelevate`

Die Weboberflaeche liest alle `.mp4`-Dateien aus `/tmp/recordings` und zeigt sie als Video-Kacheln an.
