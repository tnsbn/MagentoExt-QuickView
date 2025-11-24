<?php

class Chigusa_Quickview_Model_Gallery_Orientation
{

    public function toOptionArray()
    {
        return array(
            array(
                'value' => 'horizontal',
                'label' => 'Horizontal'
            ), 
            array(
                'value' => 'vertical',
                'label' => 'Vertical'
            )
        );
    }

}
