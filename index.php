<?php

$catalogo_servicio = [
    ['nombre' => 'Limpieza facial',          'precio' => 80000,  'duracion' => 2],
    ['nombre' => 'Manicure',                 'precio' => 35000,  'duracion' => 1],
    ['nombre' => 'Pedicure',                 'precio' => 40000,  'duracion' => 1],
    ['nombre' => 'Masaje relajante',         'precio' => 90000,  'duracion' => 1],
    ['nombre' => 'Masaje descontracturante', 'precio' => 100000, 'duracion' => 1],
    ['nombre' => 'Exfoliacion corporal',     'precio' => 60000,  'duracion' => 1],
    ['nombre' => 'Tratamiento antiedad',     'precio' => 120000, 'duracion' => 2],
];

$dias_validos = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'];

$emp = [];
$citas = [];
$datos_cargados = false;

function read_msg($msg)
{
    return trim(readline($msg));
}

function validar_vacio($msg)
{
    do
    {
        $valor = read_msg($msg);
        if ($valor === '')
        {
            echo "Este campo no puede estar vacio.\n";
        }
    } while ($valor === '');
    return $valor;
}

function read_dia($msg, $dias_validos)
{
    do
    {
        $dia = strtolower(read_msg($msg));
        $valido = in_array($dia, $dias_validos, true);
        if (!$valido)
        {
            echo "Dia invalido. Utilice uno de los siguientes: " . unir_textos($dias_validos, ", ") . "\n";
        }
    } while (!$valido);
    return $dia;
}

function read_hora($msg)
{
    do
    {
        $texto = read_msg($msg);
        $valido = false;
        $hora = 0;
        $minuto = 0;

        if (strlen($texto) === 5 && $texto[2] === ':')
        {
            $parte_hora = substr($texto, 0, 2);
            $parte_minuto = substr($texto, 3, 2);

            if (ctype_digit($parte_hora) && ctype_digit($parte_minuto))
            {
                $hora = (int)$parte_hora;
                $minuto = (int)$parte_minuto;

                $dentro_de_rango = ($hora >= 8 && $hora < 18) || ($hora === 18 && $minuto === 0);
                $valido = $dentro_de_rango && $minuto >= 0 && $minuto <= 59;
            }
        }

        if (!$valido)
        {
            echo "Formato invalido. Use HH:MM, con hora entre 08:00 y 18:00 (ej. 08:12).\n";
        }
    } while (!$valido);
    return $hora * 60 + $minuto;
}

function num_in_list($msg, $items)
{
    do
    {
        $num = read_msg($msg);
        $valido = ctype_digit($num) && (int)$num >= 1 && (int)$num <= $items;
        if (!$valido)
        {
            echo "Numero invalido, intente otra vez.\n";
        }
    } while (!$valido);
    return (int)$num;
}

function format_money($valor)
{
    return "$" . number_format($valor, 0, ',', '.');
}

function format_hora($minutos)
{
    $hora = intdiv($minutos, 60);
    $minuto = $minutos % 60;
    return zero_left($hora, 2) . ":" . zero_left($minuto, 2);
}

function space_right($texto, $ancho)
{
    $texto = (string)$texto;
    while (strlen($texto) < $ancho)
    {
        $texto = $texto . " ";
    }
    return $texto;
}

function space_left($texto, $ancho)
{
    $texto = (string)$texto;
    while (strlen($texto) < $ancho)
    {
        $texto = " " . $texto;
    }
    return $texto;
}

function zero_left($numero, $ancho)
{
    $texto = (string)$numero;
    while (strlen($texto) < $ancho)
    {
        $texto = "0" . $texto;
    }
    return $texto;
}

function line_repeat($caracter, $cantidad)
{
    $linea = "";
    for ($i = 0; $i < $cantidad; $i++)
    {
        $linea = $linea . $caracter;
    }
    return $linea;
}

function unir_textos($items, $separador)
{
    $texto = "";
    for ($i = 0; $i < count($items); $i++)
    {
        if ($i > 0)
        {
            $texto = $texto . $separador;
        }
        $texto = $texto . $items[$i];
    }
    return $texto;
}

function duration_date($cita, $catalogo)
{
    $total = 0;
    foreach ($cita['servicios'] as $id_servicio)
    {
        $total += $catalogo[$id_servicio]['duracion'];
    }
    return $total * 60;
}

function end_date($cita, $catalogo)
{
    return $cita['hora'] + duration_date($cita, $catalogo);
}

