SkyEng.Словарь
===================================

Одностраничное приложение «Словарь», с помощью которого пользователь
сможет проверить свое знание языка.

Зависимости
-------------------
1) PHP >=5.4

2) MongoDB

3) composer

Инструкция по установке
-------------------
1) Клонируйте репозиторий

2) Настройте веб-сервер таким образом, чтобы http://your-domain/ указывал на
/frontend/web, а http://your-domain/api/ на /backend/web/

Например, если у вас apache, вы можете добавить symlink /frontend/web/api со
ссылкой на /backend/web/. Не забудьте добавить строку "Options +FollowSymlinks"
в конфигурацию виртуального хоста.

3) Если необходимо, настройте dsn в /backend/config/db.php

4) Выполните инициализацию и загрузку зависимостей в корневой директории

~~~
> ./init
> composer update
~~~

5) В консоли mongo выполните:

~~~

> use skyengdict

> db.words.insert([
{ word : "apple", translation : "яблоко", rnd: Math.random() },
{ word : "pear", translation : "груша", rnd: Math.random() },
{ word : "orange", translation : "апельсин", rnd: Math.random() },
{ word : "grape", translation : "виноград", rnd: Math.random() },
{ word : "lemon", translation : "лимон", rnd: Math.random() },
{ word : "pineapple", translation : "ананас", rnd: Math.random() },
{ word : "watermelon", translation : "арбуз", rnd: Math.random() },
{ word : "coconut", translation : "кокос", rnd: Math.random() },
{ word : "banana", translation : "банан", rnd: Math.random() },
{ word : "pomelo", translation : "помело", rnd: Math.random() },
{ word : "strawberry", translation : "клубника", rnd: Math.random() },
{ word : "raspberry", translation : "малина", rnd: Math.random() },
{ word : "melon", translation : "дыня", rnd: Math.random() },
{ word : "peach", translation : "персик", rnd: Math.random() },
{ word : "apricot", translation : "абрикос", rnd: Math.random() },
{ word : "mango", translation : "манго", rnd: Math.random() },
{ word : "plum", translation : "слива", rnd: Math.random() },
{ word : "pomegranate", translation : "гранат", rnd: Math.random() },
{ word : "cherry", translation : "вишня", rnd: Math.random()}
]);

> db.words.ensureIndex({ rnd: 1 });

~~~

6) Все готово!

