<?php
/**
 * Szhopping_Cart.php Global initialization file
 * Copyright MichaÅ‚ SokoÅ‚owski 2010
 *
 * @author Micha³ Soko³owski <msokolowski@example.com>
 */

if( !defined('_I_INIT') ) die();

/**
 * @author ms
 *
 */
class Shopping_Cart {
   static $class = false;
   public $contents = array();

   //   static $GET_raw = '', $GET_array = array();

   function __construct() {
      self::$class = $this;
   }

   static function g_global() {
      if(self::$class == false) {
         self::$class = new Shopping_Cart;
      }
      return self::$class;
   }

	function shoppingCart() {
		$this->reset();
	}

	function restore_contents() {
		global $customer_id;

		if (!tep_session_is_registered('customer_id')) return false;

		// insert current cart contents in database
		if (is_array($this->contents)) {
			reset($this->contents);
			while (list($products_id, ) = each($this->contents)) {

		  $query_check = tep_db_query("select p.products_id, p.products_status, p.seller_id, cus.customers_status as seller_status from " . TABLE_PRODUCTS . " p, " . TABLE_CUSTOMERS . " cus where p.seller_id = cus.customers_id and products_id = '" . tep_db_input($products_id) . "'");
		  	
		  if( tep_db_num_rows($query_check) ) {
		  	$check = tep_db_fetch_array($query_check);
		  	if( (int)$customer_id == $check['seller_id'] ) {
		  		$qty = -1;
		  		$this->removed_buyer_seller++;
		  	} elseif ( $check['products_status'] ==0 || $check['seller_status'] != 'ACTIVE') {
		  		$qty = -1;
		  		$this->removed_nonexist++;
		  	} else {
		  		$qty = $this->contents[$products_id]['qty'];
		  	}
		  } else {
		  	$qty = -1;
		  }
		  	
		  $product_query = tep_db_query("select products_id from " . TABLE_CUSTOMERS_BASKET . " where customers_id = '" . (int)$customer_id . "' and products_id = '" . tep_db_input($products_id) . "'");

		  if (!tep_db_num_rows($product_query)) {
		  	tep_db_query("insert into " . TABLE_CUSTOMERS_BASKET . " (customers_id, products_id, customers_basket_quantity, customers_basket_date_added) values ('" . (int)$customer_id . "', '" . tep_db_input($products_id) . "', '" . tep_db_input($qty) . "', '" . date('Ymd') . "')");
		  	if (isset($this->contents[$products_id]['attributes'])) {
		  		reset($this->contents[$products_id]['attributes']);
		  		while (list($option, $value) = each($this->contents[$products_id]['attributes'])) {
		  			tep_db_query("insert into " . TABLE_CUSTOMERS_BASKET_ATTRIBUTES . " (customers_id, products_id, products_options_id, products_options_value_id) values ('" . (int)$customer_id . "', '" . tep_db_input($products_id) . "', '" . (int)$option . "', '" . (int)$value . "')");
		  		}
		  	}
		  } else {
		  	tep_db_query("update " . TABLE_CUSTOMERS_BASKET . " set customers_basket_quantity = '" . tep_db_input($qty) . "' where customers_id = '" . (int)$customer_id . "' and products_id = '" . tep_db_input($products_id) . "'");
		  }
			}
		}

		// reset per-session cart contents, but not the database contents
		$this->reset(false);

		$products_query = tep_db_query("select products_id, customers_basket_quantity from " . TABLE_CUSTOMERS_BASKET . " where customers_id = '" . (int)$customer_id . "'");
		while ($products = tep_db_fetch_array($products_query)) {
			$this->contents[$products['products_id']] = array('qty' => $products['customers_basket_quantity']);
			// attributes
			$attributes_query = tep_db_query("select products_options_id, products_options_value_id from " . TABLE_CUSTOMERS_BASKET_ATTRIBUTES . " where customers_id = '" . (int)$customer_id . "' and products_id = '" . tep_db_input($products['products_id']) . "'");
			while ($attributes = tep_db_fetch_array($attributes_query)) {
				$this->contents[$products['products_id']]['attributes'][$attributes['products_options_id']] = $attributes['products_options_value_id'];
			}
		}

		$this->cleanup();
	}