function total_date($cita, $catalogo)
{
    $total = 0;
    foreach ($cita['servicios'] as $id_servicio)
    {
        $total += $catalogo[$id_servicio]['precio'];
    }
    return $total;
}

function nombres_catalogo($catalogo)
{
    $nombres = [];
    foreach ($catalogo as $servicio)
    {
        $nombres[] = $servicio['nombre'];
    }
    return $nombres;
}

function read_especialidad_valida($nombres_catalogo)
{
    do
    {
        echo "Especialidades disponibles:\n";

        for ($i = 0; $i < count($nombres_catalogo); $i++)
        {
            echo ($i + 1) . ". " . $nombres_catalogo[$i] . "\n";
        }

        $numero = read_msg("Seleccione el numero de la especialidad: ");

        if (ctype_digit($numero) && (int)$numero >= 1 && (int)$numero <= count($nombres_catalogo))
        {
            return $nombres_catalogo[(int)$numero - 1];
        }

        echo "Numero invalido, intente otra vez.\n\n";

    } while (true);
}

function register_empleado(&$emp, $catalogo)
{
    $nombres_disponibles = nombres_catalogo($catalogo);

    do
    {
        echo "\n--- Registrar empleado ---\n";
        $nombre = validar_vacio("Nombre: ");

        $especialidades = [];

        do{
            $especialidad = read_especialidad_valida($nombres_disponibles);

            if(in_array($especialidad, $especialidades, true))
            {
                echo "Esa especialidad ya fue agregada. \n";
            }
            else
            {
                $especialidades[] = $especialidad;
            }

            $otra =strtolower(read_msg("Agregar otra especialidad? (s/n): "));
        } while($otra === 's');
        $emp[] = ['nombre' => $nombre, 'especialidades' => $especialidades];
        echo "Empleado registrado correctamente.\n";

        $otro = strtolower(read_msg("Registrar otro empleado? (s/n): "));
    } while ($otro === 's');
}

function register_date(&$citas, $emp, $catalogo, $dias_validos)
{
    if (empty($emp))
    {
        echo "\nDebe registrar al menos un empleado antes de agendar una cita.\n";
        return;
    }

    echo "\n--- Registrar cita ---\n";
    echo "Empleados disponibles:\n";
    for ($i = 0; $i < count($emp); $i++)
    {
        echo ($i + 1) . ". " . $emp[$i]['nombre'] . " (" . unir_textos($emp[$i]['especialidades'], ", ") . ")\n";
    }
    $num_empleado = num_in_list("Seleccione el numero del empleado: ", count($emp));
    $id_empleado = $num_empleado - 1;

    $especialidades_emp = $emp[$id_empleado]['especialidades'];
    $indices_disponibles = [];
    for ($i = 0; $i < count($catalogo); $i++)
    {
        if (in_array($catalogo[$i]['nombre'], $especialidades_emp, true))
        {
            $indices_disponibles[] = $i;
        }
    }

    if (empty($indices_disponibles))
    {
        echo "\nEste empleado no tiene especialidades registradas en el catalogo de servicios. No se puede agendar la cita.\n";
        return;
    }

    $cliente = validar_vacio("Cliente: ");
    $dia = read_dia("Dia (lunes a sabado): ", $dias_validos);
    $hora = read_hora("Hora de inicio (HH:MM, entre 08:00 y 18:00): ");

    $servicios = [];
    do
    {
        echo "Servicios que puede realizar " . $emp[$id_empleado]['nombre'] . ":\n";
        for ($i = 0; $i < count($indices_disponibles); $i++)
        {
            $id_catalogo = $indices_disponibles[$i];
            echo ($i + 1) . ". " . space_right($catalogo[$id_catalogo]['nombre'], 25) . space_left(format_money($catalogo[$id_catalogo]['precio']), 12) . "  " . $catalogo[$id_catalogo]['duracion'] . "h\n";
        }
        $num_servicio = num_in_list("Seleccione el numero del servicio: ", count($indices_disponibles));
        $servicios[] = $indices_disponibles[$num_servicio - 1];

        $otro = strtolower(read_msg("Agregar otro servicio a esta cita? (s/n): "));
    } while ($otro === 's');

    $citas[] = [
        'empleado' => $id_empleado,
        'cliente' => $cliente,
        'dia' => $dia,
        'hora' => $hora,
        'servicios' => $servicios,
    ];

    echo "Cita registrada correctamente.\n";
}

