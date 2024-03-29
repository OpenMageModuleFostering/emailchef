<?php
/**
 * EMailChefjob.php.
 *
 * @method  int     getStoreId()
 * @method  string  getListid()
 * @method  int     getProcessId()
 * @method  void setStoreId(int $storeId) Set the Store ID
 * @method  void setProcessId(int $id) Set the Mailedup Process ID
 * @method  void setStatus(string $status)
 * @method  void setTries(int $num) How many times we've tried.
 * @method  void setType(string $type)  What type of job?
 * @method  void setQueueDatetime(string $dateTime) Set the Datetime the job was queued.
 * @method  void setStartDatetime(string $dateTime) Set when the job was started
 * @method  void setFinishDatetime(string $dateTime) Set when the job was finished / completed
 */
class EMailChef_EMailChefSync_Model_Job extends Mage_Core_Model_Abstract
{
    const STATUS_FINISHED = 'finished';
    const STATUS_QUEUED = 'queued';
    const STATUS_STARTED = 'started';
    const STATUS_IMPORTED = 'imported'; // NewImportProccess has been called

    const TYPE_MANUAL_SYNC = 0;
    const TYPE_AUTO_SYNC = 1;

    /**
     * Constructor.
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('emailchef/job');
    }

    /**
     * Increment the number of tries we've attempted.
     *
     * @return int
     */
    public function incrementTries()
    {
        $tries = (int) $this->getTries();
        ++$tries;
        $this->setTries($tries);

        return $tries;
    }

    /**
     * Mark as finished.
     *
     * @return \EMailChef_EMailChefSync_Model_Job
     */
    public function finished()
    {
        $this->setStatus(self::STATUS_FINISHED);
        $this->setFinishDatetime(gmdate('Y-m-d H:i:s'));

        return $this;
    }

    /**
     * Mark as Queued.
     *
     * @return \EMailChef_EMailChefSync_Model_Job
     */
    public function queue()
    {
        $this->setStatus(self::STATUS_QUEUED);
        $this->setQueueDatetime(gmdate('Y-m-d H:i:s'));

        return $this;
    }

    /**
     * Is this job finished?
     *
     * @return bool
     */
    public function isFinished()
    {
        return $this->getStatus() == self::STATUS_FINISHED;
    }

    /**
     * Is this job queued?
     *
     * @return bool
     */
    public function isQueued()
    {
        return $this->getStatus() == self::STATUS_QUEUED;
    }

    /**
     * Process the job / Start.
     *
     * @return bool
     */
    public function process()
    {
        $this->setStatus(self::STATUS_STARTED);
        $this->setStartDatetime(gmdate('Y-m-d H:i:s'));

        /*
         * Now we need to do the heavy lifting!
         */
    }

    /**
     * Get a collection of jobs in the Queue.
     *
     * @param   int
     *
     * @return EMailChef_EMailChefSync_Model_Mysql4_Job_Collection
     */
    public function fetchQueuedJobsCollection($type = null)
    {
        $collection = $this->getCollection();
        /* @var $collection EMailChef_EMailChefSync_Model_Mysql4_Job_Collection */
        $collection
            ->addFieldToSelect('*')
            ->addFieldToFilter('status', array('eq' => self::STATUS_QUEUED))
        ;

        if ($type !== null) {
            $collection->addFieldToFilter('type', array('eq' => (int) $type));
        }

        return $collection;
    }

    /**
     * Get a collection of jobs in the Queue marked as Queued or Started.
     *
     * @param   int
     *
     * @return EMailChef_EMailChefSync_Model_Mysql4_Job_Collection
     */
    public function fetchQueuedOrStartedJobsCollection($type = null)
    {
        $collection = $this->getCollection();
        /* @var $collection EMailChef_EMailChefSync_Model_Mysql4_Job_Collection */
        $collection
            ->addFieldToSelect('*')
            ->addFieldToFilter('status', array('in' => array(self::STATUS_QUEUED, self::STATUS_STARTED)))
        ;

        if ($type !== null) {
            $collection->addFieldToFilter('type', array('eq' => (int) $type));
        }

        return $collection;
    }

    /**
     * Get a collection of jobs in the Queue.
     *
     * @return EMailChef_EMailChefSync_Model_Mysql4_Job_Collection
     */
    public function fetchManualSyncQueuedJobsCollection()
    {
        return $this->fetchQueuedJobsCollection(self::TYPE_MANUAL_SYNC);
    }

    /**
     * Get a collection of jobs in the Queue.
     *
     * @return EMailChef_EMailChefSync_Model_Mysql4_Job_Collection
     */
    public function fetchAutoSyncQueuedJobsCollection()
    {
        return $this->fetchQueuedJobsCollection(self::TYPE_AUTO_SYNC);
    }

    /**
     * Get associated job data (customers to sync).
     *
     * @return array
     */
    public function getJobData()
    {
        $jobTasks = Mage::getModel('emailchef/sync');
        /* @var $jobTasks EMailChef_EMailChefSync_Model_Sync */
        return $jobTasks->fetchByJobId($this->getId());
    }

    /**
     * Mark as an Auto Sync Job.
     */
    public function setAsAutoSync()
    {
        $this->setType(self::TYPE_AUTO_SYNC);

        return $this;
    }

    /**
     * Mark as an Manul Sync Job, done using Sync/Segment feature.
     */
    public function setAsManualSync()
    {
        $this->setType(self::TYPE_MANUAL_SYNC);

        return $this;
    }

    /**
     * Whether job is an auto-sync job or manual.
     *
     * @return bool
     */
    public function isAutoSync()
    {
        return (int) $this->getType() === self::TYPE_AUTO_SYNC;
    }
}
