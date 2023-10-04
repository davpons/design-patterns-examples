<?php

interface Boss
{
    public function answerHolidaysRequest(DateTimeImmutable $startDate, int $duration): string;
    public function answerSalaryIncreaseRequest(): string;
}

class BossReal implements Boss
{
    public function answerHolidaysRequest(DateTimeImmutable $startDate, int $duration): string
    {
        if ($duration > 15) {
            return 'Solicitud No aprobada, el período es demasiado largo<br>';
        }

        return 'Vacaciones autorizadas ' . $duration . ' días a partir del ' . $startDate->format('d/m/Y');
    }

    public function answerSalaryIncreaseRequest(): string
    {
        return 'Solicitud de aumento de sueldo recibida: No autorizada.';
    }
}

class BossProxy implements Boss
{
    private BossReal $bossReal;

    public function __construct(BossReal $bossReal)
    {
        $this->bossReal = $bossReal;
    }

    public function answerHolidaysRequest(DateTimeImmutable $startDate, int $duration): string
    {
        $response = 'Solicitud de vacaciones recibida primero por parte del proxy...<br>';
        $response .= 'Obteniendo respuesta del jefe real...<br>';
        $response .= $this->bossReal->answerHolidaysRequest($startDate, $duration);

        return $response;
    }

    public function answerSalaryIncreaseRequest(): string
    {
        $response  = 'Solicitud de aumento de sueldo recibida primero por parte del proxy...<br>';
        $response .= 'Obteniendo respuesta del jefe real...<br>';
        $response .= $this->bossReal->answerSalaryIncreaseRequest();

        return $response;
    }
}

class Employee
{
    private Boss $boss;

    public function __construct(Boss $boss)
    {
        $this->setBoss($boss);
    }

    public function setBoss(Boss $boss): void
    {
        $this->boss = $boss;
    }

    public function requestHolidays(DateTimeImmutable $startDate, int $duration)
    {
        echo 'El empleado envia solicitud de vacaciones...<br>';
        $answer = $this->boss->answerHolidaysRequest($startDate, $duration);
        echo 'Respuesta recibida del jefe: ' . $answer . '<br><br>';
    }

    public function requestSalaryIncrease(): void
    {
        echo 'El empleado envia solicitud de aumento de sueldo...<br>';
        $answer = $this->boss->answerSalaryIncreaseRequest();
        echo 'Respuesta recibida del jefe: ' . $answer . '<br><br>';
    }
}

class Client
{
    private BossReal $bossReal;

    public function __construct()
    {
        $this->bossReal = new BossReal();
    }

    public function executeWithoutProxy(): void
    {
        $this->execute($this->bossReal);
    }

    public function executeWithProxy(): void
    {
        $this->execute(new BossProxy($this->bossReal));
    }

    private function execute(Boss $boss): void
    {
        $employee = new Employee($boss);

        $employee->requestHolidays(new DateTimeImmutable(), 15);
        $employee->requestSalaryIncrease();
    }    
}

$client = new Client();
$client->executeWithoutProxy();
$client->executeWithProxy();
