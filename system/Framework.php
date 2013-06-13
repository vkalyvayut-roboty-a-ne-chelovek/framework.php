<?php

/*
 *
 * 1. жизненный цикл запроса
 * 1.1. получение запроса
 * 1.2. поиск подходящего роута в routes.php
 * 1.2.1. если route найден - вызов контроллера и экшена
 * 1.2.2. если не найден - проецирование(mapping) адреса на имя контроллера и экшена  - /имя-контроллера(Controller)/экшен(action)/параметры(params)
 *
 * */

class Framework
{
    private $controller;
    private $action;
    private $request;
    private $params;

    public function __construct()
    {
        $this->controller = NULL;
        $this->action = NULL;
        // список роутов
        $this->routes = require_once(ROUTES);

        $this->request = $_SERVER['REQUEST_URI'];

    }
    public function requestStart()
    {
        // поиск подходящего роута
        $route = $this->recognizeRoute($this->routes, $this->request);

        // если найден роут - пытаюсь получить контроллер для него
        if (isset($route))
        {
            $this->controller = ucfirst($route['controller']);
            $this->action = 'action_' . $route['action'];
            $this->params = $route['params'];
        }
        // если нет, пытаюсь найти контроллер соотвествующий запросу
        else
        {
            $controllerData = $this->recognizeController($this->request);

            $this->controller = ucfirst($controllerData['controller']);
            $this->action = 'action_' . strtolower($controllerData['action']);
            $this->params = $controllerData['params'];
        }
        // включаю файл контроллера
        require_once($this->findController($this->controller));

        $controllerName = 'Controller_' . $this->controller;

        // создают объект контроллера
        $currentController = new $controllerName($this->params);

        // проверяю на существование экшена в классе контроллера
        if (method_exists($currentController, $this->action))
        {
            $currentController->{$this->action}();
        }
        else
        {
            throw new Exception("Action {$this->action} does not exists in {$this->controller} controller");
        }
    }

    /*
     * Поиск подходящих роутов
     * $routes - массив с роутами
     * $request - запрашиваемый адрес
     *
     *
     * */
    public function recognizeRoute($routes, $request)
    {
        // будет содрежать совпадения для подгруп паттерна
        $matches = array();

        foreach($routes as $route => $data)
        {
            if (preg_match($route, $request, $matches))
            {
                $data['params'] = $this->_generateParamsForRoute($matches, $data['params']); // отдаю список совпадений ($matches), и список для проецирования этих совпадений на параметры ($data['params'])

                return $data;
            }
        }
    }

    /*
     * нахожу контроллер, срабатывает если не найдены роуты
     *
     * */
    public function recognizeController($request)
    {
        $data = array();
        $request = $this->_removeEmptyParts(explode('/', $request));

        // если заданы и контроллер и экшен - производится генерация параметров
        if (count($request) >= 2)
        {
            $data['controller'] = $request[0];
            $data['action'] = $request[1];
            $data['params'] = $this->_generateParamsForController($request);

            return $data;
        }
        // иначе возбудить исключение

        throw new Exception('Controller does not recognized!');
    }

    /*
     *
     * Поиск файла контроллера в директории контроллеров
     *
     * */
    public function findController($controller)
    {
        $fileList = scandir(CONTROLLER_DIR);

        $pattern = "!^$controller.php$!";

        foreach($fileList as $k => $filename)
        {
            if (preg_match($pattern, $filename))
            {
                return CONTROLLER_DIR . $filename;
            }
        }

        throw new Exception('Controller file not found!');
    }

    /*
     *
     * Служебная функция - удаляет пустые строки из массива
     *
     * */
    protected function _removeEmptyParts($data)
    {
        $_res = array();

        foreach($data as $k => $v)
        {
            if (! empty($v))
            {
                $_res[] = $v;
            }
        }

        return $_res;
    }


    /*
     *
     * Функция генерирует массив с параметрами (назависимо от того, есть ли они на самом деле или нет, если параметров нет - возвращает пустой массив)
     * $data - список совпадений в паттерне
     * $list - список совпадений, из которых генерируются параметры
     *
     * */
    protected function _generateParamsForRoute($data, $list)
    {
        $_res = array();


        // проверка - если номер сопадения больше количества совпадений вообще.
        if ((count($list) > 0) && (max(array_values($list)) + 1) > count($data))
        {
            return NULL;
        }

        // создает массив с указанными группами сопадений для генерации параметров
        foreach($list as $k => $map)
        {
            $_res[] = $data[$map];
        }

        return $_res;
    }

    /*
     *
     * Функция генерирует массив с параметрами (назависимо от того, есть ли они на самом деле или нет, если параметров нет - возвращает пустой массив)
     * $data - массив с частями запроса
     *
     * */
    protected function _generateParamsForController($data)
    {
        return array_splice($data, 2);
    }



}