# 💆 ADSO-SPA — Sistema de Agenda para Centro de Estética

Sistema de gestión de agenda para el centro de estética **ADSO-SPA**, desarrollado como proyecto práctico del programa **Análisis y Desarrollo de Software (ADSO)**.

El proyecto consiste en una aplicación ejecutada desde la terminal que permite administrar empleados, registrar citas, consultar la agenda y generar diferentes reportes sobre la información registrada durante la semana.

> **Tecnología principal:** PHP  
> **Ejecución:** Terminal  
> **Base de datos:** No utiliza  
> **Paradigma:** Programación estructurada

---

## 📋 Descripción

ADSO-SPA permite gestionar la información semanal de un centro de estética utilizando **arreglos multidimensionales, funciones propias y estructuras de control**.

Toda la información permanece almacenada en memoria mientras el programa está en ejecución. No se utilizan bases de datos, sesiones ni archivos externos para almacenar la información.

El sistema administra:

- 👤 Empleados
- 📅 Citas
- 👥 Clientes
- 💇 Servicios
- 💰 Facturación
- 📊 Comisiones
- ⚠️ Conflictos de agenda
- 📋 Agenda diaria

---

## 🛠️ Tecnologías utilizadas

- **PHP**
- Terminal / Consola
- Arreglos multidimensionales
- Funciones propias
- Estructuras de control
- Entrada de datos mediante teclado

El proyecto no utiliza programación orientada a objetos ni bases de datos.

---

## 📁 Estructura del proyecto

```text
ADSO-SPA/
│
├── index.php
└── README.md
```

El archivo principal del sistema es:

```text
index.php
```

---

## ▶️ Instalación y ejecución

### 1. Clonar el repositorio

```bash
git clone <URL_DEL_REPOSITORIO>
```

### 2. Entrar al proyecto

```bash
cd ADSO-SPA
```

### 3. Ejecutar el sistema

```bash
php index.php
```

El proyecto está diseñado para ejecutarse completamente desde la terminal mediante:

```bash
php index.php
```



---

## 📋 Menú principal

Al iniciar el programa se presenta el siguiente menú:

```text
========================================
           ADSO-SPA
     SISTEMA DE AGENDA
========================================

1. Registrar empleado
2. Registrar cita
3. Total facturado por empleado
4. Servicio más solicitado
5. Agenda de un día
6. Detección de conflictos
7. Liquidación de comisiones
8. Salir
```

Además, existe una opción especial oculta:

```text
dp
```

Esta opción permite cargar automáticamente los datos de prueba requeridos para la evaluación.

---

## 👨‍💼 Funcionalidades

### 1. Registrar empleado

Permite registrar:

- Nombre
- Especialidad

Después de registrar un empleado, el sistema permite continuar agregando empleados o regresar al menú principal.

---

### 2. Registrar cita

Para registrar una cita se debe seleccionar:

- Empleado
- Cliente
- Día
- Hora
- Uno o varios servicios

Los empleados y servicios se muestran mediante listas numeradas para que el usuario seleccione mediante su número.

#### Días disponibles

```text
Lunes
Martes
Miércoles
Jueves
Viernes
Sábado
```

#### Horario

Las citas pueden comenzar entre las:

```text
08:00 - 18:00
```

El sistema valida que los datos ingresados sean correctos y solicita nuevamente la información cuando existe un dato inválido.

---

## 💇 Catálogo de servicios

| # | Servicio | Precio | Duración |
|---:|---|---:|---:|
| 1 | Limpieza facial | $80.000 | 2 horas |
| 2 | Manicure | $35.000 | 1 hora |
| 3 | Pedicure | $40.000 | 1 hora |
| 4 | Masaje relajante | $90.000 | 1 hora |
| 5 | Masaje descontracturante | $100.000 | 1 hora |
| 6 | Exfoliación corporal | $60.000 | 1 hora |
| 7 | Tratamiento antiedad | $120.000 | 2 horas |

Los servicios forman parte de un catálogo fijo y al registrar una cita el usuario selecciona el servicio mediante su número.

Una misma cita puede contener **uno o varios servicios**.

---

## 💰 Consultas y reportes

### 3. Total facturado por empleado

Calcula cuánto dinero ha generado cada empleado durante la semana.

El resultado se presenta de **mayor a menor facturación**.

---

### 4. Servicio más solicitado

Determina:

- Servicio más utilizado
- Cantidad de veces que fue prestado
- Total facturado por ese servicio



