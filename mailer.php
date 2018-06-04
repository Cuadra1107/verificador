<?php 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

        function conectar(){ 
                $host        = "host = localhost"; 
                $port        = "port = 5432";
                $dbname      = "dbname = 'Ebeenezer'";
                $credentials = "user = Roger password = passw0rd";

                $GLOBALS['db'] = pg_connect("$host $port $dbname $credentials");

                $res = pg_query("SELECT A.id, A.expedienteav,A.create_date,A.peritoav, A.n_aviso,A.notasav,B.placa_veh as placa,A.aseguradoraav as aseguradora,B.anioveh as año, B.marcaveh as Marca_Carro, B.mod_veh as Modelo_Carro FROM av_cliente as A, veh_cliente as B where A.expedienteav = '1' and B.id = A.placa_av and A.create_date <= ( CURRENT_TIMESTAMP - INTERVAL '3 day')::date;");


                $myfile = fopen(date("Y-m-d").".csv", "w") or die("Unable to open file!");
                $txt="id,expedienteav,create_date,peritoav,n_aviso,notasav,placa,Aseguradora,año,marca_carro,modelo_carro\n";
                while($row = pg_fetch_assoc($res)){
                        if ($row["aseguradora"] == 1) {
                            $row["aseguradora"]="INS";
                        }elseif ($row["aseguradora"] == 2) {
                            $row["aseguradora"]="MAPFRE";
                        }elseif ($row["aseguradora"] == 3) {
                            $row["aseguradora"]="ASSA";                           
                        }elseif ($row["aseguradora"] == 4) {
                            $row["aseguradora"]="Oceanica";                         
                        }elseif ($row["aseguradora"] == 5) {
                            $row["aseguradora"]="Lafise";                          
                        }elseif ($row["aseguradora"] == 6) {
                            $row["aseguradora"]="Qualitas";                            
                        }
                        $txt.= $row["id"].",".$row["expedienteav"].",".$row["create_date"].",".$row["peritoav"].",".$row["n_aviso"].",".str_replace(array("\r\n", "\r", "\n",",")," ",$row["notasav"]).",".$row["placa"].",".$row["aseguradora"].",".$row["año"].",".$row["marca_carro"].",".$row["modelo_carro"]."\n";
                }
                fwrite($myfile, utf8_decode($txt));
                fclose($myfile);
        }

        function enviar_datos(){
                try{
                        $mail = new PHPMailer(true);                              // Passing `true` enables exceptions

                        //Server settings
                    $mail->SMTPDebug = 2;                                 // Enable verbose debug output
                    //$mail->isSMTP();                                      // Set mailer to use SMTP
                    $mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
                    $mail->SMTPAuth = true;                               // Enable SMTP authentication
                    $mail->Username = '';                 // SMTP username
                    $mail->Password = '';                           // SMTP password
                    $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
                    $mail->Port = 587;                                    // TCP port to connect to

                    //Recipients
                    $mail->setFrom('soporte@ebenezer.com', 'Servidor - Reporte');
                    $mail->From = 'soporte@ebenezer.com';
                    $mail->addAddress('r.cuadra2010@gmail.com');     // Add a recipient
                    $mail->addReplyTo('rocampo@rogcr.com');
                    //$mail->addCC('rocampo@rogcr.com');
                    //$mail->addCC('soporte@rogcr.com');
                    //$mail->addBCC('bcc@example.com');

                    //Attachments
                    $mail->addAttachment(date("Y-m-d").'.csv');         // Add attachments
                    //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

                    //Content
                    $mail->isHTML(true);                                  // Set email format to HTML
                    $mail->Subject = utf8_decode('Reporte para el día '.date("Y-m-d"));
                    $mail->Body    = 'Seguidamente se adjunta el reporte de gestiones para el día de hoy';
                    $mail->AltBody = 'Seguidamente se adjunta el reporte de gestiones para el día de hoy';

                    $mail->send();
                    echo 'Message has been sent';
                }
                catch(Exception $e){
                        echo "Error en algo ".$e;
                }
                
        }

        conectar();
        enviar_datos();
?>