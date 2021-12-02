FROM nginx:mainline-alpine as base

ENV TZ=UTC
ARG DOCKER_DIR=docker/web

RUN apk upgrade --no-cache --available && \
    addgroup app && \
    adduser -D -h /app -s /bin/bash -G app app

EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]

WORKDIR /app/
COPY --chown=app:app . .