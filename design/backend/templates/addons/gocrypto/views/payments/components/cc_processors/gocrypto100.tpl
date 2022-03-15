{if $addons.gocrypto.status == "D"}
    <div class="alert alert-block">
        <p>{__("gocrypto.addon_is_disabled_notice")}</p>
    </div>
{else}
    <div id="section_technical_details">

        <div class="control-group">
            <label class="control-label" for="client_id">Client ID:</label>
            <div class="controls">
                <input type="text" name="payment_data[processor_params][client_id]" id="client_id" value="{$processor_params.client_id}" >
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="client_secret">Client Secret:</label>
            <div class="controls">
                <input type="text" name="payment_data[processor_params][client_secret]" id="item_name" value="{$processor_params.client_secret}" >
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="mode">Test\Live mode:</label>
            <div class="controls">
                <select name="payment_data[processor_params][mode]" id="mode">
                    <option value="test" {if $processor_params.mode == "test"}selected="selected"{/if}>{__("test")}</option>
                    <option value="live" {if $processor_params.mode == "live"}selected="selected"{/if}>{__("live")}</option>
                </select>
            </div>
        </div>
    </div>
{/if}
