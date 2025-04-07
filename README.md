## Especificaciones

* PHP 8.2
* Symfony 6.4


## Primera Instancia del Proyecto

En primer lugar, el archivo *.env* está incluido en *.gitignore*, pero tenemos a mano nuestra configuración base en el archivo *.env.dist*, por lo que deberemos copiar ese archivo y pegarlo con el nombre de *.env* y la variable "APP_ENV" le asignaremos el valor que proceda ("DEV" o "PROD"). Así podremos cambiar la configuración que queramos sin afectar al directorio GIT. También deberemos cambiar las variables de entorno necesarias.

Luego de esto hay que realizar los siguientes comandos.

    composer install

Una vez instalados los vendors, generaremos una key para encriptar los datos.

    php bin/console iserranodev:encrypt-bundle:generate-key

Seguido de esto, procederemos a ejecutar las migraciones para tener nuestra base de datos actualizada (**importante**).

    php bin/console doctrine:migrations:migrate

Como última instancia, crearemos un usuario para poder acceder con el siguiente comando:

    php bin/console app:create-admin-user

Introduciremos los datos que nos pedirá el comando y listo, ya tiene la aplicación en funcionamiento.


## Guía para el desarrollo en el proyecto

### Base de datos:

Para realizar cambios en la base de datos, será importante guardarlos en migraciones, ya que esto aporta
más dinamismo y nos permite guardar más información que otros comandos, así como *INSERT*, *DELETE* y cualquier interacción
con la base de datos. Si queremos generar una migración en función a los cambios que hagamos, usaremos el comando:

    php bin/console doctrine:migrations:diff

Este comando comparará nuestra base de datos con las entidades en la aplicación y generará las instrucciones SQL en dicha migración.
Si queremos introducir una migración que no se ve reflejada en las entidades (Insertar un rol, por ejemplo),
deberemos ejecutar el comando:

    php bin/console doctrine:migrations:generate

**Siempre mantener concordancia con los métodos up() y down(), ya que si hubiese algún error se podría retroceder esa migración**

### Estructura:

El flujo de la aplicación siempre sigue el mismo orden.

    Controlador -> Servicio -> Repositorio

Por lo que nunca se podrá acceder a repositorios desde el controlador, ni viceversa.

A su vez todos los elementos irán agrupados en carpetas según la entidad a la que vayan dirigida. Por ejemplo:

    Controller/User/UserController

### Request:

En la ruta *src/Request/* se encuentran las request autovalidadas, las cuales se han de crear y pasar como parámetro
a un método de un controlador en el caso de que se necesite validar algún campo.

### Filtros:

El proyecto cuenta con un sistema de filtros, en la carpeta *src/Utils/Classes*, llamado FilterService, que recoge los parámetros:

* **__filters**: Filtros
* **__orders**: Orden
* **__page**: Página filtrada
* **__limit**: Página filtrada

## Atribuciones:
Plantilla KaiAdmin: https://themewagon.com/themes/kaiadmin/
