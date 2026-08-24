<?php

$catalogo = [
    ['nombre' => 'Limpieza facial',           'precio' => 80000,  'duracion' => 2],
    ['nombre' => 'Manicure',                  'precio' => 35000,  'duracion' => 1],
    ['nombre' => 'Pedicure',                  'precio' => 40000,  'duracion' => 1],
    ['nombre' => 'Masaje relajante',          'precio' => 90000,  'duracion' => 1],
    ['nombre' => 'Masaje descontracturante',  'precio' => 100000, 'duracion' => 1],
    ['nombre' => 'Exfoliación corporal',      'precio' => 60000,  'duracion' => 1],
    ['nombre' => 'Tratamiento antiedad',      'precio' => 120000, 'duracion' => 2],
];

$diasValidos = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'];

$empleados = [];   // cada uno: ['nombre' => string, 'especialidad' => string]
$citas = [];       // cada una: ['empleado' => id, 'cliente' => string, 'dia' => string, 'hora' => int, 'servicios' => [id, id, ...]]

$datosCargados = false;

function leer($mensaje) {
    return trim(readline($mensaje));
}

function leerTextoNoVacio($mensaje) {
    do {
        $valor = leer($mensaje);
        if ($valor === '') {
            echo "Este campo no puede quedar vacío.\n";
        }
    } while ($valor === '');
    return $valor;
}

function leerDia($mensaje, $diasValidos) {
    do {
        $dia = strtolower(leer($mensaje));
        $valido = in_array($dia, $diasValidos, true);
        if (!$valido) {
            $listaDias = "";
            for ($i = 0; $i < count($diasValidos); $i++) {
                if ($i > 0) {
                    $listaDias = $listaDias . ", ";
                }
                $listaDias = $listaDias . $diasValidos[$i];
            }
            echo "Día inválido. Use uno de: " . $listaDias . "\n";
        }
    } while (!$valido);
    return $dia;
}

function leerHora($mensaje) {
    do {
        $hora = leer($mensaje);
        $valido = ctype_digit($hora) && (int)$hora >= 8 && (int)$hora <= 18;
        if (!$valido) {
            echo "La hora debe ser un número entre 8 y 18.\n";
        }
    } while (!$valido);
    return (int)$hora;
}

function leerNumeroEnLista($mensaje, $totalItems) {
    do {
        $numero = leer($mensaje);
        $valido = ctype_digit($numero) && (int)$numero >= 1 && (int)$numero <= $totalItems;
        if (!$valido) {
            echo "Número inválido, intente de nuevo.\n";
        }
    } while (!$valido);
    return (int)$numero;
}

function formatearDinero($valor) {
    return "$" . number_format($valor, 0, ',', '.');
}

// --- Funciones propias de formato de texto (reemplazan printf, str_repeat, implode) ---

// Agrega espacios a la DERECHA hasta completar el ancho. Ej: espacioDerecha("Ana", 6) -> "Ana   "
function espacioDerecha($texto, $ancho) {
    $texto = (string) $texto;
    while (strlen($texto) < $ancho) {
        $texto = $texto . " ";
    }
    return $texto;
}

// Agrega espacios a la IZQUIERDA hasta completar el ancho. Ej: espacioIzquierda("5", 4) -> "   5"
function espacioIzquierda($texto, $ancho) {
    $texto = (string) $texto;
    while (strlen($texto) < $ancho) {
        $texto = " " . $texto;
    }
    return $texto;
}

// Repite un caracter "cantidad" veces. Ej: linea_repetida("-", 5) -> "-----"
function linea_repetida($caracter, $cantidad) {
    $linea = "";
    for ($i = 0; $i < $cantidad; $i++) {
        $linea = $linea . $caracter;
    }
    return $linea;
}

function duracionCita($cita, $catalogo) {
    $total = 0;
    foreach ($cita['servicios'] as $idServicio) {
        $total += $catalogo[$idServicio]['duracion'];
    }
    return $total;
}

function horaFinCita($cita, $catalogo) {
    return $cita['hora'] + duracionCita($cita, $catalogo);
}

