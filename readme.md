# LUCRARE DE LICENȚĂ
### UNIVERSITATEA DIN BUCUREȘTI
### FACULTATEA DE ADMINISTRAȚIE ȘI AFACERI
![LOGO UNIBUC](https://unibuc.ro/wp-content/uploads/2018/12/Logo-UB-vertical-COLOR-limba-romana-e1544633678557.jpg)

### APLICAȚIE PENTRU MANAGEMENTUL UNUI RESTAURANT

##### Coordonator ștințific - Prof.univ.dr.Bogdan Oancea
##### Absolvent - Sărdaru Louis-Marian

# 1. Cerinţe sistem
- PHP 7.3 sau mai nou
- Server HTTP (Apache sau nginx)
- Server mysql
- Node

# 2. Setup aplicaţie

- Trebuie creată o bază de date pentru proiect. Acest lucru se poate face cu ajutorul lunui program de management al bazelor de date cum ar fi phpmyadmin, dbeaver sau sequel pro. Se mai poate crea şi din terminal, cu mysql.
- În folderul proiectului trebuie creat un fişier .env, care conţine setările proiectului. Fişierul .env.example poate fi folosit ca model. Trebuiesc completate APP_URL, DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME şi DB_PASSWORD
- Pentru instalarea dependinţelor (pachete externe), pe un terminal in folderul aplicaţiei se vor rula "composer install"(dependinţe PHP) şi "npm install" (Dependinţe Node)
- Migrate, seed, setup - Se vor rula trei comenzi, tot în directorul proiectului:  "php artisan migrate", "php artisan db:seed" şi "php artisan key:generate"
- Este recomandată rularea comenzii "php artisan storage:link" pentru a putea folosi media library (poze).
- Frontend build - Trebuie rulată comanda "npm run prod" pentru a genera elementele interfeţei grafice (frontend, vuejs).
- Server php: Aceasta este ultima comandă: "php artisan serve". Va porni un server php pe localhost (127.0.0.1) pe portul 8000. Acum trebuie vizitat acest url, în browser: http://localhost:8000