function total_facturado_por_empleado($citas, $emp, $catalogo)
{
    echo "\n--- Total facturado por empleado ---\n";

    if (empty($emp))
    {
        echo "No hay empleados registrados.\n";
        return;
    }

    $totales = [];
    for ($i = 0; $i < count($emp); $i++)
    {
        $totales[] = 0;
    }

    foreach ($citas as $cita)
    {
        $id_emp = $cita['empleado'];
        $totales[$id_emp] = $totales[$id_emp] + total_date($cita, $catalogo);
    }

    $filas = [];
    for ($i = 0; $i < count($emp); $i++)
    {
        $filas[] = ['nombre' => $emp[$i]['nombre'], 'total' => $totales[$i]];
    }

    for ($i = 0; $i < count($filas); $i++)
    {
        for ($j = 0; $j < count($filas) - 1 - $i; $j++)
        {
            if ($filas[$j]['total'] < $filas[$j + 1]['total'])
            {
                $temp = $filas[$j];
                $filas[$j] = $filas[$j + 1];
                $filas[$j + 1] = $temp;
            }
        }
    }

    echo space_right("Empleado", 25) . space_left("Total facturado", 15) . "\n";
    echo line_repeat("-", 41) . "\n";
    foreach ($filas as $fila)
    {
        echo space_right($fila['nombre'], 25) . space_left(format_money($fila['total']), 15) . "\n";
    }
}

function servicio_mas_solicitado($citas, $catalogo)
{
    echo "\n--- Servicio mas solicitado ---\n";

    $conteo = [];
    $facturado = [];
    for ($i = 0; $i < count($catalogo); $i++)
    {
        $conteo[] = 0;
        $facturado[] = 0;
    }

    foreach ($citas as $cita)
    {
        foreach ($cita['servicios'] as $id_servicio)
        {
            $conteo[$id_servicio] = $conteo[$id_servicio] + 1;
            $facturado[$id_servicio] = $facturado[$id_servicio] + $catalogo[$id_servicio]['precio'];
        }
    }

    $total_servicios = 0;
    for ($i = 0; $i < count($conteo); $i++)
    {
        $total_servicios = $total_servicios + $conteo[$i];
    }

    if ($total_servicios === 0)
    {
        echo "No hay servicios registrados todavia.\n";
        return;
    }

    $id_ganador = 0;
    for ($i = 1; $i < count($conteo); $i++)
    {
        if ($conteo[$i] > $conteo[$id_ganador])
        {
            $id_ganador = $i;
        }
    }

    echo space_right("Servicio", 25) . space_left("Veces", 10) . space_left("Facturado", 15) . "\n";
    echo line_repeat("-", 51) . "\n";
    echo space_right($catalogo[$id_ganador]['nombre'], 25) . space_left($conteo[$id_ganador], 10) . space_left(format_money($facturado[$id_ganador]), 15) . "\n";
}

function agenda_de_un_dia($citas, $emp, $catalogo, $dias_validos)
{
    echo "\n--- Agenda de un dia ---\n";
    $dia = read_dia("Que dia desea consultar?: ", $dias_validos);

    $citas_del_dia = [];
    foreach ($citas as $cita)
    {
        if ($cita['dia'] === $dia)
        {
            $citas_del_dia[] = $cita;
        }
    }

    if (count($citas_del_dia) === 0)
    {
        echo "No hay citas registradas para {$dia}.\n";
        return;
    }

    for ($i = 0; $i < count($citas_del_dia); $i++)
    {
        for ($j = 0; $j < count($citas_del_dia) - 1 - $i; $j++)
        {
            if ($citas_del_dia[$j]['hora'] > $citas_del_dia[$j + 1]['hora'])
            {
                $temp = $citas_del_dia[$j];
                $citas_del_dia[$j] = $citas_del_dia[$j + 1];
                $citas_del_dia[$j + 1] = $temp;
            }
        }
    }

    echo space_right("Hora", 7) . space_right("Empleado", 20) . space_right("Cliente", 18) . "Servicios\n";
    echo line_repeat("-", 75) . "\n";
    foreach ($citas_del_dia as $cita)
    {
        $nombres_servicios = [];
        for ($i = 0; $i < count($cita['servicios']); $i++)
        {
            $id_servicio = $cita['servicios'][$i];
            $nombres_servicios[] = $catalogo[$id_servicio]['nombre'];
        }

        echo space_right(format_hora($cita['hora']), 7);
        echo space_right($emp[$cita['empleado']]['nombre'], 20);
        echo space_right($cita['cliente'], 18);
        echo unir_textos($nombres_servicios, ", ") . "\n";
    }
}

