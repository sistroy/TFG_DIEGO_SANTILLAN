# TFG (Trabajo de Fin de Grado) - Implementación y desarrollo de una guía de configuración de Kubernetes y Dockers en Fedora para empresas

Este repositorio contiene el trabajo realizado para el TFG (Trabajo de Fin de Grado) del grado de Informática de la Universidad UNIR. El proyecto se enfoca en el desarrollo de una guia de implementación de Kubernetes y Docker como parte de la infraestructura de despliegue para empresas

## Contenido del Repositorio

El repositorio se organiza de la siguiente manera:

- **/Dockerfiles**: Este archivo contiene el codigo de definición necesario para la creación de la imagen Docker de prueba que se explica en el TFG.

- **/files**: En esta carpeta se encuentra el código de una API desarrollada en PHP que se utilizó como ejemplo para crear la imagen y posteriormetne el pod de pruebas implementado el el TFG. Puedes explorar este código como referencia o utilizarlo como punto de partida para tus propios proyectos.

- - **/podtest.yaml**: Este archivo contiene el codigo de definición necesario para la creación de pod de pruebas que se explica en el TFG.

## Requisitos y Configuración

Antes de comenzar a utilizar este código, asegúrate de tener los siguientes requisitos en su lugar:

- Docker instalado en cada Nodo del clúster
- cri-dockerd instalado y configurado en cada Nodo del clúster
- Clúster de Kubernetes implementado .- En el TFG se especifica los pasos a seguir para su implementación
- Calico instalado y funcional en cada nodo del clúster

## Instrucciones de Uso

En el apartado 4.7 del TFG se especifica como debe levantarse las Pruebas del Clúster de Kubernetes

## Contribuciones

Si deseas contribuir a este proyecto, ¡te damos la bienvenida! Puedes realizar contribuciones mediante pull requests. Asegúrate de seguir las mejores prácticas y de documentar cualquier cambio que realices.

## Licencia

Este proyecto se distribuye bajo la licencia Apache. Consulta el archivo LICENSE para obtener más información.

## Contacto

Si tienes alguna pregunta o necesitas ayuda, no dudes en ponerte en contacto con nosotros:

- Diego Fernando Santillán Delgado
- Correo electrónico: sistroy@msn.com

¡Gracias por visitar nuestro repositorio!