---

### 5. Agenda de un día

Permite seleccionar un día y consultar todas las citas programadas.

La información se muestra ordenada por hora e incluye:

- Hora
- Empleado
- Cliente
- Servicios



---

### 6. Detección de conflictos

El sistema analiza las citas de cada empleado para detectar cuando dos citas se superponen.

La duración de una cita corresponde a la suma de las duraciones de todos los servicios incluidos en ella.

---

### 7. Liquidación de comisiones

Las comisiones se calculan según la cantidad de citas atendidas durante la semana:

| Condición | Comisión |
|---|---:|
| 6 o más citas | 12% |
| Menos de 6 citas | 8% |

Además, el empleado con mayor facturación recibe un bono adicional de:

```text
$50.000
```



---

## 🧪 Datos de prueba

El proyecto debe contar con datos suficientes para comprobar el funcionamiento de todas las consultas.

Los datos requeridos son:

- **4 empleados** como mínimo.
- **15 citas** como mínimo.
- Al menos **5 citas con 2 o más servicios**.
- Al menos **1 conflicto de agenda**.
- Todos los servicios deben pertenecer al catálogo establecido.

### Carga automática

Para evitar registrar manualmente todos los datos, el sistema dispone de la opción oculta:

```text
dp
```

Al ingresar `dp`:

1. Se cargan los datos de prueba.
2. El sistema confirma que fueron cargados correctamente.
3. Se regresa al menú principal.
4. Las opciones de registro quedan deshabilitadas.



---

## 🧠 Estructura del sistema

El proyecto utiliza una estructura de datos diseñada para representar las relaciones entre:

```text
Empleado
   │
   └── Citas
          │
          ├── Cliente
          ├── Día
          ├── Hora
          └── Servicios
                  │
                  ├── Nombre
                  ├── Precio
                  └── Duración
```

La organización de esta información mediante un arreglo multidimensional forma parte de los aspectos evaluados en el proyecto.

---

## 🔐 Validaciones

El sistema incorpora validaciones para evitar errores durante la captura de información.

Entre ellas:

- Los campos de texto no pueden estar vacíos.
- El servicio seleccionado debe existir.
- La hora debe estar entre 8 y 18.
- El día debe pertenecer a los días permitidos.
- El empleado seleccionado debe existir.
- El programa no debe cerrarse debido a un dato inválido.



---

## 📊 Formato de salida

Toda la información mostrada en la terminal debe utilizar **tablas alineadas y legibles**.

Los valores monetarios deben utilizar separador de miles.

No se utiliza `print_r` para mostrar directamente los arreglos.

Ejemplo:

```text
------------------------------------------------------------
Empleado              Citas              Facturación
------------------------------------------------------------
María López             8                $650.000
Carlos Pérez            6                $520.000
Ana Torres              4                $380.000
------------------------------------------------------------
```

---

## 📌 Restricciones del proyecto

El sistema fue desarrollado siguiendo las restricciones establecidas:

- ❌ No utiliza base de datos.
- ❌ No utiliza `$_SESSION`.
- ❌ No utiliza archivos externos para guardar información.
- ❌ No utiliza programación orientada a objetos.
- ✅ Utiliza arreglos.
- ✅ Utiliza funciones propias.
- ✅ Utiliza estructuras de control.
- ✅ Utiliza lectura de datos mediante teclado.
- ✅ Se ejecuta desde la terminal.



---

## 🎓 Objetivo académico

Este proyecto busca demostrar el manejo de:

- Arreglos multidimensionales.
- Funciones propias.
- Estructuras de control.
- Validación de datos.
- Procesamiento de información.
- Manejo de datos relacionados.
- Desarrollo de aplicaciones mediante PHP.
- Resolución de problemas mediante programación estructurada.

---

## 👨‍💻 Proyecto académico

**Programa:** Análisis y Desarrollo de Software (ADSO)

**Proyecto:** Sistema de agenda del centro de estética ADSO-SPA

**Tecnología:** PHP

**Modalidad:** Aplicación de consola

**Ejecución:**

```bash
php index.php
```

---

## 📄 Entregables

El proyecto contempla:

- Código fuente completo.
- Archivo `index.php`.
- Archivos adicionales requeridos por el proyecto.
- Repositorio en GitHub.
- Sustentación del funcionamiento del sistema.



---

## 📜 Licencia

Este proyecto fue desarrollado con fines **académicos y educativos** dentro del programa ADSO.

---