function totalCita($cita, $catalogo) {
    $total = 0;
    foreach ($cita['servicios'] as $idServicio) {
        $total += $catalogo[$idServicio]['precio'];
    }
    return $total;
}

function registrarEmpleado(&$empleados) {
    do {
        echo "\n--- Registrar empleado ---\n";
        $nombre = leerTextoNoVacio("Nombre: ");
        $especialidad = leerTextoNoVacio("Especialidad: ");

        $empleados[] = ['nombre' => $nombre, 'especialidad' => $especialidad];
        echo "Empleado registrado correctamente.\n";

        $otro = strtolower(leer("¿Registrar otro empleado? (s/n): "));
    } while ($otro === 's');
}

function registrarCita(&$citas, $empleados, $catalogo, $diasValidos) {
    if (empty($empleados)) {
        echo "\nDebe registrar al menos un empleado antes de agendar una cita.\n";
        return;
    }

    echo "\n--- Registrar cita ---\n";
    echo "Empleados disponibles:\n";
    foreach ($empleados as $i => $empleado) {
        echo ($i + 1) . ". " . $empleado['nombre'] . " (" . $empleado['especialidad'] . ")\n";
    }
    $numEmpleado = leerNumeroEnLista("Seleccione el número del empleado: ", count($empleados));
    $idEmpleado = $numEmpleado - 1;

    $cliente = leerTextoNoVacio("Cliente: ");
    $dia = leerDia("Día (lunes a sábado): ", $diasValidos);
    $hora = leerHora("Hora de inicio (8 a 18): ");

    $servicios = [];
    do {
        echo "Catálogo de servicios:\n";
        foreach ($catalogo as $i => $servicio) {
            echo ($i + 1) . ". " . espacioDerecha($servicio['nombre'], 25) . espacioIzquierda(formatearDinero($servicio['precio']), 12) . "  " . $servicio['duracion'] . "h\n";
        }
        $numServicio = leerNumeroEnLista("Seleccione el número del servicio: ", count($catalogo));
        $servicios[] = $numServicio - 1;

        $otro = strtolower(leer("¿Agregar otro servicio a esta cita? (s/n): "));
    } while ($otro === 's');

    $citas[] = [
        'empleado' => $idEmpleado,
        'cliente' => $cliente,
        'dia' => $dia,
        'hora' => $hora,
        'servicios' => $servicios,
    ];

    echo "Cita registrada correctamente.\n";
}

function totalFacturadoPorEmpleado($citas, $empleados, $catalogo) {
    echo "\n--- Total facturado por empleado ---\n";

    if (empty($empleados)) {
        echo "No hay empleados registrados.\n";
        return;
    }

    // arreglo de totales en 0, uno por cada empleado
    $totales = [];
    for ($i = 0; $i < count($empleados); $i++) {
        $totales[] = 0;
    }

    foreach ($citas as $cita) {
        $idEmp = $cita['empleado'];
        $totales[$idEmp] = $totales[$idEmp] + totalCita($cita, $catalogo);
    }

    $filas = [];
    for ($i = 0; $i < count($empleados); $i++) {
        $filas[] = ['nombre' => $empleados[$i]['nombre'], 'total' => $totales[$i]];
    }

    // ordenar de mayor a menor total (burbuja simple)
    for ($i = 0; $i < count($filas); $i++) {
        for ($j = 0; $j < count($filas) - 1 - $i; $j++) {
            if ($filas[$j]['total'] < $filas[$j + 1]['total']) {
                $temp = $filas[$j];
                $filas[$j] = $filas[$j + 1];
                $filas[$j + 1] = $temp;
            }
        }
    }

    echo espacioDerecha("Empleado", 25) . espacioIzquierda("Total facturado", 15) . "\n";
    echo linea_repetida("-", 41) . "\n";
    foreach ($filas as $fila) {
        echo espacioDerecha($fila['nombre'], 25) . espacioIzquierda(formatearDinero($fila['total']), 15) . "\n";
    }
}