	function reset($reset_database = false) {
		global $customer_id;

		$this->contents = array();
		$this->total = 0;
		$this->weight = 0;
		$this->content_type = false;

		if (tep_session_is_registered('customer_id') && ($reset_database == true)) {
			tep_db_query("delete from " . TABLE_CUSTOMERS_BASKET . " where customers_id = '" . (int)$customer_id . "'");
			tep_db_query("delete from " . TABLE_CUSTOMERS_BASKET_ATTRIBUTES . " where customers_id = '" . (int)$customer_id . "'");
			$this->removed_buyer_seller = 0;
		}

		unset($this->cartID);
		if (tep_session_is_registered('cartID')) tep_session_unregister('cartID');
	}

	function add_cart($products_id, $qty = '1', $attributes = '', $notify = true) {
		global $new_products_id_in_cart, $customer_id;

		$products_id_string = tep_get_uprid($products_id, $attributes);
		$products_id = tep_get_prid($products_id_string);

		if (defined('MAX_QTY_IN_CART') && (MAX_QTY_IN_CART > 0) && ((int)$qty > MAX_QTY_IN_CART)) {
			$qty = MAX_QTY_IN_CART;
		}

		$attributes_pass_check = true;

		if (is_array($attributes)) {
			reset($attributes);
			while (list($option, $value) = each($attributes)) {
				if (!is_numeric($option) || !is_numeric($value)) {
					$attributes_pass_check = false;
					break;
				}
			}
		}

		if (is_numeric($products_id) && is_numeric($qty) && ($attributes_pass_check == true)) {
			$check_product_query = tep_db_query("select p.products_status, p.products_quantity, p.products_quantity_unlimited, p.products_price, p.seller_id, cus.customers_status from " . TABLE_PRODUCTS . " p, " . TABLE_CUSTOMERS . " cus where p.seller_id = cus.customers_id and cus.customers_status = 'ACTIVE' and p.products_id = '" . (int)$products_id . "'");
			$check_product = tep_db_fetch_array($check_product_query);

			if(tep_not_null($customer_id) && $check_product['seller_id'] == $customer_id)
			return 'buyer_seller';
				

			if($check_product['products_quantity']<$qty && $check_product['products_quantity_unlimited'] == 0)
			return 'not_enough_products';
				
			if($check_product['products_price'] = 0)
			return 'not_to_sell';

			if (($check_product !== false) && ($check_product['products_status'] == '1')) {
				if ($notify == true) {
					$new_products_id_in_cart = $products_id;
					tep_session_register('new_products_id_in_cart');
				}

				if ($this->in_cart($products_id_string)) {
					$this->update_quantity($products_id_string, $qty, $attributes);
				} else {
					$this->contents[$products_id_string] = array('qty' => (int)$qty);
					// insert into database
					if (tep_session_is_registered('customer_id')) tep_db_query("insert into " . TABLE_CUSTOMERS_BASKET . " (customers_id, products_id, customers_basket_quantity, customers_basket_date_added) values ('" . (int)$customer_id . "', '" . tep_db_input($products_id_string) . "', '" . (int)$qty . "', '" . date('Ymd') . "')");

					if (is_array($attributes)) {
						reset($attributes);
						while (list($option, $value) = each($attributes)) {
							$this->contents[$products_id_string]['attributes'][$option] = $value;
							// insert into database
							if (tep_session_is_registered('customer_id')) tep_db_query("insert into " . TABLE_CUSTOMERS_BASKET_ATTRIBUTES . " (customers_id, products_id, products_options_id, products_options_value_id) values ('" . (int)$customer_id . "', '" . tep_db_input($products_id_string) . "', '" . (int)$option . "', '" . (int)$value . "')");
						}
					}
				}

				$this->cleanup();

				// assign a temporary unique ID to the order contents to prevent hack attempts during the checkout procedure
				$this->cartID = $this->generate_cart_id();
			}
		}
	}

