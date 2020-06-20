<?php
  document::$layout = 'ajax';

  if (empty($_GET['product_id'])) return;
  if (empty($_GET['currency_code'])) $_GET['currency_code'] = settings::get('store_currency_code');
  if (empty($_GET['currency_value'])) $_GET['currency_value'] = currency::$currencies[$_GET['currency_code']]['value'];

  $product = reference::product($_GET['product_id'], $_GET['language_code'], $_GET['currency_code'], $_GET['customer']['id']);
  if (empty($product->id)) return;

  if (empty($_POST)) {

    $fields = [
      'name',
      'sku',
      'gtin',
      'taric',
      'weight',
      'weight_class',
      'dim_x',
      'dim_y',
      'dim_z',
      'dim_class',
      'price',
      'tax',
    ];

    foreach ($fields as $field) {
      if (isset($product->$field)) $_POST[$field] = $product->$field;
    }

    $price = !empty($product->campaign['price']) ? $product->campaign['price'] : $product->price;
    $_POST['price'] = currency::format_raw($price, $_GET['currency_code'], $_GET['currency_value']);
    $_POST['tax'] = tax::get_tax($price, $product->tax_class_id, $_GET['customer']);
  }
?>

<div id="modal-add-order-item" class="modal fade" style="max-width: 640px;">

  <h2><?php echo language::translate('title_add_product', 'Add Product'); ?></h2>

  <div class="modal-body">

    <?php echo functions::form_draw_form_begin('form_add_product', 'post'); ?>
      <?php echo functions::form_draw_hidden_field('product_id', $product->id); ?>

      <div class="form-group">
        <div class="thumbnail">
<?php
  list($width, $height) = functions::image_scale_by_width(320, settings::get('product_image_ratio'));
  echo '<img src="'. document::href_link(WS_DIR_APP . functions::image_thumbnail(FS_DIR_APP . 'images/' . $product->image, $width, $height, settings::get('product_image_clipping'))) .'" />';
?>
        </div>
      </div>

      <div class="row">
        <div class="form-group col-md-9">
          <label><?php echo language::translate('title_name', 'Name'); ?></label>
          <?php echo functions::form_draw_text_field('name', true); ?>
        </div>

        <div class="form-group col-md-3">
          <label><?php echo language::translate('title_product_id', 'Product ID'); ?></label>
          <?php echo functions::form_draw_number_field('product_id', true, 'readonly'); ?>
        </div>
      </div>

      <div class="row">
        <div class="form-group col-md-6">
          <label><?php echo language::translate('title_sku', 'SKU'); ?></label>
          <?php echo functions::form_draw_text_field('sku', true); ?>
        </div>

        <div class="form-group col-md-6">
          <label><?php echo language::translate('title_gtin', 'GTIN'); ?></label>
          <?php echo functions::form_draw_text_field('gtin', true); ?>
        </div>

        <div class="form-group col-md-6">
          <label><?php echo language::translate('title_taric', 'TARIC'); ?></label>
          <?php echo functions::form_draw_text_field('taric', true); ?>
        </div>
      </div>

      <div class="row">
        <div class="form-group col-md-4">
          <label><?php echo language::translate('title_weight', 'Weight'); ?></label>
          <div class="input-group">
            <?php echo functions::form_draw_decimal_field('weight', true, 2, 0); ?>
            <span class="input-group-addon"><?php echo functions::form_draw_weight_classes_list('weight_class', true, false, 'style="width: auto;"'); ?></span>
          </div>
        </div>

        <div class="form-group col-md-8">
          <label><?php echo language::translate('title_dimensions', 'Dimensions'); ?></label>
          <div class="input-group">
            <?php echo functions::form_draw_decimal_field('dim_x', true, 1, 0); ?>
            <span class="input-group-addon">x</span>
            <?php echo functions::form_draw_decimal_field('dim_y', true, 1, 0); ?>
            <span class="input-group-addon">x</span>
            <?php echo functions::form_draw_decimal_field('dim_z', true, 1, 0); ?>
            <span class="input-group-addon">
              <?php echo functions::form_draw_length_classes_list('dim_class', true, false, 'style="width: auto;"'); ?>
            </span>
          </div>
        </div>
      </div>

      <div class="row">
          <div class="form-group col-md-4">
          <label><?php echo language::translate('title_quantity', 'quantity'); ?></label>
          <?php echo functions::form_draw_decimal_field('quantity', 1); ?>
        </div>

          <div class="form-group col-md-4">
          <label><?php echo language::translate('title_price', 'Price'); ?></label>
          <?php echo functions::form_draw_currency_field($_GET['currency_code'], 'price', true); ?>
        </div>

          <div class="form-group col-md-4">
          <label><?php echo language::translate('title_tax', 'Tax'); ?></label>
          <?php echo functions::form_draw_currency_field($_GET['currency_code'], 'tax', true); ?>
        </div>
      </div>

      <div class="form-group">
        <?php if (!empty($product->stock_options)) {?>
        <table class="table table-default table-striped data-table">
          <thead>
            <tr>
              <th><?php echo language::translate('title_stock_option', 'Stock Option'); ?></th>
              <th><?php echo language::translate('title_sku', 'SKU'); ?></th>
              <th><?php echo language::translate('title_in_stock', 'In Stock'); ?></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($product->stock_options as $stock_option) { ?>
            <tr>
              <td><?php echo $stock_option['name']; ?></td>
              <td><?php echo $stock_option['sku']; ?></td>
              <td class="text-center"><?php echo (float)$stock_option['quantity']; ?></td>
            </tr>
            <?php } ?>
          </tbody>
          <tfoot>
            <tr>
              <td colspan="2"></td>
              <td class="text-right"><strong><?php echo language::translate('title_total', 'Total'); ?>: </strong><?php echo (float)$product->quantity; ?></td>
            </tr>
          </tfoot>
        </table>
        <?php } ?>
      </div>

      <div class="btn-group">
        <?php echo functions::form_draw_button('ok', language::translate('title_ok', 'OK'), 'button', '', 'ok'); ?>
        <?php echo functions::form_draw_button('cancel', language::translate('title_cancel', 'Cancel'), 'button', 'onclick="$.featherlight.close();"', 'cancel'); ?>
      </div>

    <?php echo functions::form_draw_form_end(); ?>
  </div>

