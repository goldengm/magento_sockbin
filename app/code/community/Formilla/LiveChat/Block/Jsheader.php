<?php

class Formilla_LiveChat_Block_Jsheader extends Mage_Core_Block_Template
{
    protected $_chatId = false;


    public function getChatId() {
        if($this->_chatId === false)
            $this->_chatId = Mage::getStoreConfig('livechat/chat/id');

        return $this->_chatId;
    }

    public function isActive() {
        return
                $this->getChatId();
    }
}