	function update_quantity($products_id, $quantity = '', $attributes = '') {
		global $customer_id;

		$products_id_string = tep_get_uprid($products_id, $attributes);
		$products_id = tep_get_prid($products_id_string);

		if (defined('MAX_QTY_IN_CART') && (MAX_QTY_IN_CART > 0) && ((int)$quantity > MAX_QTY_IN_CART)) {
			$quantity = MAX_QTY_IN_CART;
		}

		$attributes_pass_check = true;

		if (is_array($attributes)) {
			reset($attributes);
			while (list($option, $value) = each($attributes)) {
				if (!is_numeric($option) || !is_numeric($value)) {
					$attributes_pass_check = false;
					break;
				}
			}
		}

		if (is_numeric($products_id) && isset($this->contents[$products_id_string]) && is_numeric($quantity) && ($attributes_pass_check == true)) {
			$this->contents[$products_id_string] = array('qty' => (int)$quantity);
			// update database
			if (tep_session_is_registered('customer_id')) tep_db_query("update " . TABLE_CUSTOMERS_BASKET . " set customers_basket_quantity = '" . (int)$quantity . "' where customers_id = '" . (int)$customer_id . "' and products_id = '" . tep_db_input($products_id_string) . "'");

			if (is_array($attributes)) {
				reset($attributes);
				while (list($option, $value) = each($attributes)) {
					$this->contents[$products_id_string]['attributes'][$option] = $value;
					// update database
					if (tep_session_is_registered('customer_id')) tep_db_query("update " . TABLE_CUSTOMERS_BASKET_ATTRIBUTES . " set products_options_value_id = '" . (int)$value . "' where customers_id = '" . (int)$customer_id . "' and products_id = '" . tep_db_input($products_id_string) . "' and products_options_id = '" . (int)$option . "'");
				}
			}
		}
	}

	function cleanup() {
		global $customer_id;

		reset($this->contents);
		while (list($key,) = each($this->contents)) {
			if ($this->contents[$key]['qty'] < 1) {
				unset($this->contents[$key]);
				// remove from database
				if (tep_session_is_registered('customer_id')) {
					tep_db_query("delete from " . TABLE_CUSTOMERS_BASKET . " where customers_id = '" . (int)$customer_id . "' and products_id = '" . tep_db_input($key) . "'");
					tep_db_query("delete from " . TABLE_CUSTOMERS_BASKET_ATTRIBUTES . " where customers_id = '" . (int)$customer_id . "' and products_id = '" . tep_db_input($key) . "'");
				}
			}
		}
	}

	function count_contents() {  // get total number of items in cart
		$total_items = 0;
		if (is_array($this->contents)) {
			reset($this->contents);
			while (list($products_id, ) = each($this->contents)) {
				$total_items += $this->get_quantity($products_id);
			}
		}

		return $total_items;
	}

	function get_quantity($products_id) {
		if (isset($this->contents[$products_id])) {
			return $this->contents[$products_id]['qty'];
		} else {
			return 0;
		}
	}

	function in_cart($products_id) {
		if (isset($this->contents[$products_id])) {
			return true;
		} else {
			return false;
		}
	}

	function remove($products_id) {
		global $customer_id;

		unset($this->contents[$products_id]);
		// remove from database
		if (tep_session_is_registered('customer_id')) {
			tep_db_query("delete from " . TABLE_CUSTOMERS_BASKET . " where customers_id = '" . (int)$customer_id . "' and products_id = '" . tep_db_input($products_id) . "'");
			tep_db_query("delete from " . TABLE_CUSTOMERS_BASKET_ATTRIBUTES . " where customers_id = '" . (int)$customer_id . "' and products_id = '" . tep_db_input($products_id) . "'");
		}

		// assign a temporary unique ID to the order contents to prevent hack attempts during the checkout procedure
		$this->cartID = $this->generate_cart_id();
	}

	function remove_all() {
		$this->reset();
	}

	function get_product_id_list() {
		$product_id_list = '';
		if (is_array($this->contents)) {
			reset($this->contents);
			while (list($products_id, ) = each($this->contents)) {
				$product_id_list .= ', ' . $products_id;
			}
		}

		return substr($product_id_list, 2);
	}

