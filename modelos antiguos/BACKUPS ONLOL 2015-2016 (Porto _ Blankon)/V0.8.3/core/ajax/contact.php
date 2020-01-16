<?php
require('../core.php');
if ($_POST['metadata'] != ""){
    // Es un SPAMbot
    exit();
}
if (isset($_POST['email']) && !empty($_POST['email']))
{
	if(empty($_SESSION['MAILDATE']))
	{
		$_SESSION['MAILDATE'] = time();


		// mensaje
		$mensaje = '
		Email del tio: '.$_POST['name'].$_POST['email'].'<br>
		Mensaje:: '.$_POST['comment'].'<br>
		';

		// Para enviar un correo HTML, debe establecerse la cabecera Content-type
		$cabeceras  = 'MIME-Version: 1.0' . "\r\n";
		$cabeceras .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

		// Cabeceras adicionales
		$cabeceras .= 'From: ONLoL <contact@onlol.net>' . "\r\n";
		$cabeceras .= 'Cc: contact@onlol.net' . "\r\n";
		$cabeceras .= 'Bcc: contact@onlol.net' . "\r\n";

		// Mail to site owner
		mail($_POST['email'], '¡Gracias por contactarnos!', '¡Hemos recibido tu solicitud de contacto! <br>Detalles:<br> Razón: '.$_POST['subject'].'<br> Mensaje: '.$_POST['comment'].'<br> ¡Que tengas un buen día! <br>', $cabeceras);
		
		// Mail to user
		mail('andirexulon@gmail.com', 'Solicitud de contacto: '.$_POST['subject'], '¡Hemos recibido una solicitud de contacto! <br>Detalles:<br> Razón: '.$_POST['subject'].'<br> Mensaje: '.$_POST['comment'].'<br> ¡Que tengas un buen día! <br>', $cabeceras);
		$db->query('INSERT INTO contacts (email,summoner,ip,reason,subject) VALUES ("'.$_POST['email'].'","'.$_POST['name'].'","'.ip().'","'.$_POST['comment'].'","'.$_POST['subject'].'")');
		echo 'Mensaje enviado correctamente';
	}
	else
	{
		if(($_SESSION['MAILDATE'] + 300) < time())
		{
			$_SESSION['MAILDATE'] = time();


		// mensaje
		$mensaje = '
		Email del tio: '.$_POST['name'].$_POST['email'].'<br>
		Mensaje:: '.$_POST['comment'].'<br>
		';

		// Para enviar un correo HTML, debe establecerse la cabecera Content-type
		$cabeceras  = 'MIME-Version: 1.0' . "\r\n";
		$cabeceras .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

		// Cabeceras adicionales
		$cabeceras .= 'From: ONLoL <contact@onlol.net>' . "\r\n";
		$cabeceras .= 'Cc: contact@onlol.net' . "\r\n";
		$cabeceras .= 'Bcc: contact@onlol.net' . "\r\n";

		// Mail to site owner
		mail($_POST['email'], 'Gracias por contactarnos!', '¡Hemos recibido tu solicitud de contacto! <br>Detalles:<br> Razón: '.$_POST['subject'].'<br> Mensaje: '.$_POST['comment'].'<br> ¡Que tengas un buen día! <br>', $cabeceras);
		
		// Mail to user
		mail('andirexulon@gmail.com', 'Solicitud de contacto: '.$_POST['subject'], '¡Hemos recibido una solicitud de contacto! <br>Detalles:<br> Razón: '.$_POST['subject'].'<br> Mensaje: '.$_POST['comment'].'<br> ¡Que tengas un buen día! <br>', $cabeceras);
		echo 'Mensaje enviado correctamente';
		$db->query('INSERT INTO contacts (email,summoner,ip,reason,subject) VALUES ("'.$_POST['email'].'","'.$_POST['name'].'","'.ip().'","'.$_POST['comment'].'","'.$_POST['subject'].'")');
		}
		else
		{
			echo 'No se ha podido enviar el correo electrónico debido a que contactaste recientemente.';
		}
		
	}

}
?>