#!/bin/bash

echo -e "\n\033[0;32mOptional background mode\033[0m"
read -p "Enter 'bg' to start your stack in background mode: " BG

if [[ "$BG" == "bg" ]]; then
    echo -e "\033[0;32mOk starting stack in background!\033[0m\n"
    docker-compose up -d --build
else
    echo -e "\033[0;32mAlright, starting stack in foreground!\033[0m\n"
    docker-compose up --build
fi