</div>

<script>
  $('form[name="form_add_product"] button[name="ok"]').unbind('click').click(function(e){
    e.preventDefault();

    var error = false,
        form = $(this).closest('form');

    var item = {
      id: '',
      product_id: $(form).find(':input[name="product_id"]').val(),
      option_stock_combination: $(form).find(':input[name="option_stock_combination"]').val(),
      name: $(form).find(':input[name="name"]').val(),
      sku: $(form).find(':input[name="sku"]').val(),
      gtin: $(form).find(':input[name="gtin"]').val(),
      taric: $(form).find(':input[name="taric"]').val(),
      weight: parseFloat($(form).find(':input[name="weight"]').val()),
      weight_class: $(form).find(':input[name="weight_class"]').val(),
      dim_x: parseFloat($(form).find(':input[name="dim_x"]').val()),
      dim_y: parseFloat($(form).find(':input[name="dim_y"]').val()),
      dim_z: parseFloat($(form).find(':input[name="dim_z"]').val()),
      dim_class: $(form).find(':input[name="dim_class"]').val(),
      quantity: parseFloat($(form).find(':input[name="quantity"]').val()),
      price: parseFloat($(form).find(':input[name="price"]').val()),
      tax: parseFloat($(form).find(':input[name="tax"]').val())
    };

    var available_stock_options = <?php echo !empty($product->id) ? json_encode($product->options_stock, JSON_UNESCAPED_SLASHES) : '[]'; ?>;

    $.each(available_stock_options, function(i, stock_option) {
      var matched = false;
      $.each(stock_option.combination.split(','), function(j, current_stock_combination){
        if ($.inArray(current_stock_combination, selected_option_combinations) != -1) matched = true;
      });

      if (matched) {
        item.option_stock_combination = stock_option.combination;
        item.sku = stock_option.sku;
        item.gtin = stock_option.gtin;
        if (stock_option.weight > 0) {
          item.weight = stock_option.weight;
          item.weight_class = stock_option.weight_class;
        }
        if (stock_option.dim_x > 0) {
          item.dim_x = stock_option.dim_x;
          item.dim_y = stock_option.dim_y;
          item.dim_z = stock_option.dim_z;
          item.dim_class = stock_option.dim_class;
        }
      }
    });

    addItem(item);
    $.featherlight.close();
  });
</script>