{capture name="mainbox"}
    {capture name="add_golocation"}
        {include file="addons/wk_cl_geolocation/views/wk_cl_geolocation/update.tpl" countries_list=$countries_list currencies_list=$currencies_list languages_list=$languages_list}
    {/capture}

     {capture name="sidebar"}
        {include file="common/saved_search.tpl" dispatch="wk_cl_geolocation.manage" view_type="wk_cl_geolocation"}
        {include file="addons/wk_cl_geolocation/views/wk_cl_geolocation/components/search_form.tpl" dispatch="wk_cl_geolocation.manage"}
    {/capture}  
    
    <form action="{""|fn_url}" method="post" target="_self" name="wk_geolocation_form">
        {include file="common/pagination.tpl" save_current_page=true save_current_url=true div_id=$smarty.request.content_id}
        {assign var="c_url" value=$config.current_url|fn_query_remove:"sort_by":"sort_order"}
        {assign var="c_icon" value="<i class=\"exicon-`$search.sort_order_rev`\"></i>"}
        {assign var="c_dummy" value="<i class=\"exicon-dummy\"></i>"}
        {assign var="rev" value=$smarty.request.content_id|default:"pagination_contents"}
        
        {if $location_list}
        <table width="100%" class="table table-middle">
        <thead>
                <tr>
                    <th width="1%" class="left">
                    {include file="common/check_items.tpl"}
                    </th>
                    <th width="15%"><a class="cm-ajax" href="{"`$c_url`&sort_by=country_code&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id={$rev}>{__("country")}{if $search.sort_by == "country_code"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a></th>
                    <th width="15%"><a class="cm-ajax" href="{"`$c_url`&sort_by=lang_code&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id={$rev}>{__("language")}{if $search.sort_by == "lang_code"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a></th>
                    <th width="14%"><a class="cm-ajax" href="{"`$c_url`&sort_by=currency_code&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id={$rev}>{__("currency")}{if $search.sort_by == "currency_code"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a></th>
                    <th width="8%">&nbsp;</th>
                    <th width="5%"><a class="cm-ajax" href="{"`$c_url`&sort_by=status&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id={$rev}>{__("status")}{if $search.sort_by == "status"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a></th>
                </tr>
            </thead>
             {foreach from=$location_list item="location"}
                <tr class="cm-row-status-{$location.status|lower}" data-ct-company-id="{$location.country_code}">
                    <td class="left">
                        <input type="checkbox" name="location_country[{$location.country_code}][country_code]" value="{$location.country_code}" class="cm-item" />
                    </td>
                    <td class="row_status">
                    {btn type="dialog" text=$location.country_code|fn_get_country_name href="wk_cl_geolocation.update?country_code=`$location.country_code`" target_id="content_group`$location.country_code`" prefix=$location.country_code country_code=$location.country_code lang_code=$location.lang_code currency_code=$location.currency_code title=__("edit_golocation") }
                    </td>
                    <td class="row_status">
                        {assign var="lang_code_" value=$location.lang_code}
                        {$languages.$lang_code_.name}
                    </td>
                    <td class="row_status">{assign var="currency_code_" value=$location.currency_code}{$currencies.$currency_code_.description}</td>
                    <td class="nowrap row_status">
                        <div class="hidden-tools">
                            {capture name="tools_list"}
                                <li>
                                    {btn type="dialog" text={__('edit')} href="wk_cl_geolocation.update?country_code=`$location.country_code`" target_id="content_group`$location.country_code`" prefix=$location.country_code country_code=$location.country_code lang_code=$location.lang_code currency_code=$location.currency_code title=__("edit_golocation") }
                                </li>
                                <li>{btn type="list" text=__("delete") href="wk_cl_geolocation.delete?country_code=`$location.country_code`" class="cm-confirm cm-post"}</li>
                            {/capture}
                            {dropdown content=$smarty.capture.tools_list}
                        </div>
                    </td>  
                    <td class="right nowrap row_status">
                        {include file="common/select_popup.tpl" id=$location.country_code status=$location.status hidden="" object_id_name="country_code" table="wk_cl_geolocation" popup_additional_class="`$popup_additional_class` dropleft" non_editable=$non_editable}
                    </td>
                </tr>
            {/foreach}
        </table>
        {else}
            <p class="no-items">{__("no_data")}</p>
        {/if}
        {include file="common/pagination.tpl" div_id=$smarty.request.content_id}
    </form>
{/capture}

{capture name="buttons"}
    {if $location_list}
        {capture name="tools_items"}
            <li>{btn type="delete_selected" dispatch="dispatch[wk_cl_geolocation.m_delete]" form="wk_geolocation_form"}</li>  
        {/capture}
        {dropdown content=$smarty.capture.tools_items}
        {include file="buttons/save.tpl" but_name="dispatch[wk_cl_geolocation.m_update]" but_role="submit-button" but_target_form="wk_geolocation_form"}
    {/if}
{/capture}

{capture name="adv_buttons"}
    {include file="common/popupbox.tpl" id="add_golocation" text=__("new_geolocation") title=__("add_golocation") content=$smarty.capture.add_golocation act="general" icon="icon-plus"}
{/capture}

{include file="common/mainbox.tpl" title={__('wk_geolocation')} content=$smarty.capture.mainbox  buttons=$smarty.capture.buttons sidebar=$smarty.capture.sidebar adv_buttons=$smarty.capture.adv_buttons select_languages=true}