function deteccion_conflictos($citas, $emp, $catalogo)
{
    echo "\n--- Deteccion de conflictos de agenda ---\n";

    $encontrados = false;
    $total = count($citas);

    for ($i = 0; $i < $total; $i++)
    {
        for ($j = $i + 1; $j < $total; $j++)
        {
            $cita_a = $citas[$i];
            $cita_b = $citas[$j];

            if ($cita_a['empleado'] !== $cita_b['empleado'] || $cita_a['dia'] !== $cita_b['dia'])
            {
                continue;
            }

            $inicio_a = $cita_a['hora'];
            $fin_a = end_date($cita_a, $catalogo);
            $inicio_b = $cita_b['hora'];
            $fin_b = end_date($cita_b, $catalogo);

            if ($inicio_a < $fin_b && $inicio_b < $fin_a)
            {
                $encontrados = true;
                $nombre_empleado = $emp[$cita_a['empleado']]['nombre'];
                echo "Conflicto: {$nombre_empleado} el {$cita_a['dia']} entre {$cita_a['cliente']} ";
                echo "(" . format_hora($inicio_a) . "-" . format_hora($fin_a) . ") y {$cita_b['cliente']} (" . format_hora($inicio_b) . "-" . format_hora($fin_b) . ")\n";
            }
        }
    }

    if (!$encontrados)
    {
        echo "No se encontraron conflictos de agenda.\n";
    }
}

function liquidacion_comisiones($citas, $emp, $catalogo)
{
    echo "\n--- Liquidacion de comisiones ---\n";

    if (empty($emp))
    {
        echo "No hay empleados registrados.\n";
        return;
    }

    $totales = [];
    $num_citas = [];
    for ($i = 0; $i < count($emp); $i++)
    {
        $totales[] = 0;
        $num_citas[] = 0;
    }

    foreach ($citas as $cita)
    {
        $id_emp = $cita['empleado'];
        $totales[$id_emp] = $totales[$id_emp] + total_date($cita, $catalogo);
        $num_citas[$id_emp] = $num_citas[$id_emp] + 1;
    }

    $max_total = $totales[0];
    for ($i = 1; $i < count($totales); $i++)
    {
        if ($totales[$i] > $max_total)
        {
            $max_total = $totales[$i];
        }
    }

    echo space_right("Empleado", 22) . space_left("Citas", 8) . space_left("Facturado", 15) . space_left("Comision", 12) . space_left("Total a pagar", 15) . "\n";
    echo line_repeat("-", 76) . "\n";

    for ($i = 0; $i < count($emp); $i++)
    {
        if ($num_citas[$i] >= 6)
        {
            $porcentaje = 0.12;
            $porcentaje_texto = "12";
        }
        else
        {
            $porcentaje = 0.08;
            $porcentaje_texto = "8";
        }

        $comision = $totales[$i] * $porcentaje;

        if ($totales[$i] === $max_total && $max_total > 0)
        {
            $bono = 50000;
        }
        else
        {
            $bono = 0;
        }

        $total_pagar = $comision + $bono;

        $etiqueta = $porcentaje_texto . "%";
        if ($bono > 0)
        {
            $etiqueta = $etiqueta . " +bono";
        }

        echo space_right($emp[$i]['nombre'], 22);
        echo space_left($num_citas[$i], 8);
        echo space_left(format_money($totales[$i]), 15);
        echo space_left($etiqueta, 12);
        echo space_left(format_money($total_pagar), 15);
        echo "\n";
    }
}

