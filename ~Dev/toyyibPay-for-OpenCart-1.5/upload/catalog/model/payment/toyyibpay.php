<?php

/**
 * ToyyibPay OpenCart Plugin
 * 
 * @package Payment Gateway
 * @author ToyyibPay Team
 */
class ModelPaymentToyyibPay extends Model {

    public function getMethod($address, $total) {

        $this->load->language('payment/toyyibpay');

        if ($this->config->get('toyyibpay_status')) {

            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int) $this->config->get('toyyibpay_geo_zone_id') . "' AND country_id = '" . (int) $address['country_id'] . "' AND (zone_id = '" . (int) $address['zone_id'] . "' OR zone_id = '0')");
            
            if ($total < $this->config->get('toyyibpay_minlimit')) {
                $status = FALSE;
            } else if (!$this->config->get('toyyibpay_geo_zone_id')) {
                $status = TRUE;
            } elseif ($query->num_rows) {
                $status = TRUE;
            } else {
                $status = FALSE;
            }
        } else {
            $status = FALSE;
        }

        $method_data = array();

        if ($status) {
            $method_data = array(
                'code' => 'toyyibpay',
                'title' => $this->language->get('text_title'),
                'sort_order' => $this->config->get('toyyibpay_sort_order')
            );
        }

        return $method_data;
    }

}
