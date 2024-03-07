<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class LotteryModule extends Module
{
    public function __construct()
    {
        $this->name = 'lotterymodule';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Your Name';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '1.6',
            'max' => _PS_VERSION_
        ];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Lottery Module');
        $this->description = $this->l('A module for running a lottery.');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
    }

    public function install()
    {
        if (!parent::install() ||
            !$this->registerHook('displayTop') ||
            !$this->installDB()) {
            return false;
        }
        return true;
    }

    public function uninstall()
    {
        if (!$this->uninstallDB() || !parent::uninstall()) {
            return false;
        }
        return true;
    }

    private function installDB()
    {
        return Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS '._DB_PREFIX_.'lottery (
                id_lottery INT UNSIGNED NOT NULL AUTO_INCREMENT,
                date DATE NOT NULL,
                participants INT(10) UNSIGNED NOT NULL DEFAULT 0,
                PRIMARY KEY (id_lottery)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;'
        );
    }

    private function uninstallDB()
    {
        return Db::getInstance()->execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'lottery');
    }

    public function hookDisplayTop($params)
    {
        if (Tools::isSubmit('submit_lottery')) {
            $this->processLottery();
        }

        $this->context->smarty->assign([
            'lottery_form_url' => $this->context->link->getModuleLink('lotterymodule', 'display')
        ]);

        return $this->display(FILE, 'views/templates/hook/lottery_form.tpl');
    }

    protected function processLottery()
    {
        $date = date('Y-m-d');
        $row = Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'lottery WHERE date = \''.$date.'\'');

        if (!$row) {
            Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'lottery (date, participants) VALUES (\''.$date.'\', 1)');
            $participants = 1;
        } else {
            $participants = $row['participants'] + 1;
            Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'lottery SET participants = '.$participants.' WHERE date = \''.$date.'\'');
        }

        $isWinner = ($participants === mt_rand(1, $participants));
        if ($isWinner) {
            $this->context->smarty->assign('lottery_result', $this->l('Congratulations! You won!'));
        } else {
            $this->context->smarty->assign('lottery_result', $this->l('Sorry, you did not win. Try again tomorrow!'));
        }
    }
}