function servicioMasSolicitado($citas, $catalogo) {
    echo "\n--- Servicio más solicitado ---\n";

    $conteo = [];
    $facturado = [];
    for ($i = 0; $i < count($catalogo); $i++) {
        $conteo[] = 0;
        $facturado[] = 0;
    }

    foreach ($citas as $cita) {
        foreach ($cita['servicios'] as $idServicio) {
            $conteo[$idServicio] = $conteo[$idServicio] + 1;
            $facturado[$idServicio] = $facturado[$idServicio] + $catalogo[$idServicio]['precio'];
        }
    }

    $totalServicios = 0;
    for ($i = 0; $i < count($conteo); $i++) {
        $totalServicios = $totalServicios + $conteo[$i];
    }

    if ($totalServicios === 0) {
        echo "No hay servicios registrados todavía.\n";
        return;
    }

    // buscar cuál servicio tiene el conteo más alto
    $idGanador = 0;
    for ($i = 1; $i < count($conteo); $i++) {
        if ($conteo[$i] > $conteo[$idGanador]) {
            $idGanador = $i;
        }
    }

    echo espacioDerecha("Servicio", 25) . espacioIzquierda("Veces", 10) . espacioIzquierda("Facturado", 15) . "\n";
    echo linea_repetida("-", 51) . "\n";
    echo espacioDerecha($catalogo[$idGanador]['nombre'], 25) . espacioIzquierda($conteo[$idGanador], 10) . espacioIzquierda(formatearDinero($facturado[$idGanador]), 15) . "\n";
}

function agendaDeUnDia($citas, $empleados, $catalogo, $diasValidos) {
    echo "\n--- Agenda de un día ---\n";
    $dia = leerDia("¿Qué día desea consultar?: ", $diasValidos);

    // filtrar manualmente las citas de ese día
    $citasDelDia = [];
    foreach ($citas as $cita) {
        if ($cita['dia'] === $dia) {
            $citasDelDia[] = $cita;
        }
    }

    if (count($citasDelDia) === 0) {
        echo "No hay citas registradas para {$dia}.\n";
        return;
    }

    // ordenar por hora, de menor a mayor (burbuja simple)
    for ($i = 0; $i < count($citasDelDia); $i++) {
        for ($j = 0; $j < count($citasDelDia) - 1 - $i; $j++) {
            if ($citasDelDia[$j]['hora'] > $citasDelDia[$j + 1]['hora']) {
                $temp = $citasDelDia[$j];
                $citasDelDia[$j] = $citasDelDia[$j + 1];
                $citasDelDia[$j + 1] = $temp;
            }
        }
    }

    echo espacioDerecha("Hora", 6) . espacioDerecha("Empleado", 20) . espacioDerecha("Cliente", 18) . "Servicios\n";
    echo linea_repetida("-", 75) . "\n";
    foreach ($citasDelDia as $cita) {
        // armar el texto de servicios a mano, sin array_map ni implode
        $textoServicios = "";
        for ($i = 0; $i < count($cita['servicios']); $i++) {
            if ($i > 0) {
                $textoServicios = $textoServicios . ", ";
            }
            $idServicio = $cita['servicios'][$i];
            $textoServicios = $textoServicios . $catalogo[$idServicio]['nombre'];
        }

        echo espacioDerecha($cita['hora'] . ":00", 6);
        echo espacioDerecha($empleados[$cita['empleado']]['nombre'], 20);
        echo espacioDerecha($cita['cliente'], 18);
        echo $textoServicios . "\n";
    }
}

function deteccionConflictos($citas, $empleados, $catalogo) {
    echo "\n--- Detección de conflictos de agenda ---\n";

    $encontrados = false;
    $total = count($citas);

    for ($i = 0; $i < $total; $i++) {
        for ($j = $i + 1; $j < $total; $j++) {
            $citaA = $citas[$i];
            $citaB = $citas[$j];

            if ($citaA['empleado'] !== $citaB['empleado'] || $citaA['dia'] !== $citaB['dia']) {
                continue;
            }

            $inicioA = $citaA['hora'];
            $finA = horaFinCita($citaA, $catalogo);
            $inicioB = $citaB['hora'];
            $finB = horaFinCita($citaB, $catalogo);

            if ($inicioA < $finB && $inicioB < $finA) {
                $encontrados = true;
                $nombreEmpleado = $empleados[$citaA['empleado']]['nombre'];
                echo "Conflicto: {$nombreEmpleado} el {$citaA['dia']} entre {$citaA['cliente']} ";
                echo "({$inicioA}:00-{$finA}:00) y {$citaB['cliente']} ({$inicioB}:00-{$finB}:00)\n";
            }
        }
    }

    if (!$encontrados) {
        echo "No se encontraron conflictos de agenda.\n";
    }
}

