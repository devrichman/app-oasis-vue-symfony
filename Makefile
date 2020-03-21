up:
	./scripts/up.sh

down:
	docker-compose down

api:
	docker-compose exec api bash

webapp:
	docker-compose exec webapp bash