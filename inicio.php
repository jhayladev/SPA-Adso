<?php

    $dias_dispo = ['lunes','martes','miercoles','jueves','viernes','sabado'];

    $emp = [];

    $citas = [];

    $cargar_datos = false;

    $catalogo_servicio = [
        ['nombre' => 'Limpieza Facial', 'precio' => 80000, 'duracion' => 2],
        ['nombre' => 'Manicure', 'precio' => 35000, 'duracion' => 1],
        ['nombre' => 'Pedicure', 'precio' => 40000, 'duracion' => 1],
        ['nombre' => 'Masaje relajante', 'precio' => 90000, 'duracion' => 1],
        ['nombre' => 'Masaje descontrurante', 'precio' => 100000, 'duracion' => 1],
        ['nombre' => 'Exfoliacion corporal', 'precio' => 60000, 'duracion' => 1],
        ['nombre' => 'Tratamiento antiedad', 'precio' => 120000, 'duracion' => 2],
    ];

    function read_msg($msg)
    {
        return trim(readline($msg));
    }

    function validar_vacio($msg)
    {
        do{
            $valor = read_msg($msg);
            if ($valor === ''){
                echo "Este campo no puede estar vacio";
            }
        }while($valor === '');
        return $valor;
    }

    function read_dias($msg, $dias_validos)
    {
        do {
            $dia = strtolower(read_msg($msg));
            $valido = in_array($dia, $dias_validos, true);
            if(!$valido)
            {
                $listDias = "";
                for ($i = 0; $i < count($dias_validos); $i++)
                {
                    if ($i > 0)
                    {
                        $listDias = $listDias . ", ";
                    }
                    $listDias = $listDias . $dias_validos[$i];
                }
                echo "Dia invalido. Utilize uno de los siguientes: " . $listDias . "\n";
            }
        } while (!$valido);
        return $dia;
    }

    function read_hora($msg)
    {
        do{ 
            $hora = read_msg($msg);
            $valido = ctype_digit($hora) && (int)$hora >= 8 && (int)$hora <=18;
            if(!$valido){
                echo "La hora debe ser un numero entre 8 y 18. \n";
            }
        } while(!$valido);
        return (int)$hora;
    }

    function num_in_list ($msg, $items)
    {
        do{
            $num = read_msg($msg);
            $valido = ctype_digit($num) && (int)$num >= 1 && (int)$num <= $items;
            if(!$valido){
                echo "Numero invalido, intente otra vez. \n";
            }
        }while(!$valido);
        return (int)$num ;
    }

    function format_money ($valor)
    {
        return "$" . number_format($valor, 0, ',', '.');
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
            $texto = " " . $texto ;
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

    function duration_date($cita, $catalogo)
    {
        $total = 0;
        foreach($cita['servicios'] as $idServicio)
        {
            $total += $catalogo[$idServicio]['duracion'];
        }
        return $total;
    }

    function end_date($cita, $catalogo)
    {
        return $cita['hora'] + duration_date($cita, $catalogo);
    }

    function total_date($cita, $catalogo)
    {
        $total = 0;
        foreach($cita['servicios'] as $idServicio)
        {
            $total += $catalogo[$idServicio]['precio'];
        }
        return $total;
    }
    
    function register_empleado(&$empleado)
    {
        do{
            echo "\n--- Registrar empleado ---\n";
            $nombre = validar_vacio("Nombre: ");
            $especialidad = validar_vacio("Especialidad: ");

            $empleados[] = ['nombre' => $nombre, 'especialidad' => $especialidad];
            echo "Empleado registrado correctamente.\n";

            $otro = strtolower(leer("¿Registrar otro empleado? (s/n): "));
        }while($otro === 's');
    }

    function register_date()
    {
        
    }

    




    
?>