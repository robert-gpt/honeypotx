#!/usr/bin/env bash
# 07_promtail.sh — Promtail

set -e
CONF="honeypot/config"

echo "==> Promtail config..."
cat > "$CONF/promtail-config.yaml" <<'EOF'
server:
  http_listen_port: 9080
positions:
  filename: /var/lib/promtail/positions.yaml

clients:
  - url: http://LOKI_IP_PLACEHOLDER:3100/loki/api/v1/push

scrape_configs:
#─────────── Apache access / error ───────────
  - job_name: apache-access
    static_configs:
      - labels: { job: apache, type: access, __path__: /var/log/apache2/access.log }
    pipeline_stages:
      - regex:
          expression: '^(?P<remote_ip>\S+) .* "(?P<method>\S+) (?P<path>\S+)'
        - replace:
            source: remote_ip
            expression: "(\d+\.\d+)\.\d+\.\d+"
            replace: "$1.x.x"
            target: remote_ip
      - labels: { remote_ip, method }

  - job_name: apache-error
    static_configs:
      - labels: { job: apache, type: error, __path__: /var/log/apache2/error.log }
    pipeline_stages:
      - regex:
          expression: 'IP=(?P<remote_ip>\d+\.\d+\.\d+\.\d+).*usuario=''(?P<user>[^']+)'' contraseña=''(?P<pass>[^']+)'' - Query=(?P<query>.+)'
        - replace:
            source: remote_ip
            expression: "(\d+\.\d+)\.\d+\.\d+"
            replace: "$1.x.x"
            target: remote_ip
      - labels: { remote_ip, user }
#─────────── MySQL slow / error / general ───────────
  - job_name: mysql-error
    static_configs:
      - labels: { job: mysql, type: error, __path__: /var/log/mysql/error.log }

  - job_name: mysql-general
    static_configs:
      - labels: { job: mysql, type: general, __path__: /var/log/mysql/general.log }
    pipeline_stages:
      - regex:
          expression: '(?P<remote_ip>(?:[0-9]{1,3}\.){3}[0-9]{1,3})'
        - replace:
            source: remote_ip
            expression: "(\d+\.\d+)\.\d+\.\d+"
            replace: "$1.x.x"
            target: remote_ip
      - labels: { remote_ip }

#─────────── ProFTPD ───────────
  - job_name: ftp
    static_configs:
      - labels: { job: ftp, __path__: /var/log/proftpd/*.log }

#─────────── Fakessh ───────────
  - job_name: fakessh
    static_configs:
      - labels: { job: fakessh, __path__: /var/log/fakessh/fakessh.log }

EOF
sed -i "s/LOKI_IP_PLACEHOLDER/$LOKI_IP/" "$CONF/promtail-config.yaml"

echo "Promtail configurado."
