<?php

namespace App\Http\Controllers;

use App\About;
use App\Mail\SendNumberMail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Sunra\PhpSimple\HtmlDomParser;

class ParserController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendNumber()
    {
        //$file_name = 'C:/Users/Julio/Documents/theWinningNumber.xml';
        //$file_name = 'http://www.flalottery.com/video/en/theWinningNumber.xml';
        $file_name = "http://www.flalottery.com/video/en/theWinningNumber.xml";

        echo("Archivo: ".$file_name);
        echo(" </br> ");
        echo(" </br> ");

        $html = HtmlDomParser::file_get_html($file_name);
        //$html = HtmlDomParser::file_get_html($file_name, false, null, 0);

        $htmlCode = $html->find('item[game=pick3]');

        echo("Code: ".$htmlCode[0]->plaintext);
        echo(" </br> ");
        echo(" </br> ");

        $dias_semana_esp = ["Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado"];
        $meses_esp = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
        $fechaToday = Carbon::now(config('app.timezone'));

        $fecha = $htmlCode[0]->plaintext;

        $pos = strpos($fecha,"winning numbers are ");
        $date = substr($fecha,$pos+37,10);
        $date = strtotime($date);
        $date = date('d/m/Y', $date);
        $mediodia_fecha = $date;

        $numero = substr($fecha,$pos+20,5);
        $mediodia_centena = substr($numero, 0, 1);
        $mediodia_fijo = substr($numero, 2, 1).substr($numero, 4, 1);

        $numero = substr($fecha,$pos+53,5);
        $noche_centena = substr($numero, 0, 1);
        $noche_fijo = substr($numero, 2, 1).substr($numero, 4, 1);

        $date = substr($fecha,$pos+71,10);
        $date = strtotime($date);
        $date = date('d/m/Y', $date);
        $noche_fecha = $date;

        $mediodia_fecha = Carbon::createFromFormat("d/m/Y", $mediodia_fecha);
        $noche_fecha = Carbon::createFromFormat("d/m/Y", $noche_fecha);
		
        $is_mediodia = true;
        $is_noche = true;

        //Comprobar si la fecha de la tirada es la misma de Hoy
        if($fechaToday->diffInDays($mediodia_fecha) != 0) {
            $mediodia_centena = '-';
            $mediodia_fijo = '-';
            $is_mediodia = false;
        }
        if($fechaToday->diffInDays($noche_fecha) != 0) {
            $noche_centena = '-';
            $noche_fijo = '-';
            $is_noche = false;
        }

        $fechaTodayFinal = "";
        $today_dia_semana = $dias_semana_esp[$fechaToday->dayOfWeek];
        $today_dia = $fechaToday->day;
        $today_mes = $meses_esp[$fechaToday->month - 1];
        $today_anno = $fechaToday->year;

        $fechaTodayFinal = $fechaTodayFinal.$today_dia_semana.", ".$today_dia." de ".$today_mes." de ".$today_anno;

        echo("Fecha Hoy: ".$fechaTodayFinal);
        echo(" </br> ");
        echo(" </br> ");

        if ($today_dia < 10) {
            $today_dia = '0'.$today_dia;
        }
        $fechaDB = $today_anno.'-'.$fechaToday->month.'-'.$today_dia;

        //Enviar datos para el envío del correo con la tirada del día
        $to = ['narvas@nauta.cu'];

        //Actualizar fechas de envío
        if ($is_mediodia && $is_noche) {
            $checkedMediodia = $this->isChecked($fechaDB, 'M');
            $checkedNoche = $this->isChecked($fechaDB, 'N');

            if ($checkedMediodia) {
                $mediodia_centena = '-';
            }
            if ($checkedNoche) {
                $noche_centena = '-';
            }

            echo("Fecha Hoy: ".$fechaTodayFinal);
            echo(" </br> ");
            echo("Mediodía Centena: ".$mediodia_centena);
            echo(" </br> ");
            echo("Mediodía Fijo: ".$mediodia_fijo);
            echo(" </br> ");
            echo("Noche Centena: ".$noche_centena);
            echo(" </br> ");
            echo("Noche Fijo: ".$noche_fijo);
            echo(" </br> ");

            if ($mediodia_centena != '-' && $noche_centena == '-') {
                echo("Sending email..."); echo(" </br> ");
                Mail::to($to)->send(new SendNumberMail($mediodia_centena, $mediodia_fijo, $noche_centena, $noche_fijo, $fechaTodayFinal));
                $this->insertChecked($fechaDB, 'M');
            }
            if ($mediodia_centena == '-' && $noche_centena != '-') {
                echo("Sending email..."); echo(" </br> ");
                Mail::to($to)->send(new SendNumberMail($mediodia_centena, $mediodia_fijo, $noche_centena, $noche_fijo, $fechaTodayFinal));
                $this->insertChecked($fechaDB, 'N');
            }
            if ($mediodia_centena != '-' && $noche_centena != '-') {
                echo("Sending email..."); echo(" </br> ");
                Mail::to($to)->send(new SendNumberMail($mediodia_centena, $mediodia_fijo, $noche_centena, $noche_fijo, $fechaTodayFinal));
                $this->insertChecked($fechaDB, 'M');
                $this->insertChecked($fechaDB, 'N');
            }
        }

        if ($is_mediodia && !$is_noche) {
            $checkedMediodia = $this->isChecked($fechaDB, 'M');

            if (!$checkedMediodia) {
                $noche_centena  = '-';
                echo("Sending email..."); echo(" </br> ");
                Mail::to($to)->send(new SendNumberMail($mediodia_centena, $mediodia_fijo, $noche_centena, $noche_fijo, $fechaTodayFinal));
                $this->insertChecked($fechaDB, 'M');
            }
        }

        if (!$is_mediodia && $is_noche) {
            $checkedNoche = $this->isChecked($fechaDB, 'N');

            if (!$checkedNoche) {
                $mediodia_centena = '-';
                echo("Sending email..."); echo(" </br> ");
                Mail::to($to)->send(new SendNumberMail($mediodia_centena, $mediodia_fijo, $noche_centena, $noche_fijo, $fechaTodayFinal));
                $this->insertChecked($fechaDB, 'N');
            }
        }

    }

    public function isChecked($fechaDB, $horario) {
        $isChecked = DB::table('checks')->where('fecha', $fechaDB)->where('horario', $horario)->first();

        if (count($isChecked) != 0) {
            return true;
        }
        return false;
    }

    public function insertChecked($fechaDB, $horario) {
        $sumarizedData = [
            'fecha' => $fechaDB,
            'horario' => $horario
        ];
        DB::table('checks')->insert($sumarizedData);
    }

}
