FROM thecodingmachine/nodejs:12-apache

COPY --chown=docker:docker . .

ARG VUE_APP_GRAPHQL_HTTP

ENV VUE_APP_GRAPHQL_HTTP=$VUE_APP_GRAPHQL_HTTP

RUN npm install &&\
    npm run build
