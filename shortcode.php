<?php

add_action( 'init', 'aco_register_shortcodes' );

function aco_register_shortcodes(){
   add_shortcode('awcdp_deposit_table', 'aco_shortcode_deposit_function');
}

function aco_shortcode_deposit_function($atts, $content = null){
  
  extract(shortcode_atts(array(
      'order_id' => '',
   ), $atts));
  
	if($order_id == '') return;
	
	$order = wc_get_order($order_id);
	if ($order){
		$has_deposit = $order->get_meta('_awcdp_deposits_order_has_deposit', true);
        if ( $has_deposit == 'yes' ) {
			$schedule = $order->get_meta('_awcdp_deposits_payment_schedule', true);
			if ( is_array($schedule)){
			?>	
				
<p><?php esc_html_e('Partial payment details', 'deposits-partial-payments-for-woocommerce') ?></p>
<table class="woocommerce-table  awcdp_deposits_summary">
  <thead>
    <tr>
        <th ><?php esc_html_e('ID', 'deposits-partial-payments-for-woocommerce'); ?></th>
        <th ><?php esc_html_e('Payment', 'deposits-partial-payments-for-woocommerce'); ?></th>
        <th><?php esc_html_e('Amount', 'deposits-partial-payments-for-woocommerce'); ?></th>
        <th><?php esc_html_e('Status', 'deposits-partial-payments-for-woocommerce'); ?></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach($schedule as $timestamp => $payment){
        $title = '';
        if(isset($payment['title'])){
          $title  = $payment['title'];
        } else {
          if(!is_numeric($timestamp)){
            if($timestamp === 'unlimited'){
              $title = esc_html__('Future Payments', 'deposits-partial-payments-for-woocommerce');
            } elseif($timestamp === 'deposit'){
              $title = esc_html__('Deposit', 'deposits-partial-payments-for-woocommerce');
            } else {
              $title = $timestamp;
            }
          } else {
            $title =  date_i18n(wc_date_format(),$timestamp);
          }
      }
      $title = apply_filters('awcdp_partial_payment_title',$title,$payment);
      $payment_order = false;
      if(isset($payment['id']) && !empty($payment['id'])) $payment_order = wc_get_order($payment['id']);
      if(!$payment_order) continue;
      $payment_id = $payment_order ? $payment_order->get_order_number(): '-';
      $status = $payment_order ? wc_get_order_status_name($payment_order->get_status()) : '-';
      $amount = $payment_order ? $payment_order->get_total() : $payment['total'];
      $price_args = array('currency' => $payment_order->get_currency());
      ?>
      <tr>
          <td> <?php echo wp_kses_post( $payment_id); ?> </td>
          <td> <?php echo wp_kses_post( $title); ?> </td>
          <td> <?php echo wp_kses_post( wc_price($amount,$price_args)); ?> </td>
          <td> <?php echo wp_kses_post( $status); ?> </td>
      </tr>
    <?php
    }
    ?>
    </tbody>
</table>


		  <?php 
				
			}
		}
	}
}



?>
