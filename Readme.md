# Graduation project

Дипломный проект, пока в зайчаточном состоянии, но потихоньку пилится.

Суть: API для управления хостингом

# В главных ролях:
- PHP 7.2
- MySQL 5.7
- Symfony 4.1
- Redis
- Nginx
- JWT
- GRPC + Protobuf
- G~~r~~aylog
- Docker

# Howto:

Файлы для запуска проекта находятся в каталоге **docker**. Все описанные далее команды выполняются в нём.  
Конфиги Nginx для хоста — в **application/nginx-config** (впрочем, кроме проксирования запросов в контейнеры они ни хрена не делают)  
Урлы для API: **public.api.local** и **internal.api.local**. Сменить их можно в **application/.env**. Упаси Вас Ктулху делать это где-то ещё.

Собрать контейнеры (при первом запуске этот этап будет выполнен автоматически):
```bash
docker-compose build
```

Обычный запуск проекта:  
```bash
docker-compose up -d
```

Для тестирования необходимо также поднять отдельный контейнер с тестовой базой:
```bash
docker-compose -f docker-compose.yml -f docker-compose-test.yml up -d
```

Просмотр логов контейнера:
```bash
docker-compose logs -f *имя_сервиса*
```
Или
```bash
docker logs -f *имя контейнера*
```

Остановить и удалить все контейнеры:
```bash
docker-compose down
```

Если запущен контейнер с тестовой базой:
```bash
docker-compose -f docker-compose.yml -f docker-compose-test.yml down
```

# Что есть сейчас

- Регистрация
- Аутентификация (реализация через Security Bundle + JWT)

# Tips

- Сменить подсетку докера  
https://support.zenoss.com/hc/en-us/articles/203582809-How-to-Change-the-Default-Docker-Subnet
