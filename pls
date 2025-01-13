#!/usr/bin/env bash

# Display general help message
show_help() {
  echo "Usage: pls [command] [options]"
  echo "Commands:"
  echo "  build     Build Docker images"
  echo "  up        Start Docker containers"
  echo "  stop      Stop Docker containers"
  echo "  down      Stop Docker containers"
  echo "  logs      Show logs for Docker containers"
  echo "Options:"
  echo "  Any additional options will be passed directly to the respective docker compose commands"
}

main() {
  COMMAND=$1
  shift

  PROFILE=$1
  if [[ "$PROFILE" == "app" || "$PROFILE" == "worker" || "$PROFILE" == "scheduler" ]]; then
    PROFILE_OPTION="--profile=$PROFILE"
    shift
  else 
    PROFILE_OPTION="--profile=*"
  fi
 
  export PLS_USER_ID=$(id -u)
  export PLS_GROUP_ID=$(id -g)

  case "$COMMAND" in
    build)
      exec docker compose -f docker-compose.dev.yml "$PROFILE_OPTION" build "$@"
      ;;
    up)
      exec docker compose -f docker-compose.dev.yml "$PROFILE_OPTION" up "$@"
      ;;
    down)
      exec docker compose -f docker-compose.dev.yml "$PROFILE_OPTION" down "$@"
      ;;
    logs)
      exec docker compose -f docker-compose.dev.yml logs "$@"
      ;;
    stop)
      exec docker compose -f docker-compose.dev.yml "$PROFILE_OPTION" stop "$@"
      ;;
    *)
      echo "Unknown command: $COMMAND"
      show_help
      exit 1
      ;;
  esac
}

if [[ $# -eq 0 ]]; then
  show_help
  exit 1
fi

main "$@"