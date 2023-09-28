<?php

/**
 * La interfaz Target representa la interfaz que ya siguen las clases de la aplicación.
 */
interface Notification
{
    public function send(string $title, string $message): void;
}

/**
 * A continuación se muestra un ejemplo de la clase existente que sigue la interfaz Target. 
 * Lo cierto es que muchas apps reales pueden no tener esta interfaz claramente definida.
 * Si estás en ese situación, la mejor opción sería extender el Adaptador de una de las clases
 * existentes de la aplicación. Si eso te  resulta incómodo (por ejemplo, SlackNotification 
 * no parece una subclase de EmailNotification), entonces extraer una interfaz debería ser el primer paso.
 */
class EmailNotification implements Notification
{
    private string $adminEmail;

    public function __construct(string $adminEmail)
    {
        $this->adminEmail = $adminEmail;
    }

    public function send(string $title, string $message): void
    {
        // mail($this->adminEmail, $title, $message);
        echo "Sent email with title '$title' to '{$this->adminEmail}' that says '$message'.<br><br>";
    }
}

/*
 * SlackApi es una clase útil, incompatible con la interfaz Notification. 
 * No puedes simplemente ingresar y cambiar el código de la clase para seguir la interfaz de Notification, 
 * ya que el código podría estar proporcionado por una biblioteca de terceros.
 */
class SlackApi
{
    private string $login;
    private string $apiKey;

    public function __construct(string $login, string $apiKey)
    {
        $this->login = $login;
        $this->apiKey = $apiKey;
    }

    public function logIn(): void
    {
        // Send authentication request to Slack web service.
        echo "Logged in to a slack account '{$this->login}'.<br>";
    }

    public function sendMessage(string $chatId, string $message): void
    {
        // Send message post request to Slack web service.
        echo "Posted following message into the '$chatId' chat: '$message'.<br>";
    }    
}

/**
 * El Adaptador es una clase que vincula la interfaz Notification y la clase SlackApi.
 * En este caso, permite que la aplicación envíe notificaciones mediante Slack API.
 */
class SlackNotification implements Notification
{
    private SlackApi $slack;
    private string $chatId;

    public function __construct(SlackApi $slack, string $chatId)
    {
        $this->slack = $slack;
        $this->chatId = $chatId;
    }

    // Un Adaptador no sólo es capaz de adaptar interfaces, 
    // sino que también puede convertir datos entrantes al 
    // formato requerido por el Adaptado.
    public function send(string $title, string $message): void
    {
        $slackMessage = "#" . $title . "# " . strip_tags($message);
        $this->slack->logIn();
        $this->slack->sendMessage($this->chatId, $slackMessage);     
    }
}

/**
 * El código del cliente funciona con cualquier clase que siga la interfaz Notification.
 */
class ClientNotifier
{
    private Notification $notification;

    public function __construct(Notification $notification)
    {
        $this->notification = $notification;
    }

    public function sendAlert(): void
    {
        $title = 'Website is down!';
        $message = 'Our website is not responding. Call admins and bring it up!';

        $this->notification->send($title, $message);
    }
}

// Notification with mail
$notifier = new ClientNotifier(new EmailNotification('devs@mail.com'));
$notifier->sendAlert();

// Notification with Slack
$slackApi = new SlackApi('example.com', 'XXXXXXX');
$notifier = new ClientNotifier(new SlackNotification($slackApi, 'example.com devs'));
$notifier->sendAlert();
