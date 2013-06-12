<?php

require_once(SYSTEM_DIR . 'rain.tpl.class.php');

class Controller
{
    protected $params;
    protected $view;

    /*
     * конструтор принимает в качестве аргументов массив с параметрами(array(1, 2)) и массив с проекциями именя для параметров(array('name' => 1, 'name2' => 2))
     * */
    public function __construct($params = NULL, $paramMap = NULL)
    {
        // если задана проекция параметров на их имена, иначе они будут доступны только по числовому индексу
        if (isset($paramMap))
        {
            foreach($paramMap as $key => $param)
            {
                $this->params[$key] = $params[$param];
            }
        }
        else
        {
            $this->params = $params;
        }

        $this->_createView();

    }

    /*
     * метод инициализирующий представление
     *
     * */
    protected function _createView()
    {
        $this->view = new raintpl();
        raintpl::$tpl_dir = VIEW_DIR . str_replace('Controller_', '', get_class($this)) . '/'; // директория с представленияи для текущего класса. назание формируется имя-класс/имя-представления
        raintpl::$cache_dir = VIEW_CACHE_DIR; // директория для скомпилированных шаблонов
    }


    /*
     * простой метод получения параметра.
     *
     * */
    protected function getParam($name, $default = NULL)
    {
        $_t = $this->params;


        if (array_key_exists($name, $_t))
        {
            return $_t[$name];
        }
        else
        {
            return $default;
        }
    }
}