	function calculate() {
		global $currencies;

		$this->total = 0;
		$this->weight = 0;
		if (!is_array($this->contents)) return 0;

		reset($this->contents);
		while (list($products_id, ) = each($this->contents)) {
			$qty = $this->contents[$products_id]['qty'];

			// products price
			$product_query = tep_db_query("select products_id, products_price, products_tax_class_id, products_weight from " . TABLE_PRODUCTS . " where products_id = '" . (int)$products_id . "'");
			if ($product = tep_db_fetch_array($product_query)) {
				$prid = $product['products_id'];
				$products_tax = tep_get_tax_rate($product['products_tax_class_id']);
				$products_price = $product['products_price'];
				$products_weight = $product['products_weight'];

				$specials_query = tep_db_query("select specials_new_products_price from " . TABLE_SPECIALS . " where products_id = '" . (int)$prid . "' and status = '1'");
				if (tep_db_num_rows ($specials_query)) {
					$specials = tep_db_fetch_array($specials_query);
					$products_price = $specials['specials_new_products_price'];
				}

				$this->total += $currencies->calculate_price($products_price, $products_tax, $qty);
				$this->weight += ($qty * $products_weight);
			}

			// attributes price
			if (isset($this->contents[$products_id]['attributes'])) {
				reset($this->contents[$products_id]['attributes']);
				while (list($option, $value) = each($this->contents[$products_id]['attributes'])) {
					$attribute_price_query = tep_db_query("select options_values_price, price_prefix from " . TABLE_PRODUCTS_ATTRIBUTES . " where products_id = '" . (int)$prid . "' and options_id = '" . (int)$option . "' and options_values_id = '" . (int)$value . "'");
					$attribute_price = tep_db_fetch_array($attribute_price_query);
					if ($attribute_price['price_prefix'] == '+') {
						$this->total += $currencies->calculate_price($attribute_price['options_values_price'], $products_tax, $qty);
					} else {
						$this->total -= $currencies->calculate_price($attribute_price['options_values_price'], $products_tax, $qty);
					}
				}
			}
		}
	}

	function attributes_price($products_id) {
		$attributes_price = 0;

		if (isset($this->contents[$products_id]['attributes'])) {
			reset($this->contents[$products_id]['attributes']);
			while (list($option, $value) = each($this->contents[$products_id]['attributes'])) {
				$attribute_price_query = tep_db_query("select options_values_price, price_prefix from " . TABLE_PRODUCTS_ATTRIBUTES . " where products_id = '" . (int)$products_id . "' and options_id = '" . (int)$option . "' and options_values_id = '" . (int)$value . "'");
				$attribute_price = tep_db_fetch_array($attribute_price_query);
				if ($attribute_price['price_prefix'] == '+') {
					$attributes_price += $attribute_price['options_values_price'];
				} else {
					$attributes_price -= $attribute_price['options_values_price'];
				}
			}
		}

		return $attributes_price;
	}

