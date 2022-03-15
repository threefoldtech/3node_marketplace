<div class="control-group">
    <label class="control-label" for="test_api_key">{__("test_api_key")}:</label>
    <div class="controls">
        <input type="text" name="payment_data[processor_params][test_api_key]" id="test_api_key" value="{$processor_params.test_api_key}" >
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="test_api_key">{__("live_api_key")}:</label>
    <div class="controls">
        <input type="text" name="payment_data[processor_params][live_api_key]" id="live_api_key" value="{$processor_params.live_api_key}" >
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="mode">{__("test_live_mode")}:</label>
    <div class="controls">
        <select name="payment_data[processor_params][mode]" id="mode">
            <option value="test" {if $processor_params.mode == "test"}selected="selected"{/if}>{__("test")}</option>
            <option value="live" {if $processor_params.mode == "live"}selected="selected"{/if}>{__("live")}</option>
        </select>
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="order_prefix">{__("order_prefix")}:</label>
    <div class="controls">
        <input type="text" name="payment_data[processor_params][order_prefix]" id="order_prefix" value="{$processor_params.order_prefix}" >
    </div>
</div>
