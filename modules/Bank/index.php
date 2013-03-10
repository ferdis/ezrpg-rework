<?php
defined('IN_EZRPG') or exit;

class Module_Bank extends Base_Module {

    public function start() {
        //The player must be logged in
        requireLogin();

        switch ($_GET['act']) {
            case 'withdraw':
                $this->withdraw();
                break;
            
            case 'deposit':
                $this->deposit();
                break;
        }

        $this->tpl->display('bank.tpl');
    }

    private function withdraw() {
        if (isset($_POST['amount'])) {

            $amount = filter_input(INPUT_POST, 'amount', FILTER_SANITIZE_NUMBER_INT) ? : 0;

            if ($amount > $this->player->bank) {
                $this->setMessage('You don\'t have that much gold in the bank!', 'fail');
                header('Location: index.php?mod=Bank');
                exit;
            }

            $money = $this->player->money + $amount;
            $bank = $this->player->bank - $amount;

            $this->db->execute(
                'UPDATE `<ezrpg>players` SET `money` = ?, `bank` = ? WHERE `id` = ?', 
                array($money, $bank, $this->player->id)
            );
            
            $this->setMessage('You have withdrawn ' . $amount . ' gold from the bank!', 'good');
            header('Location: index.php?mod=Bank');
            exit;
        }
    }

    private function deposit() {
        if (isset($_POST['amount'])) {

            $amount = filter_input(INPUT_POST, 'amount', FILTER_SANITIZE_NUMBER_INT) ? : 0;

            if ($amount > $this->player->money) {
                $this->setMessage('You don\'t have that much gold in the bank!', 'fail');
                header('Location: index.php?mod=Bank');
                exit;
            }

            $money = $this->player->money - $amount;
            $bank = $this->player->bank + $amount;

            $this->db->execute(
                'UPDATE `<ezrpg>players` SET `money`=?, `bank`=? WHERE `id`=?', 
                array($money, $bank, $this->player->id)
            );
            
            $this->setMessage('You have deposited ' . $amount . ' gold into the bank!', 'good');
            $msg = 'You have deposit ' . $amount . ' money to the bank!';
            header('Location: index.php?mod=Bank');
            exit;
        }
    }
}