	function get_products() {
		global $languages_id;

		if (!is_array($this->contents)) return false;

		$products_array = array();
		reset($this->contents);
		while (list($products_id, ) = each($this->contents)) {
			$products_query = tep_db_query("select p.products_id, pd.products_name, p.products_model, p.products_image, p.products_price, p.products_weight, p.products_tax_class_id, p.seller_id, c.customers_username from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_CUSTOMERS . " c where p.products_id = '" . (int)$products_id . "' and pd.products_id = p.products_id and pd.language_id = '" . (int)$languages_id . "' and c.customers_id = p.seller_id");
			if ($products = tep_db_fetch_array($products_query)) {
				$prid = $products['products_id'];
				$products_price = $products['products_price'];

				$specials_query = tep_db_query("select specials_new_products_price from " . TABLE_SPECIALS . " where products_id = '" . (int)$prid . "' and status = '1'");
				if (tep_db_num_rows($specials_query)) {
					$specials = tep_db_fetch_array($specials_query);
					$products_price = $specials['specials_new_products_price'];
				}

				$products_array[] = array('id' => $products_id,
                                    'name' => $products['products_name'],
                                    'model' => $products['products_model'],
                                    'image' => $products['products_image'],
                                    'price' => $products_price,
                                    'quantity' => $this->contents[$products_id]['qty'],
                                    'weight' => $products['products_weight'],
                                    'final_price' => ($products_price + $this->attributes_price($products_id)),
                                    'tax_class_id' => $products['products_tax_class_id'],
									'seller_id' => $products['seller_id'],
									'customers_username' => $products['customers_username'],
                                    'attributes' => (isset($this->contents[$products_id]['attributes']) ? $this->contents[$products_id]['attributes'] : ''));

			}
	  foreach ($products_array as $key => $row) {
	  	$customers_username[$key] = strtolower($row['customers_username']);
	  	$products_name[$key] = strtolower($row['name']);
	  }
	  @array_multisort($customers_username, SORT_ASC, $products_name, SORT_ASC, $products_array);
		}
		return $products_array;
	}

	function show_total() {
		$this->calculate();

		return $this->total;
	}

	function show_weight() {
		$this->calculate();

		return $this->weight;
	}

	function generate_cart_id($length = 5) {
		return tep_create_random_value($length, 'digits');
	}

	function get_content_type() {
		$this->content_type = false;

		if ( (DOWNLOAD_ENABLED == 'true') && ($this->count_contents() > 0) ) {
			reset($this->contents);
			while (list($products_id, ) = each($this->contents)) {
				if (isset($this->contents[$products_id]['attributes'])) {
					reset($this->contents[$products_id]['attributes']);
					while (list(, $value) = each($this->contents[$products_id]['attributes'])) {
						$virtual_check_query = tep_db_query("select count(*) as total from " . TABLE_PRODUCTS_ATTRIBUTES . " pa, " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . " pad where pa.products_id = '" . (int)$products_id . "' and pa.options_values_id = '" . (int)$value . "' and pa.products_attributes_id = pad.products_attributes_id");
						$virtual_check = tep_db_fetch_array($virtual_check_query);

						if ($virtual_check['total'] > 0) {
							switch ($this->content_type) {
								case 'physical':
									$this->content_type = 'mixed';

									return $this->content_type;
									break;
								default:
									$this->content_type = 'virtual';
									break;
							}
						} else {
							switch ($this->content_type) {
								case 'virtual':
									$this->content_type = 'mixed';

									return $this->content_type;
									break;
								default:
									$this->content_type = 'physical';
									break;
							}
						}
					}
				} else {
					switch ($this->content_type) {
						case 'virtual':
							$this->content_type = 'mixed';

							return $this->content_type;
							break;
						default:
							$this->content_type = 'physical';
							break;
					}
				}
			}
		} else {
			$this->content_type = 'physical';
		}

		return $this->content_type;
	}

	function unserialize($broken) {
		for(reset($broken);$kv=each($broken);) {
			$key=$kv['key'];
			if (gettype($this->$key)!="user function")
			$this->$key=$kv['value'];
		}
	}

	function get_sellers() {
		global $customer_id;

		$ids_products[] = 0;
		reset($this->contents);
		$this->sellers = array();
		while (list($products_id, ) = each($this->contents)) {
			$ids_products[] = (int)$products_id;
		}
		$sellers_check_query = tep_db_query("select DISTINCT(c.customers_id) as seller_id, c.customers_username as seller_username, c.customers_firstname, c.customers_lastname, c.customers_email_address, c.customers_telephone, c.customers_seller_commission, c.customers_seller_process_vat, c.customers_seller_company_school, c.customers_seller_public_name, c.customers_seller_public_email, c.customers_seller_public_telephone, c.customers_seller_public_url, c.customers_seller_public_message from " . TABLE_PRODUCTS . " p, " . TABLE_CUSTOMERS . " c where p.products_id IN (" . implode(',',$ids_products) . ") and p.seller_id = c.customers_id");
		while($sellers_check = tep_db_fetch_array($sellers_check_query)) {
				
			if(tep_not_null($customer_id) && $id==$customer_id) continue;
				
			$id = $sellers_check['seller_id'];
			$this->sellers[$id]['username'] = $sellers_check['seller_username'];
			$this->sellers[$id]['email_address'] = $sellers_check['customers_seller_public_email'];
			$this->sellers[$id]['fullname'] = $sellers_check['customers_seller_public_name'];
			$this->sellers[$id]['customers_telephone'] = $sellers_check['customers_seller_public_telephone'];
			$this->sellers[$id]['customers_seller_commission'] = $sellers_check['customers_seller_commission'];
			$this->sellers[$id]['customers_seller_process_vat'] = $sellers_check['customers_seller_process_vat'];
			$this->sellers[$id]['customers_seller_public_message'] = $sellers_check['customers_seller_public_message'];
			$this->sellers[$id]['customers_seller_public_url'] = $sellers_check['customers_seller_public_url'];
		}
		return $this->sellers;
	}

	function get_delivery_opt() {
		$ids_products[] = 0;
		reset($this->contents);
		$this->sellers = array();
		while (list($products_id, ) = each($this->contents)) {
			$ids_products[] = (int)$products_id;
		}

		$delivery_check_query = tep_db_query("select pd.products_id, pd.products_delivery from  " . TABLE_PRODUCTS_DESCRIPTION . " pd where pd.products_id IN (" . implode(',',$ids_products) . ")");
		while($delivery_check = tep_db_fetch_array($delivery_check_query)) {
			$this->delivery_opt[$delivery_check['products_id']] = stripslashes($delivery_check['products_delivery']);
		}
		return $this->delivery_opt;
	}

	function __sleep() {
		unset($this->sellers);
		unset($this->delivery_opt);
		return( array_keys( get_object_vars( $this ) ) );
	}

}

?>