<?php

class Controller_Hello extends Controller
{
    public function __construct($params = NULL)
    {
        $map = array('name' => 0); // карта для проецирования параметров, теперь параметр name будет указывать на сожержимое неименоавного параметра с индексом 0
        parent::__construct($params,$map);
    }
    public function action_index()
    {
        // получаю записи из таблицы
        $data = Model::factory('Test')->find_many();

        // преобразую данные в массив
        $prepared = array();
        foreach($data as $k => $v)
        {
            $prepared[] = $v->as_array();
        }

        // передаю переменные шаблону
        $this->view->assign('name', $this->getParam('name', 'no-name'));
        $this->view->assign('data', $prepared);
        $this->view->assign('message', 'some-test');

        // выводу шаблон
        $this->view->draw('index');
    }

}