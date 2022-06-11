<?php
namespace BMorais\Database;
/**
 * CLASSE BANCO
 *  Esta classe faz conexão com o banco de dados mysql utilizando o pdo
 *
 * @author Bruno Morais <brunomoraisti@gmail.com>
 * @version 2
 * @copyright GPL © 2021, bmorais.com
 * @date 2022-05-18
 * @package php
 * @subpackage class
 * @access private
 */


use App\Controllers\ErroController;
use App\Lib\EmailClass;
use PDO;
use PDOException;


class Connect
{

    /** @var PDOException */
    private static $error;

    /** @var PDO */
    private static $instance;

    /**
     * Connect constructor.
     */
    final private function __construct()
    {
    }
    /**
     * Connect clone.
     */
    private function __clone()
    {
    }

    public static function getInstance($database=CONFIG_DATA_LAYER["dbname"]):?PDO
    {
        if (!isset (self::$instance)) {

            try {
                self::$instance = new PDO(CONFIG_DATA_LAYER["driver"] . ":host=" . CONFIG_DATA_LAYER["host"] . ";dbname=" . $database . ";port=" . CONFIG_DATA_LAYER["port"],
                    CONFIG_DATA_LAYER["username"],
                    CONFIG_DATA_LAYER["passwd"],
                    CONFIG_DATA_LAYER["options"]);
            } catch (PDOException $e) {
                self::setError($e);
            }
        }

        return self::$instance;
    }

    /**
     * @return PDOException|null
     */
    public static function getError(): ?PDOException
    {
        return self::$error;
    }

    public static function setError(PDOException $e, string $sql=''){
        self::$error = $e;
        $message = "<h4>Erro! Problema ao tentar conectar com o banco de dados</h5><hr>";
        $message .= "<p><b>Arquivo:</b>  " . $e->getFile() . "<br/>";
        $message .= "<b>SQL:</b>  " . $sql . "<br/>";
        $message .= "<b>Linha:</b>  " . $e->getLine() . "<br/>";
        $message .= "<b>Mensagem:</b>  " . $e->getMessage() . "<br/>";
        $message .= "<b>Informações adicionais:</b>  " . $e->getMessage() . "<br/>" . $e->getCode() . "<br/>" . $e->getPrevious() . "<br/>" . $e->getTraceAsString() . "<br/></p>";

        if (CONFIG_DATA_LAYER["display_errors_details"]) {
            echo $message;
        } else {
            //echo "<h4>Ooops! Aconteceu algo inesperado, tente mais tarde! Nossa equipe já foi informada</h5><hr>";
            EmailClass::sendEmail("Erro no servidor | ".date('d/m/Y H:i'),$message,array(CONFIG_DEVELOPER['email']));
            (new ErroController())->database();
        }
        die;
    }
}