function liquidacionComisiones($citas, $empleados, $catalogo) {
    echo "\n--- Liquidación de comisiones ---\n";

    if (empty($empleados)) {
        echo "No hay empleados registrados.\n";
        return;
    }

    $totales = [];
    $numCitas = [];
    for ($i = 0; $i < count($empleados); $i++) {
        $totales[] = 0;
        $numCitas[] = 0;
    }

    foreach ($citas as $cita) {
        $idEmp = $cita['empleado'];
        $totales[$idEmp] = $totales[$idEmp] + totalCita($cita, $catalogo);
        $numCitas[$idEmp] = $numCitas[$idEmp] + 1;
    }

    // buscar el total más alto, a mano
    $maxTotal = $totales[0];
    for ($i = 1; $i < count($totales); $i++) {
        if ($totales[$i] > $maxTotal) {
            $maxTotal = $totales[$i];
        }
    }

    echo espacioDerecha("Empleado", 22) . espacioIzquierda("Citas", 8) . espacioIzquierda("Facturado", 15) . espacioIzquierda("Comisión", 12) . espacioIzquierda("Total a pagar", 15) . "\n";
    echo linea_repetida("-", 76) . "\n";

    for ($i = 0; $i < count($empleados); $i++) {
        // porcentaje como número Y como texto, para no depender de round()
        // (0.12 * 100 en decimales a veces da 11.999999... por eso antes se usaba round)
        if ($numCitas[$i] >= 6) {
            $porcentaje = 0.12;
            $porcentajeTexto = "12";
        } else {
            $porcentaje = 0.08;
            $porcentajeTexto = "8";
        }

        $comision = $totales[$i] * $porcentaje;

        if ($totales[$i] === $maxTotal && $maxTotal > 0) {
            $bono = 50000;
        } else {
            $bono = 0;
        }

        $totalPagar = $comision + $bono;

        $etiqueta = $porcentajeTexto . "%";
        if ($bono > 0) {
            $etiqueta = $etiqueta . " +bono";
        }

        echo espacioDerecha($empleados[$i]['nombre'], 22);
        echo espacioIzquierda($numCitas[$i], 8);
        echo espacioIzquierda(formatearDinero($totales[$i]), 15);
        echo espacioIzquierda($etiqueta, 12);
        echo espacioIzquierda(formatearDinero($totalPagar), 15);
        echo "\n";
    }
}

