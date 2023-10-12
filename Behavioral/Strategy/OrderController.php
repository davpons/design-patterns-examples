<?php

/**
 * Este es el enrutador y controlador de nuestra aplicación.
 * Al recibir una solicitud, esta clase decide qué comportamiento
 * se debe ejecutar. Cuando la aplicación recibe una solicitud de pago,
 * la clase OrderController también decide qué método de pago debe utilizar
 * para procesar la solicitud. Por tanto, la clase actúa como
 * Contexto y Cliente al mismo tiempo.
 */
class OrderController
{
    public function post(string $url, array $data): void
    {
        echo sprintf(
            'Controller: Solicitud POST para %s con %s<br>',
            $url,
            json_encode($data)
        );

        $path = parse_url($url, PHP_URL_PATH);

        if (preg_match('#^/orders?$#', $path, $matches)) {
            $this->postNewOrder($data);
        } else {
            echo 'Controller: 404 page.<br>';
        }
    }

    public function get(string $url): void
    {
        echo sprintf(
            'Controller: Solicitud GET para %s<br>',
            $url
        );

        $path = parse_url($url, PHP_URL_PATH);
        $query = parse_url($url, PHP_URL_QUERY);
        $data = [];
        if ($query !== null) {
            parse_str($query, $data);
        }

        if (preg_match('#^/orders?$#', $path, $matches)) {
            $this->getAllOrders();
        } elseif (
            preg_match(
                '#^/order/([0-9]+?)/payment/([a-z]+?)(/return)?$#',
                $path,
                $matches
            )
        ) {
            $order = Order::get($matches[1]);

            // El método de pago (estrategia) se selecciona de acuerdo
            // con el valor transmitido junto con la solicitud.
            $paymentMethod = PaymentFactory::getPaymentMethod($matches[2]);

            if (!isset($matches[3])) {
                $this->getPayment($paymentMethod, $order, $data);
            } else {
                $this->getPaymentReturn($paymentMethod, $order, $data);
            }
        } else {
            echo 'Controller: 404 page.<br>';
        }
    }

    /**
     * POST /order {data}
     */
    public function postNewOrder(array $data): void
    {
        $order = new Order($data);
        echo 'Controller: Creado nuevo pedido #' . $order->id . '<br>';
    }

    /**
     * GET /orders
     */
    public function getAllOrders(): void
    {
        echo 'Controller: Todos los pedidos:<br>';
        foreach (Order::get() as $order) {
            echo json_encode($order, JSON_PRETTY_PRINT) . '<br>';
        }
    }

    /**
     * GET /order/123/payment/XX
     */
    public function getPayment(
        PaymentMethod $method,
        Order $order,
        array $data
    ): void {
        $form = $method->getPaymentForm($order);
        echo 'Controller: Este es el formulario de pago:<br>';
        echo $form . '<br>';
    }

    /**
     * GET /order/123/payment/XXX/return?key=AJHKSJHJ3423&success=true
     */
    public function getPaymentReturn(
        PaymentMethod $method,
        Order $order,
        array $data
    ): void {
        try {
            // Otro tipo de trabajo delegado al método de pago.
            if ($method->validateReturn($order, $data)) {
                echo 'Controller: Gracias por su pedido!<br>';
                $order->complete();
            }
        } catch (\Exception $ex) {
            echo 'Controller: Se ha producido una error (' . $ex->getMessage() .')<br>';
        }
    }
}

/**
 * Una representación simplificada de un pedido.
 */
class Order
{
    public int $id;
    public string $email;
    public string $product;
    public string $status;
    public float $total;

    /**
     * En aras de la simplicidad, almacenaremos todos los pedidos creados aquí...
     */
    private static array $orders = [];

    public static function get(int $orderId = null): mixed
    {
        if ($orderId === null) {
            return static::$orders;
        } else {
            return static::$orders[$orderId];
        }
    }

    /**
     * El constructor de pedidos asigna los valores de los campos del pedido.
     * Para simplificar las cosas, no existe validación alguna.
     */
    public function __construct(array $attributes)
    {
        $this->id = count(static::$orders);
        $this->status = 'new';
        foreach ($attributes as $key => $value) {
            $this->{$key} = $value;
        }
        static::$orders[$this->id] = $this;
    }

