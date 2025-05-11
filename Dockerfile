FROM docker/whalesay:latest
LABEL Name=lianaplantback Version=0.0.1
RUN apt-get -y update && apt-get install -y fortunes && apt-get install -y cron
CMD ["sh", "-c", "/usr/games/fortune -a | cowsay"]
