<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class MyLotteryModule extends Module
{
    public function __construct()
    {
        $this->name = 'mylotterymodule';
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

        $this->displayName = $this->l('My Lottery Module');
        $this->description = $this->l('A simple lottery module.');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
    }

    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        return parent::install() &&
            $this->registerHook('displayTop');
    }

    public function hookDisplayTop($params)
    {
        if (Tools::isSubmit('submit_lottery')) {
            $this->processLottery();
        }

        $this->context->smarty->assign([
            'lottery_form_url' => $this->context->link->getModuleLink('mylotterymodule', 'display')
        ]);

        return $this->display(FILE, 'views/templates/hook/lottery_form.tpl');
    }

    protected function processLottery()
    {
        $ipAddress = Tools::getRemoteAddr();
        $isWinner = $this->isWinner($ipAddress);

        if ($isWinner) {
            $this->context->smarty->assign('lottery_result', $this->l('Congratulations! You won!'));
        } else {
            $this->context->smarty->assign('lottery_result', $this->l('Sorry, you did not win. Try again tomorrow!'));
        }
    }

    protected function isWinner($ipAddress)
    {
        $ipLong = ip2long($ipAddress);
        $hash = crc32($ipLong);
        return $hash % 10000 === 0; 
    }
}
