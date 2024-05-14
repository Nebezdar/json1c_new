<?php
namespace App;

require_once(__DIR__ . "/../vendor/autoload.php");


use PDO;

// $input = file_get_contents("php://input");
// $cv = new ComagicVisitors($input);

/**
 * Обработка уведомлений Comagic о звонках
 *
 * @property PDO   $pdo Клиент БД
 * @property array $json Тело запроса полученного от Comagic
 *
 * @method __construct        Конструктор класса
 *
 * @method databaseConnection Проверка доменного имени сайта
 *                            и подключение к соответствующей БД
 *
 * @method callDataProcessing
 */
class ComagicVisitors
{
    protected array $json;
    protected PDO $pdo;
    protected string $suffix;

    protected string $response;

    /**
     * Конструктор класса. Преобразует тело запроса в массив
     * и запускает основные методы
     *
     * @param string $json Тело запроса полученное от Сomagic
     */
    public function __construct(string $json)
    {
        $this->json = json_decode($json, true);
        $this->domainIdentification();
        $this->json1c();
        $this->clientIndentification();
    }

    protected function domainIdentification(): void
    {
        switch ($this->json['site_domain_name']) {
            case "-#hy-fwtwd-site":
                $this->suffix = "HY";
                break;
            case "-#hyfwwd-site":
                $this->suffix = "HL";
                break;
            case "-#sw-fwsw-site":
                $this->suffix = "SW";
                break;
            case "-#cm-fcmzz-site":
                $this->suffix = "CZ";
                break;
            case "-#wm-fwikam-site":
                $this->suffix = "WM";
                break;
            default:
                new \Exception("Ошибка: Неверное доменное имя");
        }
        file_put_contents("php://output", "Work in process");
        $this->databaseConnection($this->suffix);
    }

    protected function databaseConnection(string $suffix): void
    {
        $this->pdo = new PDO(
            'mysql:host=' . $_ENV['DB_HOST_' . $suffix] . ';dbname=' . $_ENV['DB_DATABASE_' . $suffix],
            $_ENV['DB_USERNAME_' . $suffix],
            $_ENV['DB_PASSWORD_' . $suffix]
        );
    }



    protected function json1c(): void
    {
        $sql = file_get_contents(__DIR__ . "/../sql/json1c.sql");
        $this->pdo->query($sql);
    }

    protected function clientIndentification(): void
    {

            $dbn = "SELECT visitor_info FROM `client_id` 
                    WHERE 'client_id' = :client_id";
            $stn = $this->pdo->prepare($dbn);
            $stn->execute();
            $stn->bindParam('client_id', $this->response, PDO::PARAM_STR);
            if ($this->response = null)
            {
                $this->response = "";
            }
    }

    protected function droppedCallProcess(): void
    {
        $sql = file_get_contents(__DIR__ . "/../sql/droppedCall.sql");
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue("id", 0, PDO::PARAM_INT);
        $stmt->bindValue("client_id", $this->response, PDO::PARAM_STR);
        $stmt->bindValue("fluid_tag",  $this->json['fluid_tag'], PDO::PARAM_STR);
        $stmt->bindValue("client_mail", $this->json['client_mail'], PDO::PARAM_STR);
        $stmt->bindValue("client_mail_id", $this->json['client_mail_id'], PDO::PARAM_STR);
        $stmt->bindValue("client_code", $this->json['client_code'], PDO::PARAM_STR);
        $stmt->bindValue("invoice_id", $this->json['invoice_id'], PDO::PARAM_STR);
        $stmt->bindValue("invoice_status", $this->json['invoice_status'], PDO::PARAM_INT);
        $stmt->bindValue("invoice_number", $this->json['invoice_number'], PDO::PARAM_INT);
        $stmt->bindValue("invoice_date", $this->json['invoice_date'], PDO::PARAM_STMT);
        $stmt->bindValue("invoice_price", $this->json['invoice_price'], PDO::PARAM_INT);
        $stmt->execute();
    }


}
