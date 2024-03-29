<?php
/**
 * Log.php.
 */
class EMailChef_EMailChefSync_Model_Log extends Mage_Core_Model_Abstract
{
    const TYPE_DEBUG = 'DEBUG';
    const TYPE_API = 'API';
    const TYPE_JOB = 'JOB';
    const TYPE_JOB_DATA = 'SYNC';

    /**
     * Constructor.
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('emailchef/log');
    }
}