function cargar_datos_prueba(&$emp, &$citas)
{
    $emp = [
        ['nombre' => 'Laura Gomez',  'especialidades' => ['Facial', 'Antiedad']],
        ['nombre' => 'Carlos Perez', 'especialidades' => ['Masajes']],
        ['nombre' => 'Ana Ruiz',     'especialidades' => ['Uñas', 'Pedicure']],
        ['nombre' => 'Diego Torres', 'especialidades' => ['Corporal']],
    ];

    $citas = [
        ['empleado' => 0, 'cliente' => 'Ayla Gonzales',  'dia' => 'lunes',     'hora' => 480, 'servicios' => [0]],
        ['empleado' => 0, 'cliente' => 'Juana Rios',     'dia' => 'lunes',     'hora' => 660, 'servicios' => [1]],
        ['empleado' => 1, 'cliente' => 'Pedro Gomez',    'dia' => 'martes',    'hora' => 600, 'servicios' => [3]],
        ['empleado' => 1, 'cliente' => 'Luis Cano',      'dia' => 'martes',    'hora' => 780, 'servicios' => [4]],
        ['empleado' => 2, 'cliente' => 'Sofia Nuñez',    'dia' => 'miercoles', 'hora' => 540, 'servicios' => [1, 2]],
        ['empleado' => 2, 'cliente' => 'Valentina Cruz', 'dia' => 'miercoles', 'hora' => 840, 'servicios' => [1]],
        ['empleado' => 3, 'cliente' => 'Andres Silva',   'dia' => 'jueves',    'hora' => 480, 'servicios' => [5]],
        ['empleado' => 3, 'cliente' => 'Camila Rojas',   'dia' => 'jueves',    'hora' => 600, 'servicios' => [6]],
        ['empleado' => 0, 'cliente' => 'Jorge Diaz',     'dia' => 'viernes',   'hora' => 660, 'servicios' => [0, 1]],
        ['empleado' => 1, 'cliente' => 'Natalia Vega',   'dia' => 'viernes',   'hora' => 540, 'servicios' => [3, 4]],
        ['empleado' => 2, 'cliente' => 'Ricardo Mora',   'dia' => 'sabado',    'hora' => 480, 'servicios' => [2]],
        ['empleado' => 3, 'cliente' => 'Paula Ortiz',    'dia' => 'sabado',    'hora' => 540, 'servicios' => [5, 6]],
        ['empleado' => 0, 'cliente' => 'Esteban Lara',   'dia' => 'sabado',    'hora' => 780, 'servicios' => [1]],
        ['empleado' => 1, 'cliente' => 'Daniela Vargas', 'dia' => 'lunes',     'hora' => 900, 'servicios' => [3]],
        ['empleado' => 2, 'cliente' => 'Miguel Angel',   'dia' => 'martes',    'hora' => 960, 'servicios' => [2, 1]],
        ['empleado' => 2, 'cliente' => 'Julian Paez',    'dia' => 'miercoles', 'hora' => 570, 'servicios' => [2]],
    ];
}

function mostrar_menu()
{
    static $primera_vez = true;

    if ($primera_vez)
    {
        $primera_vez = false;
    }
    else
    {
        sleep(1);
    }
    system('clear');

    echo "\n============================================\n";
    echo " ADSO-SPA - Sistema de agenda\n";
    echo "============================================\n";
    echo "1. Registrar empleado\n";
    echo "2. Registrar cita\n";
    echo "3. Total facturado por empleado\n";
    echo "4. Servicio mas solicitado\n";
    echo "5. Agenda de un dia\n";
    echo "6. Deteccion de conflictos\n";
    echo "7. Liquidacion de comisiones\n";
    echo "8. Salir\n";
    echo "============================================\n";
}

function pausar()
{
    read_msg("\nPresione Enter para continuar...\n");
}

$opcion = "";

while ($opcion !== "8")
{
    mostrar_menu();
    $opcion = read_msg("Seleccione una opcion: ");

    $opciones_bloqueables = ["dp", "1", "2"];

    if (in_array($opcion, $opciones_bloqueables, true) && $datos_cargados)
    {
        echo "\nOpcion no disponible: los datos de prueba ya fueron cargados.\n";
        pausar();
        continue;
    }

    switch ($opcion)
    {
        case "dp":
            cargar_datos_prueba($emp, $citas);
            $datos_cargados = true;
            echo "\nDatos de prueba cargados exitosamente.\n";
            pausar();
            break;

        case "1":
            register_empleado($emp, $catalogo_servicio);
            pausar();
            break;

        case "2":
            register_date($citas, $emp, $catalogo_servicio, $dias_validos);
            pausar();
            break;

        case "3":
            total_facturado_por_empleado($citas, $emp, $catalogo_servicio);
            pausar();
            break;

        case "4":
            servicio_mas_solicitado($citas, $catalogo_servicio);
            pausar();
            break;

        case "5":
            agenda_de_un_dia($citas, $emp, $catalogo_servicio, $dias_validos);
            pausar();
            break;

        case "6":
            deteccion_conflictos($citas, $emp, $catalogo_servicio);
            pausar();
            break;

        case "7":
            liquidacion_comisiones($citas, $emp, $catalogo_servicio);
            pausar();
            break;

        case "8":
            echo "\nSaliendo del programa...\n";
            break;

        default:
            echo "\nOpcion invalida, intente de nuevo.\n";
            pausar();
    }
}
// esto no tiene ia, mera logitec.com