# dockerfile
FROM node:20-alpine AS build
WORKDIR /app
COPY package*.json ./
RUN npm ci || npm i
COPY . .
ARG VITE_APP_AUTH_URL
ENV VITE_APP_AUTH_URL=$VITE_APP_AUTH_URL
RUN npm run build

FROM nginx:1.25-alpine
COPY docker/production/nginx/conf.d /etc/nginx/conf.d
WORKDIR /usr/share/nginx/html
COPY --from=build /app/dist .