function cargarDatosPrueba(&$empleados, &$citas) {
    $empleados = [
        ['nombre' => 'Laura Gómez',  'especialidad' => 'Facial'],
        ['nombre' => 'Carlos Pérez', 'especialidad' => 'Masajes'],
        ['nombre' => 'Ana Ruiz',     'especialidad' => 'Uñas'],
        ['nombre' => 'Diego Torres', 'especialidad' => 'Corporal'],
    ];

    // Servicios según posición en $catalogo:
    // 0 Limpieza facial, 1 Manicure, 2 Pedicure, 3 Masaje relajante,
    // 4 Masaje descontracturante, 5 Exfoliación corporal, 6 Tratamiento antiedad
    $citas = [
        ['empleado' => 0, 'cliente' => 'María Peña',     'dia' => 'lunes',     'hora' => 8,  'servicios' => [0]],
        ['empleado' => 0, 'cliente' => 'Juana Ríos',     'dia' => 'lunes',     'hora' => 11, 'servicios' => [1]],
        ['empleado' => 1, 'cliente' => 'Pedro Gómez',    'dia' => 'martes',    'hora' => 10, 'servicios' => [3]],
        ['empleado' => 1, 'cliente' => 'Luis Cano',      'dia' => 'martes',    'hora' => 13, 'servicios' => [4]],
        ['empleado' => 2, 'cliente' => 'Sofía Núñez',    'dia' => 'miercoles', 'hora' => 9,  'servicios' => [1, 2]],
        ['empleado' => 2, 'cliente' => 'Valentina Cruz', 'dia' => 'miercoles', 'hora' => 14, 'servicios' => [1]],
        ['empleado' => 3, 'cliente' => 'Andrés Silva',   'dia' => 'jueves',    'hora' => 8,  'servicios' => [5]],
        ['empleado' => 3, 'cliente' => 'Camila Rojas',   'dia' => 'jueves',    'hora' => 10, 'servicios' => [6]],
        ['empleado' => 0, 'cliente' => 'Jorge Díaz',     'dia' => 'viernes',   'hora' => 11, 'servicios' => [0, 1]],
        ['empleado' => 1, 'cliente' => 'Natalia Vega',   'dia' => 'viernes',   'hora' => 9,  'servicios' => [3, 4]],
        ['empleado' => 2, 'cliente' => 'Ricardo Mora',   'dia' => 'sabado',    'hora' => 8,  'servicios' => [2]],
        ['empleado' => 3, 'cliente' => 'Paula Ortiz',    'dia' => 'sabado',    'hora' => 9,  'servicios' => [5, 6]],
        ['empleado' => 0, 'cliente' => 'Esteban Lara',   'dia' => 'sabado',    'hora' => 13, 'servicios' => [1]],
        ['empleado' => 1, 'cliente' => 'Daniela Vargas', 'dia' => 'lunes',     'hora' => 15, 'servicios' => [3]],
        ['empleado' => 2, 'cliente' => 'Miguel Ángel',   'dia' => 'martes',    'hora' => 16, 'servicios' => [2, 1]],
    ];
}

function mostrarMenu() {
    static $primeraVez = true;

    if ($primeraVez) {
        $primeraVez = false;
    } else {
        sleep(1);
    }
    system('clear');

    echo "\n============================================\n";
    echo " ADSO-SPA - Sistema de agenda\n";
    echo "============================================\n";
    echo "1. Registrar empleado\n";
    echo "2. Registrar cita\n";
    echo "3. Total facturado por empleado\n";
    echo "4. Servicio más solicitado\n";
    echo "5. Agenda de un día\n";
    echo "6. Detección de conflictos\n";
    echo "7. Liquidación de comisiones\n";
    echo "8. Salir\n";
    echo "============================================\n";
}

function pausar() {
    leer("\nPresione Enter para continuar...\n") . "Espere 1 segundo para continuar...";
    
}

$opcion = "";

while ($opcion !== "8") {
    mostrarMenu();
    $opcion = leer("Seleccione una opción: ");

    switch ($opcion) {
        case "dp":
            if ($datosCargados) {
                echo "\nOpción no disponible: los datos de prueba ya fueron cargados.\n";
            } else {
                cargarDatosPrueba($empleados, $citas);
                $datosCargados = true;
                echo "\nDatos de prueba cargados exitosamente.\n";
            }
            pausar();
            break;

        case "1":
            if ($datosCargados) {
                echo "\nOpción no disponible: los datos de prueba ya fueron cargados.\n";
            } else {
                registrarEmpleado($empleados);
            }
            pausar();
            break;

        case "2":
            if ($datosCargados) {
                echo "\nOpción no disponible: los datos de prueba ya fueron cargados.\n";
            } else {
                registrarCita($citas, $empleados, $catalogo, $diasValidos);
            }
            pausar();
            break;

        case "3":
            totalFacturadoPorEmpleado($citas, $empleados, $catalogo);
            pausar();
            break;

        case "4":
            servicioMasSolicitado($citas, $catalogo);
            pausar();
            break;

        case "5":
            agendaDeUnDia($citas, $empleados, $catalogo, $diasValidos);
            pausar();
            break;

        case "6":
            deteccionConflictos($citas, $empleados, $catalogo);
            pausar();
            break;

        case "7":
            liquidacionComisiones($citas, $empleados, $catalogo);
            pausar();
            break;

        case "8":
            echo "\nSaliendo del programa...\n";
            break;

        default:
            echo "\nOpción inválida, intente de nuevo.\n";
            pausar();
    }
}

// esto no tiene ia, pura logicaaa