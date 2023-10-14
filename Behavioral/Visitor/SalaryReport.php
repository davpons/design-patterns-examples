<?php

/**
 * La interfaz Componente declara un método para aceptar objetos visitantes.
 * En este método, un componente concreto debe llamar a un método de visitante
 * específico que tenga el mismo tipo de parámetro que ese componente.
 */
interface Entity
{
    public function accept(Visitor $visitor): string;
}

class Company implements Entity
{
    public function __construct(
        private string $name,
        private array $departments
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    public function getDepartments(): array
    {
        return $this->departments;
    }

    /**
     * El componente Company debe llamar al método visitCompany.
     * El mismo principio se aplica a todos los componentes.
     */
    public function accept(Visitor $visitor): string
    {
        return $visitor->visitCompany($this);
    }
}

class Department implements Entity
{
    public function __construct(
        private string $name,
        private array $employees
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmployees(): array
    {
        return $this->employees;
    }

    public function getCost(): int
    {
        $cost = 0;
        foreach ($this->employees as $employee) {
            $cost += $employee->getSalary();
        }

        return $cost;
    }

    public function accept(Visitor $visitor): string
    {
        return $visitor->visitDepartment($this);
    }
}

class Employee implements Entity
{
    public function __construct(
        private string $name,
        private string $position,
        private int $salary
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    public function getPosition(): string
    {
        return $this->position;
    }

    public function getSalary(): int
    {
        return $this->salary;
    }

    public function accept(Visitor $visitor): string
    {
        return $visitor->visitEmployee($this);
    }
}

/**
 * La interfaz Visitor declara un conjunto de métodos de visita
 * para cada una de las clases de Componente Concreto.
 */
interface Visitor
{
    public function visitCompany(Company $company): string;
    public function visitDepartment(Department $department): string;
    public function visitEmployee(Employee $employee): string;
}

/**
 * El Visitante de Concreto debe proporcionar implementaciones
 * para cada clase de Componentes de Concreto.
 */
class SalaryReport implements Visitor
{
    public function visitCompany(Company $company): string
    {
        $output = '';
        $total = 0;

        foreach ($company->getDepartments() as $department) {
            $total += $department->getCost();
            $output .= '-- ' . $this->visitDepartment($department);
        }

        return $company->getName() . ' (' . $total . ')<br>' . $output;
    }

    public function visitDepartment(Department $department): string
    {
        $output = '';

        foreach ($department->getEmployees() as $employee) {
            $output .= '---- ' . $this->visitEmployee($employee);
        }

        return $department->getName() . ' (' . $department->getCost() . ')<br>' . $output;
    }

    public function visitEmployee(Employee $employee): string
    {
        return $employee->getSalary() . ' ' . $employee->getName() . ' (' . $employee->getPosition() . ')<br>';
    }
}

/**
 * Código cliente
 */
$mobileDev = new Department('Mobile Development', [
    new Employee('Albert Falmore', 'diseñador', 80000),
    new Employee('Ali Halabay', 'programador', 110000),
    new Employee('Sara Konor', 'programador', 90000),
    new Employee('Monica Fernandez', 'Ingeniero', 120000),
    new Employee('Jorge Romero', 'Ingeniero', 120000),
]);
$techSupport = new Department("Tech Support", [
    new Employee("Larry Ulbrecht", "supervisor", 70000),
    new Employee("Elton Pale", "operator", 30000),
    new Employee("Rajeet Kumar", "operator", 30000),
    new Employee("John Burnovsky", "operator", 34000),
    new Employee("Sergey Korolev", "operator", 35000),
]);
$company = new Company('SuperStarDevelopment', [
    $mobileDev,
    $techSupport,
]);

$visitor = new SalaryReport();
echo 'Cliente: Puedo imprimir un informe para toda una empresa:<br>';
echo $company->accept($visitor);
echo '<br><br>';

echo 'Cliente: o un informe para diferentes entidades como un empleado, un departamento o toda la empresa:<br>';
$someEmployee = new Employee("Pepito Flores", "mantenimiento", 35000);
$differentEntities = [$someEmployee, $techSupport, $company];
foreach ($differentEntities as $entity) {
    echo $entity->accept($visitor);
}

// Posibles visitor alternativos:
// $export = new JSONExport(); 
// echo $company->accept($export);
