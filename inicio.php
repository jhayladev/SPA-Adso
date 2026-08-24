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

    function Read_Msg($msg)
    {
        return trim(readline($msg));
    }

    function Validar_Vacio($msg)
    {
        do{
            $valor = Read_Msg($msg);
            if ($valor === ''){
                echo "Este campo no puede estar vacio";
            }
        }while($valor === '');
        return $valor;
    }

    function ReadDias($msg, $dias_validos)
    {
        do {
            $dia = strtolower(Read_Msg($msg));
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

    function ReadHora($msg)
    {
        do{ 
            $hora = Read_Msg($msg);
            $valido = ctype_digit($hora) && (int)$hora >= 8 && (int)$hora <=18;
            if(!$valido){
                echo "La hora debe ser un numero entre 8 y 18. \n";
            }
        } while(!$valido);
        return (int)$hora;
    }

    function Num_in_List ($msg, $items)
    {
        do{
            $num = Read_Msg($msg);
            $valido = ctype_digit($num) && (int)$num >= 1 && (int)$num <= $items;
            if(!$valido){
                echo "Numero invalido, intente otra vez. \n";
            }
        }while(!$valido);
        return (int)$num ;
    }

    function Format_Money ($valor)
    {
        return "$" . number_format($valor, 0, ',', '.');
    }

    




    
?>