    /**
     * Este método es llamado cuando se paga un pedido.
     */
    public function complete(): void
    {
        $this->status = 'completed';
        echo 'Order: ' . $this->id . ' ahora está ' . $this->status;
    }
}

/**
 * Esta clase ayuda a producir un objeto de estrategia
 * adecuado para manejar un pago.
 */
class PaymentFactory
{
    public static function getPaymentMethod(string $id): PaymentMethod
    {
        switch ($id) {
            case 'cc':
                return new CreditCardPayment();
            case 'paypal':
                return new PayPalPayment();
            default:
                throw new \Exception('Método de pago desconocido');

        }
    }
}

/**
 * La interfaz de Estrategia describe cómo un cliente puede utilizar
 * varias Estrategias Concretas.
 * Tenga en cuenta que en la mayoría de los ejemplos que puede encontrar
 * en la Web, las estrategias tienden a hacer algo pequeño dentro de un
 * solo método. Sin embargo, en realidad, tus estrategias pueden ser
 * mucho más sólidas (al tener varios métodos, por ejemplo).
 */
interface PaymentMethod
{
    public function getPaymentForm(Order $order): string;
    public function validateReturn(Order $order, array $data): bool;
}

/**
 * Esta Estrategia Concreta proporciona una forma de pago
 * y valida las devoluciones de pagos con tarjeta de crédito.
 */
class CreditCardPayment implements PaymentMethod
{
    static private string $store_secret_key = 'swordfish';

    public function getPaymentForm(Order $order): string
    {
        $returnUrl = "https://our-website.com/order/{$order->id}/payment/cc/return";

        return <<<FORM
<form action="https://my-credit-card-processor.com/charge" method="POST">
    <input type="hidden" id="email" value="{$order->email}">
    <input type="hidden" id="total" value="{$order->total}">
    <input type="hidden" id="returnURL" value="$returnURL">
    <input type="text" id="cardholder-name">
    <input type="text" id="credit-card">
    <input type="text" id="expiration-date">
    <input type="text" id="ccv-number">
    <input type="submit" value="Pay">
</form>
FORM;
    }

    public function validateReturn(Order $order, array $data): bool
    {
        echo 'CreditCardPayment: validating...<br>';

        if ($data['key'] != md5($order->id . static::$store_secret_key)) {
            throw new Exception('La clave de pago no es válida.');
        }

        if (!isset($data['success']) || !$data['success'] || $data['success'] == 'false') {
            throw new \Exception('Payment failed.');
        }

        if (floatval($data['total']) < $order->total) {
            throw new \Exception('El importe del pago no es correcto.');
        }

        echo 'Hecho!<br>';

        return true;
    }
}

/**
 * Esta estrategia concreta proporciona un formulario de pago
 * y valida las devoluciones de pagos de PayPal.
 */
class PayPalPayment implements PaymentMethod
{
    public function getPaymentForm(Order $order): string
    {
        $returnURL = "https://our-website.com/order/{$order->id}/payment/paypal/return";

        return <<<FORM
<form action="https://paypal.com/payment" method="POST">
    <input type="hidden" id="email" value="{$order->email}">
    <input type="hidden" id="total" value="{$order->total}">
    <input type="hidden" id="returnURL" value="$returnURL">
    <input type="submit" value="Pay on PayPal">
</form>
FORM;
    }

    public function validateReturn(Order $order, array $data): bool
    {
        echo 'PayPalPayment: validando...';

        // ...

        echo 'Hecho!<br>';

        return true;
    }
}

$controller = new OrderController();

echo 'Creamos algunos pedidos...<br>';

$controller->post('/orders', [
    "email" => "me@example.com",
    "product" => "ABC Cat food (XL)",
    "total" => 9.95,
]);

$controller->post("/orders", [
    "email" => "me@example.com",
    "product" => "XYZ Cat litter (XXL)",
    "total" => 19.95,
]);

echo "<br>Cliente: Listar pedidos...<br>";

$controller->get('/orders');

echo '<br>Cliente: Me gustaría pagar con el segundo método, muéstrame el formulario de pago.<br>';

$controller->get('/order/1/payment/paypal');
