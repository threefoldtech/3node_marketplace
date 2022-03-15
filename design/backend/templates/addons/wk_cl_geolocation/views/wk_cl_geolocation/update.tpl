<div id="content_group{$id}">
    <form action="{""|fn_url}" method="post" name="add_geolocation_form" class="form-horizontal" >
        <div class="tabs cm-j-tabs">
            <ul class="nav nav-tabs">
                <li id="tab_general_{$id}" class="cm-js active"><a>{__("general")}</a></li>
            </ul>
        </div>
        <div class="cm-tabs-content" id="content_tab_general_{$id}">
            <fieldset>
                <input type="hidden" name="location_country_code" value="{$id}" />
                <div class="control-group">
                    <label for="elm_country_code" class="control-label cm-required">{__("country")}:</label>
                    <div class="controls">
                        <select id="elm_country_code" name="geolocation_data[country_code]" class="span7">
                            {foreach from=$countries_list key=code item=country_desc}
                                <option value="{$code}" {if $location_data.country_code eq $code} selected {/if}>
                                    {$country_desc}
                                </option>
                            {/foreach}
                        </select>
                    </div>
                </div>  

                <div class="control-group">
                    <label for="elm_lang_code" class="control-label cm-required">{__("language")}:</label>
                    <div class="controls">
                        <select id="elm_lang_code" name="geolocation_data[lang_code]" class="span7">
                            {foreach from=$languages_list key=lang_code item=lang}
                                <option value="{$lang.lang_code}" {if $location_data.lang_code eq $lang.lang_code}selected{/if}>{$lang.name}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>  
                <div class="control-group">
                    <label for="elm_currency" class="control-label cm-required">{__("currency")}:</label>
                    <div class="controls">
                        <select id="elm_currency" name="geolocation_data[currency_code]" class="span7">
                            {foreach from=$currencies item=currency_data}
                                <option value="{$currency_data.currency_code}" {if $location_data.currency_code==$currency_data.currency_code}selected{/if}>{$currency_data.description}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>

                {include file="common/select_status.tpl" input_name="geolocation_data[status]" id="elm_wk_geolocation_status" obj=$location_data}

                <div class="buttons-container">
                    {include file="buttons/save_cancel.tpl" but_name="dispatch[wk_cl_geolocation.update]" cancel_action="close" save=$country_code}
                </div>
            </fieldset>
        </div>
    </form>
</div>
