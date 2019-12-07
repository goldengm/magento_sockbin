<?php

/**
 * Class Snowdog_SuccessRegistration_Block_Adminhtml_Registration_Grid
 */
class Snowdog_SuccessRegistration_Block_Adminhtml_Registration_Grid
    extends Mage_Adminhtml_Block_Widget_Grid
{

    /**
     * Snowdog_SuccessRegistration_Block_Adminhtml_Registration_Grid constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('registrationGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setVarNameFilter('registration_filter');
    }

    /**
     * Prepare collection
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('snowsuccessregistration/registration')
            ->getCollection();

        $this->setCollection($collection);

        parent::_prepareCollection();

        return $this;
    }

    /**
     * Prepare columns
     *
     * @return $this
     *
     * @throws Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn('entity_id',
            array(
                'header'=> Mage::helper('snowsuccessregistration')
                    ->__('ID'),
                'width' => '50px',
                'type'  => 'number',
                'index' => 'entity_id',
            )
        );

        $this->addColumn('customer_email',
            array(
                'header'=> Mage::helper('snowsuccessregistration')
                    ->__('Customer Email'),
                'type'  => 'text',
                'index' => 'customer_email',
            )
        );

        $this->addColumn('order_id',
            array(
                'header'=> Mage::helper('snowsuccessregistration')->__('Order ID'),
                'type'  => 'number',
                'index' => 'order_id',
            )
        );

        return parent::_prepareColumns();
    }

    /**
     * Get grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }
}