{if fn_allowed_for('ULTIMATE') && !$runtime.company_id || $runtime.simple_ultimate || fn_allowed_for('MULTIVENDOR')}
    {include file="common/subheader.tpl" title=__("text_mollie_status_map") target="#text_mollie_status_map"}
    <div id="text_mollie_status_map" class="in collapse">
        {assign var="statuses" value=$smarty.const.STATUSES_ORDER|fn_get_simple_statuses}
        <div class="control-group">
            <label class="control-label" for="elm_mollie_refunded">{__("refunded")}:</label>

            <div class="controls">
                <select name="mollie_settings[mollie_statuses][refunded]" id="elm_mollie_refunded">
                    {foreach from=$statuses item="s" key="k"}
                        <option value="{$k}"
                                {if (isset($mollie_settings.mollie_statuses.refunded) && $mollie_settings.mollie_statuses.refunded == $k) || (!isset($mollie_settings.mollie_statuses.refunded) && $k == 'I')}selected="selected"{/if}>{$s}</option>
                    {/foreach}
                </select>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="elm_mollie_completed">{__("paid")}:</label>

            <div class="controls">
                <select name="mollie_settings[mollie_statuses][paid]" id="elm_mollie_paid">
                    {foreach from=$statuses item="s" key="k"}
                        <option value="{$k}"
                                {if (isset($mollie_settings.mollie_statuses.paid) && $mollie_settings.mollie_statuses.paid == $k) || (!isset($mollie_settings.mollie_statuses.paid) && $k == 'P')}selected="selected"{/if}>{$s}</option>
                    {/foreach}
                </select>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="elm_mollie_pending">{__("pending")}:</label>

            <div class="controls">
                <select name="mollie_settings[mollie_statuses][pending]" id="elm_mollie_pending">
                    {foreach from=$statuses item="s" key="k"}
                        <option value="{$k}"
                                {if (isset($mollie_settings.mollie_statuses.pending) && $mollie_settings.mollie_statuses.pending == $k) || (!isset($mollie_settings.mollie_statuses.pending) && $k == 'O')}selected="selected"{/if}>{$s}</option>
                    {/foreach}
                </select>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="elm_mollie_created">{__("open")}:</label>

            <div class="controls">
                <select name="mollie_settings[mollie_statuses][open]" id="elm_mollie_open">
                    {foreach from=$statuses item="s" key="k"}
                        <option value="{$k}"
                                {if (isset($mollie_settings.mollie_statuses.open) && $mollie_settings.mollie_statuses.open == $k) || (!isset($mollie_settings.mollie_statuses.open) && $k == 'O')}selected="selected"{/if}>{$s}</option>
                    {/foreach}
                </select>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="elm_mollie_denied">{__("cancelled")}:</label>

            <div class="controls">
                <select name="mollie_settings[mollie_statuses][cancelled]" id="elm_mollie_cancelled">
                    {foreach from=$statuses item="s" key="k"}
                        <option value="{$k}"
                                {if (isset($mollie_settings.mollie_statuses.cancelled) && $mollie_settings.mollie_statuses.cancelled == $k) || (!isset($mollie_settings.mollie_statuses.cancelled) && $k == 'I')}selected="selected"{/if}>{$s}</option>
                    {/foreach}
                </select>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="elm_mollie_expired">{__("expired")}:</label>

            <div class="controls">
                <select name="mollie_settings[mollie_statuses][expired]" id="elm_mollie_expired">
                    {foreach from=$statuses item="s" key="k"}
                        <option value="{$k}"
                                {if (isset($mollie_settings.mollie_statuses.expired) && $mollie_settings.mollie_statuses.expired == $k) || (!isset($mollie_settings.mollie_statuses.expired) && $k == 'F')}selected="selected"{/if}>{$s}</option>
                    {/foreach}
                </select>
            </div>
        </div>
    </div>
{/if}
