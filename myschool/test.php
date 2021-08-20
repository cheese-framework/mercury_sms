<?

use App\Helper\DateLib;
use App\Notifiable\Mailable;

include_once "../init.php";
$data = [
    'to' => 'cletusokoys@gmail.com',
    'name' => 'Chibuike',
    'subject' => 'Urgent',
    'fromName' => 'Cizkid',
    'from' => 'calebchibuike110@gmail.com',
    'message' => 'This is the body of our email'
];

$mail = new Mailable('welcome', $data);
$mail->build()->send();
