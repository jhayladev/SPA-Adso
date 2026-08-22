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
            echo "Día inválido. Use uno de: " . implode(", ", $diasValidos) . "\n";
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
        printf("%d. %s (%s)\n", $i + 1, $empleado['nombre'], $empleado['especialidad']);
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
            printf("%d. %-25s %12s  %dh\n", $i + 1, $servicio['nombre'], formatearDinero($servicio['precio']), $servicio['duracion']);
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

    $totales = array_fill(0, count($empleados), 0);
    foreach ($citas as $cita) {
        $totales[$cita['empleado']] += totalCita($cita, $catalogo);
    }

    $filas = [];
    foreach ($empleados as $i => $empleado) {
        $filas[] = ['nombre' => $empleado['nombre'], 'total' => $totales[$i]];
    }
    usort($filas, function ($a, $b) {
        return $b['total'] <=> $a['total'];
    });

    printf("%-25s %15s\n", "Empleado", "Total facturado");
    echo str_repeat("-", 41) . "\n";
    foreach ($filas as $fila) {
        printf("%-25s %15s\n", $fila['nombre'], formatearDinero($fila['total']));
    }
}

function servicioMasSolicitado($citas, $catalogo) {
    echo "\n--- Servicio más solicitado ---\n";

    $conteo = array_fill(0, count($catalogo), 0);
    $facturado = array_fill(0, count($catalogo), 0);

    foreach ($citas as $cita) {
        foreach ($cita['servicios'] as $idServicio) {
            $conteo[$idServicio]++;
            $facturado[$idServicio] += $catalogo[$idServicio]['precio'];
        }
    }

    if (array_sum($conteo) === 0) {
        echo "No hay servicios registrados todavía.\n";
        return;
    }

    $maxVeces = max($conteo);
    $idGanador = array_search($maxVeces, $conteo, true);

    printf("%-25s %10s %15s\n", "Servicio", "Veces", "Facturado");
    echo str_repeat("-", 51) . "\n";
    printf("%-25s %10d %15s\n", $catalogo[$idGanador]['nombre'], $maxVeces, formatearDinero($facturado[$idGanador]));
}

function agendaDeUnDia($citas, $empleados, $catalogo, $diasValidos) {
    echo "\n--- Agenda de un día ---\n";
    $dia = leerDia("¿Qué día desea consultar?: ", $diasValidos);

    $citasDelDia = array_values(array_filter($citas, function ($cita) use ($dia) {
        return $cita['dia'] === $dia;
    }));

    if (empty($citasDelDia)) {
        echo "No hay citas registradas para {$dia}.\n";
        return;
    }

    usort($citasDelDia, function ($a, $b) {
        return $a['hora'] <=> $b['hora'];
    });

    printf("%-6s %-20s %-18s %s\n", "Hora", "Empleado", "Cliente", "Servicios");
    echo str_repeat("-", 75) . "\n";
    foreach ($citasDelDia as $cita) {
        $nombresServicios = array_map(function ($id) use ($catalogo) {
            return $catalogo[$id]['nombre'];
        }, $cita['servicios']);

        printf(
            "%-6s %-20s %-18s %s\n",
            $cita['hora'] . ":00",
            $empleados[$cita['empleado']]['nombre'],
            $cita['cliente'],
            implode(", ", $nombresServicios)
        );
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

    $totales = array_fill(0, count($empleados), 0);
    $numCitas = array_fill(0, count($empleados), 0);

    foreach ($citas as $cita) {
        $totales[$cita['empleado']] += totalCita($cita, $catalogo);
        $numCitas[$cita['empleado']]++;
    }

    $maxTotal = max($totales);

    printf("%-22s %8s %15s %12s %15s\n", "Empleado", "Citas", "Facturado", "Comisión", "Total a pagar");
    echo str_repeat("-", 76) . "\n";
    foreach ($empleados as $i => $empleado) {
        $porcentaje = $numCitas[$i] >= 6 ? 0.12 : 0.08;
        $comision = $totales[$i] * $porcentaje;
        $bono = ($totales[$i] === $maxTotal && $maxTotal > 0) ? 50000 : 0;
        $totalPagar = $comision + $bono;

        $etiqueta = round($porcentaje * 100) . "%" . ($bono > 0 ? " +bono" : "");

        printf(
            "%-22s %8d %15s %12s %15s\n",
            $empleado['nombre'],
            $numCitas[$i],
            formatearDinero($totales[$i]),
            $etiqueta,
            formatearDinero($totalPagar)
        );
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
    leer("\nPresione Enter para continuar...");
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
            // Pendiente: se termina más adelante
            echo "\nEsta opción todavía no está lista.\n";
            pausar();
            break;

        case "5":
            // Pendiente: se termina más adelante
            echo "\nEsta opción todavía no está lista.\n";
            pausar();
            break;

        case "6":
            // Pendiente: se termina más adelante
            echo "\nEsta opción todavía no está lista.\n";
            pausar();
            break;

        case "7":
            // Pendiente: se termina más adelante
            echo "\nEsta opción todavía no está lista.\n";
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